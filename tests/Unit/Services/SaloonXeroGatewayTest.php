<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Services;

use Calcinai\OAuth2\Client\Provider\Xero;
use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Contracts\XeroGateway;
use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use Dcodegroup\XeroIntegration\Exceptions\UnauthorizedXero;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Exceptions\XeroRateLimitExceededException;
use Dcodegroup\XeroIntegration\Exceptions\XeroValidationException;
use Dcodegroup\XeroIntegration\Http\Saloon\Endpoints\XeroEndpoint;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\FindXeroRecordRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\ListXeroRecordsRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\SaveXeroRecordRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\XeroActionBodyRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\XeroActionRequest;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Dcodegroup\XeroIntegration\XeroQuery;
use Illuminate\Support\Facades\RateLimiter;
use InvalidArgumentException;
use League\OAuth2\Client\Token\AccessToken;
use Mockery\MockInterface;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Laravel\Facades\Saloon;

beforeEach(function () {
    $this->token = XeroToken::factory()->create(['current_tenant_id' => 'tenant-1']);
    $this->endpoint = new XeroEndpoint(XeroApiEnum::PAYROLL_AU, 'Timesheets', 'TimesheetID', ['EmployeeID'], true);
});

test('requests carry the bearer token, tenant id and json accept header', function () {
    $mock = Saloon::fake([FindXeroRecordRequest::class => MockResponse::make(['Timesheets' => [['TimesheetID' => 'ts-1']]])]);

    app(XeroGateway::class)->find($this->endpoint, 'ts-1');

    $mock->assertSent(function ($request, $response) {
        $headers = $response->getPendingRequest()->headers();

        return $response->getPendingRequest()->getUrl() === 'https://api.xero.com/payroll.xro/1.0/Timesheets/ts-1'
            && $headers->get('Authorization') === 'Bearer '.$this->token->access_token
            && $headers->get('Xero-tenant-id') === 'tenant-1'
            && $headers->get('Accept') === 'application/json';
    });
});

test('find returns null for a missing record', function () {
    Saloon::fake([FindXeroRecordRequest::class => MockResponse::make(['Message' => 'not found'], 404)]);

    expect(app(XeroGateway::class)->find($this->endpoint, 'missing'))->toBeNull();
});

test('list builds a where clause with guid filters and walks every page', function () {
    $queries = [];

    Saloon::fake([
        ListXeroRecordsRequest::class => function (PendingRequest $request) use (&$queries) {
            $queries[] = $request->query()->all();
            $page = (int) $request->query()->get('page');

            return MockResponse::make(['Timesheets' => $page <= 2 ? [['TimesheetID' => "ts-{$page}"]] : []]);
        },
    ]);

    $records = app(XeroGateway::class)->list($this->endpoint, ['EmployeeID' => '11111111-1111-1111-1111-111111111111', 'Status' => 'DRAFT']);

    expect($records->pluck('TimesheetID')->all())->toBe(['ts-1', 'ts-2'])
        ->and($queries)->toHaveCount(3)
        ->and($queries[0]['where'])->toBe('EmployeeID=Guid("11111111-1111-1111-1111-111111111111") AND Status=="DRAFT"')
        ->and(array_column($queries, 'page'))->toBe([1, 2, 3]);
});

test('list escapes backslashes and quotes in string filters', function () {
    $queries = [];

    Saloon::fake([
        ListXeroRecordsRequest::class => function (PendingRequest $request) use (&$queries) {
            $queries[] = $request->query()->all();

            return MockResponse::make(['Timesheets' => []]);
        },
    ]);

    app(XeroGateway::class)->list($this->endpoint, ['Status' => 'a\\"b\\']);

    expect($queries[0]['where'])->toBe('Status=="a\\\\\\"b\\\\"');
});

test('list rejects non-guid values for guid filters', function () {
    Saloon::fake([ListXeroRecordsRequest::class => MockResponse::make(['Timesheets' => []])]);

    app(XeroGateway::class)->list($this->endpoint, ['EmployeeID' => 'x") OR (1==1']);
})->throws(InvalidArgumentException::class, "Filter 'EmployeeID' must be a GUID.");

test('list rejects invalid filter field names', function () {
    Saloon::fake([ListXeroRecordsRequest::class => MockResponse::make(['Timesheets' => []])]);

    app(XeroGateway::class)->list($this->endpoint, ['Status=="x" OR Foo' => 'y']);
})->throws(InvalidArgumentException::class);

test('record ids are url encoded in the request path', function () {
    $paths = [];

    Saloon::fake([
        FindXeroRecordRequest::class => function (PendingRequest $request) use (&$paths) {
            $paths[] = $request->getUri()->getPath();

            return MockResponse::make(['Timesheets' => []]);
        },
    ]);

    app(XeroGateway::class)->find($this->endpoint, '../Employees?x=1');

    expect($paths[0])->toEndWith('/Timesheets/..%2FEmployees%3Fx%3D1');
});

test('list makes a single call for an unpaginated endpoint', function () {
    $endpoint = new XeroEndpoint(XeroApiEnum::PAYROLL_AU, 'PayrollCalendars', 'PayrollCalendarID');
    $mock = Saloon::fake([ListXeroRecordsRequest::class => MockResponse::make(['PayrollCalendars' => [['PayrollCalendarID' => 'c-1']]])]);

    $records = app(XeroGateway::class)->list($endpoint);

    expect($records)->toHaveCount(1);
    $mock->assertSentCount(1);
});

test('payroll au dates are normalised to iso 8601', function () {
    $ms = Carbon::parse('2026-11-02', 'UTC')->getTimestampMs();
    Saloon::fake([FindXeroRecordRequest::class => MockResponse::make(['Timesheets' => [['StartDate' => "/Date({$ms}+0000)/"]]])]);

    expect(app(XeroGateway::class)->find($this->endpoint, 'ts-1')['StartDate'])->toBe('2026-11-02T00:00:00');
});

test('save creates with a bare array body for payroll au and updates by id', function () {
    $mock = Saloon::fake([SaveXeroRecordRequest::class => MockResponse::make(['Timesheets' => [['TimesheetID' => 'ts-9']]])]);
    $gateway = app(XeroGateway::class);

    $created = $gateway->save($this->endpoint, ['EmployeeID' => '11111111-1111-1111-1111-111111111111']);
    $gateway->save($this->endpoint, ['EmployeeID' => '11111111-1111-1111-1111-111111111111'], 'ts-9');

    expect($created['TimesheetID'])->toBe('ts-9');

    $sent = $mock->getRecordedResponses();

    expect(array_map(fn ($response) => $response->getPendingRequest()->getUrl(), $sent))->toBe([
        'https://api.xero.com/payroll.xro/1.0/Timesheets',
        'https://api.xero.com/payroll.xro/1.0/Timesheets/ts-9',
    ])->and($sent[0]->getPendingRequest()->body()->all())->toBe([['EmployeeID' => '11111111-1111-1111-1111-111111111111']]);
});

test('accounting saves wrap the record under the resource name', function () {
    expect(XeroApiEnum::ACCOUNTING->wrapRecord('Contacts', ['Name' => 'A']))->toBe(['Contacts' => [['Name' => 'A']]])
        ->and(XeroApiEnum::ACCOUNTING->baseUrl())->toBe('https://api.xero.com/api.xro/2.0');
});

test('a 401 refreshes the token once and replays the request', function () {
    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAccessToken')->once()->andReturn(new AccessToken([
            'access_token' => 'fresh-access-token',
            'refresh_token' => 'fresh-refresh-token',
            'id_token' => 'fresh-id-token',
            'token_type' => 'Bearer',
            'expires' => now()->addHour()->timestamp,
            'scope' => 'openid',
        ]));
    });

    $mock = Saloon::fake([
        MockResponse::make([], 401),
        MockResponse::make(['Timesheets' => [['TimesheetID' => 'ts-1']]]),
    ]);

    $record = app(XeroGateway::class)->find($this->endpoint, 'ts-1');

    expect($record['TimesheetID'])->toBe('ts-1');
    $mock->assertSentCount(2);
    expect($this->token->fresh()->access_token)->toBe('fresh-access-token');
});

test('a second 401 surfaces as UnauthorizedXero', function () {
    $this->mock(Xero::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAccessToken')->once()->andReturn(new AccessToken([
            'access_token' => 'fresh-access-token',
            'refresh_token' => 'fresh-refresh-token',
            'id_token' => 'fresh-id-token',
            'token_type' => 'Bearer',
            'expires' => now()->addHour()->timestamp,
            'scope' => 'openid',
        ]));
    });
    Saloon::fake([FindXeroRecordRequest::class => MockResponse::make([], 401)]);

    app(XeroGateway::class)->find($this->endpoint, 'ts-1');
})->throws(UnauthorizedXero::class);

test('a 400 becomes a validation exception carrying the xero messages', function () {
    Saloon::fake([SaveXeroRecordRequest::class => MockResponse::make([
        'Timesheets' => [['ValidationErrors' => [['Message' => 'Period overlaps'], ['Message' => 'Rate not found']]]],
    ], 400)]);

    app(XeroGateway::class)->save($this->endpoint, ['EmployeeID' => '11111111-1111-1111-1111-111111111111']);
})->throws(XeroValidationException::class, 'Period overlaps; Rate not found');

test('a 429 becomes a rate limit exception and fills the bucket', function () {
    Saloon::fake([FindXeroRecordRequest::class => MockResponse::make([], 429)]);

    try {
        app(XeroGateway::class)->find($this->endpoint, 'ts-1');
    } catch (XeroRateLimitExceededException) {
        expect(RateLimiter::tooManyAttempts(XeroQuery::getRateLimiterKey(), 60))->toBeTrue();

        return;
    }

    test()->fail('Expected a rate limit exception.');
});

test('calls are refused once the local bucket is full', function () {
    config(['xero-integration.rate_limit.no' => 1]);
    $mock = Saloon::fake([FindXeroRecordRequest::class => MockResponse::make(['Timesheets' => []])]);
    $gateway = app(XeroGateway::class);

    $gateway->find($this->endpoint, 'ts-1');

    expect(fn () => $gateway->find($this->endpoint, 'ts-2'))->toThrow(XeroRateLimitExceededException::class);
    $mock->assertSentCount(1);

    RateLimiter::clear(XeroQuery::getRateLimiterKey());
});

test('other failures become an integration exception', function () {
    Saloon::fake([FindXeroRecordRequest::class => MockResponse::make([], 500)]);

    app(XeroGateway::class)->find($this->endpoint, 'ts-1');
})->throws(XeroIntegrationException::class, 'status 500');

describe('payroll au 2.0', function () {
    beforeEach(function () {
        $this->v2 = new XeroEndpoint(XeroApiEnum::PAYROLL_AU_V2, 'Timesheets', 'timesheetID', ['employeeId', 'payrollCalendarId'], true);
    });

    test('it targets the 2.0 base url and takes a single object body', function () {
        $mock = Saloon::fake([SaveXeroRecordRequest::class => MockResponse::make(['timesheet' => ['timesheetID' => 'ts-1']])]);

        $saved = app(XeroGateway::class)->save($this->v2, ['employeeID' => 'e-1']);

        expect($saved['timesheetID'])->toBe('ts-1')
            ->and(XeroApiEnum::PAYROLL_AU_V2->baseUrl())->toBe('https://api.xero.com/payroll.xro/2.0');

        $mock->assertSent(fn ($request, $response) => $response->getPendingRequest()->getUrl() === 'https://api.xero.com/payroll.xro/2.0/Timesheets'
            && $response->getPendingRequest()->body()->all() === ['employeeID' => 'e-1']);
    });

    test('find reads the single record key and list reads the lower-camel list key', function () {
        Saloon::fake([
            FindXeroRecordRequest::class => MockResponse::make(['timesheet' => ['timesheetID' => 'ts-1']]),
            ListXeroRecordsRequest::class => MockResponse::make(['pagination' => ['page' => 1, 'pageCount' => 1], 'timesheets' => [['timesheetID' => 'ts-1']]]),
        ]);
        $gateway = app(XeroGateway::class);

        expect($gateway->find($this->v2, 'ts-1')['timesheetID'])->toBe('ts-1')
            ->and($gateway->list($this->v2)->pluck('timesheetID')->all())->toBe(['ts-1']);
    });

    test('list sends guid filters in the filter parameter and the rest as query parameters', function () {
        $queries = [];

        Saloon::fake([
            ListXeroRecordsRequest::class => function (PendingRequest $request) use (&$queries) {
                $queries[] = $request->query()->all();

                return MockResponse::make(['pagination' => ['page' => 1, 'pageCount' => 1], 'timesheets' => []]);
            },
        ]);

        app(XeroGateway::class)->list($this->v2, [
            'employeeId' => '11111111-1111-1111-1111-111111111111',
            'payrollCalendarId' => '22222222-2222-2222-2222-222222222222',
            'startDate' => '2026-11-02',
            'status' => 'Draft',
        ]);

        expect($queries[0])->toBe([
            'startDate' => '2026-11-02',
            'status' => 'Draft',
            'filter' => 'employeeId==11111111-1111-1111-1111-111111111111,payrollCalendarId==22222222-2222-2222-2222-222222222222',
            'page' => 1,
        ]);
    });

    test('list stops at the reported page count', function () {
        $mock = Saloon::fake([
            ListXeroRecordsRequest::class => function (PendingRequest $request) {
                $page = (int) $request->query()->get('page');

                return MockResponse::make(['pagination' => ['page' => $page, 'pageCount' => 2], 'timesheets' => [['timesheetID' => "ts-{$page}"]]]);
            },
        ]);

        $records = app(XeroGateway::class)->list($this->v2);

        expect($records->pluck('timesheetID')->all())->toBe(['ts-1', 'ts-2']);
        $mock->assertSentCount(2);
    });

    test('actions build an encoded path and return the decoded response', function () {
        $paths = [];

        $record = function (PendingRequest $request) use (&$paths) {
            $paths[] = $request->getMethod()->value.' '.$request->getUri()->getPath();

            return MockResponse::make(['timesheetLine' => ['numberOfUnits' => 3]]);
        };

        Saloon::fake([XeroActionRequest::class => $record, XeroActionBodyRequest::class => $record]);
        $gateway = app(XeroGateway::class);

        $response = $gateway->action($this->v2, 'put', ['ts-1', 'Lines', 'a/b'], ['numberOfUnits' => 3]);
        $gateway->action($this->v2, 'POST', ['ts-1', 'Approve']);

        expect($response['timesheetLine']['numberOfUnits'])->toBe(3)
            ->and($paths)->toBe([
                'PUT /payroll.xro/2.0/Timesheets/ts-1/Lines/a%2Fb',
                'POST /payroll.xro/2.0/Timesheets/ts-1/Approve',
            ]);
    });

    test('a 2.0 problem response becomes a validation exception carrying the details', function () {
        Saloon::fake([SaveXeroRecordRequest::class => MockResponse::make([
            'type' => 'application/problem+json',
            'title' => 'BadRequest',
            'status' => '400',
            'detail' => 'Validation error occurred.',
            'invalidFields' => [['name' => 'earningsRateID', 'reason' => 'The earnings rate was not found.']],
        ], 400)]);

        app(XeroGateway::class)->save($this->v2, ['employeeID' => 'e-1']);
    })->throws(XeroValidationException::class, 'The earnings rate was not found.');

    test('actions without a body send no body and actions with a body send json', function () {
        $sent = [];

        $record = function (PendingRequest $request) use (&$sent) {
            $sent[] = [$request->getMethod()->value, $request->body()?->all()];

            return MockResponse::make(['timesheet' => []]);
        };

        Saloon::fake([XeroActionRequest::class => $record, XeroActionBodyRequest::class => $record]);
        $gateway = app(XeroGateway::class);

        $gateway->action($this->v2, 'POST', ['ts-1', 'Approve']);
        $gateway->action($this->v2, 'DELETE', ['ts-1', 'Lines', 'l-1']);
        $gateway->action($this->v2, 'POST', ['ts-1', 'Lines'], ['date' => '2026-11-03']);

        expect($sent)->toBe([['POST', null], ['DELETE', null], ['POST', ['date' => '2026-11-03']]]);
    });

    test('dot and empty path segments are rejected', function (string $segment) {
        Saloon::fake([FindXeroRecordRequest::class => MockResponse::make(['timesheet' => []])]);

        app(XeroGateway::class)->find($this->v2, $segment);
    })->with(['..', '.', ''])->throws(InvalidArgumentException::class, 'Invalid Xero path segment.');

    test('reserved query parameters cannot be passed as filter fields', function (string $field) {
        Saloon::fake([ListXeroRecordsRequest::class => MockResponse::make(['timesheets' => []])]);

        app(XeroGateway::class)->list($this->v2, [$field => 'x']);
    })->with(['filter', 'where', 'page', 'order', 'Filter'])->throws(InvalidArgumentException::class);

    test('a trailing newline does not pass guid validation', function () {
        Saloon::fake([ListXeroRecordsRequest::class => MockResponse::make(['timesheets' => []])]);

        app(XeroGateway::class)->list($this->v2, ['employeeId' => "11111111-1111-1111-1111-111111111111\n"]);
    })->throws(InvalidArgumentException::class, 'must be a GUID');

    test('validation messages are truncated', function () {
        Saloon::fake([SaveXeroRecordRequest::class => MockResponse::make(['detail' => str_repeat('x', 2000)], 400)]);

        try {
            app(XeroGateway::class)->save($this->v2, ['employeeID' => 'e-1']);
        } catch (XeroValidationException $exception) {
            expect(strlen($exception->getMessage()))->toBeLessThanOrEqual(503);

            return;
        }

        test()->fail('Expected a validation exception.');
    });
});

<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\XeroContactData;
use Dcodegroup\XeroIntegration\Enums\XeroContactStatusEnum;
use Mockery;
use XeroPHP\Models\Accounting\Contact;

test('can instantiate XeroContactData with required fields', function () {
    $data = new XeroContactData(
        ContactID: null,
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Acme Corp',
        FirstName: null,
        LastName: null,
        EmailAddress: null,
        UpdatedDateUTC: Carbon::now(),
    );

    expect($data->Name)->toBe('Acme Corp')
        ->and($data->ContactStatus)->toBe(XeroContactStatusEnum::ACTIVE);
});

test('optional fields default to null', function () {
    $data = new XeroContactData(
        ContactID: null,
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Test Co',
        FirstName: null,
        LastName: null,
        EmailAddress: null,
        UpdatedDateUTC: Carbon::now(),
    );

    expect($data->ContactID)->toBeNull()
        ->and($data->FirstName)->toBeNull()
        ->and($data->LastName)->toBeNull()
        ->and($data->EmailAddress)->toBeNull()
        ->and($data->ContactPersons)->toBeNull()
        ->and($data->Addresses)->toBeNull()
        ->and($data->Phones)->toBeNull();
});

test('IsSupplier & IsCustomer defaults to false', function () {
    $data = new XeroContactData(
        ContactID: null,
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Test Co',
        FirstName: null,
        LastName: null,
        EmailAddress: null,
        UpdatedDateUTC: Carbon::now(),
    );

    expect($data->IsSupplier)->toBeFalse()
        ->and($data->IsCustomer)->toBeFalse();
});

test('toXeroArray returns correct keys', function () {
    $data = new XeroContactData(
        ContactID: null,
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Acme Corp',
        FirstName: null,
        LastName: null,
        EmailAddress: null,
        UpdatedDateUTC: Carbon::now(),
        ContactPersons: collect(),
        Addresses: collect(),
        Phones: collect(),
    );

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys([
        'ContactID', 'ContactStatus', 'Name', 'FirstName', 'LastName',
        'EmailAddress', 'UpdatedDateUTC', 'IsSupplier', 'IsCustomer',
    ]);
});

test('toXeroArray returns correct values', function () {
    $data = new XeroContactData(
        ContactID: 'contact-uuid',
        ContactStatus: XeroContactStatusEnum::ACTIVE,
        Name: 'Acme Corp',
        FirstName: 'John',
        LastName: 'Doe',
        EmailAddress: 'john@acme.com',
        UpdatedDateUTC: Carbon::now(),
        ContactPersons: collect(),
        Addresses: collect(),
        Phones: collect(),
    );

    $array = $data->toXeroArray();

    expect($array['ContactID'])->toBe('contact-uuid')
        ->and($array['Name'])->toBe('Acme Corp')
        ->and($array['FirstName'])->toBe('John')
        ->and($array['LastName'])->toBe('Doe')
        ->and($array['EmailAddress'])->toBe('john@acme.com');
});

test('fromXero maps xero model to XeroContactData', function () {
    $xeroContact = Mockery::mock(Contact::class);
    $xeroContact->shouldReceive('offsetGet')->andReturnUsing(fn ($key) => match ($key) {
        'ContactID' => 'contact-uuid-123',
        'ContactStatus' => XeroContactStatusEnum::ACTIVE,
        'Name' => 'Acme Corp',
        'FirstName' => 'John',
        'LastName' => 'Doe',
        'EmailAddress' => 'john@acme.com',
        'UpdatedDateUTC' => Carbon::now(),
        'ContactPersons' => null,
        'IsSupplier' => false,
        'IsCustomer' => true,
        'Addresses' => null,
        'Phones' => null,
        'HasAttachments' => false,
        'HasValidationErrors' => false,
        default => null,
    });
    $xeroContact->shouldReceive('offsetExists')->andReturn(true);

    $data = XeroContactData::fromXero($xeroContact);

    expect($data)->toBeInstanceOf(XeroContactData::class)
        ->and($data->Name)->toBe('Acme Corp')
        ->and($data->ContactID)->toBe('contact-uuid-123')
        ->and($data->EmailAddress)->toBe('john@acme.com');
});

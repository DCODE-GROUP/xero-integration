<?php

namespace Dcodegroup\XeroIntegration\Services;

use Dcodegroup\XeroIntegration\Contracts\XeroGateway;
use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use Dcodegroup\XeroIntegration\Exceptions\UnauthorizedXero;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Exceptions\XeroRateLimitExceededException;
use Dcodegroup\XeroIntegration\Exceptions\XeroValidationException;
use Dcodegroup\XeroIntegration\Http\Saloon\Connectors\XeroConnector;
use Dcodegroup\XeroIntegration\Http\Saloon\Endpoints\XeroEndpoint;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\FindXeroRecordRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\ListXeroRecordsRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\SaveXeroRecordRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\XeroActionBodyRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\Requests\XeroActionRequest;
use Dcodegroup\XeroIntegration\Http\Saloon\XeroPayloadNormalizer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Saloon\Enums\Method;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Request;
use Saloon\Http\Response;

class SaloonXeroGateway implements XeroGateway
{
    /**
     * Upper bound on pages fetched by one list call, so a bad filter cannot loop forever.
     */
    protected const MAX_PAGES = 100;

    /**
     * @var array<string, XeroConnector>
     */
    protected array $connectors = [];

    public function __construct(protected XeroRateLimiter $rateLimiter) {}

    public function find(XeroEndpoint $endpoint, string $id): ?array
    {
        $response = $this->send($endpoint->api, new FindXeroRecordRequest($endpoint, $id), notFoundIsNull: true);

        return $response === null ? null : $this->records($endpoint, $response, single: true)->first();
    }

    public function list(XeroEndpoint $endpoint, array $filters = []): Collection
    {
        $records = collect();

        for ($page = 1; $page <= self::MAX_PAGES; $page++) {
            $response = $this->send($endpoint->api, new ListXeroRecordsRequest($endpoint, $filters, $page));
            $pageRecords = $this->records($endpoint, $response);

            $records = $records->concat($pageRecords);

            if (! $endpoint->paginated || $pageRecords->isEmpty() || $this->isLastPage($response, $page)) {
                break;
            }
        }

        return $records->values();
    }

    public function save(XeroEndpoint $endpoint, array $record, ?string $id = null): array
    {
        $saved = $this->records(
            $endpoint,
            $this->send($endpoint->api, new SaveXeroRecordRequest($endpoint, $record, $id)),
            single: true,
        )->first();

        if ($saved === null) {
            throw new XeroIntegrationException('Xero did not return the saved record.');
        }

        return $saved;
    }

    public function action(XeroEndpoint $endpoint, string $method, array $segments, array $body = []): array
    {
        $request = $body === []
            ? new XeroActionRequest(Method::from(strtoupper($method)), $endpoint->path($segments))
            : new XeroActionBodyRequest(Method::from(strtoupper($method)), $endpoint->path($segments), $body);

        $response = $this->send($endpoint->api, $request);

        return XeroPayloadNormalizer::normalize($response->json());
    }

    protected function connector(XeroApiEnum $api): XeroConnector
    {
        return $this->connectors[$api->value] ??= new XeroConnector($api);
    }

    /**
     * @return ($notFoundIsNull is true ? Response|null : Response)
     */
    protected function send(XeroApiEnum $api, Request $request, bool $notFoundIsNull = false): ?Response
    {
        try {
            return $this->connector($api)->send($request);
        } catch (RequestException $exception) {
            if ($notFoundIsNull && $exception->getResponse()->status() === 404) {
                return null;
            }

            throw $this->translate($exception);
        } catch (FatalRequestException $exception) {
            throw $this->translate($exception);
        }
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function records(XeroEndpoint $endpoint, Response $response, bool $single = false): Collection
    {
        $itemKey = $single ? $endpoint->api->itemKey($endpoint->resource) : null;

        if ($itemKey !== null) {
            $record = $response->json($itemKey);

            return $record === null
                ? collect()
                : collect([XeroPayloadNormalizer::normalize($record)]);
        }

        $records = $response->json($endpoint->resolvedListKey()) ?? [];

        return collect(XeroPayloadNormalizer::normalize($records))->values();
    }

    /**
     * Whether a 2.0 response reports that this was the final page. 1.0 APIs report nothing, so an empty page ends the walk.
     */
    protected function isLastPage(Response $response, int $page): bool
    {
        $pageCount = $response->json('pagination.pageCount');

        return is_int($pageCount) && $page >= $pageCount;
    }

    protected function translate(RequestException|FatalRequestException $exception): XeroIntegrationException|XeroValidationException|UnauthorizedXero|XeroRateLimitExceededException
    {
        if ($exception instanceof FatalRequestException) {
            return new XeroIntegrationException('Could not reach Xero: '.$exception->getMessage(), 0, $exception);
        }

        $response = $exception->getResponse();

        return match ($response->status()) {
            401 => new UnauthorizedXero('Xero rejected the access token.'),
            429 => $this->rateLimited($exception),
            400 => new XeroValidationException($this->validationMessage($response)),
            default => new XeroIntegrationException("Xero request failed with status {$response->status()}."),
        };
    }

    protected function rateLimited(RequestException $exception): XeroRateLimitExceededException
    {
        $this->rateLimiter->exhaust();

        return XeroRateLimitExceededException::exceeded();
    }

    protected function validationMessage(Response $response): string
    {
        $messages = [];
        $body = $response->json();

        array_walk_recursive($body, function (mixed $value, string|int $key) use (&$messages): void {
            if (in_array($key, ['Message', 'reason', 'detail'], true) && is_string($value)) {
                $messages[] = $value;
            }
        });

        return Str::limit(implode('; ', array_unique($messages)), 500) ?: 'Xero rejected the request.';
    }
}

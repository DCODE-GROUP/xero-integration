<?php

namespace Dcodegroup\XeroIntegration\Data\Traits;

use BackedEnum;
use DateTimeInterface;
use Dcodegroup\XeroIntegration\Contracts\XeroGateway;
use Dcodegroup\XeroIntegration\Data\Contracts\HasXeroEndpoint;
use Spatie\LaravelData\Optional;

/**
 * Direct API access to read and write a data type. Add it, implement `getXeroEndpoint()`, and the type no
 * longer touches xero-php.
 *
 * @phpstan-require-implements HasXeroEndpoint
 */
trait SyncsWithXeroApi
{
    use ReadsFromXeroApi;

    /**
     * Create the record in Xero, or update it when it already has an id.
     *
     * Nested records travel inside the parent payload, so `$syncRelated` is accepted only to stay compatible
     * with the xero-php sync signature. Types whose API needs more than one call override this method.
     */
    public function sendToXero(bool $syncRelated = true): void
    {
        $endpoint = static::getXeroEndpoint();
        $id = data_get($this, $endpoint->idKey);

        $saved = app(XeroGateway::class)->save(
            $endpoint,
            $this->toXeroPayload(),
            empty($id) || ! is_string($id) ? null : $id,
        );

        $savedId = $saved[$endpoint->idKey] ?? null;

        if (! empty($savedId)) {
            $this->{$endpoint->idKey} = $savedId;

            $this->linkLocalModelToXero($savedId);
        }
    }

    /**
     * Record the Xero ID against the local model this data was built from, if there is one.
     */
    protected function linkLocalModelToXero(string $xeroId): void
    {
        $this->getLocalModel()?->xeroRecord()?->updateOrCreate( // @phpstan-ignore-line method.notFound
            ['xero_id' => $xeroId],
            ['xero_id' => $xeroId],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toXeroPayload(): array
    {
        return static::payloadValue($this->toXeroArray());
    }

    /**
     * Make a value JSON-safe, dropping nulls and unset optionals.
     *
     * @param  array<int|string, mixed>  $values
     * @return array<int|string, mixed>
     */
    protected static function payloadValue(array $values): array
    {
        $payload = [];

        foreach ($values as $key => $value) {
            if ($value === null || $value instanceof Optional) {
                continue;
            }

            $payload[$key] = match (true) {
                is_array($value) => static::payloadValue($value),
                $value instanceof DateTimeInterface => $value->format('Y-m-d\TH:i:s'),
                $value instanceof BackedEnum => $value->value,
                default => $value,
            };
        }

        return $payload;
    }
}

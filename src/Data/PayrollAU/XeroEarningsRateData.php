<?php

namespace Dcodegroup\XeroIntegration\Data\PayrollAU;

use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Data\Contracts\HasXeroEndpoint;
use Dcodegroup\XeroIntegration\Data\Contracts\ListsXeroRecords;
use Dcodegroup\XeroIntegration\Data\Traits\CachesXeroLookups;
use Dcodegroup\XeroIntegration\Data\Traits\ReadsFromXeroApi;
use Dcodegroup\XeroIntegration\Enums\XeroApiEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Http\Saloon\Endpoints\XeroEndpoint;
use Illuminate\Support\Collection;
use LogicException;
use XeroPHP\Remote\Model as XeroModel;

/**
 * Read-only view of a Xero AU earnings rate.
 */
class XeroEarningsRateData extends AbstractXeroData implements HasXeroEndpoint, ListsXeroRecords
{
    use CachesXeroLookups;
    use ReadsFromXeroApi {
        list as protected listFromXero;
    }

    public function __construct(
        public string $EarningsRateID,
        public string $Name,
        public ?string $EarningsType = null,
        public ?string $RateType = null,
        public ?string $TypeOfUnits = null,
        public ?string $AccountCode = null,
        public ?float $RatePerUnit = null,
        public ?float $Multiplier = null,
        public ?bool $CurrentRecord = null,
    ) {}

    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::EARNINGS_RATE_AU;
    }

    /**
     * @throws LogicException Earnings rates are read through the direct API, not xero-php. Use fromXeroArray().
     */
    public static function fromXero(XeroModel $xeroObject): self
    {
        throw new LogicException('Earnings rates are read through the direct API. Use fromXeroArray().');
    }

    /**
     * @throws LogicException Earnings rates are read-only.
     */
    public function toXeroArray(): array
    {
        throw new LogicException('Earnings rates are read-only and cannot be sent to Xero.');
    }

    /**
     * Earnings rates have no endpoint of their own: Xero returns them inside the PayItems resource.
     */
    public static function getXeroEndpoint(): XeroEndpoint
    {
        return new XeroEndpoint(
            api: XeroApiEnum::PAYROLL_AU,
            resource: 'PayItems',
            idKey: 'EarningsRateID',
            listKey: 'PayItems.EarningsRates',
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromXeroArray(array $payload): self
    {
        return new self(
            EarningsRateID: $payload['EarningsRateID'],
            Name: $payload['Name'],
            EarningsType: $payload['EarningsType'] ?? null,
            RateType: $payload['RateType'] ?? null,
            TypeOfUnits: $payload['TypeOfUnits'] ?? null,
            AccountCode: $payload['AccountCode'] ?? null,
            RatePerUnit: isset($payload['RatePerUnit']) ? (float) $payload['RatePerUnit'] : null,
            Multiplier: isset($payload['Multiplier']) ? (float) $payload['Multiplier'] : null,
            CurrentRecord: $payload['CurrentRecord'] ?? null,
        );
    }

    /**
     * List the current earnings rates.
     *
     * Rates explicitly flagged as not current are excluded.
     *
     * @return Collection<int, self>
     */
    public static function list(): Collection
    {
        return static::listFromXero()
            ->reject(fn (self $rate): bool => $rate->CurrentRecord === false)
            ->values();
    }
}

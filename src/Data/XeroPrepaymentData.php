<?php

namespace Dcodegroup\XeroIntegration\Data;

use Dcodegroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Dcodegroup\XeroIntegration\Enums\XeroLineAmountTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroPrepaymentStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroPrepaymentTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Prepayment as XeroPrepayment;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroPrepaymentData extends AbstractXeroData
{
    use XeroSyncTrait;

    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::PREPAYMENT;

    protected array $searchFields = [
        'PrepaymentID',
        'Status',
    ];

    protected array $relatedFields = [
        'Contact',
        'LineItems',
    ];

    public function __construct(
        public string|Optional|null $PrepaymentID,
        public XeroContactData $Contact,
        /** @var Collection<int,XeroItemData> */
        public Collection $LineItems,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $Date,
        public XeroPrepaymentStatusEnum $Status,
        public float $SubTotal,
        public float $TotalTax,
        public float $Total,
        public XeroPrepaymentTypeEnum $Type,
        public XeroLineAmountTypeEnum|Optional|null $LineAmountTypes,
        public string|Optional|null $CurrencyCode,
        public float|Optional|null $CurrencyRate,
        public string|Optional|null $RemainingCredit,
        public bool|Optional|null $HasAttachments,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $UpdatedDateUTC,
    ) {}

    /**
     * Create from Xero Model
     *
     * @param  XeroPrepayment  $xeroPrepayment
     */
    public static function fromXero(XeroModel|XeroPrepayment $xeroPrepayment): self
    {
        return new static(
            PrepaymentID: data_get($xeroPrepayment, 'PrepaymentID'),
            Contact: XeroContactData::fromXero(data_get($xeroPrepayment, 'Contact')),
            LineItems: XeroItemData::toCollection(data_get($xeroPrepayment, 'LineItems')),
            Date: Carbon::instance(data_get($xeroPrepayment, 'Date')),
            Status: XeroPrepaymentStatusEnum::TryFrom(data_get($xeroPrepayment, 'Status')),
            SubTotal: data_get($xeroPrepayment, 'SubTotal'),
            TotalTax: data_get($xeroPrepayment, 'TotalTax'),
            Total: data_get($xeroPrepayment, 'Total'),
            Type: XeroPrepaymentTypeEnum::TryFrom(data_get($xeroPrepayment, 'Type')),
            LineAmountTypes: XeroLineAmountTypeEnum::TryFrom(data_get($xeroPrepayment, 'LineAmountTypes')),
            CurrencyCode: data_get($xeroPrepayment, 'CurrencyCode'),
            CurrencyRate: data_get($xeroPrepayment, 'CurrencyRate'),
            RemainingCredit: data_get($xeroPrepayment, 'RemainingCredit'),
            HasAttachments: data_get($xeroPrepayment, 'HasAttachments'),
            UpdatedDateUTC: data_get($xeroPrepayment, 'UpdatedDateUTC') ? Carbon::instance(data_get($xeroPrepayment, 'UpdatedDateUTC')) : null,
        );
    }

    public function toXeroArray(): array
    {
        return [
            'PrepaymentID' => data_get($this, 'PrepaymentID'),
            'Contact' => data_get($this, 'Contact')?->toXeroArray(),
            'LineItems' => XeroItemData::toXeroCollection(data_get($this, 'LineItems')),
            'Date' => data_get($this, 'Date'),
            'Status' => data_get($this, 'Status')?->getXeroValue(),
            'SubTotal' => data_get($this, 'SubTotal'),
            'TotalTax' => data_get($this, 'TotalTax'),
            'Total' => data_get($this, 'Total'),
            'Type' => data_get($this, 'Type')?->getXeroValue(),
            'LineAmountTypes' => data_get($this, 'LineAmountTypes')?->getXeroValue(),
            'CurrencyCode' => data_get($this, 'CurrencyCode'),
            'CurrencyRate' => data_get($this, 'CurrencyRate'),
            'RemainingCredit' => data_get($this, 'RemainingCredit'),
            'HasAttachments' => data_get($this, 'HasAttachments'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
        ];
    }

    public function mapToData(Model $model): array
    {
        return [];
    }

    public function mapToModel(): array
    {
        return [];
    }
}

<?php

namespace Dcodegroup\XeroIntegration\Data;

use Dcodegroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Dcodegroup\XeroIntegration\Enums\XeroLineAmountTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroOverpaymentStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroOverpaymentTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Overpayment as XeroOverpayment;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroOverpaymentData extends AbstractXeroData
{
    use XeroSyncTrait;

    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::OVERPAYMENT;

    protected array $searchFields = [
        'OverpaymentID',
        'Status',
    ];

    protected array $relatedFields = [
        'Contact',
        'LineItems',
        'Payments',
    ];

    public function __construct(
        public string|Optional|null $OverpaymentID,
        public XeroContactData $Contact,
        /** @var Collection<int,XeroItemData> */
        public Collection $LineItems,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $Date,
        public XeroOverpaymentStatusEnum $Status, // ToDo: change to Enum
        public float $SubTotal,
        public float $TotalTax,
        public float $Total,
        public XeroOverpaymentTypeEnum $Type,
        public XeroLineAmountTypeEnum|Optional|null $LineAmountTypes,
        public string|Optional|null $CurrencyCode,
        public float|Optional|null $CurrencyRate,
        public string|Optional|null $RemainingCredit,
        /** @var Collection<int,XeroPaymentData>|null */
        public Collection|Optional|null $Payments,
        public bool|Optional|null $HasAttachments,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $UpdatedDateUTC,
        public string|Optional|null $Reference,
    ) {}

    /**
     * Create from Xero Model
     *
     * @param  XeroOverpayment  $xeroOverpayment
     */
    public static function fromXero(XeroModel|XeroOverpayment $xeroOverpayment): self
    {
        return new static(
            OverpaymentID: data_get($xeroOverpayment, 'OverpaymentID'),
            Contact: XeroContactData::fromXero(data_get($xeroOverpayment, 'Contact')),
            LineItems: XeroItemData::toCollection(data_get($xeroOverpayment, 'LineItems')),
            Date: Carbon::instance(data_get($xeroOverpayment, 'Date')),
            Status: XeroOverpaymentStatusEnum::tryFrom(data_get($xeroOverpayment, 'Status')),
            SubTotal: data_get($xeroOverpayment, 'SubTotal'),
            TotalTax: data_get($xeroOverpayment, 'TotalTax'),
            Total: data_get($xeroOverpayment, 'Total'),
            Type: XeroOverpaymentTypeEnum::tryFrom(data_get($xeroOverpayment, 'Type')),
            LineAmountTypes: XeroLineAmountTypeEnum::tryFrom(data_get($xeroOverpayment, 'LineAmountTypes')),
            CurrencyCode: data_get($xeroOverpayment, 'CurrencyCode'),
            CurrencyRate: data_get($xeroOverpayment, 'CurrencyRate'),
            RemainingCredit: data_get($xeroOverpayment, 'RemainingCredit'),
            Payments: XeroPaymentData::toCollection(data_get($xeroOverpayment, 'Payments')),
            HasAttachments: data_get($xeroOverpayment, 'HasAttachments'),
            UpdatedDateUTC: data_get($xeroOverpayment, 'UpdatedDateUTC') ? Carbon::instance(data_get($xeroOverpayment, 'UpdatedDateUTC')) : null,
            Reference: data_get($xeroOverpayment, 'Reference')
        );
    }

    public function toXeroArray(): array
    {
        return [
            'OverpaymentID' => data_get($this, 'OverpaymentID'),
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
            'Payments' => XeroPaymentData::toXeroCollection(data_get($this, 'Payments')),
            'HasAttachments' => data_get($this, 'HasAttachments'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
            'Reference' => data_get($this, 'Reference'),
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

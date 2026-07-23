<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Traits\XeroSyncTrait;
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
abstract class XeroOverpaymentData extends AbstractXeroData
{
    use XeroSyncTrait;

    protected string $xeroRelationship = 'overpayment';

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
        /** @var Collection<int,XeroInvoiceItemData> */
        public Collection $LineItems,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $Date,
        public string $Status, // ToDo: change to Enum
        public float $SubTotal,
        public float $TotalTax,
        public float $Total,
        public string $Type,
        public string|Optional|null $LineAmountTypes = null,
        public string|Optional|null $CurrencyCode = null,
        public float|Optional|null $CurrencyRate = null,
        public string|Optional|null $RemainingCredit = null,
        /** @var Collection<int,XeroPaymentData>|null */
        public Collection|Optional|null $Payments = null,
        public bool|Optional|null $HasAttachments = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $UpdatedDateUTC = null,
    ) {}

    /**
     * Create from Xero Model
     *
     * @param  XeroOverpayment  $xeroOverpayment
     */
    protected static function fromXero(XeroModel|XeroOverpayment $xeroOverpayment): self
    {
        return new static(
            OverpaymentID: data_get($xeroOverpayment, 'OverpaymentID'),
            Contact: XeroContactData::fromXero(data_get($xeroOverpayment, 'Contact')),
            LineItems: XeroInvoiceItemData::toCollection(data_get($xeroOverpayment, 'LineItems')),
            Date: Carbon::instance(data_get($xeroOverpayment, 'Date')),
            Status: data_get($xeroOverpayment, 'Status'),
            SubTotal: data_get($xeroOverpayment, 'SubTotal'),
            TotalTax: data_get($xeroOverpayment, 'TotalTax'),
            Total: data_get($xeroOverpayment, 'Total'),
            Type: data_get($xeroOverpayment, 'Type'),
            LineAmountTypes: data_get($xeroOverpayment, 'LineAmountTypes'),
            CurrencyCode: data_get($xeroOverpayment, 'CurrencyCode'),
            CurrencyRate: data_get($xeroOverpayment, 'CurrencyRate'),
            RemainingCredit: data_get($xeroOverpayment, 'RemainingCredit'),
            Payments: XeroPaymentData::toCollection(data_get($xeroOverpayment, 'Payments')),
            HasAttachments: data_get($xeroOverpayment, 'HasAttachments'),
            UpdatedDateUTC: data_get($xeroOverpayment, 'UpdatedDateUTC') ? Carbon::instance(data_get($xeroOverpayment, 'UpdatedDateUTC')) : null,
        );
    }

    public function toXeroArray(): array
    {
        return [
            'OverpaymentID' => data_get($this, 'OverpaymentID'),
            'Contact' => data_get($this, 'Contact')?->toXeroArray(),
            'LineItems' => XeroInvoiceItemData::toXeroCollection(data_get($this, 'LineItems')),
            'Date' => data_get($this, 'Date'),
            'Status' => data_get($this, 'Status'),
            'SubTotal' => data_get($this, 'SubTotal'),
            'TotalTax' => data_get($this, 'TotalTax'),
            'Total' => data_get($this, 'Total'),
            'Type' => data_get($this, 'Type'),
            'LineAmountTypes' => data_get($this, 'LineAmountTypes'),
            'CurrencyCode' => data_get($this, 'CurrencyCode'),
            'CurrencyRate' => data_get($this, 'CurrencyRate'),
            'RemainingCredit' => data_get($this, 'RemainingCredit'),
            'Payments' => XeroPaymentData::toXeroCollection(data_get($this, 'Payments')),
            'HasAttachments' => data_get($this, 'HasAttachments'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
        ];
    }
}

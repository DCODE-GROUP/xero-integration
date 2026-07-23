<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Traits\XeroSyncTrait;
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
abstract class XeroPrepaymentData extends AbstractXeroData
{
    use XeroSyncTrait;

    protected string $xeroRelationship = 'prepayment';

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
        public bool|Optional|null $HasAttachments = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon|Optional|null $UpdatedDateUTC = null,
    ) {}

    /**
     * Create from Xero Model
     *
     * @param  XeroPrepayment  $xeroPrepayment
     */
    protected static function fromXero(XeroModel|XeroPrepayment $xeroPrepayment): self
    {
        return new static(
            PrepaymentID: data_get($xeroPrepayment, 'PrepaymentID'),
            Contact: XeroContactData::fromXero(data_get($xeroPrepayment, 'Contact')),
            LineItems: XeroInvoiceItemData::toCollection(data_get($xeroPrepayment, 'LineItems')),
            Date: Carbon::instance(data_get($xeroPrepayment, 'Date')),
            Status: data_get($xeroPrepayment, 'Status'),
            SubTotal: data_get($xeroPrepayment, 'SubTotal'),
            TotalTax: data_get($xeroPrepayment, 'TotalTax'),
            Total: data_get($xeroPrepayment, 'Total'),
            Type: data_get($xeroPrepayment, 'Type'),
            LineAmountTypes: data_get($xeroPrepayment, 'LineAmountTypes'),
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
            'HasAttachments' => data_get($this, 'HasAttachments'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
        ];
    }
}

<?php

namespace Dcodegroup\XeroIntegration\Data;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\Contracts\XeroSyncable;
use Dcodegroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Dcodegroup\XeroIntegration\Enums\XeroPaymentStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroPaymentTypesEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Payment as XeroPayment;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroPaymentData extends AbstractXeroData implements XeroSyncable
{
    use XeroSyncTrait;

    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::PAYMENT;

    protected string $key = 'PaymentID';

    protected array $searchFields = [
        'Reference',
        'Date',
        'Amount',
        'Status',
        'PaymentType',
    ];

    protected array $relatedFields = [
        'Invoice',
        'CreditNote',
        'Prepayment',
        'Overpayment',
    ];

    public function __construct(
        /** @var XeroInvoiceData|Optional|null */
        public XeroInvoiceData|Optional|null $Invoice = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $Date,
        public float $Amount,
        public string|Optional|null $Reference = null,
        public XeroPaymentTypesEnum $PaymentType,
        public string|Optional|null $PaymentID = null,
        /** @var XeroCreditNoteData|Optional|null */
        public XeroCreditNoteData|Optional|null $CreditNote = null,
        /** @var XeroPrepaymentData|Optional|null */
        public XeroPrepaymentData|Optional|null $Prepayment = null,
        /** @var XeroOverpaymentData|Optional|null */
        public XeroOverpaymentData|Optional|null $Overpayment = null,
        public float|Optional|null $CurrencyRate = null,
        public string|Optional|null $Details = null,
        public string|Optional|null $BatchPaymentID = null,
        public string|Optional|null $IsReconciled = null,
        public XeroPaymentStatusEnum|Optional|null $Status = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s')]
        public Carbon|Optional|null $UpdatedDateUTC = null,
    ) {}

    /**
     * Create from Xero Model
     *
     * @param  XeroPayment  $xeroPayment
     */
    public static function fromXero(XeroModel|XeroPayment $xeroPayment): self
    {
        return new static(
            Invoice: XeroInvoiceData::fromXero(data_get($xeroPayment, 'Invoice')),
            Date: Carbon::parse(data_get($xeroPayment, 'Date')),
            Amount: data_get($xeroPayment, 'Amount'),
            Reference: data_get($xeroPayment, 'Reference'),
            PaymentType: XeroPaymentTypesEnum::from(data_get($xeroPayment, 'PaymentType')),
            PaymentID: data_get($xeroPayment, 'PaymentID'),
            CreditNote: XeroCreditNoteData::fromXero(data_get($xeroPayment, 'CreditNote')),
            Prepayment: XeroPrepaymentData::fromXero(data_get($xeroPayment, 'Prepayment')),
            Overpayment: XeroOverpaymentData::fromXero(data_get($xeroPayment, 'Overpayment')),
            CurrencyRate: data_get($xeroPayment, 'CurrencyRate'),
            Details: data_get($xeroPayment, 'Details'),
            BatchPaymentID: data_get($xeroPayment, 'BatchPaymentID'),
            IsReconciled: data_get($xeroPayment, 'IsReconciled'),
            Status: data_get($xeroPayment, 'Status'),
            UpdatedDateUTC: data_get($xeroPayment, 'UpdatedDateUTC') ? Carbon::parse(data_get($xeroPayment, 'UpdatedDateUTC')) : null,
        );
    }

    public function toXeroArray(): array
    {
        return [
            'PaymentID' => data_get($this, 'PaymentID'),
            'Invoice' => data_get($this, 'Invoice')?->toXeroArray(),
            'CreditNote' => data_get($this, 'CreditNote')?->toXeroArray(),
            'Prepayment' => data_get($this, 'Prepayment')?->toXeroArray(),
            'Overpayment' => data_get($this, 'Overpayment')?->toXeroArray(),
            'Date' => data_get($this, 'Date'),
            'Amount' => data_get($this, 'Amount'),
            'Reference' => data_get($this, 'Reference'),
            'Details' => data_get($this, 'Details'),
            'CurrencyRate' => data_get($this, 'CurrencyRate'),
            'BatchPaymentID' => data_get($this, 'BatchPaymentID'),
            'IsReconciled' => data_get($this, 'IsReconciled'),
            'Status' => data_get($this, 'Status')?->getXeroValue(),
            'PaymentType' => data_get($this, 'PaymentType')?->getXeroValue(),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
        ];
    }
}

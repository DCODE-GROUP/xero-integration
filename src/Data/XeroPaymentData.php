<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use DcodeGroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use DcodeGroup\XeroIntegration\Enums\XeroPaymentTypesEnum;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Payment as XeroPayment;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
abstract class XeroPaymentData extends AbstractXeroData implements XeroSyncable
{
    use XeroSyncTrait;

    protected string $xeroRelationship = 'payment';

    protected array $searchFields = [
        'Reference',
        'Date',
        'Amount',
    ];

    protected array $relatedFields = [
        'Invoice',
    ];

    public function __construct(
        /** @var XeroInvoiceData|Optional|null */
        public XeroInvoiceData|Optional|null $Invoice,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $Date,
        public float $Amount,
        public string|Optional|null $Reference,
        public XeroPaymentTypesEnum $PaymentType,
        public string|Optional|null $PaymentID,
    ) {}

    /**
     * Create from Xero Model
     *
     * @param  XeroPayment  $xeroPayment
     */
    protected static function fromXero(XeroModel|XeroPayment $xeroPayment): self
    {
        return new static(
            PaymentID: data_get($xeroPayment, 'PaymentID'),
            Invoice: XeroInvoiceData::fromXero(data_get($xeroPayment, 'Invoice')),
            Date: Carbon::parse(data_get($xeroPayment, 'Date')),
            Amount: data_get($xeroPayment, 'Amount'),
            Reference: data_get($xeroPayment, 'Reference'),
            PaymentType: XeroPaymentTypesEnum::from(data_get($xeroPayment, 'PaymentType')),
        );
    }

    public function toXeroArray(): array
    {
        return [
            'PaymentID' => data_get($this, 'PaymentID'),
            'Invoice' => data_get($this, 'Invoice')?->toXeroArray(),
            'Date' => data_get($this, 'Date'),
            'Amount' => data_get($this, 'Amount'),
            'Reference' => data_get($this, 'Reference')?->getXeroValue(),
            'PaymentType' => data_get($this, 'PaymentType')?->getXeroValue(),
        ];
    }
}

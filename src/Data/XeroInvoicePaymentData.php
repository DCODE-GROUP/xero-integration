<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Payment as XeroPayment;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroInvoicePaymentData extends Data implements XeroSyncable
{
    final public function __construct(
        public string|Optional|null $PaymentID,
        public string|Optional|null $InvoiceID,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $Date,
        public float $Amount,
        public string $PaymentType,
    ) {}

    /**
     * Create from Xero Model
     * @param XeroPayment $xeroPayment
     * @return self
     */
    public static function fromXero(XeroModel|XeroPayment $xeroPayment): self
    {
        return new static(
            PaymentID: $xeroPayment->getPaymentID(),
            InvoiceID: $xeroPayment->getInvoice()->getInvoiceID(),
            Date: Carbon::parse($xeroPayment->getDate()),
            Amount: $xeroPayment->getAmount(),
            PaymentType: $xeroPayment->getPaymentType(),
        );
    }

    public function toXeroArray(): array
    {
        return [
            'PaymentID' => $this->PaymentID,
            'InvoiceID' => $this->InvoiceID,
            'Date' => $this->Date,
            'Amount' => $this->Amount,
            'PaymentType' => $this->PaymentType,
        ];
    }

    abstract public static function fromModel(Model $model): self;

    abstract public function syncToModel(): Model;

    abstract public function localModel(): ?Model;
}

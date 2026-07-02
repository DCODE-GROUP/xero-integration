<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Phone as XeroPhone;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroPhoneData extends Data implements XeroSyncable
{
    /**
     * Summary of __construct
     */
    final public function __construct(
        public string|Optional|null $PhoneType,
        public string|Optional|null $PhoneNumber,
        public string|Optional|null $PhoneAreCode,
        public string|Optional|null $PhoneCountryCode,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'PhoneType' => $this->PhoneType,
            'PhoneNumber' => $this->PhoneNumber,
            'PhoneAreaCode' => $this->PhoneAreCode,
            'PhoneCountryCode' => $this->PhoneCountryCode,
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  XeroPhone  $xeroPhone
     */
    public static function fromXero(XeroModel|XeroPhone $xeroPhone): self
    {
        return new static(
            PhoneType: $xeroPhone->getPhoneType(),
            PhoneNumber: $xeroPhone->getPhoneNumber(),
            PhoneAreCode: $xeroPhone->getPhoneAreaCode(),
            PhoneCountryCode: $xeroPhone->getPhoneCountryCode(),
        );
    }

    abstract public static function fromModel(Model $model): self;

    abstract public function syncToModel(): Model;

    abstract public function localModel(): ?Model;
}

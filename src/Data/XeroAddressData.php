<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;
use XeroPHP\Models\Accounting\Address as XeroAddress;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroAddressData extends Data implements XeroSyncable
{
    final public function __construct(
        public ?string $AddressType,
        public ?string $AddressLine1,
        public ?string $AddressLine2,
        public ?string $AddressLine3,
        public ?string $AddressLine4,
        public ?string $City,
        public ?string $Region,
        public ?string $PostalCode,
        public ?string $Country,
        public ?string $AttentionTo,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'AddressType' => $this->AddressType,
            'AddressLine1' => $this->AddressLine1,
            'AddressLine2' => $this->AddressLine2,
            'AddressLine3' => $this->AddressLine3,
            'AddressLine4' => $this->AddressLine4,
            'City' => $this->City,
            'Region' => $this->Region,
            'PostalCode' => $this->PostalCode,
            'Country' => $this->Country,
            'AttentionTo' => $this->AttentionTo,
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  XeroAddress  $xeroAddress
     */
    public static function fromXero(XeroModel|XeroAddress $xeroAddress): self
    {
        return new static(
            AddressType: data_get($xeroAddress, 'AddressType'),
            AddressLine1: data_get($xeroAddress, 'AddressLine1'),
            AddressLine2: data_get($xeroAddress, 'AddressLine2'),
            AddressLine3: data_get($xeroAddress, 'AddressLine3'),
            AddressLine4: data_get($xeroAddress, 'AddressLine4'),
            City: data_get($xeroAddress, 'City'),
            Region: data_get($xeroAddress, 'Region'),
            PostalCode: data_get($xeroAddress, 'PostalCode'),
            Country: data_get($xeroAddress, 'Country'),
            AttentionTo: data_get($xeroAddress, 'AttentionTo'),
        );
    }

    abstract public static function fromModel(Model $model): self;

    abstract public function syncToModel(): Model;

    abstract public function localModel(): ?Model;
}

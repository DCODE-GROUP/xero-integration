<?php

namespace DcodeGroup\XeroIntegration\Data;

use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Address as XeroAddress;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroAddressData extends AbstractXeroData
{
    protected string $xeroRelationship = 'address';

    protected array $searchFields = [
        'AddressType',
        'AddressLine1',
        'City',
        'PostalCode',
        'Country',
    ];

    protected array $relatedFields = [];

    final public function __construct(
        public string|Optional|null $AddressType,
        public string|Optional|null $AddressLine1,
        public string|Optional|null $AddressLine2,
        public string|Optional|null $AddressLine3,
        public string|Optional|null $AddressLine4,
        public string|Optional|null $City,
        public string|Optional|null $Region,
        public string|Optional|null $PostalCode,
        public string|Optional|null $Country,
        public string|Optional|null $AttentionTo,
    ) {}

    protected function toXeroArray(): array
    {
        return [
            'AddressType' => data_get($this, 'AddressType'),
            'AddressLine1' => data_get($this, 'AddressLine1'),
            'AddressLine2' => data_get($this, 'AddressLine2'),
            'AddressLine3' => data_get($this, 'AddressLine3'),
            'AddressLine4' => data_get($this, 'AddressLine4'),
            'City' => data_get($this, 'City'),
            'Region' => data_get($this, 'Region'),
            'PostalCode' => data_get($this, 'PostalCode'),
            'Country' => data_get($this, 'Country'),
            'AttentionTo' => data_get($this, 'AttentionTo'),
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  XeroAddress  $xeroAddress
     */
    protected static function fromXero(XeroModel|XeroAddress $xeroAddress): self
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
}

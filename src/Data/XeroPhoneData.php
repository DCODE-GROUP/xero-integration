<?php

namespace DcodeGroup\XeroIntegration\Data;

use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Phone as XeroPhone;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroPhoneData extends AbstractXeroData
{
    protected string $xeroRelationship = 'phone';

    protected array $searchFields = [
        'PhoneType',
        'PhoneNumber',
        'PhoneAreaCode',
        'PhoneCountryCode',
    ];

    protected array $relatedFields = [];

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
            'PhoneType' => data_get($this, 'PhoneType'),
            'PhoneNumber' => data_get($this, 'PhoneNumber'),
            'PhoneAreaCode' => data_get($this, 'PhoneAreCode'),
            'PhoneCountryCode' => data_get($this, 'PhoneCountryCode'),
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  XeroPhone  $xeroPhone
     */
    protected static function fromXero(XeroModel|XeroPhone $xeroPhone): self
    {
        return new static(
            PhoneType: data_get($xeroPhone, 'PhoneType'),
            PhoneNumber: data_get($xeroPhone, 'PhoneNumber'),
            PhoneAreCode: data_get($xeroPhone, 'PhoneAreaCode'),
            PhoneCountryCode: data_get($xeroPhone, 'PhoneCountryCode'),
        );
    }
}

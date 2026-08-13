<?php

namespace Dcodegroup\XeroIntegration\Data;

use Dcodegroup\XeroIntegration\Enums\XeroPhoneTypeEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Phone as XeroPhone;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroPhoneData extends AbstractXeroData
{
    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::PHONE;

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
    public function __construct(
        public XeroPhoneTypeEnum|Optional|null $PhoneType = null,
        public string|Optional|null $PhoneNumber = null,
        public string|Optional|null $PhoneAreaCode = null,
        public string|Optional|null $PhoneCountryCode = null,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'PhoneType' => data_get($this, 'PhoneType'),
            'PhoneNumber' => data_get($this, 'PhoneNumber'),
            'PhoneAreaCode' => data_get($this, 'PhoneAreaCode'),
            'PhoneCountryCode' => data_get($this, 'PhoneCountryCode'),
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
            PhoneType: XeroPhoneTypeEnum::tryFrom(data_get($xeroPhone, 'PhoneType')),
            PhoneNumber: data_get($xeroPhone, 'PhoneNumber'),
            PhoneAreaCode: data_get($xeroPhone, 'PhoneAreaCode'),
            PhoneCountryCode: data_get($xeroPhone, 'PhoneCountryCode'),
        );
    }
}

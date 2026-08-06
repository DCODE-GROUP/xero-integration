<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Enums\XeroPhoneTypeEnum;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Phone as XeroPhone;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
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
    public function __construct(
        public XeroPhoneTypeEnum|Optional|null $PhoneType,
        public string|Optional|null $PhoneNumber,
        public string|Optional|null $PhoneAreaCode,
        public string|Optional|null $PhoneCountryCode,
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
    protected static function fromXero(XeroModel|XeroPhone $xeroPhone): self
    {
        return new static(
            PhoneType: data_get($xeroPhone, 'PhoneType')?->getXeroValue(),
            PhoneNumber: data_get($xeroPhone, 'PhoneNumber'),
            PhoneAreaCode: data_get($xeroPhone, 'PhoneAreaCode'),
            PhoneCountryCode: data_get($xeroPhone, 'PhoneCountryCode'),
        );
    }
}

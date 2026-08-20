<?php

namespace Dcodegroup\XeroIntegration\Data\Accounting;

use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\TaxRate\TaxComponent as XeroTaxComponent;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroTaxComponentData extends AbstractXeroData
{
    protected array $searchFields = [
        'Name',
    ];

    public function __construct(
        public string|Optional|null $Name = null,
        public float|Optional|null $Rate = null,
        public bool|Optional|null $IsCompound = null,
        public bool|Optional|null $IsNonRecoverable = null,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'Name' => data_get($this, 'Name'),
            'Rate' => data_get($this, 'Rate'),
            'IsCompound' => data_get($this, 'IsCompound'),
            'IsNonRecoverable' => data_get($this, 'IsNonRecoverable'),
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  XeroTaxComponent  $xeroTaxComponent
     */
    public static function fromXero(XeroModel|XeroTaxComponent $xeroTaxComponent): self
    {
        return new static(
            Name: data_get($xeroTaxComponent, 'Name'),
            Rate: data_get($xeroTaxComponent, 'Rate'),
            IsCompound: data_get($xeroTaxComponent, 'IsCompound'),
            IsNonRecoverable: data_get($xeroTaxComponent, 'IsNonRecoverable'),
        );
    }
}

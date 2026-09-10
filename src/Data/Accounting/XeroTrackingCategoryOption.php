<?php

namespace Dcodegroup\XeroIntegration\Data\Accounting;

use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Spatie\LaravelData\Optional;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroTrackingCategoryOption extends AbstractXeroData
{
    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::ITEM_TRACKING_CATEGORY;
    }

    protected string $key = 'TrackingOptionID';

    protected array $searchFields = [
        'Name',
    ];

    protected array $relatedFields = [];

    public function __construct(
        public string|Optional|null $TrackingOptionID = null,
        public string|Optional|null $Name = null,
        public string|Optional|null $Status = null,
        public bool $HasValidationErrors = false,
        public bool $IsDeleted = false,
        public bool $IsArchived = false,
        public bool $IsActive = false,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'Name' => data_get($this, 'Name'),
            'HasValidationErrors' => data_get($this, 'HasValidationErrors', false),
            'Status' => data_get($this, 'Status', false),
            'IsDeleted' => data_get($this, 'IsDeleted', false),
            'IsArchived' => data_get($this, 'IsArchived', false),
            'IsActive' => data_get($this, 'IsActive', false),
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  array  $xeroTrackingCategoryOption
     */
    public static function fromXero(XeroModel|array $xeroTrackingCategoryOption): self
    {
        return new static(
            Name: data_get($xeroTrackingCategoryOption, 'Name'),
            HasValidationErrors: data_get($xeroTrackingCategoryOption, 'HasValidationErrors', false),
            Status: data_get($xeroTrackingCategoryOption, 'Status'),
            IsDeleted: data_get($xeroTrackingCategoryOption, 'IsDeleted', false),
            IsArchived: data_get($xeroTrackingCategoryOption, 'IsArchived', false),
            IsActive: data_get($xeroTrackingCategoryOption, 'IsActive', false),
        );
    }
}

<?php

namespace Dcodegroup\XeroIntegration\Data\Accounting;

use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Optional;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroTrackingCategory extends AbstractXeroData
{
    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::ITEM_TRACKING_CATEGORY;
    }

    protected string $key = 'TrackingCategoryID';

    protected array $searchFields = [
        'Name',
    ];

    protected array $relatedFields = [];

    public function __construct(
        public string|Optional|null $TrackingCategoryID = null,
        public string|Optional|null $Name = null,
        public string|Optional|null $Status = null,
        /** @var Collection<int|string, XeroTrackingCategoryOption> */
        public Collection|Optional|null $Options = null,
    ) {
        if ($Options === null || $Options instanceof Optional) {
            $this->Options = collect();
        }
    }

    public function toXeroArray(): array
    {
        return [
            'Name' => data_get($this, 'Name'),
            'Status' => data_get($this, 'Status', false),
            'Options' => $this->Options->map(fn ($opt) => $opt->toXeroArray()),
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  array  $xeroTrackingCategory
     */
    public static function fromXero(XeroModel|array $xeroTrackingCategory): self
    {
        return new static(
            TrackingCategoryID: data_get($xeroTrackingCategory, 'TrackingCategoryID'),
            Name: data_get($xeroTrackingCategory, 'Name'),
            Status: data_get($xeroTrackingCategory, 'Status'),
            Options: collect(data_get($xeroTrackingCategory, 'Options', []))
                ->map(fn (XeroModel $option) => XeroTrackingCategoryOption::fromXero($option)),
        );
    }
}

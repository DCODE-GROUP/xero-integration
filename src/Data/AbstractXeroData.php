<?php

namespace Dcodegroup\XeroIntegration\Data;

use Dcodegroup\XeroIntegration\Data\Contracts\HasXeroData;
use Dcodegroup\XeroIntegration\Models\XeroRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use XeroPHP\Remote\Collection as XeroCollection;
use XeroPHP\Remote\Model as XeroModel;

abstract class AbstractXeroData implements HasXeroData
{
    public function getLocalModel(): ?Model
    {
        if (! property_exists($this, 'key') || empty($this->key)) {
            return null;
        }

        $record = XeroRecord::where('xero_id', $this->{$this->key})
            ->first();

        return $record?->recordable;
    }

    /**
     * Create a Collection of Xero Data objects from an array or XeroCollection of Xero Entities.
     *
     * @param  array<XeroModel>|XeroCollection|null  $items
     */
    public static function toCollection(array|XeroCollection|null $items): ?Collection
    {
        if (empty($items)) {
            return null;
        }

        $collection = collect();

        foreach ($items as $item) {
            $collection->push(static::fromXero($item));
        }

        return $collection;
    }

    /**
     * Create a Xero collection from an array of Xero Data objects.
     *
     * @param  Collection<self>  $items
     */
    public static function toXeroCollection(Collection $items): ?XeroCollection
    {
        if ($items->isEmpty()) {
            return null;
        }

        $collection = new XeroCollection;

        foreach ($items as $item) {
            $collection->append($item->toXeroArray());
        }

        return $collection;
    }

    abstract public function toXeroArray(): array;

    abstract public static function fromXero(XeroModel $xeroObject): self;
}

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
    protected Model $localModel;

    public function getLocalModel(): ?Model
    {
        if (! empty($this->localModel)) {
            return $this->localModel;
        }

        if (! property_exists($this, 'key') || empty($this->key)) {
            return null;
        }

        $record = XeroRecord::where('xero_id', $this->{$this->key})
            ->first();

        if (! empty($record) && ! empty($record->recordable)) {
            $this->localModel = $record->recordable;
        }

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
     * Create a plain array of Xero records from a collection of Xero Data objects.
     *
     * `XeroModel::fromStringArray()` nulls-out array-typed properties whose value is
     * not a plain `array` (an `ArrayObject` subclass like `XeroCollection` fails the
     * `is_array()` check), so we MUST return a plain array here so nested collections
     * (LineItems, ContactPersons, etc.) survive `validate()` and reach the API.
     *
     * @param  Collection<self>  $items
     * @return array<int, array<string, mixed>>|null
     */
    public static function toXeroCollection(Collection $items): ?array
    {
        if ($items->isEmpty()) {
            return null;
        }

        return $items->map(fn (self $item) => $item->toXeroArray())->values()->all();
    }

    abstract public function toXeroArray(): array;

    abstract public static function fromXero(XeroModel $xeroObject): self;
}

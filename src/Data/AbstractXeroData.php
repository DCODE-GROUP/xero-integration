<?php

namespace DcodeGroup\XeroIntegration\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use XeroPHP\Remote\Collection as XeroCollection;
use XeroPHP\Remote\Model as XeroModel;

abstract class AbstractXeroData
{
    protected ?Model $localModel = null;

    public function fromModel(Model $model): self
    {
        $this->localModel = $model;

        return new self(...$this->mapToData($model)); // @phpstan-ignore-line new.abstract
    }

    protected function getXeroId(): ?string
    {
        return $this->localModel->xeroRecord->xero_id ?? null;
    }

    /**
     * Create a Collection of Xero Data objects from an array of Xero Entities.
     *
     * @param  array<Model>  $items
     */
    public static function toCollection(array $items): ?Collection
    {
        if (empty($items)) {
            return null;
        }

        $collection = collect();

        foreach ($items as $item) {
            $collection->push(self::fromModel($item)); // @phpstan-ignore-line staticMethod.callToAbstract
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

    protected function syncToLocalModel(): void
    {
        $this->localModel->update($this->mapToModel());
    }

    abstract protected function toXeroArray(): array;

    abstract protected static function fromXero(XeroModel $xeroObject): self;

    abstract protected function mapToData(Model $model): array;

    abstract protected function mapToModel(): array;

}

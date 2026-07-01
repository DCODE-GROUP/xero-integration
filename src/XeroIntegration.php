<?php

namespace DcodeGroup\XeroIntegration;

use DcodeGroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Illuminate\Support\Collection;
use XeroPHP\Remote\Model;

class XeroIntegration
{
    public function __construct(
        protected XeroApp $app,
        protected XeroQuery $query
    ) {}

    public function __call(string $name, ?array $arguments)
    {
        $returnValue = call_user_func_array([$this->query, $name], $arguments);

        if (is_object($returnValue) && get_class($returnValue) === XeroQuery::class) {
            $this->query = $returnValue;

            return $this;
        }

        return $returnValue;
    }

    public function find($guid): ?Model
    {
        $class = $this->query->getFrom();

        return $this->app->loadByGUID($class, $guid);
    }

    public function limit(int $limit): self
    {
        $this->query->pageSize($limit);

        return $this;
    }

    /**
     * @return Collection|Model[]
     */
    public function get(): Collection
    {
        return collect($this->query->execute());
    }

    public function firstOrFail()
    {
        $results = $this->query->execute();

        if ($results->isEmpty()) {
            throw new XeroIntegrationException('No records found in Xero.');
        }

        return $results->first();
    }
}

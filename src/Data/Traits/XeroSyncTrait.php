<?php

namespace DcodeGroup\XeroIntegration\Data\Traits;

use DcodeGroup\XeroIntegration\Exceptions\XeroIntegrationException;
use DcodeGroup\XeroIntegration\XeroApp;
use DcodeGroup\XeroIntegration\XeroQuery;
use Exception;
use Illuminate\Support\Collection;
use XeroPHP\Remote\Model as XeroModel;

trait XeroSyncTrait
{
    protected ?XeroApp $xeroApp = null;

    public function sendToXero(string $tenant): void
    {
        $xeroApp = $this->getXeroApp();

        $xeroModel = $xeroApp->getModelForRelationship($this->xeroRelationship);

        $queryModel = $xeroApp->load($xeroModel);

        $xeroRecord = null;

        if (! empty($this->localModel->xeroRecord->xero_id)) {
            $xeroRecord = $this->searchForRecordInXero($queryModel);

            if (! empty($xeroRecord) && ! empty($xeroRecord->getGUID())) {
                $this->updateXeroRecord($xeroRecord->getGUID());
            }
        }

        if (empty($xeroRecord)) {
            $xeroRecord = new $xeroModel($xeroApp);
        }

        $xeroRecord = $this->buildXeroRecord($xeroRecord);

        $this->saveXeroRecord($xeroRecord, true);
    }

    protected function searchForRecordInXero(XeroQuery $xeroQuery): ?XeroModel
    {
        $query = $xeroQuery;

        foreach ($this->searchFields as $index => $field) {
            $value = data_get($this, $field);

            if (empty($value)) {
                continue;
            }

            if ($index === 0) {
                $query->where($field, $value);

                continue;
            }

            $query->orWhere($field, $value);
        }

        return $query->first();
    }

    protected function getXeroApp(): XeroApp
    {
        if (empty($this->xeroApp)) {
            $this->xeroApp = app(XeroApp::class);
        }

        return $this->xeroApp;
    }

    protected function updateXeroRecord(string $xeroId): void
    {
        $this->localModel->xeroRecord()?->updateOrCreate( // @phpstan-ignore-line method.notFound
            ['xero_id' => $xeroId],
            ['xero_id' => $xeroId]
        );
    }

    protected function buildXeroRecord(XeroModel $xeroRecord): XeroModel
    {
        $xeroArray = $this->toXeroArray();

        $xeroRecord->fromStringArray($xeroArray);

        if (! $xeroRecord->validate()) {
            throw new XeroIntegrationException('Xero Record is not valid');
        }

        foreach ($xeroArray as $field => $value) {
            if (is_null($value)) {
                $xeroRecord->setDirty($field);
            }
        }

        return $xeroRecord;
    }

    protected function saveXeroRecord(XeroModel $xeroRecord, bool $related = false): void
    {
        try {
            $this->xeroApp->save($xeroRecord, true);
        } catch (Exception $e) {
            throw new XeroIntegrationException('Failed to save Xero Record', 0, $e);
        }

        $xeroId = $xeroRecord->getGUID();

        if (empty($xeroId)) {
            throw new XeroIntegrationException('Failed to retrieve GUID from Xero Record after saving');
        }

        $this->updateXeroRecord($xeroId);

        if ($related && ! empty($this->searchFields)) {
            $this->updateRelatedXeroRecords($xeroRecord);
        }
    }

    protected function updateRelatedXeroRecords(XeroModel $xeroRecord)
    {
        foreach ($this->searchFields as $key => $relClass) {
            $related = data_get($this, $key);

            if (empty($related)) {
                continue;
            }

            $relXeroRecord = data_get($xeroRecord, $key);

            if ($related instanceof Collection) {
                foreach ($related as $key => $rel) {
                    $keyRelXeroRecord = data_get($relXeroRecord, $key);
                    $rel->saveXeroRecord($keyRelXeroRecord, true);
                }
            } else {
                $related->saveXeroRecord($relXeroRecord, true);
            }
        }
    }
}

<?php

namespace Dcodegroup\XeroIntegration\Data\Traits;

use Dcodegroup\XeroIntegration\Contracts\XeroDataFinder;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Exceptions\XeroValidationException;
use Dcodegroup\XeroIntegration\XeroApp;
use Dcodegroup\XeroIntegration\XeroQuery;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use XeroPHP\Models\Accounting\Attachment;
use XeroPHP\Remote\Model as XeroModel;

trait XeroSyncTrait
{
    protected ?XeroApp $xeroApp = null;

    protected ?XeroModel $xeroRecord = null;

    public function sendToXero(bool $syncRelated = true): void
    {
        $xeroApp = $this->getXeroApp();

        $xeroModel = static::getXeroRelationship()->getModelClass();

        $queryModel = $xeroApp->load($xeroModel);

        $xeroRecord = null;

        $localModel = $this->getLocalModel();

        if (! empty($localModel)) {
            $xeroRecord = $this->searchForRecordInXero($queryModel);

            if (! empty($xeroRecord) && ! empty($xeroRecord->getGUID())) {
                $this->updateXeroRecord($xeroRecord->getGUID());
            }
        }

        if (empty($xeroRecord)) {
            $xeroRecord = new $xeroModel($xeroApp);
        }

        $xeroRecord = $this->buildXeroRecord($xeroRecord);

        $this->saveXeroRecord($xeroRecord, $syncRelated);
    }

    /**
     * Attach binary content to the record most recently synchronized to Xero.
     */
    public function attachBinary(string $content, string $fileName, string $mimeType): void
    {
        if (! $this->xeroRecord || ! method_exists($this->xeroRecord, 'addAttachment')) {
            throw new XeroIntegrationException('A Xero record must be synchronized before attaching files');
        }

        $attachment = Attachment::createFromBinary($content, $fileName, $mimeType);
        $this->xeroRecord->addAttachment($attachment);
    }

    protected function searchForRecordInXero(?XeroQuery $query = null): ?XeroModel
    {
        if (empty($query)) {
            $query = app(XeroApp::class)->load(static::getXeroRelationship()->getModelClass());
        }

        $keyValue = data_get($this, $this->key);
        if (!$keyValue) {
            return null;
        }

        return app(XeroDataFinder::class)->find(static::class, $keyValue);
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
        $this->getLocalModel()?->xeroRecord()?->updateOrCreate( // @phpstan-ignore-line method.notFound
            ['xero_id' => $xeroId],
            ['xero_id' => $xeroId]
        );
    }

    protected function buildXeroRecord(XeroModel $xeroRecord): XeroModel
    {
        $xeroArray = $this->toXeroArray();
        $remoteData = $xeroRecord->toStringArray();

        $props = $xeroRecord->getProperties();
        $dirtyField = [];
        foreach ($xeroArray as $field => $value) {
            if (data_get($remoteData, $field) !== $value) {
                $remoteData[$field] = $value;
                $dirtyField[] = $field;
            }
        }
        $xeroRecord->fromStringArray($remoteData, true);

        if (! $xeroRecord->validate()) {
            throw new XeroIntegrationException('Xero Record is not valid');
        }

        foreach ($dirtyField as $field) {
            $xeroRecord->setDirty($field);
        }

        return $xeroRecord;
    }

    protected function saveXeroRecord(XeroModel $xeroRecord, bool $related = false): void
    {
        $localModel = $this->getLocalModel();

        try {
            $this->xeroApp->save($xeroRecord, true);
        } catch (Exception $e) {
            $message = Str::of($e->getMessage());
            if ($message->startsWith('A validation exception occurred')) {
                $validationMessage = $message->after('(')->beforeLast(')');
                throw new XeroValidationException($validationMessage, 0, $e);
            } else {
                throw new XeroIntegrationException('Failed to save Xero Record', 0, $e);
            }
        }

        $xeroId = $xeroRecord->getGUID();

        if (empty($xeroId)) {
            throw new XeroIntegrationException('Failed to retrieve GUID from Xero Record after saving');
        }

        if (! empty($this->key)) {
            $this->{$this->key} = $xeroId;
        }

        $this->xeroRecord = $xeroRecord;

        if (! empty($localModel)) {
            $this->updateXeroRecord($xeroId);
        }

        if ($related && ! empty($this->relatedFields)) {
            $this->updateRelatedXeroRecords($xeroRecord);
        }
    }

    protected function updateRelatedXeroRecords(XeroModel $xeroRecord)
    {
        foreach ($this->relatedFields as $key => $relClass) {
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

    public static function find(string $xeroId): ?self
    {
        $xeroModel = app(XeroDataFinder::class)->find(static::class, $xeroId);

        return $xeroModel ? static::fromXero($xeroModel) : null;
    }
}

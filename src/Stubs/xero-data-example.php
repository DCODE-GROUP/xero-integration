<?php

namespace App\Data\Xero;

use App\Models\Invoice;
use DcodeGroup\XeroIntegration\Data\XeroInvoiceData as PackageXeroInvoiceData;
use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use Illuminate\Database\Eloquent\Model;

class InvoiceData extends PackageXeroInvoiceData implements XeroSyncable
{
    public static function fromModel(Model|Invoice $model): self
    {
        // Functionality to map your models to Xero Models
        return new self(
            InvoiceNumber: $model->invoice_number,
            Total: $model->total,
            InvoiceDate: $model->date,
        );
    }

    public function syncToModel(): Model
    {
        $record = $this->localModel();

        $record->update([
            'invoice_number' => $this->InvoiceNumber,
            'total' => $this->Total,
            'date' => $this->InvoiceDate,
        ]);

        return $record;
    }

    public function localModel(): ?Model
    {
        return Invoice::whereHas('xeroRecord', function ($query) {
            $query->where('xero_id', $this->InvoiceID);
        })->firstOrNew();
    }
}

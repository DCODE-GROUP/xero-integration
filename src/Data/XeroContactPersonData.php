<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroContactPersonData extends Data implements XeroSyncable
{
    final public function __construct(
        public ?string $FirstName,
        public ?string $LastName,
        public ?string $EmailAddress,
        public bool $IncludeInEmails
    ) {}

    public function toXeroArray(): array
    {
        return [
            'FirstName' => $this->FirstName,
            'LastName' => $this->LastName,
            'EmailAddress' => $this->EmailAddress,
            'IncludeInEmails' => $this->IncludeInEmails,
        ];
    }

    /**
     * Create from Xero Model
     *
     * @param  array  $xeroContactPerson
     */
    public static function fromXero(XeroModel|array $xeroContactPerson): self
    {
        return new static(
            FirstName: data_get($xeroContactPerson, 'FirstName'),
            LastName: data_get($xeroContactPerson, 'LastName'),
            EmailAddress: data_get($xeroContactPerson, 'EmailAddress'),
            IncludeInEmails: data_get($xeroContactPerson, 'IncludeInEmails'),
        );
    }

    abstract public static function fromModel(Model $model): self;

    abstract public function syncToModel(): Model;

    abstract public function localModel(): ?Model;
}

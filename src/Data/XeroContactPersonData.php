<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Optional;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroContactPersonData extends AbstractXeroData
{
    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::CONTACT_PERSON;

    protected array $searchFields = [
        'EmailAddress',
    ];

    protected array $relatedFields = [];

    public function __construct(
        public string|Optional|null $FirstName,
        public string|Optional|null $LastName,
        public string|Optional|null $EmailAddress,
        public bool $IncludeInEmails = false
    ) {}

    public function toXeroArray(): array
    {
        return [
            'FirstName' => data_get($this, 'FirstName'),
            'LastName' => data_get($this, 'LastName'),
            'EmailAddress' => data_get($this, 'EmailAddress'),
            'IncludeInEmails' => data_get($this, 'IncludeInEmails'),
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

    public function mapToData(Model $model): array
    {
        return [];
    }

    public function mapToModel(): array
    {
        return [];
    }
}

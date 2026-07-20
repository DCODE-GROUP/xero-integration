<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use DcodeGroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Contact as XeroContact;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
abstract class XeroContactData extends AbstractXeroData implements XeroSyncable
{
    use XeroSyncTrait;

    protected string $xeroRelationship = 'contact';

    protected array $searchFields = [
        'EmailAddress',
    ];

    protected array $relatedFields = [
        'ContactPersons',
        'Addresses',
        'Phones',
    ];

    public function __construct(
        public string|Optional|null $ContactID,
        public string $ContactStatus, // ToDo: Change Enum
        public string|Optional|null $Name,
        public string|Optional|null $FirstName,
        public string|Optional|null $LastName,
        public string|Optional|null $EmailAddress,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public Carbon $UpdatedDateUTC,
        /** @var Collection<int,XeroContactPersonData>|null */
        public ?Collection $ContactPersons = null,
        public bool $IsSupplier = false,
        public bool $IsCustomer = true,
        /** @var Collection<int,XeroAddressData>|null */
        public ?Collection $Addresses = null,
        /** @var Collection<int,XeroPhoneData>|null */
        public ?Collection $Phones = null,
    ) {}

    /**
     * Create from Xero Model
     *
     * @param  XeroContact  $xeroContact
     */
    protected static function fromXero(XeroModel|XeroContact $xeroContact): self
    {
        return new static(
            ContactID: data_get($xeroContact, 'ContactID'),
            ContactStatus: data_get($xeroContact, 'ContactStatus'),
            Name: data_get($xeroContact, 'Name'),
            FirstName: data_get($xeroContact, 'FirstName'),
            LastName: data_get($xeroContact, 'LastName'),
            EmailAddress: data_get($xeroContact, 'EmailAddress'),
            UpdatedDateUTC: Carbon::parse(data_get($xeroContact, 'UpdatedDateUTC')),
            ContactPersons: XeroContactPersonData::toCollection(data_get($xeroContact, 'ContactPersons')),
            Addresses: XeroAddressData::toCollection(data_get($xeroContact, 'Addresses')),
            IsSupplier: data_get($xeroContact, 'IsSupplier'),
            IsCustomer: data_get($xeroContact, 'IsCustomer'),
            Phones: XeroPhoneData::toCollection(data_get($xeroContact, 'Phones')),
        );
    }

    public function toXeroArray(): array
    {
        return [
            'ContactID' => data_get($this, 'ContactID'),
            'ContactStatus' => data_get($this, 'ContactStatus'),
            'Name' => data_get($this, 'Name'),
            'FirstName' => data_get($this, 'FirstName'),
            'LastName' => data_get($this, 'LastName'),
            'EmailAddress' => data_get($this, 'EmailAddress'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
            'IsSupplier' => data_get($this, 'IsSupplier'),
            'IsCustomer' => data_get($this, 'IsCustomer'),
            'ContactPersons' => XeroContactPersonData::toXeroCollection(data_get($this, 'ContactPersons')),
            'Addresses' => XeroAddressData::toXeroCollection(data_get($this, 'Addresses')),
            'Phones' => XeroPhoneData::toXeroCollection(data_get($this, 'Phones')),
        ];
    }
}

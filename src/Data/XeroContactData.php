<?php

namespace DcodeGroup\XeroIntegration\Data;

use DcodeGroup\XeroIntegration\Data\Contracts\XeroSyncable;
use DcodeGroup\XeroIntegration\Data\XeroAddressData;
use DcodeGroup\XeroIntegration\Data\XeroContactPersonData;
use DcodeGroup\XeroIntegration\Data\XeroPhoneData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Contact as XeroContact;
use XeroPHP\Remote\Model as XeroModel;

abstract class XeroContactData extends Data implements XeroSyncable
{
    final public function __construct(
        public string|Optional|null $ContactID,
        public string $ContactStatus,
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
        /** @var Collection<int,XeroContactAddressData>|null */
        public ?Collection $Addresses = null,
        /** @var Collection<int,XeroPhoneData>|null */
        public ?Collection $Phones = null,
    ) {}

    /**
     * Create from Xero Model
     * @param XeroContact $xeroContact
     * @return self
     */
    public static function fromXero(XeroModel|XeroContact $xeroContact): self
    {
        return new static(
            ContactID: $xeroContact->getContactID(),
            ContactStatus: $xeroContact->getContactStatus(),
            Name: $xeroContact->getName(),
            FirstName: $xeroContact->getFirstName(),
            LastName: $xeroContact->getLastName(),
            EmailAddress: $xeroContact->getEmailAddress(),
            UpdatedDateUTC: Carbon::parse($xeroContact->getUpdatedDateUTC()),
            ContactPersons: collect($xeroContact->getContactPersons())->map(fn ($person) => XeroContactPersonData::fromXero($person)),
            Addresses: collect($xeroContact->getAddresses())->map(fn ($address) => XeroAddressData::fromXero($address)),
            IsSupplier: $xeroContact->getIsSupplier(),
            IsCustomer: $xeroContact->getIsCustomer(),
            Phones: collect($xeroContact->getPhones())->map(fn ($phone) => XeroPhoneData::fromXero($phone)),
        );
    }

    public function toXeroArray(): array
    {
        return [
            'ContactID' => $this->ContactID,
            'ContactStatus' => $this->ContactStatus,
            'Name' => $this->Name,
            'FirstName' => $this->FirstName,
            'LastName' => $this->LastName,
            'EmailAddress' => $this->EmailAddress,
            'UpdatedDateUTC' => $this->UpdatedDateUTC,
            'IsSupplier' => $this->IsSupplier,
            'IsCustomer' => $this->IsCustomer,
            'ContactPersons' => $this->ContactPersons?->map(fn ($person) => $person->toXeroArray())->toArray(),
            'Addresses' => $this->Addresses?->map(fn ($address) => $address->toXeroArray())->toArray(),
            'Phones' => $this->Phones?->map(fn ($phone) => $phone->toXeroArray())->toArray(),
        ];
    }

    abstract public static function fromModel(Model $model): self;

    abstract public function syncToModel(): Model;

    abstract public function localModel(): ?Model;
}

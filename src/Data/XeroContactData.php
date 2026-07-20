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
        public string|Optional|null $ContactNumber = null,
        public string|Optional|null $AccountNumber = null,
        public string|Optional|null $SkypeUserName = null,
        public string|Optional|null $BankAccountDetails = null,
        public string|Optional|null $TaxNumber = null,
        public string|Optional|null $CompanyNumber = null,
        public string|Optional|null $AccountsReceivableTaxType = null,
        public string|Optional|null $AccountsPayableTaxType = null,
        public string|Optional|null $DefaultCurrency = null,
        public string|Optional|null $XeroNetworkKey = null,
        public string|Optional|null $MergedToContactID = null,
        public string|Optional|null $SalesDefaultAccountCode = null,
        public string|Optional|null $PurchasesDefaultAccountCode = null,
        public string|Optional|null $TrackingCategoryName = null,
        public string|Optional|null $TrackingCategoryOption = null,
        public string|Optional|null $Website = null,
        public string|Optional|null $BatchPayments = null,
        public float|Optional|null $Discount = null,
        public string|Optional|null $Balances = null,
        public bool $HasAttachments = false,
    ) {}

    /**
     * Create from Xero Model
     *
     * @param  XeroContact  $xeroContact
     */
    protected static function fromXero(XeroModel|XeroContact $xeroContact): self
    {
        return new static(
            ContactID : data_get($xeroContact, 'ContactID'),
            ContactStatus : data_get($xeroContact, 'ContactStatus'),
            Name : data_get($xeroContact, 'Name'),
            FirstName : data_get($xeroContact, 'FirstName'),
            LastName : data_get($xeroContact, 'LastName'),
            EmailAddress : data_get($xeroContact, 'EmailAddress'),
            UpdatedDateUTC : Carbon::parse(data_get($xeroContact, 'UpdatedDateUTC')),
            ContactPersons : XeroContactPersonData::toCollection(data_get($xeroContact, 'ContactPersons')),
            IsSupplier : data_get($xeroContact, 'IsSupplier'),
            IsCustomer : data_get($xeroContact, 'IsCustomer'),
            Addresses : XeroAddressData::toCollection(data_get($xeroContact, 'Addresses')),
            Phones : XeroPhoneData::toCollection(data_get($xeroContact, 'Phones')),
            ContactNumber : data_get($xeroContact, 'ContactNumber'),
            AccountNumber : data_get($xeroContact, 'AccountNumber'),
            SkypeUserName : data_get($xeroContact, 'SkypeUserName'),
            BankAccountDetails : data_get($xeroContact, 'BankAccountDetails'),
            TaxNumber : data_get($xeroContact, 'TaxNumber'),
            CompanyNumber : data_get($xeroContact, 'CompanyNumber'),
            AccountsReceivableTaxType : data_get($xeroContact, 'AccountsReceivableTaxType'),
            AccountsPayableTaxType : data_get($xeroContact, 'AccountsPayableTaxType'),
            DefaultCurrency : data_get($xeroContact, 'DefaultCurrency'),
            XeroNetworkKey : data_get($xeroContact, 'XeroNetworkKey'),
            MergedToContactID : data_get($xeroContact, 'MergedToContactID'),
            SalesDefaultAccountCode : data_get($xeroContact, 'SalesDefaultAccountCode'),
            PurchasesDefaultAccountCode : data_get($xeroContact, 'PurchasesDefaultAccountCode'),
            TrackingCategoryName : data_get($xeroContact, 'TrackingCategoryName'),
            TrackingCategoryOption : data_get($xeroContact, 'TrackingCategoryOption'),
            Website : data_get($xeroContact, 'Website'),
            BatchPayments : data_get($xeroContact, 'BatchPayments'),
            Discount : data_get($xeroContact, 'Discount'),
            Balances : data_get($xeroContact, 'Balances'),
            HasAttachments : data_get($xeroContact, 'HasAttachments'),
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
            'ContactNumber' => data_get($this, 'ContactNumber'),
            'AccountNumber' => data_get($this, 'AccountNumber'),
            'SkypeUserName' => data_get($this, 'SkypeUserName'),
            'BankAccountDetails' => data_get($this, 'BankAccountDetails'),
            'TaxNumber' => data_get($this, 'TaxNumber'),
            'CompanyNumber' => data_get($this, 'CompanyNumber'),
            'AccountsReceivableTaxType' => data_get($this, 'AccountsReceivableTaxType'),
            'AccountsPayableTaxType' => data_get($this, 'AccountsPayableTaxType'),
            'DefaultCurrency' => data_get($this, 'DefaultCurrency'),
            'XeroNetworkKey' => data_get($this, 'XeroNetworkKey'),
            'MergedToContactID' => data_get($this, 'MergedToContactID'),
            'SalesDefaultAccountCode' => data_get($this, 'SalesDefaultAccountCode'),
            'PurchasesDefaultAccountCode' => data_get($this, 'PurchasesDefaultAccountCode'),
            'TrackingCategoryName' => data_get($this, 'TrackingCategoryName'),
            'TrackingCategoryOption' => data_get($this, 'TrackingCategoryOption'),
            'Website' => data_get($this, 'Website'),
            'BatchPayments' => data_get($this, 'BatchPayments'),
            'Discount' => data_get($this, 'Discount'),
            'Balances' => data_get($this, 'Balances'),
            'HasAttachments' => data_get($this, 'HasAttachments'),
        ];
    }
}

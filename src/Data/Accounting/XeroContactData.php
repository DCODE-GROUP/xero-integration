<?php

namespace Dcodegroup\XeroIntegration\Data\Accounting;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Data\Accounting\XeroAddressData;
use Dcodegroup\XeroIntegration\Data\Accounting\XeroContactPersonData;
use Dcodegroup\XeroIntegration\Data\Accounting\XeroPhoneData;
use Dcodegroup\XeroIntegration\Data\Contracts\XeroSyncable;
use Dcodegroup\XeroIntegration\Data\Traits\XeroSyncTrait;
use Dcodegroup\XeroIntegration\Enums\XeroContactStatusEnum;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Contact as XeroContact;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroContactData extends AbstractXeroData implements XeroSyncable
{
    use XeroSyncTrait;

    protected XeroRelationshipsEnum $xeroRelationship = XeroRelationshipsEnum::CONTACT;

    protected string $key = 'ContactID';

    protected array $searchFields = [
        'EmailAddress',
    ];

    protected array $relatedFields = [
        'ContactPersons',
        'Addresses',
        'Phones',
    ];

    public function __construct(
        public XeroContactStatusEnum $ContactStatus,
        public string $Name,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public Carbon $UpdatedDateUTC,
        public bool $IsSupplier = false,
        public bool $IsCustomer = false,
        public bool $HasAttachments = false,
        public string|Optional|null $ContactID = null,
        public string|Optional|null $FirstName = null,
        public string|Optional|null $LastName = null,
        public string|Optional|null $EmailAddress = null,
        /** @var Collection<int,XeroContactPersonData>|Optional|null */
        public Collection|Optional|null $ContactPersons = null,
        /** @var Collection<int,XeroAddressData>|Optional|null */
        public Collection|Optional|null $Addresses = null,
        /** @var Collection<int,XeroPhoneData>|Optional|null */
        public Collection|Optional|null $Phones = null,
        public string|Optional|null $ContactNumber = null,
        public string|Optional|null $AccountNumber = null,
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
        public string|Optional|null $PaymentTerms = null,
        public string|Optional|null $Website = null,
        public string|Optional|null $BatchPayments = null,
        public float|Optional|null $Discount = null,
        public string|Optional|null $Balances = null,
    ) {}

    /**
     * Create from Xero Model
     */
    public static function fromXero(XeroModel|XeroContact $xeroContact): self
    {
        return new static(
            ContactID : data_get($xeroContact, 'ContactID'),
            ContactStatus : XeroContactStatusEnum::tryFrom(data_get($xeroContact, 'ContactStatus')) ?? XeroContactStatusEnum::ACTIVE,
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
            PaymentTerms: data_get($xeroContact, 'PaymentTerms'),
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
            'ContactStatus' => data_get($this, 'ContactStatus')?->getXeroValue(),
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
            'PaymentTerms' => data_get($this, 'PaymentTerms'),
        ];
    }
}

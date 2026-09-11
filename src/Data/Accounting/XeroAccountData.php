<?php

namespace Dcodegroup\XeroIntegration\Data\Accounting;

use Carbon\Carbon;
use Dcodegroup\XeroIntegration\Data\AbstractXeroData;
use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;
use XeroPHP\Models\Accounting\Account as XeroAccount;
use XeroPHP\Remote\Model as XeroModel;

/**
 * @phpstan-consistent-constructor
 */
class XeroAccountData extends AbstractXeroData
{
    public static function getXeroRelationship(): XeroRelationshipsEnum
    {
        return XeroRelationshipsEnum::ACCOUNT;
    }

    protected string $key = 'AccountID';

    protected array $searchFields = [
        'Code',
        'Name',
        'Type',
    ];

    protected array $relatedFields = [];

    public function __construct(
        public string $Code,
        public string $Name,
        public string $Type,
        public string|Optional|null $AccountID = null,
        public string|Optional|null $BankAccountNumber = null,
        public string|Optional|null $Status = null,
        public string|Optional|null $Description = null,
        public string|Optional|null $BankAccountType = null,
        public string|Optional|null $CurrencyCode = null,
        public string|Optional|null $TaxType = null,
        public bool|Optional|null $EnablePaymentsToAccount = null,
        public bool|Optional|null $ShowInExpenseClaims = null,
        public string|Optional|null $Class = null,
        public string|Optional|null $SystemAccount = null,
        public string|Optional|null $ReportingCode = null,
        public string|Optional|null $ReportingCodeName = null,
        public bool|Optional|null $HasAttachments = null,
        #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM, setTimeZone: 'UTC')]
        public Carbon|Optional|null $UpdatedDateUTC = null,
    ) {}

    public function toXeroArray(): array
    {
        return [
            'AccountID' => data_get($this, 'AccountID'),
            'Code' => data_get($this, 'Code'),
            'Name' => data_get($this, 'Name'),
            'Type' => data_get($this, 'Type'),
            'BankAccountNumber' => data_get($this, 'BankAccountNumber'),
            'Status' => data_get($this, 'Status'),
            'Description' => data_get($this, 'Description'),
            'BankAccountType' => data_get($this, 'BankAccountType'),
            'CurrencyCode' => data_get($this, 'CurrencyCode'),
            'TaxType' => data_get($this, 'TaxType'),
            'EnablePaymentsToAccount' => data_get($this, 'EnablePaymentsToAccount'),
            'ShowInExpenseClaims' => data_get($this, 'ShowInExpenseClaims'),
            'Class' => data_get($this, 'Class'),
            'SystemAccount' => data_get($this, 'SystemAccount'),
            'ReportingCode' => data_get($this, 'ReportingCode'),
            'ReportingCodeName' => data_get($this, 'ReportingCodeName'),
            'HasAttachments' => data_get($this, 'HasAttachments'),
            'UpdatedDateUTC' => data_get($this, 'UpdatedDateUTC'),
        ];
    }

    /**
     * Create from Xero Model
     */
    public static function fromXero(XeroModel|XeroAccount $xeroAccount): self
    {
        $updatedDate = data_get($xeroAccount, 'UpdatedDateUTC');

        return new static(
            Code: data_get($xeroAccount, 'Code'),
            Name: data_get($xeroAccount, 'Name'),
            Type: data_get($xeroAccount, 'Type'),
            AccountID: data_get($xeroAccount, 'AccountID'),
            BankAccountNumber: data_get($xeroAccount, 'BankAccountNumber'),
            Status: data_get($xeroAccount, 'Status'),
            Description: data_get($xeroAccount, 'Description'),
            BankAccountType: data_get($xeroAccount, 'BankAccountType'),
            CurrencyCode: data_get($xeroAccount, 'CurrencyCode'),
            TaxType: data_get($xeroAccount, 'TaxType'),
            EnablePaymentsToAccount: data_get($xeroAccount, 'EnablePaymentsToAccount'),
            ShowInExpenseClaims: data_get($xeroAccount, 'ShowInExpenseClaims'),
            Class: data_get($xeroAccount, 'Class'),
            SystemAccount: data_get($xeroAccount, 'SystemAccount'),
            ReportingCode: data_get($xeroAccount, 'ReportingCode'),
            ReportingCodeName: data_get($xeroAccount, 'ReportingCodeName'),
            HasAttachments: data_get($xeroAccount, 'HasAttachments'),
            UpdatedDateUTC: $updatedDate === null ? null : Carbon::instance($updatedDate),
        );
    }
}

<?php

namespace DcodeGroup\XeroIntegration\Http\Resource;

use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateAccount;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateAccountAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateBankTransactionAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateBankTransactionHistoryRecord;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateBankTransactions;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateBankTransfer;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateBankTransferAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateBankTransferHistoryRecord;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateBatchPayment;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateBatchPaymentHistoryRecord;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateBrandingThemePaymentServices;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateContactAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateContactGroup;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateContactGroupContacts;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateContactHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateContacts;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateCreditNoteAllocation;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateCreditNoteAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateCreditNoteHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateCreditNotes;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateCurrency;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateExpenseClaimHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateExpenseClaims;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateInvoiceAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateInvoiceHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateInvoices;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateItemHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateItems;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateLinkedTransaction;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateManualJournalAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateManualJournalHistoryRecord;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateManualJournals;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateOverpaymentAllocations;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateOverpaymentHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreatePayment;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreatePaymentHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreatePayments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreatePaymentService;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreatePrepaymentAllocations;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreatePrepaymentHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreatePurchaseOrderAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreatePurchaseOrderHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreatePurchaseOrders;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateQuoteAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateQuoteHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateQuotes;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateReceipt;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateReceiptAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateReceiptHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateRepeatingInvoiceAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateRepeatingInvoiceHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateRepeatingInvoices;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateTaxRates;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateTrackingCategory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\CreateTrackingOptions;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeleteAccount;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeleteBatchPayment;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeleteBatchPaymentByUrlParam;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeleteContactGroupContact;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeleteContactGroupContacts;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeleteCreditNoteAllocations;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeleteItem;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeleteLinkedTransaction;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeleteOverpaymentAllocations;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeletePayment;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeletePrepaymentAllocations;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeleteTrackingCategory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\DeleteTrackingOptions;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\EmailInvoice;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetAccount;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetAccountAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetAccountAttachmentById;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetAccountAttachments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetAccounts;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransaction;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransactionAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransactionAttachmentById;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransactionAttachments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransactions;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransactionsHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransfer;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransferAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransferAttachmentById;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransferAttachments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransferHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBankTransfers;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBatchPayment;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBatchPaymentHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBatchPayments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBrandingTheme;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBrandingThemePaymentServices;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBrandingThemes;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBudget;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetBudgets;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetContact;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetContactAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetContactAttachmentById;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetContactAttachments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetContactByContactNumber;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetContactCissettings;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetContactGroup;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetContactGroups;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetContactHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetContacts;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetCreditNote;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetCreditNoteAsPdf;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetCreditNoteAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetCreditNoteAttachmentById;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetCreditNoteAttachments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetCreditNoteHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetCreditNotes;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetCurrencies;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetExpenseClaim;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetExpenseClaimHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetExpenseClaims;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetInvoice;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetInvoiceAsPdf;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetInvoiceAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetInvoiceAttachmentById;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetInvoiceAttachments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetInvoiceHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetInvoiceReminders;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetInvoices;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetItem;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetItemHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetItems;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetJournal;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetJournalByNumber;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetJournals;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetLinkedTransaction;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetLinkedTransactions;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetManualJournal;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetManualJournalAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetManualJournalAttachmentById;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetManualJournalAttachments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetManualJournals;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetManualJournalsHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetOnlineInvoice;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetOrganisationActions;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetOrganisationCissettings;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetOrganisations;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetOverpayment;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetOverpaymentHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetOverpayments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPayment;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPaymentHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPayments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPaymentServices;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPrepayment;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPrepaymentHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPrepayments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPurchaseOrder;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPurchaseOrderAsPdf;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPurchaseOrderAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPurchaseOrderAttachmentById;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPurchaseOrderAttachments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPurchaseOrderByNumber;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPurchaseOrderHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetPurchaseOrders;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetQuote;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetQuoteAsPdf;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetQuoteAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetQuoteAttachmentById;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetQuoteAttachments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetQuoteHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetQuotes;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReceipt;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReceiptAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReceiptAttachmentById;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReceiptAttachments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReceiptHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReceipts;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetRepeatingInvoice;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetRepeatingInvoiceAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetRepeatingInvoiceAttachmentById;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetRepeatingInvoiceAttachments;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetRepeatingInvoiceHistory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetRepeatingInvoices;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReportAgedPayablesByContact;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReportAgedReceivablesByContact;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReportBalanceSheet;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReportBankSummary;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReportBudgetSummary;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReportExecutiveSummary;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReportFromId;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReportProfitAndLoss;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReportsList;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReportTenNinetyNine;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetReportTrialBalance;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetTaxRateByTaxType;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetTaxRates;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetTrackingCategories;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetTrackingCategory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetUser;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\GetUsers;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\PostSetup;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateAccount;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateAccountAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateBankTransaction;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateBankTransactionAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateBankTransferAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateContact;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateContactAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateContactGroup;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateCreditNote;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateCreditNoteAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateExpenseClaim;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateInvoice;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateInvoiceAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateItem;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateLinkedTransaction;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateManualJournal;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateManualJournalAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateOrCreateBankTransactions;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateOrCreateContacts;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateOrCreateCreditNotes;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateOrCreateInvoices;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateOrCreateItems;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateOrCreateManualJournals;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateOrCreatePurchaseOrders;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateOrCreateQuotes;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateOrCreateRepeatingInvoices;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdatePurchaseOrder;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdatePurchaseOrderAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateQuote;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateQuoteAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateReceipt;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateReceiptAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateRepeatingInvoice;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateRepeatingInvoiceAttachmentByFileName;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateTaxRate;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateTrackingCategory;
use DcodeGroup\XeroIntegration\Http\Requests\Accounting\UpdateTrackingOptions;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Accounting extends BaseResource
{
    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getAccounts(?string $where = null, ?string $order = null, ?string $ifModifiedSince = null): Response
    {
        return $this->connector->send(new GetAccounts($where, $order, $ifModifiedSince));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createAccount(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateAccount($idempotencyKey));
    }

    /**
     * @param  string  $accountId  Unique identifier for Account object
     */
    public function getAccount(string $accountId): Response
    {
        return $this->connector->send(new GetAccount($accountId));
    }

    /**
     * @param  string  $accountId  Unique identifier for Account object
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateAccount(string $accountId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateAccount($accountId, $idempotencyKey));
    }

    /**
     * @param  string  $accountId  Unique identifier for Account object
     */
    public function deleteAccount(string $accountId): Response
    {
        return $this->connector->send(new DeleteAccount($accountId));
    }

    /**
     * @param  string  $accountId  Unique identifier for Account object
     */
    public function getAccountAttachments(string $accountId): Response
    {
        return $this->connector->send(new GetAccountAttachments($accountId));
    }

    /**
     * @param  string  $accountId  Unique identifier for Account object
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getAccountAttachmentById(string $accountId, string $attachmentId, string $contentType): Response
    {
        return $this->connector->send(new GetAccountAttachmentById($accountId, $attachmentId, $contentType));
    }

    /**
     * @param  string  $accountId  Unique identifier for Account object
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getAccountAttachmentByFileName(string $accountId, string $fileName, string $contentType): Response
    {
        return $this->connector->send(new GetAccountAttachmentByFileName($accountId, $fileName, $contentType));
    }

    /**
     * @param  string  $accountId  Unique identifier for Account object
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createAccountAttachmentByFileName(
        string $accountId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateAccountAttachmentByFileName($accountId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $accountId  Unique identifier for Account object
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateAccountAttachmentByFileName(
        string $accountId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateAccountAttachmentByFileName($accountId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getBatchPayments(
        ?string $where = null,
        ?string $order = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetBatchPayments($where, $order, $ifModifiedSince));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createBatchPayment(?bool $summarizeErrors = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateBatchPayment($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function deleteBatchPayment(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new DeleteBatchPayment($idempotencyKey));
    }

    /**
     * @param  string  $batchPaymentId  Unique identifier for BatchPayment
     */
    public function getBatchPayment(string $batchPaymentId): Response
    {
        return $this->connector->send(new GetBatchPayment($batchPaymentId));
    }

    /**
     * @param  string  $batchPaymentId  Unique identifier for BatchPayment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function deleteBatchPaymentByUrlParam(string $batchPaymentId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new DeleteBatchPaymentByUrlParam($batchPaymentId, $idempotencyKey));
    }

    /**
     * @param  string  $batchPaymentId  Unique identifier for BatchPayment
     */
    public function getBatchPaymentHistory(string $batchPaymentId): Response
    {
        return $this->connector->send(new GetBatchPaymentHistory($batchPaymentId));
    }

    /**
     * @param  string  $batchPaymentId  Unique identifier for BatchPayment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createBatchPaymentHistoryRecord(string $batchPaymentId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateBatchPaymentHistoryRecord($batchPaymentId, $idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  int  $page  Up to 100 bank transactions will be returned in a single API call with line items details
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  int  $pageSize  Number of records to retrieve per page
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getBankTransactions(
        ?string $where = null,
        ?string $order = null,
        ?int $page = null,
        ?int $unitdp = null,
        ?int $pageSize = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetBankTransactions($where, $order, $page, $unitdp, $pageSize, $ifModifiedSince));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createBankTransactions(
        ?bool $summarizeErrors = null,
        ?int $unitdp = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateBankTransactions($summarizeErrors, $unitdp, $idempotencyKey));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateOrCreateBankTransactions(
        ?bool $summarizeErrors = null,
        ?int $unitdp = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateOrCreateBankTransactions($summarizeErrors, $unitdp, $idempotencyKey));
    }

    /**
     * @param  string  $bankTransactionId  Xero generated unique identifier for a bank transaction
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     */
    public function getBankTransaction(string $bankTransactionId, ?int $unitdp = null): Response
    {
        return $this->connector->send(new GetBankTransaction($bankTransactionId, $unitdp));
    }

    /**
     * @param  string  $bankTransactionId  Xero generated unique identifier for a bank transaction
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateBankTransaction(
        string $bankTransactionId,
        ?int $unitdp = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateBankTransaction($bankTransactionId, $unitdp, $idempotencyKey));
    }

    /**
     * @param  string  $bankTransactionId  Xero generated unique identifier for a bank transaction
     */
    public function getBankTransactionAttachments(string $bankTransactionId): Response
    {
        return $this->connector->send(new GetBankTransactionAttachments($bankTransactionId));
    }

    /**
     * @param  string  $bankTransactionId  Xero generated unique identifier for a bank transaction
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getBankTransactionAttachmentById(
        string $bankTransactionId,
        string $attachmentId,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetBankTransactionAttachmentById($bankTransactionId, $attachmentId, $contentType));
    }

    /**
     * @param  string  $bankTransactionId  Xero generated unique identifier for a bank transaction
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getBankTransactionAttachmentByFileName(
        string $bankTransactionId,
        string $fileName,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetBankTransactionAttachmentByFileName($bankTransactionId, $fileName, $contentType));
    }

    /**
     * @param  string  $bankTransactionId  Xero generated unique identifier for a bank transaction
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createBankTransactionAttachmentByFileName(
        string $bankTransactionId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateBankTransactionAttachmentByFileName($bankTransactionId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $bankTransactionId  Xero generated unique identifier for a bank transaction
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateBankTransactionAttachmentByFileName(
        string $bankTransactionId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateBankTransactionAttachmentByFileName($bankTransactionId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $bankTransactionId  Xero generated unique identifier for a bank transaction
     */
    public function getBankTransactionsHistory(string $bankTransactionId): Response
    {
        return $this->connector->send(new GetBankTransactionsHistory($bankTransactionId));
    }

    /**
     * @param  string  $bankTransactionId  Xero generated unique identifier for a bank transaction
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createBankTransactionHistoryRecord(
        string $bankTransactionId,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateBankTransactionHistoryRecord($bankTransactionId, $idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getBankTransfers(
        ?string $where = null,
        ?string $order = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetBankTransfers($where, $order, $ifModifiedSince));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createBankTransfer(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateBankTransfer($idempotencyKey));
    }

    /**
     * @param  string  $bankTransferId  Xero generated unique identifier for a bank transfer
     */
    public function getBankTransfer(string $bankTransferId): Response
    {
        return $this->connector->send(new GetBankTransfer($bankTransferId));
    }

    /**
     * @param  string  $bankTransferId  Xero generated unique identifier for a bank transfer
     */
    public function getBankTransferAttachments(string $bankTransferId): Response
    {
        return $this->connector->send(new GetBankTransferAttachments($bankTransferId));
    }

    /**
     * @param  string  $bankTransferId  Xero generated unique identifier for a bank transfer
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getBankTransferAttachmentById(
        string $bankTransferId,
        string $attachmentId,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetBankTransferAttachmentById($bankTransferId, $attachmentId, $contentType));
    }

    /**
     * @param  string  $bankTransferId  Xero generated unique identifier for a bank transfer
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getBankTransferAttachmentByFileName(
        string $bankTransferId,
        string $fileName,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetBankTransferAttachmentByFileName($bankTransferId, $fileName, $contentType));
    }

    /**
     * @param  string  $bankTransferId  Xero generated unique identifier for a bank transfer
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createBankTransferAttachmentByFileName(
        string $bankTransferId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateBankTransferAttachmentByFileName($bankTransferId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $bankTransferId  Xero generated unique identifier for a bank transfer
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateBankTransferAttachmentByFileName(
        string $bankTransferId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateBankTransferAttachmentByFileName($bankTransferId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $bankTransferId  Xero generated unique identifier for a bank transfer
     */
    public function getBankTransferHistory(string $bankTransferId): Response
    {
        return $this->connector->send(new GetBankTransferHistory($bankTransferId));
    }

    /**
     * @param  string  $bankTransferId  Xero generated unique identifier for a bank transfer
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createBankTransferHistoryRecord(string $bankTransferId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateBankTransferHistoryRecord($bankTransferId, $idempotencyKey));
    }

    public function getBrandingThemes(): Response
    {
        return $this->connector->send(new GetBrandingThemes);
    }

    /**
     * @param  string  $brandingThemeId  Unique identifier for a Branding Theme
     */
    public function getBrandingTheme(string $brandingThemeId): Response
    {
        return $this->connector->send(new GetBrandingTheme($brandingThemeId));
    }

    /**
     * @param  string  $brandingThemeId  Unique identifier for a Branding Theme
     */
    public function getBrandingThemePaymentServices(string $brandingThemeId): Response
    {
        return $this->connector->send(new GetBrandingThemePaymentServices($brandingThemeId));
    }

    /**
     * @param  string  $brandingThemeId  Unique identifier for a Branding Theme
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createBrandingThemePaymentServices(string $brandingThemeId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateBrandingThemePaymentServices($brandingThemeId, $idempotencyKey));
    }

    /**
     * @param  string  $ids  Filter by BudgetID. Allows you to retrieve a specific individual budget.
     * @param  string  $dateTo  Filter by start date
     * @param  string  $dateFrom  Filter by end date
     */
    public function getBudgets(?string $ids = null, ?string $dateTo = null, ?string $dateFrom = null): Response
    {
        return $this->connector->send(new GetBudgets($ids, $dateTo, $dateFrom));
    }

    /**
     * @param  string  $budgetId  Unique identifier for Budgets
     * @param  string  $dateTo  Filter by start date
     * @param  string  $dateFrom  Filter by end date
     */
    public function getBudget(string $budgetId, ?string $dateTo = null, ?string $dateFrom = null): Response
    {
        return $this->connector->send(new GetBudget($budgetId, $dateTo, $dateFrom));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  array  $ids  Filter by a comma separated list of ContactIDs. Allows you to retrieve a specific set of contacts in a single call.
     * @param  int  $page  e.g. page=1 - Up to 100 contacts will be returned in a single API call.
     * @param  bool  $includeArchived  e.g. includeArchived=true - Contacts with a status of ARCHIVED will be included in the response
     * @param  bool  $summaryOnly  Use summaryOnly=true in GET Contacts and Invoices endpoint to retrieve a smaller version of the response object. This returns only lightweight fields, excluding computation-heavy fields from the response, making the API calls quick and efficient.
     * @param  string  $searchTerm  Search parameter that performs a case-insensitive text search across the Name, FirstName, LastName, ContactNumber and EmailAddress fields.
     * @param  int  $pageSize  Number of records to retrieve per page
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getContacts(
        ?string $where = null,
        ?string $order = null,
        ?array $ids = null,
        ?int $page = null,
        ?bool $includeArchived = null,
        ?bool $summaryOnly = null,
        ?string $searchTerm = null,
        ?int $pageSize = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetContacts($where, $order, $ids, $page, $includeArchived, $summaryOnly, $searchTerm, $pageSize, $ifModifiedSince));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createContacts(?bool $summarizeErrors = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateContacts($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateOrCreateContacts(?bool $summarizeErrors = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateOrCreateContacts($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  string  $contactNumber  This field is read only on the Xero contact screen, used to identify contacts in external systems (max length = 50).
     */
    public function getContactByContactNumber(string $contactNumber): Response
    {
        return $this->connector->send(new GetContactByContactNumber($contactNumber));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     */
    public function getContact(string $contactId): Response
    {
        return $this->connector->send(new GetContact($contactId));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateContact(string $contactId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateContact($contactId, $idempotencyKey));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     */
    public function getContactAttachments(string $contactId): Response
    {
        return $this->connector->send(new GetContactAttachments($contactId));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getContactAttachmentById(string $contactId, string $attachmentId, string $contentType): Response
    {
        return $this->connector->send(new GetContactAttachmentById($contactId, $attachmentId, $contentType));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getContactAttachmentByFileName(string $contactId, string $fileName, string $contentType): Response
    {
        return $this->connector->send(new GetContactAttachmentByFileName($contactId, $fileName, $contentType));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createContactAttachmentByFileName(
        string $contactId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateContactAttachmentByFileName($contactId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateContactAttachmentByFileName(
        string $contactId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateContactAttachmentByFileName($contactId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     */
    public function getContactCissettings(string $contactId): Response
    {
        return $this->connector->send(new GetContactCissettings($contactId));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     */
    public function getContactHistory(string $contactId): Response
    {
        return $this->connector->send(new GetContactHistory($contactId));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createContactHistory(string $contactId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateContactHistory($contactId, $idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     */
    public function getContactGroups(?string $where = null, ?string $order = null): Response
    {
        return $this->connector->send(new GetContactGroups($where, $order));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createContactGroup(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateContactGroup($idempotencyKey));
    }

    /**
     * @param  string  $contactGroupId  Unique identifier for a Contact Group
     */
    public function getContactGroup(string $contactGroupId): Response
    {
        return $this->connector->send(new GetContactGroup($contactGroupId));
    }

    /**
     * @param  string  $contactGroupId  Unique identifier for a Contact Group
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateContactGroup(string $contactGroupId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateContactGroup($contactGroupId, $idempotencyKey));
    }

    /**
     * @param  string  $contactGroupId  Unique identifier for a Contact Group
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createContactGroupContacts(string $contactGroupId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateContactGroupContacts($contactGroupId, $idempotencyKey));
    }

    /**
     * @param  string  $contactGroupId  Unique identifier for a Contact Group
     */
    public function deleteContactGroupContacts(string $contactGroupId): Response
    {
        return $this->connector->send(new DeleteContactGroupContacts($contactGroupId));
    }

    /**
     * @param  string  $contactGroupId  Unique identifier for a Contact Group
     * @param  string  $contactId  Unique identifier for a Contact
     */
    public function deleteContactGroupContact(string $contactGroupId, string $contactId): Response
    {
        return $this->connector->send(new DeleteContactGroupContact($contactGroupId, $contactId));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  int  $page  e.g. page=1 – Up to 100 credit notes will be returned in a single API call with line items shown for each credit note
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  int  $pageSize  Number of records to retrieve per page
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getCreditNotes(
        ?string $where = null,
        ?string $order = null,
        ?int $page = null,
        ?int $unitdp = null,
        ?int $pageSize = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetCreditNotes($where, $order, $page, $unitdp, $pageSize, $ifModifiedSince));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createCreditNotes(
        ?bool $summarizeErrors = null,
        ?int $unitdp = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateCreditNotes($summarizeErrors, $unitdp, $idempotencyKey));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateOrCreateCreditNotes(
        ?bool $summarizeErrors = null,
        ?int $unitdp = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateOrCreateCreditNotes($summarizeErrors, $unitdp, $idempotencyKey));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     */
    public function getCreditNote(string $creditNoteId, ?int $unitdp = null): Response
    {
        return $this->connector->send(new GetCreditNote($creditNoteId, $unitdp));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateCreditNote(string $creditNoteId, ?int $unitdp = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateCreditNote($creditNoteId, $unitdp, $idempotencyKey));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     */
    public function getCreditNoteAttachments(string $creditNoteId): Response
    {
        return $this->connector->send(new GetCreditNoteAttachments($creditNoteId));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getCreditNoteAttachmentById(
        string $creditNoteId,
        string $attachmentId,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetCreditNoteAttachmentById($creditNoteId, $attachmentId, $contentType));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getCreditNoteAttachmentByFileName(
        string $creditNoteId,
        string $fileName,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetCreditNoteAttachmentByFileName($creditNoteId, $fileName, $contentType));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  string  $fileName  Name of the attachment
     * @param  bool  $includeOnline  Allows an attachment to be seen by the end customer within their online invoice
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createCreditNoteAttachmentByFileName(
        string $creditNoteId,
        string $fileName,
        ?bool $includeOnline = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateCreditNoteAttachmentByFileName($creditNoteId, $fileName, $includeOnline, $idempotencyKey));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateCreditNoteAttachmentByFileName(
        string $creditNoteId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateCreditNoteAttachmentByFileName($creditNoteId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     */
    public function getCreditNoteAsPdf(string $creditNoteId): Response
    {
        return $this->connector->send(new GetCreditNoteAsPdf($creditNoteId));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createCreditNoteAllocation(
        string $creditNoteId,
        ?bool $summarizeErrors = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateCreditNoteAllocation($creditNoteId, $summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  string  $allocationId  Unique identifier for Allocation object
     */
    public function deleteCreditNoteAllocations(string $creditNoteId, string $allocationId): Response
    {
        return $this->connector->send(new DeleteCreditNoteAllocations($creditNoteId, $allocationId));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     */
    public function getCreditNoteHistory(string $creditNoteId): Response
    {
        return $this->connector->send(new GetCreditNoteHistory($creditNoteId));
    }

    /**
     * @param  string  $creditNoteId  Unique identifier for a Credit Note
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createCreditNoteHistory(string $creditNoteId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateCreditNoteHistory($creditNoteId, $idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     */
    public function getCurrencies(?string $where = null, ?string $order = null): Response
    {
        return $this->connector->send(new GetCurrencies($where, $order));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createCurrency(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateCurrency($idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getExpenseClaims(
        ?string $where = null,
        ?string $order = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetExpenseClaims($where, $order, $ifModifiedSince));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createExpenseClaims(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateExpenseClaims($idempotencyKey));
    }

    /**
     * @param  string  $expenseClaimId  Unique identifier for a ExpenseClaim
     */
    public function getExpenseClaim(string $expenseClaimId): Response
    {
        return $this->connector->send(new GetExpenseClaim($expenseClaimId));
    }

    /**
     * @param  string  $expenseClaimId  Unique identifier for a ExpenseClaim
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateExpenseClaim(string $expenseClaimId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateExpenseClaim($expenseClaimId, $idempotencyKey));
    }

    /**
     * @param  string  $expenseClaimId  Unique identifier for a ExpenseClaim
     */
    public function getExpenseClaimHistory(string $expenseClaimId): Response
    {
        return $this->connector->send(new GetExpenseClaimHistory($expenseClaimId));
    }

    /**
     * @param  string  $expenseClaimId  Unique identifier for a ExpenseClaim
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createExpenseClaimHistory(string $expenseClaimId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateExpenseClaimHistory($expenseClaimId, $idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  array  $ids  Filter by a comma-separated list of InvoicesIDs.
     * @param  array  $invoiceNumbers  Filter by a comma-separated list of InvoiceNumbers.
     * @param  array  $contactIds  Filter by a comma-separated list of ContactIDs.
     * @param  array  $statuses  Filter by a comma-separated list Statuses. For faster response times we recommend using these explicit parameters instead of passing OR conditions into the Where filter.
     * @param  int  $page  e.g. page=1 – Up to 100 invoices will be returned in a single API call with line items shown for each invoice
     * @param  bool  $includeArchived  e.g. includeArchived=true - Invoices with a status of ARCHIVED will be included in the response
     * @param  bool  $createdByMyApp  When set to true you'll only retrieve Invoices created by your app
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  bool  $summaryOnly  Use summaryOnly=true in GET Contacts and Invoices endpoint to retrieve a smaller version of the response object. This returns only lightweight fields, excluding computation-heavy fields from the response, making the API calls quick and efficient.
     * @param  int  $pageSize  Number of records to retrieve per page
     * @param  string  $searchTerm  Search parameter that performs a case-insensitive text search across the fields e.g. InvoiceNumber, Reference.
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getInvoices(
        ?string $where = null,
        ?string $order = null,
        ?array $ids = null,
        ?array $invoiceNumbers = null,
        ?array $contactIds = null,
        ?array $statuses = null,
        ?int $page = null,
        ?bool $includeArchived = null,
        ?bool $createdByMyApp = null,
        ?int $unitdp = null,
        ?bool $summaryOnly = null,
        ?int $pageSize = null,
        ?string $searchTerm = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetInvoices($where, $order, $ids, $invoiceNumbers, $contactIds, $statuses, $page, $includeArchived, $createdByMyApp, $unitdp, $summaryOnly, $pageSize, $searchTerm, $ifModifiedSince));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createInvoices(
        ?bool $summarizeErrors = null,
        ?int $unitdp = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateInvoices($summarizeErrors, $unitdp, $idempotencyKey));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateOrCreateInvoices(
        ?bool $summarizeErrors = null,
        ?int $unitdp = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateOrCreateInvoices($summarizeErrors, $unitdp, $idempotencyKey));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     */
    public function getInvoice(string $invoiceId, ?int $unitdp = null): Response
    {
        return $this->connector->send(new GetInvoice($invoiceId, $unitdp));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateInvoice(string $invoiceId, ?int $unitdp = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateInvoice($invoiceId, $unitdp, $idempotencyKey));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     */
    public function getInvoiceAsPdf(string $invoiceId): Response
    {
        return $this->connector->send(new GetInvoiceAsPdf($invoiceId));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     */
    public function getInvoiceAttachments(string $invoiceId): Response
    {
        return $this->connector->send(new GetInvoiceAttachments($invoiceId));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getInvoiceAttachmentById(string $invoiceId, string $attachmentId, string $contentType): Response
    {
        return $this->connector->send(new GetInvoiceAttachmentById($invoiceId, $attachmentId, $contentType));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getInvoiceAttachmentByFileName(string $invoiceId, string $fileName, string $contentType): Response
    {
        return $this->connector->send(new GetInvoiceAttachmentByFileName($invoiceId, $fileName, $contentType));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     * @param  string  $fileName  Name of the attachment
     * @param  bool  $includeOnline  Allows an attachment to be seen by the end customer within their online invoice
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createInvoiceAttachmentByFileName(
        string $invoiceId,
        string $fileName,
        ?bool $includeOnline = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateInvoiceAttachmentByFileName($invoiceId, $fileName, $includeOnline, $idempotencyKey));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateInvoiceAttachmentByFileName(
        string $invoiceId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateInvoiceAttachmentByFileName($invoiceId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     */
    public function getOnlineInvoice(string $invoiceId): Response
    {
        return $this->connector->send(new GetOnlineInvoice($invoiceId));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function emailInvoice(string $invoiceId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new EmailInvoice($invoiceId, $idempotencyKey));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     */
    public function getInvoiceHistory(string $invoiceId): Response
    {
        return $this->connector->send(new GetInvoiceHistory($invoiceId));
    }

    /**
     * @param  string  $invoiceId  Unique identifier for an Invoice
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createInvoiceHistory(string $invoiceId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateInvoiceHistory($invoiceId, $idempotencyKey));
    }

    public function getInvoiceReminders(): Response
    {
        return $this->connector->send(new GetInvoiceReminders);
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getItems(
        ?string $where = null,
        ?string $order = null,
        ?int $unitdp = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetItems($where, $order, $unitdp, $ifModifiedSince));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createItems(
        ?bool $summarizeErrors = null,
        ?int $unitdp = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateItems($summarizeErrors, $unitdp, $idempotencyKey));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateOrCreateItems(
        ?bool $summarizeErrors = null,
        ?int $unitdp = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateOrCreateItems($summarizeErrors, $unitdp, $idempotencyKey));
    }

    /**
     * @param  string  $itemId  Unique identifier for an Item
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     */
    public function getItem(string $itemId, ?int $unitdp = null): Response
    {
        return $this->connector->send(new GetItem($itemId, $unitdp));
    }

    /**
     * @param  string  $itemId  Unique identifier for an Item
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateItem(string $itemId, ?int $unitdp = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateItem($itemId, $unitdp, $idempotencyKey));
    }

    /**
     * @param  string  $itemId  Unique identifier for an Item
     */
    public function deleteItem(string $itemId): Response
    {
        return $this->connector->send(new DeleteItem($itemId));
    }

    /**
     * @param  string  $itemId  Unique identifier for an Item
     */
    public function getItemHistory(string $itemId): Response
    {
        return $this->connector->send(new GetItemHistory($itemId));
    }

    /**
     * @param  string  $itemId  Unique identifier for an Item
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createItemHistory(string $itemId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateItemHistory($itemId, $idempotencyKey));
    }

    /**
     * @param  int  $offset  Offset by a specified journal number. e.g. journals with a JournalNumber greater than the offset will be returned
     * @param  bool  $paymentsOnly  Filter to retrieve journals on a cash basis. Journals are returned on an accrual basis by default.
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getJournals(
        ?int $offset = null,
        ?bool $paymentsOnly = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetJournals($offset, $paymentsOnly, $ifModifiedSince));
    }

    /**
     * @param  string  $journalId  Unique identifier for a Journal
     */
    public function getJournal(string $journalId): Response
    {
        return $this->connector->send(new GetJournal($journalId));
    }

    /**
     * @param  int  $journalNumber  Number of a Journal
     */
    public function getJournalByNumber(int $journalNumber): Response
    {
        return $this->connector->send(new GetJournalByNumber($journalNumber));
    }

    /**
     * @param  int  $page  Up to 100 linked transactions will be returned in a single API call. Use the page parameter to specify the page to be returned e.g. page=1.
     * @param  string  $linkedTransactionId  The Xero identifier for an Linked Transaction
     * @param  string  $sourceTransactionId  Filter by the SourceTransactionID. Get the linked transactions created from a particular ACCPAY invoice
     * @param  string  $contactId  Filter by the ContactID. Get all the linked transactions that have been assigned to a particular customer.
     * @param  string  $status  Filter by the combination of ContactID and Status. Get  the linked transactions associated to a  customer and with a status
     * @param  string  $targetTransactionId  Filter by the TargetTransactionID. Get all the linked transactions allocated to a particular ACCREC invoice
     */
    public function getLinkedTransactions(
        ?int $page = null,
        ?string $linkedTransactionId = null,
        ?string $sourceTransactionId = null,
        ?string $contactId = null,
        ?string $status = null,
        ?string $targetTransactionId = null,
    ): Response {
        return $this->connector->send(new GetLinkedTransactions($page, $linkedTransactionId, $sourceTransactionId, $contactId, $status, $targetTransactionId));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createLinkedTransaction(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateLinkedTransaction($idempotencyKey));
    }

    /**
     * @param  string  $linkedTransactionId  Unique identifier for a LinkedTransaction
     */
    public function getLinkedTransaction(string $linkedTransactionId): Response
    {
        return $this->connector->send(new GetLinkedTransaction($linkedTransactionId));
    }

    /**
     * @param  string  $linkedTransactionId  Unique identifier for a LinkedTransaction
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateLinkedTransaction(string $linkedTransactionId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateLinkedTransaction($linkedTransactionId, $idempotencyKey));
    }

    /**
     * @param  string  $linkedTransactionId  Unique identifier for a LinkedTransaction
     */
    public function deleteLinkedTransaction(string $linkedTransactionId): Response
    {
        return $this->connector->send(new DeleteLinkedTransaction($linkedTransactionId));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  int  $page  e.g. page=1 – Up to 100 manual journals will be returned in a single API call with line items shown for each overpayment
     * @param  int  $pageSize  Number of records to retrieve per page
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getManualJournals(
        ?string $where = null,
        ?string $order = null,
        ?int $page = null,
        ?int $pageSize = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetManualJournals($where, $order, $page, $pageSize, $ifModifiedSince));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createManualJournals(?bool $summarizeErrors = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateManualJournals($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateOrCreateManualJournals(?bool $summarizeErrors = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateOrCreateManualJournals($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  string  $manualJournalId  Unique identifier for a ManualJournal
     */
    public function getManualJournal(string $manualJournalId): Response
    {
        return $this->connector->send(new GetManualJournal($manualJournalId));
    }

    /**
     * @param  string  $manualJournalId  Unique identifier for a ManualJournal
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateManualJournal(string $manualJournalId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateManualJournal($manualJournalId, $idempotencyKey));
    }

    /**
     * @param  string  $manualJournalId  Unique identifier for a ManualJournal
     */
    public function getManualJournalAttachments(string $manualJournalId): Response
    {
        return $this->connector->send(new GetManualJournalAttachments($manualJournalId));
    }

    /**
     * @param  string  $manualJournalId  Unique identifier for a ManualJournal
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getManualJournalAttachmentById(
        string $manualJournalId,
        string $attachmentId,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetManualJournalAttachmentById($manualJournalId, $attachmentId, $contentType));
    }

    /**
     * @param  string  $manualJournalId  Unique identifier for a ManualJournal
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getManualJournalAttachmentByFileName(
        string $manualJournalId,
        string $fileName,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetManualJournalAttachmentByFileName($manualJournalId, $fileName, $contentType));
    }

    /**
     * @param  string  $manualJournalId  Unique identifier for a ManualJournal
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createManualJournalAttachmentByFileName(
        string $manualJournalId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateManualJournalAttachmentByFileName($manualJournalId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $manualJournalId  Unique identifier for a ManualJournal
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateManualJournalAttachmentByFileName(
        string $manualJournalId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateManualJournalAttachmentByFileName($manualJournalId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $manualJournalId  Unique identifier for a ManualJournal
     */
    public function getManualJournalsHistory(string $manualJournalId): Response
    {
        return $this->connector->send(new GetManualJournalsHistory($manualJournalId));
    }

    /**
     * @param  string  $manualJournalId  Unique identifier for a ManualJournal
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createManualJournalHistoryRecord(string $manualJournalId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateManualJournalHistoryRecord($manualJournalId, $idempotencyKey));
    }

    public function getOrganisations(): Response
    {
        return $this->connector->send(new GetOrganisations);
    }

    public function getOrganisationActions(): Response
    {
        return $this->connector->send(new GetOrganisationActions);
    }

    /**
     * @param  string  $organisationId  The unique Xero identifier for an organisation
     */
    public function getOrganisationCissettings(string $organisationId): Response
    {
        return $this->connector->send(new GetOrganisationCissettings($organisationId));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  int  $page  e.g. page=1 – Up to 100 overpayments will be returned in a single API call with line items shown for each overpayment
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  int  $pageSize  Number of records to retrieve per page
     * @param  array  $references  Filter by a comma-separated list of References
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getOverpayments(
        ?string $where = null,
        ?string $order = null,
        ?int $page = null,
        ?int $unitdp = null,
        ?int $pageSize = null,
        ?array $references = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetOverpayments($where, $order, $page, $unitdp, $pageSize, $references, $ifModifiedSince));
    }

    /**
     * @param  string  $overpaymentId  Unique identifier for a Overpayment
     */
    public function getOverpayment(string $overpaymentId): Response
    {
        return $this->connector->send(new GetOverpayment($overpaymentId));
    }

    /**
     * @param  string  $overpaymentId  Unique identifier for a Overpayment
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createOverpaymentAllocations(
        string $overpaymentId,
        ?bool $summarizeErrors = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateOverpaymentAllocations($overpaymentId, $summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  string  $overpaymentId  Unique identifier for a Overpayment
     * @param  string  $allocationId  Unique identifier for Allocation object
     */
    public function deleteOverpaymentAllocations(string $overpaymentId, string $allocationId): Response
    {
        return $this->connector->send(new DeleteOverpaymentAllocations($overpaymentId, $allocationId));
    }

    /**
     * @param  string  $overpaymentId  Unique identifier for a Overpayment
     */
    public function getOverpaymentHistory(string $overpaymentId): Response
    {
        return $this->connector->send(new GetOverpaymentHistory($overpaymentId));
    }

    /**
     * @param  string  $overpaymentId  Unique identifier for a Overpayment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createOverpaymentHistory(string $overpaymentId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateOverpaymentHistory($overpaymentId, $idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  int  $page  Up to 100 payments will be returned in a single API call
     * @param  int  $pageSize  Number of records to retrieve per page
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getPayments(
        ?string $where = null,
        ?string $order = null,
        ?int $page = null,
        ?int $pageSize = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetPayments($where, $order, $page, $pageSize, $ifModifiedSince));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createPayments(?bool $summarizeErrors = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreatePayments($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createPayment(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreatePayment($idempotencyKey));
    }

    /**
     * @param  string  $paymentId  Unique identifier for a Payment
     */
    public function getPayment(string $paymentId): Response
    {
        return $this->connector->send(new GetPayment($paymentId));
    }

    /**
     * @param  string  $paymentId  Unique identifier for a Payment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function deletePayment(string $paymentId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new DeletePayment($paymentId, $idempotencyKey));
    }

    /**
     * @param  string  $paymentId  Unique identifier for a Payment
     */
    public function getPaymentHistory(string $paymentId): Response
    {
        return $this->connector->send(new GetPaymentHistory($paymentId));
    }

    /**
     * @param  string  $paymentId  Unique identifier for a Payment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createPaymentHistory(string $paymentId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreatePaymentHistory($paymentId, $idempotencyKey));
    }

    public function getPaymentServices(): Response
    {
        return $this->connector->send(new GetPaymentServices);
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createPaymentService(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreatePaymentService($idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  int  $page  e.g. page=1 – Up to 100 prepayments will be returned in a single API call with line items shown for each overpayment
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  int  $pageSize  Number of records to retrieve per page
     * @param  array  $invoiceNumbers  Filter by a comma-separated list of InvoiceNumbers
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getPrepayments(
        ?string $where = null,
        ?string $order = null,
        ?int $page = null,
        ?int $unitdp = null,
        ?int $pageSize = null,
        ?array $invoiceNumbers = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetPrepayments($where, $order, $page, $unitdp, $pageSize, $invoiceNumbers, $ifModifiedSince));
    }

    /**
     * @param  string  $prepaymentId  Unique identifier for a PrePayment
     */
    public function getPrepayment(string $prepaymentId): Response
    {
        return $this->connector->send(new GetPrepayment($prepaymentId));
    }

    /**
     * @param  string  $prepaymentId  Unique identifier for a PrePayment
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createPrepaymentAllocations(
        string $prepaymentId,
        ?bool $summarizeErrors = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreatePrepaymentAllocations($prepaymentId, $summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  string  $prepaymentId  Unique identifier for a PrePayment
     * @param  string  $allocationId  Unique identifier for Allocation object
     */
    public function deletePrepaymentAllocations(string $prepaymentId, string $allocationId): Response
    {
        return $this->connector->send(new DeletePrepaymentAllocations($prepaymentId, $allocationId));
    }

    /**
     * @param  string  $prepaymentId  Unique identifier for a PrePayment
     */
    public function getPrepaymentHistory(string $prepaymentId): Response
    {
        return $this->connector->send(new GetPrepaymentHistory($prepaymentId));
    }

    /**
     * @param  string  $prepaymentId  Unique identifier for a PrePayment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createPrepaymentHistory(string $prepaymentId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreatePrepaymentHistory($prepaymentId, $idempotencyKey));
    }

    /**
     * @param  string  $status  Filter by purchase order status
     * @param  string  $dateFrom  Filter by purchase order date (e.g. GET https://.../PurchaseOrders?DateFrom=2015-12-01&DateTo=2015-12-31
     * @param  string  $dateTo  Filter by purchase order date (e.g. GET https://.../PurchaseOrders?DateFrom=2015-12-01&DateTo=2015-12-31
     * @param  string  $order  Order by an any element
     * @param  int  $page  To specify a page, append the page parameter to the URL e.g. ?page=1. If there are 100 records in the response you will need to check if there is any more data by fetching the next page e.g ?page=2 and continuing this process until no more results are returned.
     * @param  int  $pageSize  Number of records to retrieve per page
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getPurchaseOrders(
        ?string $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $order = null,
        ?int $page = null,
        ?int $pageSize = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetPurchaseOrders($status, $dateFrom, $dateTo, $order, $page, $pageSize, $ifModifiedSince));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createPurchaseOrders(?bool $summarizeErrors = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreatePurchaseOrders($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateOrCreatePurchaseOrders(?bool $summarizeErrors = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateOrCreatePurchaseOrders($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  string  $purchaseOrderId  Unique identifier for an Purchase Order
     */
    public function getPurchaseOrderAsPdf(string $purchaseOrderId): Response
    {
        return $this->connector->send(new GetPurchaseOrderAsPdf($purchaseOrderId));
    }

    /**
     * @param  string  $purchaseOrderId  Unique identifier for an Purchase Order
     */
    public function getPurchaseOrder(string $purchaseOrderId): Response
    {
        return $this->connector->send(new GetPurchaseOrder($purchaseOrderId));
    }

    /**
     * @param  string  $purchaseOrderId  Unique identifier for an Purchase Order
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updatePurchaseOrder(string $purchaseOrderId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdatePurchaseOrder($purchaseOrderId, $idempotencyKey));
    }

    /**
     * @param  string  $purchaseOrderNumber  Unique identifier for a PurchaseOrder
     */
    public function getPurchaseOrderByNumber(string $purchaseOrderNumber): Response
    {
        return $this->connector->send(new GetPurchaseOrderByNumber($purchaseOrderNumber));
    }

    /**
     * @param  string  $purchaseOrderId  Unique identifier for an Purchase Order
     */
    public function getPurchaseOrderHistory(string $purchaseOrderId): Response
    {
        return $this->connector->send(new GetPurchaseOrderHistory($purchaseOrderId));
    }

    /**
     * @param  string  $purchaseOrderId  Unique identifier for an Purchase Order
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createPurchaseOrderHistory(string $purchaseOrderId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreatePurchaseOrderHistory($purchaseOrderId, $idempotencyKey));
    }

    /**
     * @param  string  $purchaseOrderId  Unique identifier for an Purchase Order
     */
    public function getPurchaseOrderAttachments(string $purchaseOrderId): Response
    {
        return $this->connector->send(new GetPurchaseOrderAttachments($purchaseOrderId));
    }

    /**
     * @param  string  $purchaseOrderId  Unique identifier for an Purchase Order
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getPurchaseOrderAttachmentById(
        string $purchaseOrderId,
        string $attachmentId,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetPurchaseOrderAttachmentById($purchaseOrderId, $attachmentId, $contentType));
    }

    /**
     * @param  string  $purchaseOrderId  Unique identifier for an Purchase Order
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getPurchaseOrderAttachmentByFileName(
        string $purchaseOrderId,
        string $fileName,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetPurchaseOrderAttachmentByFileName($purchaseOrderId, $fileName, $contentType));
    }

    /**
     * @param  string  $purchaseOrderId  Unique identifier for an Purchase Order
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createPurchaseOrderAttachmentByFileName(
        string $purchaseOrderId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreatePurchaseOrderAttachmentByFileName($purchaseOrderId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $purchaseOrderId  Unique identifier for an Purchase Order
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updatePurchaseOrderAttachmentByFileName(
        string $purchaseOrderId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdatePurchaseOrderAttachmentByFileName($purchaseOrderId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $dateFrom  Filter for quotes after a particular date
     * @param  string  $dateTo  Filter for quotes before a particular date
     * @param  string  $expiryDateFrom  Filter for quotes expiring after a particular date
     * @param  string  $expiryDateTo  Filter for quotes before a particular date
     * @param  string  $contactId  Filter for quotes belonging to a particular contact
     * @param  string  $status  Filter for quotes of a particular Status
     * @param  int  $page  e.g. page=1 – Up to 100 Quotes will be returned in a single API call with line items shown for each quote
     * @param  string  $order  Order by an any element
     * @param  string  $quoteNumber  Filter by quote number (e.g. GET https://.../Quotes?QuoteNumber=QU-0001)
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getQuotes(
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $expiryDateFrom = null,
        ?string $expiryDateTo = null,
        ?string $contactId = null,
        ?string $status = null,
        ?int $page = null,
        ?string $order = null,
        ?string $quoteNumber = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetQuotes($dateFrom, $dateTo, $expiryDateFrom, $expiryDateTo, $contactId, $status, $page, $order, $quoteNumber, $ifModifiedSince));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createQuotes(?bool $summarizeErrors = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateQuotes($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateOrCreateQuotes(?bool $summarizeErrors = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateOrCreateQuotes($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  string  $quoteId  Unique identifier for an Quote
     */
    public function getQuote(string $quoteId): Response
    {
        return $this->connector->send(new GetQuote($quoteId));
    }

    /**
     * @param  string  $quoteId  Unique identifier for an Quote
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateQuote(string $quoteId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateQuote($quoteId, $idempotencyKey));
    }

    /**
     * @param  string  $quoteId  Unique identifier for an Quote
     */
    public function getQuoteHistory(string $quoteId): Response
    {
        return $this->connector->send(new GetQuoteHistory($quoteId));
    }

    /**
     * @param  string  $quoteId  Unique identifier for an Quote
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createQuoteHistory(string $quoteId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateQuoteHistory($quoteId, $idempotencyKey));
    }

    /**
     * @param  string  $quoteId  Unique identifier for an Quote
     */
    public function getQuoteAsPdf(string $quoteId): Response
    {
        return $this->connector->send(new GetQuoteAsPdf($quoteId));
    }

    /**
     * @param  string  $quoteId  Unique identifier for an Quote
     */
    public function getQuoteAttachments(string $quoteId): Response
    {
        return $this->connector->send(new GetQuoteAttachments($quoteId));
    }

    /**
     * @param  string  $quoteId  Unique identifier for an Quote
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getQuoteAttachmentById(string $quoteId, string $attachmentId, string $contentType): Response
    {
        return $this->connector->send(new GetQuoteAttachmentById($quoteId, $attachmentId, $contentType));
    }

    /**
     * @param  string  $quoteId  Unique identifier for an Quote
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getQuoteAttachmentByFileName(string $quoteId, string $fileName, string $contentType): Response
    {
        return $this->connector->send(new GetQuoteAttachmentByFileName($quoteId, $fileName, $contentType));
    }

    /**
     * @param  string  $quoteId  Unique identifier for an Quote
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createQuoteAttachmentByFileName(
        string $quoteId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateQuoteAttachmentByFileName($quoteId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $quoteId  Unique identifier for an Quote
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateQuoteAttachmentByFileName(
        string $quoteId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateQuoteAttachmentByFileName($quoteId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getReceipts(
        ?string $where = null,
        ?string $order = null,
        ?int $unitdp = null,
        ?string $ifModifiedSince = null,
    ): Response {
        return $this->connector->send(new GetReceipts($where, $order, $unitdp, $ifModifiedSince));
    }

    /**
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createReceipt(?int $unitdp = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateReceipt($unitdp, $idempotencyKey));
    }

    /**
     * @param  string  $receiptId  Unique identifier for a Receipt
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     */
    public function getReceipt(string $receiptId, ?int $unitdp = null): Response
    {
        return $this->connector->send(new GetReceipt($receiptId, $unitdp));
    }

    /**
     * @param  string  $receiptId  Unique identifier for a Receipt
     * @param  int  $unitdp  e.g. unitdp=4 – (Unit Decimal Places) You can opt in to use four decimal places for unit amounts
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateReceipt(string $receiptId, ?int $unitdp = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateReceipt($receiptId, $unitdp, $idempotencyKey));
    }

    /**
     * @param  string  $receiptId  Unique identifier for a Receipt
     */
    public function getReceiptAttachments(string $receiptId): Response
    {
        return $this->connector->send(new GetReceiptAttachments($receiptId));
    }

    /**
     * @param  string  $receiptId  Unique identifier for a Receipt
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getReceiptAttachmentById(string $receiptId, string $attachmentId, string $contentType): Response
    {
        return $this->connector->send(new GetReceiptAttachmentById($receiptId, $attachmentId, $contentType));
    }

    /**
     * @param  string  $receiptId  Unique identifier for a Receipt
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getReceiptAttachmentByFileName(string $receiptId, string $fileName, string $contentType): Response
    {
        return $this->connector->send(new GetReceiptAttachmentByFileName($receiptId, $fileName, $contentType));
    }

    /**
     * @param  string  $receiptId  Unique identifier for a Receipt
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createReceiptAttachmentByFileName(
        string $receiptId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateReceiptAttachmentByFileName($receiptId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $receiptId  Unique identifier for a Receipt
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateReceiptAttachmentByFileName(
        string $receiptId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateReceiptAttachmentByFileName($receiptId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $receiptId  Unique identifier for a Receipt
     */
    public function getReceiptHistory(string $receiptId): Response
    {
        return $this->connector->send(new GetReceiptHistory($receiptId));
    }

    /**
     * @param  string  $receiptId  Unique identifier for a Receipt
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createReceiptHistory(string $receiptId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateReceiptHistory($receiptId, $idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     */
    public function getRepeatingInvoices(?string $where = null, ?string $order = null): Response
    {
        return $this->connector->send(new GetRepeatingInvoices($where, $order));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createRepeatingInvoices(?bool $summarizeErrors = null, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateRepeatingInvoices($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  bool  $summarizeErrors  If false return 200 OK and mix of successfully created objects and any with validation errors
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateOrCreateRepeatingInvoices(
        ?bool $summarizeErrors = null,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateOrCreateRepeatingInvoices($summarizeErrors, $idempotencyKey));
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     */
    public function getRepeatingInvoice(string $repeatingInvoiceId): Response
    {
        return $this->connector->send(new GetRepeatingInvoice($repeatingInvoiceId));
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateRepeatingInvoice(string $repeatingInvoiceId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateRepeatingInvoice($repeatingInvoiceId, $idempotencyKey));
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     */
    public function getRepeatingInvoiceAttachments(string $repeatingInvoiceId): Response
    {
        return $this->connector->send(new GetRepeatingInvoiceAttachments($repeatingInvoiceId));
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     * @param  string  $attachmentId  Unique identifier for Attachment object
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getRepeatingInvoiceAttachmentById(
        string $repeatingInvoiceId,
        string $attachmentId,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetRepeatingInvoiceAttachmentById($repeatingInvoiceId, $attachmentId, $contentType));
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     * @param  string  $fileName  Name of the attachment
     * @param  string  $contentType  The mime type of the attachment file you are retrieving i.e image/jpg, application/pdf
     */
    public function getRepeatingInvoiceAttachmentByFileName(
        string $repeatingInvoiceId,
        string $fileName,
        string $contentType,
    ): Response {
        return $this->connector->send(new GetRepeatingInvoiceAttachmentByFileName($repeatingInvoiceId, $fileName, $contentType));
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createRepeatingInvoiceAttachmentByFileName(
        string $repeatingInvoiceId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new CreateRepeatingInvoiceAttachmentByFileName($repeatingInvoiceId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     * @param  string  $fileName  Name of the attachment
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateRepeatingInvoiceAttachmentByFileName(
        string $repeatingInvoiceId,
        string $fileName,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateRepeatingInvoiceAttachmentByFileName($repeatingInvoiceId, $fileName, $idempotencyKey));
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     */
    public function getRepeatingInvoiceHistory(string $repeatingInvoiceId): Response
    {
        return $this->connector->send(new GetRepeatingInvoiceHistory($repeatingInvoiceId));
    }

    /**
     * @param  string  $repeatingInvoiceId  Unique identifier for a Repeating Invoice
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createRepeatingInvoiceHistory(string $repeatingInvoiceId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateRepeatingInvoiceHistory($repeatingInvoiceId, $idempotencyKey));
    }

    /**
     * @param  string  $reportYear  The year of the 1099 report
     */
    public function getReportTenNinetyNine(?string $reportYear = null): Response
    {
        return $this->connector->send(new GetReportTenNinetyNine($reportYear));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     * @param  string  $date  The date of the Aged Payables By Contact report
     * @param  string  $fromDate  filter by the from date of the report e.g. 2021-02-01
     * @param  string  $toDate  filter by the to date of the report e.g. 2021-02-28
     */
    public function getReportAgedPayablesByContact(
        string $contactId,
        ?string $date = null,
        ?string $fromDate = null,
        ?string $toDate = null,
    ): Response {
        return $this->connector->send(new GetReportAgedPayablesByContact($contactId, $date, $fromDate, $toDate));
    }

    /**
     * @param  string  $contactId  Unique identifier for a Contact
     * @param  string  $date  The date of the Aged Receivables By Contact report
     * @param  string  $fromDate  filter by the from date of the report e.g. 2021-02-01
     * @param  string  $toDate  filter by the to date of the report e.g. 2021-02-28
     */
    public function getReportAgedReceivablesByContact(
        string $contactId,
        ?string $date = null,
        ?string $fromDate = null,
        ?string $toDate = null,
    ): Response {
        return $this->connector->send(new GetReportAgedReceivablesByContact($contactId, $date, $fromDate, $toDate));
    }

    /**
     * @param  string  $date  The date of the Balance Sheet report
     * @param  int  $periods  The number of periods for the Balance Sheet report
     * @param  string  $timeframe  The period size to compare to (MONTH, QUARTER, YEAR)
     * @param  string  $trackingOptionId1  The tracking option 1 for the Balance Sheet report
     * @param  string  $trackingOptionId2  The tracking option 2 for the Balance Sheet report
     * @param  bool  $standardLayout  The standard layout boolean for the Balance Sheet report
     * @param  bool  $paymentsOnly  return a cash basis for the Balance Sheet report
     */
    public function getReportBalanceSheet(
        ?string $date = null,
        ?int $periods = null,
        ?string $timeframe = null,
        ?string $trackingOptionId1 = null,
        ?string $trackingOptionId2 = null,
        ?bool $standardLayout = null,
        ?bool $paymentsOnly = null,
    ): Response {
        return $this->connector->send(new GetReportBalanceSheet($date, $periods, $timeframe, $trackingOptionId1, $trackingOptionId2, $standardLayout, $paymentsOnly));
    }

    /**
     * @param  string  $fromDate  filter by the from date of the report e.g. 2021-02-01
     * @param  string  $toDate  filter by the to date of the report e.g. 2021-02-28
     */
    public function getReportBankSummary(?string $fromDate = null, ?string $toDate = null): Response
    {
        return $this->connector->send(new GetReportBankSummary($fromDate, $toDate));
    }

    /**
     * @param  string  $reportId  Unique identifier for a Report
     */
    public function getReportFromId(string $reportId): Response
    {
        return $this->connector->send(new GetReportFromId($reportId));
    }

    /**
     * @param  string  $date  The date for the Bank Summary report e.g. 2018-03-31
     * @param  int  $periods  The number of periods to compare (integer between 1 and 12)
     * @param  int  $timeframe  The period size to compare to (1=month, 3=quarter, 12=year)
     */
    public function getReportBudgetSummary(?string $date = null, ?int $periods = null, ?int $timeframe = null): Response
    {
        return $this->connector->send(new GetReportBudgetSummary($date, $periods, $timeframe));
    }

    /**
     * @param  string  $date  The date for the Bank Summary report e.g. 2018-03-31
     */
    public function getReportExecutiveSummary(?string $date = null): Response
    {
        return $this->connector->send(new GetReportExecutiveSummary($date));
    }

    public function getReportsList(): Response
    {
        return $this->connector->send(new GetReportsList);
    }

    /**
     * @param  string  $fromDate  filter by the from date of the report e.g. 2021-02-01
     * @param  string  $toDate  filter by the to date of the report e.g. 2021-02-28
     * @param  int  $periods  The number of periods to compare (integer between 1 and 12)
     * @param  string  $timeframe  The period size to compare to (MONTH, QUARTER, YEAR)
     * @param  string  $trackingCategoryId  The trackingCategory 1 for the ProfitAndLoss report
     * @param  string  $trackingCategoryId2  The trackingCategory 2 for the ProfitAndLoss report
     * @param  string  $trackingOptionId  The tracking option 1 for the ProfitAndLoss report
     * @param  string  $trackingOptionId2  The tracking option 2 for the ProfitAndLoss report
     * @param  bool  $standardLayout  Return the standard layout for the ProfitAndLoss report
     * @param  bool  $paymentsOnly  Return cash only basis for the ProfitAndLoss report
     */
    public function getReportProfitAndLoss(
        ?string $fromDate = null,
        ?string $toDate = null,
        ?int $periods = null,
        ?string $timeframe = null,
        ?string $trackingCategoryId = null,
        ?string $trackingCategoryId2 = null,
        ?string $trackingOptionId = null,
        ?string $trackingOptionId2 = null,
        ?bool $standardLayout = null,
        ?bool $paymentsOnly = null,
    ): Response {
        return $this->connector->send(new GetReportProfitAndLoss($fromDate, $toDate, $periods, $timeframe, $trackingCategoryId, $trackingCategoryId2, $trackingOptionId, $trackingOptionId2, $standardLayout, $paymentsOnly));
    }

    /**
     * @param  string  $date  The date for the Trial Balance report e.g. 2018-03-31
     * @param  bool  $paymentsOnly  Return cash only basis for the Trial Balance report
     */
    public function getReportTrialBalance(?string $date = null, ?bool $paymentsOnly = null): Response
    {
        return $this->connector->send(new GetReportTrialBalance($date, $paymentsOnly));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function postSetup(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new PostSetup($idempotencyKey));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     */
    public function getTaxRates(?string $where = null, ?string $order = null): Response
    {
        return $this->connector->send(new GetTaxRates($where, $order));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createTaxRates(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateTaxRates($idempotencyKey));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateTaxRate(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateTaxRate($idempotencyKey));
    }

    /**
     * @param  string  $taxType  A valid TaxType code
     */
    public function getTaxRateByTaxType(string $taxType): Response
    {
        return $this->connector->send(new GetTaxRateByTaxType($taxType));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  bool  $includeArchived  e.g. includeArchived=true - Categories and options with a status of ARCHIVED will be included in the response
     */
    public function getTrackingCategories(
        ?string $where = null,
        ?string $order = null,
        ?bool $includeArchived = null,
    ): Response {
        return $this->connector->send(new GetTrackingCategories($where, $order, $includeArchived));
    }

    /**
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createTrackingCategory(?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateTrackingCategory($idempotencyKey));
    }

    /**
     * @param  string  $trackingCategoryId  Unique identifier for a TrackingCategory
     */
    public function getTrackingCategory(string $trackingCategoryId): Response
    {
        return $this->connector->send(new GetTrackingCategory($trackingCategoryId));
    }

    /**
     * @param  string  $trackingCategoryId  Unique identifier for a TrackingCategory
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateTrackingCategory(string $trackingCategoryId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new UpdateTrackingCategory($trackingCategoryId, $idempotencyKey));
    }

    /**
     * @param  string  $trackingCategoryId  Unique identifier for a TrackingCategory
     */
    public function deleteTrackingCategory(string $trackingCategoryId): Response
    {
        return $this->connector->send(new DeleteTrackingCategory($trackingCategoryId));
    }

    /**
     * @param  string  $trackingCategoryId  Unique identifier for a TrackingCategory
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function createTrackingOptions(string $trackingCategoryId, ?string $idempotencyKey = null): Response
    {
        return $this->connector->send(new CreateTrackingOptions($trackingCategoryId, $idempotencyKey));
    }

    /**
     * @param  string  $trackingCategoryId  Unique identifier for a TrackingCategory
     * @param  string  $trackingOptionId  Unique identifier for a Tracking Option
     * @param  string  $idempotencyKey  This allows you to safely retry requests without the risk of duplicate processing. 128 character max.
     */
    public function updateTrackingOptions(
        string $trackingCategoryId,
        string $trackingOptionId,
        ?string $idempotencyKey = null,
    ): Response {
        return $this->connector->send(new UpdateTrackingOptions($trackingCategoryId, $trackingOptionId, $idempotencyKey));
    }

    /**
     * @param  string  $trackingCategoryId  Unique identifier for a TrackingCategory
     * @param  string  $trackingOptionId  Unique identifier for a Tracking Option
     */
    public function deleteTrackingOptions(string $trackingCategoryId, string $trackingOptionId): Response
    {
        return $this->connector->send(new DeleteTrackingOptions($trackingCategoryId, $trackingOptionId));
    }

    /**
     * @param  string  $where  Filter by an any element
     * @param  string  $order  Order by an any element
     * @param  string  $ifModifiedSince  Only records created or modified since this timestamp will be returned
     */
    public function getUsers(?string $where = null, ?string $order = null, ?string $ifModifiedSince = null): Response
    {
        return $this->connector->send(new GetUsers($where, $order, $ifModifiedSince));
    }

    /**
     * @param  string  $userId  Unique identifier for a User
     */
    public function getUser(string $userId): Response
    {
        return $this->connector->send(new GetUser($userId));
    }
}

<?php

namespace Dcodegroup\XeroIntegration;

use Dcodegroup\XeroIntegration\Enums\XeroRelationshipsEnum;
use Dcodegroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Dcodegroup\XeroIntegration\Facades\XeroIntegrationService;
use Dcodegroup\XeroIntegration\Models\XeroToken;
use Illuminate\Support\Str;
use Override;
use XeroPHP\Application;

/**
 * @method XeroIntegration address()
 * @method XeroIntegration contact()
 * @method XeroIntegration contactPerson()
 * @method XeroIntegration creditNote()
 * @method XeroIntegration invoice()
 * @method XeroIntegration item()
 * @method XeroIntegration overpayment()
 * @method XeroIntegration payment()
 * @method XeroIntegration phone()
 * @method XeroIntegration prepayment()
 * @method XeroIntegration quote()
 * @method XeroIntegration timesheet_au()
 */
class XeroApp extends Application
{
    public function __construct()
    {
        $tokenModel = $this->getTokenModel();
        $oauthToken = $this->getOauthToken($tokenModel);

        parent::__construct($oauthToken, $tokenModel->current_tenant_id);

        $this->config['webhook'] = [
            'signing_key' => config('xero-integration.webhooks.secret'),
        ];
    }

    public function __call(string $name, ?array $arguments)
    {
        $relationship = $this->findRelationship($name);

        if (empty($relationship)) {
            throw new XeroIntegrationException("Model '{$name}' not found in Xero integration.");
        }

        return new XeroIntegration($this, $this->load($relationship->getModelClass()));
    }

    public function __get(string $name)
    {
        $relationship = $this->findRelationship($name);

        if (empty($relationship)) {
            return null;
        }

        return $this->{$relationship->value}()->get();
    }

    public function getModelForRelationship(string $relationship): string
    {
        $enum = $this->findRelationship($relationship);

        if (empty($enum)) {
            throw new XeroIntegrationException("Relationship '{$relationship}' not found in Xero integration.");
        }

        return $enum->getModelClass();
    }

    protected function findRelationship(string $name): ?XeroRelationshipsEnum
    {
        $normalized = Str::camel(Str::singular($name));

        return XeroRelationshipsEnum::tryFrom($name) ?? XeroRelationshipsEnum::tryFrom($normalized);
    }

    /**
     * @param  string  $model
     * @return XeroQuery
     */
    #[Override]
    public function load($model)
    {
        $query = new XeroQuery($this);

        return $query->from($model);
    }

    protected function getTokenModel()
    {
        $tokenModel = XeroIntegrationService::getTokenModel();

        if (empty($tokenModel)) {
            throw new XeroIntegrationException('No Xero token found in the database. Please connect your Xero account.');
        }

        return $tokenModel;
    }

    protected function getOauthToken(?XeroToken $tokenModel = null)
    {
        $oauthToken = XeroIntegrationService::getToken($tokenModel);
        $tokenModel->refresh_token = $oauthToken->getRefreshToken();
        $tokenModel->access_token = $oauthToken->getToken();
        $tokenModel->expires = $oauthToken->getExpires();
        $tokenModel->save();

        if (empty($oauthToken)) {
            throw new XeroIntegrationException('Unable to retrieve a valid OAuth token for Xero integration.');
        }

        return $oauthToken;
    }
}

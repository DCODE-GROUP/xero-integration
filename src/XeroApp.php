<?php

namespace DcodeGroup\XeroIntegration;

use DcodeGroup\XeroIntegration\Exceptions\XeroIntegrationException;
use DcodeGroup\XeroIntegration\Facades\XeroIntegrationService;
use DcodeGroup\XeroIntegration\Models\XeroToken;
use Illuminate\Support\Collection;
use Override;
use XeroPHP\Application;

/**
 * @method XeroIntegration quotes()
 * @method XeroIntegration contacts()
 * @method XeroIntegration invoices()
 */
class XeroApp extends Application
{
    protected array $relationshipToModelMap = [
        'quotes' => 'XeroPHP\Models\Accounting\Quote',
        'contacts' => 'XeroPHP\Models\Accounting\Contact',
        'invoices' => 'XeroPHP\Models\Accounting\Invoice',
    ];

    public function __construct()
    {
        $tokenModel = $this->getTokenModel();
        $oauthToken = $this->getOauthToken($tokenModel);

        parent::__construct($oauthToken, $tokenModel->current_tenant_id);
    }

    public function __call(string $name, ?array $arguments)
    {
        $relationships = array_keys($this->relationshipToModelMap);

        if (! in_array($name, $relationships)) {
            throw new XeroIntegrationException("Model '{$name}' not found in Xero integration.");
        }

        $model = $this->relationshipToModelMap[$name];

        return new XeroIntegration($this, $this->load($model));
    }

    public function __get(string $name)
    {
        $relationships = array_keys($this->relationshipToModelMap);

        if (! in_array($name, $relationships)) {
            return null;
        }

        return $this->$name()->get();
    }

    public function getModelForRelationship(string $relationship): string
    {
        if (! array_key_exists($relationship, $this->relationshipToModelMap)) {
            throw new XeroIntegrationException("Relationship '{$relationship}' not found in Xero integration.");
        }

        return $this->relationshipToModelMap[$relationship];
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

    protected function getAvailableRelationships(): Collection
    {
        $reflection = new \ReflectionProperty(self::class, 'relationshipToModelMap');
        $relationshipToModelMap = $reflection->getDefaultValue();

        $relationships = array_keys($relationshipToModelMap);
        sort($relationships);

        return collect($relationships);
    }
}

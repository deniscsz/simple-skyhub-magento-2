<?php


namespace Resultate\Skyhub\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \SkyHub\Api;

class Data extends AbstractHelper
{
    /**
     * Extension's system configurations path
     */
    const XML_SKYHUB_ACTIVED = "skyhub/options/active";
    const XML_SKYHUB_API_EMAIL = "skyhub/options/email";
    const XML_SKYHUB_API_KEY = "skyhub/options/api_key";
    const XML_SKYHUB_ATTRIBUTE_SET_TO_SYNC = "skyhub/options/attribute_set_to_sync";
    const XML_SKYHUB_ATTRIBUTES_TO_SYNC = "skyhub/options/attributes_to_sync";
    
    protected $_skyhubApi = null;

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isEnabled()
    {
        return $this->getConfig(self::XML_SKYHUB_ACTIVED);
    }

    public function getApiKey()
    {
        return $this->getConfig(self::XML_SKYHUB_API_KEY);
    }

    public function getApiEmail()
    {
        return $this->getConfig(self::XML_SKYHUB_API_EMAIL);
    }

    public function getAttributeSetToSync()
    {
        return $this->getConfig(self::XML_SKYHUB_ATTRIBUTE_SET_TO_SYNC);
    }

    public function getAttributesToSync()
    {
        return $this->getConfig(self::XML_SKYHUB_ATTRIBUTES_TO_SYNC);
    }

    protected function _setSkyhubApi()
    {
        $email   = $this->getApiEmail();
        $apiKey  = $this->getApiKey();

        /** @var \SkyHub\Api $api */
        $api = new \SkyHub\Api($email, $apiKey);
        return $this->_skyhubApi = $api;
    }

    public function getSkyhubApi()
    {
        if ($this->_skyhubApi !== null) {
            return $this->_skyhubApi;
        } else {
            return $this->_setSkyhubApi();
        }
    }
}

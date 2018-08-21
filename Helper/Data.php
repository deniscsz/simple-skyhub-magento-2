<?php


namespace Resultate\Skyhub\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \SkyHub\Api;

class Data extends AbstractHelper
{
    /**
     * Extension's system configurations path
     */
    const XML_SKYHUB_ACTIVED                = "skyhub/options/active";
    const XML_SKYHUB_API_EMAIL              = "skyhub/options/email";
    const XML_SKYHUB_API_KEY                = "skyhub/options/api_key";
    const XML_SKYHUB_STATUS_PENDING         = "skyhub/options/status_pending";
    const XML_SKYHUB_STATUS_INVOICED        = "skyhub/options/status_invoiced";
    const XML_SKYHUB_STATUS_CANCELED        = "skyhub/options/status_canceled";
    const XML_SKYHUB_STATUS_REFUNDED        = "skyhub/options/status_refunded";
    const XML_SKYHUB_STATUS_SHIPMED         = "skyhub/options/status_shipmed";
    const XML_SKYHUB_STATUS_DELIVERED       = "skyhub/options/status_delivered";
    const XML_SKYHUB_ATTRIBUTE_SET_TO_SYNC  = "skyhub/options/attribute_set_to_sync";
    const XML_SKYHUB_ATTRIBUTES_TO_SYNC     = "skyhub/options/attributes_to_sync";
    
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

    public function getStatusPending()
    {
        return $this->getConfig(self::XML_SKYHUB_STATUS_PENDING);
    }

    public function getStatusInvoiced()
    {
        return $this->getConfig(self::XML_SKYHUB_STATUS_INVOICED);
    }

    public function getStatusCanceled()
    {
        return $this->getConfig(self::XML_SKYHUB_STATUS_CANCELED);
    }

    public function getStatusRefunded()
    {
        return $this->getConfig(self::XML_SKYHUB_STATUS_REFUNDED);
    }

    public function getStatusShipmed()
    {
        return $this->getConfig(self::XML_SKYHUB_STATUS_SHIPMED);
    }

    public function getStatusDelivered()
    {
        return $this->getConfig(self::XML_SKYHUB_STATUS_DELIVERED);
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

    public function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++)
        {
            if($mask[$i] == '#')
            {
                if(isset($val[$k])){
                    $maskared .= $val[$k++];
                }
            } else {
                if(isset($mask[$i])){
                    $maskared .= $mask[$i];
                }
            }
        }
        return $maskared;
    }

    public function getFormatedTaxVat($taxvat)
    {
        $taxvat = preg_replace( '/[^0-9]/', '', $taxvat );
        if(strlen($taxvat) == 11)
        {
            return $this->mask($taxvat, '###.###.###-##');
        }else{
            return $this->mask($taxvat, '##.###.###/####-##');
        }
    }

    public function getPricesProduct( $product ){
        $isBundle   = $product->getTypeId() == 'bundle' ? TRUE : FALSE;
        $price      = 0.0;
        $finalPrice = 0.0;
        $weights    = 0.0;

        if( $isBundle && $product->getId() ) {

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $_productFactory = $objectManager->get('\Magento\Catalog\Model\ProductFactory')->create()->load( $product->getId() );

            $selectionCollection = $product->getTypeInstance(true)
                                           ->getSelectionsCollection(
                                                $_productFactory->getTypeInstance(true)->getOptionsIds($_productFactory),
                                                $_productFactory
                                            );

            foreach ($selectionCollection as $proselection) {
                $price        += ( $proselection->getSelectionQty() * $proselection->getPrice() );
                $finalPrice   += ( $proselection->getSelectionQty() * $proselection->getFinalPrice() );
                $weights      += ( $proselection->getSelectionQty() * $proselection->getWeight() );
            }

            $_specialPrice = (float) $product->getSpecialPrice() / 100;
            $finalPrice = $finalPrice * $_specialPrice;
        }
        else {
            $price        = $product->getPrice();
            $finalPrice   = $product->getFinalPrice();
            $weights      = $product->getWeight();
        }

        $prices = [ 'price' => $price, 'finalPrice' => $finalPrice, 'isBundle' => $isBundle, 'weights' => $weights ];

        return $prices;
    }
}
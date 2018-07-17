<?php


namespace Resultate\Skyhub\Model\Payment;

class Marketplace_Cc extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = "marketplace_cc";
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        return parent::isAvailable($quote);
    }
}

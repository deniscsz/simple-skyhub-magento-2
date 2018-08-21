<?php
namespace Resultate\Skyhub\Model\Quote;

class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) 
    {
        $this->setCode('skyhubtax');
        $this->eventManager = $eventManager;
        $this->calculator = $validator;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
    }
    
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_registry = $objectManager->get('\Magento\Framework\Registry');
        $TotalAmount = $_registry->registry('skyhub_tax') ? $_registry->registry('skyhub_tax') : false;
        
        if(!$TotalAmount)
        {
            return $this;
        }

        $label = 'Skyhub Fee';
        $discountAmount = $TotalAmount; 
        $appliedCartDiscount = 0;
        
        if($total->getDiscountDescription())
        {
            $appliedCartDiscount = $total->getDiscountAmount();
            $discountAmount = $total->getDiscountAmount() + $discountAmount;
            $label = $total->getDiscountDescription().', '.$label;
        } 
        
        $total->setDiscountDescription($label);
        $total->setDiscountAmount($discountAmount);
        $total->setBaseDiscountAmount($discountAmount);
        $total->setSubtotalWithDiscount($total->getSubtotal() + $discountAmount);
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $discountAmount);
        
        if(isset($appliedCartDiscount))
        {
            $total->addTotalAmount($this->getCode(), $discountAmount + $appliedCartDiscount);
            $total->addBaseTotalAmount($this->getCode(), $discountAmount + $appliedCartDiscount);
        } 
        else 
        {
            $total->addTotalAmount($this->getCode(), $discountAmount);
            $total->addBaseTotalAmount($this->getCode(), $discountAmount);
        }
        return $this;
    }
    
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;
        $amount = $total->getDiscountAmount();
        
        if ($amount != 0)
        { 
            $description = $total->getDiscountDescription();
            $result = [
                'code' => $this->getCode(),
                'title' => strlen($description) ? __('Fee (%1)', $description) : __('Discount'),
                'value' => $amount
            ];
        }
        return $result;
    }
}
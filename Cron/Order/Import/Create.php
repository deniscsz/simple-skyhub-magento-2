<?php 

namespace Resultate\Skyhub\Cron\Order\Import;

use Resultate\Skyhub\Cron\Order\Import\AbstractImportCron;
use Magento\Sales\Model\Order;

class Create extends AbstractImportCron
{
    private $_newCustomer,
            $_cart,
            $cartManagementInterface,
            $cartRepogitoryInterface,
            $_priceDiff;
            
    protected function processOrder(array $orderData)
    {
        try
        {
            $orderCollection = $this->_objectManager->get('\Magento\Sales\Model\ResourceModel\Order\CollectionFactory')->create()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("skyhub_id", array("eq" => $orderData['code']));
                
            if($orderCollection->getSize())
            {
                $order = $orderCollection->getFirstItem();
            }
            else
            {
                $storeId   = $this->helper->getCartStore();
                $store     = $this->_storeManager->getStore($storeId);

                $customer = $this->getCustomer($orderData['customer']);
                if(!$customer)
                {
                    return;
                }
                //init the quote
                $this->_cart = $this->createCart();
                $this->_cart->setStore($store);
                
                $customer = $this->customerRepository->getById($customer->getEntityId());
            
                $this->_cart->setCurrency();
                $this->_cart->assignCustomer($customer);

                $this->addItems($orderData['items']);
                $this->_priceDiff = round($this->_priceDiff, 2);
                if($this->_registry->registry('skyhub_tax') !== false)
                {
                    $this->_registry->unregister('skyhub_tax');
                }
                $this->_registry->register('skyhub_tax', $this->_priceDiff);
                

                $this->setAddress($orderData['billing_address']);
                if($this->_registry->registry('shipping_price'))
                {
                    $this->_registry->unregister('shipping_price');
                }
                $this->_registry->register('shipping_price', $orderData['shipping_cost']);
                
                // Collect Rates and Set Shipping & Payment Method
                $shippingAddress = $this->_cart->getShippingAddress();
                $shippingAddress->setCollectShippingRates(true)
                    ->collectShippingRates()
                    ->setShippingMethod('marketplace_marketplace'); //shipping method                    


                $paymentMethod = $this->getPaymentMethod($orderData['payments']);                
                $this->_cart->setPaymentMethod($paymentMethod);
                $this->_cart->setInventoryProcessed($this->helper->getInventoryProcessed());
                $this->_cart->getPayment()->importData(['method' => $paymentMethod]);

                $this->_cart->collectTotals();
                $this->_cart->save();
                $this->_cart = $this->cartRepogitoryInterface->get($this->_cart->getId());
                $order_id = $this->cartManagementInterface->placeOrder($this->_cart->getId());

                $order = $this->_objectManager->create('\Magento\Sales\Model\Order')
                    ->load($order_id);
                $order->setEmailSent(0);
                $order->setSkyhubId($orderData['code']);
                $order->setSkyhubChannel($orderData['channel']);
            }

            if($order->getEntityId())
            {
                echo '['.$order_id.'] '.'Imported ' . $orderData['code'] . PHP_EOL;
                
                $orderStatus = $this->getOrderStatus($orderData);

                $order->setState($orderStatus, true);
                $order->setStatus($orderStatus, true);
                $order->addStatusToHistory($order->getStatus(), 'Order processed successfully by Skyhub');
                $order->save();

                if($this->_newCustomer)
                {
                    $customer->delete();
                }
            }
            return true;
        }
        catch(\Exception $e)
        {
            echo 'Error: '. $e->getMessage() . PHP_EOL;
            $this->logger->critical($e);
        }
        return false;
    }

    private function createCart()
    {
        $this->cartManagementInterface = $this->_objectManager->get('\Magento\Quote\Api\CartManagementInterface');
        $this->cartRepogitoryInterface = $this->_objectManager->get('\Magento\Quote\Api\CartRepositoryInterface');

        $cart_id = $this->cartManagementInterface->createEmptyCart();
        return $this->cartRepogitoryInterface->get($cart_id);

    }

    private function addItems($items)
    {
        $product = $this->_objectManager->get('\Magento\Catalog\Model\ProductRepository');
        $storeId   = $this->helper->getCartStore();
        foreach($items as $item)
        {
            $product = $product->get($item['product_id'], false, $storeId);
            $this->addPriceDiff($item, $product);

            $this->_cart->addProduct(
                $product,
                intval($item['qty'])
            );
        }
    }

    private function addPriceDiff($item , $product)
    {
        $this->_priceDiff = $this->_priceDiff ? $this->_priceDiff : 0; 
        
        $itemPrice = isset($item['special_price']) && $item['special_price'] > 0 && $item['original_price'] > $item['special_price'] 
            ? $item['special_price'] : $item['original_price'];

        $prices = $this->helper->getPricesProduct( $product );
        $productPrice = isset($prices['finalPrice']) && $prices['finalPrice'] > 0 && $prices['price'] > $prices['finalPrice'] 
        ? $prices['finalPrice'] : $prices['price'];
        
        $this->_priceDiff += ($itemPrice - $productPrice);
    }

    private function setAddress($address)
    {
        $fullName = explode(" ", $address['full_name']);
        $address['firstname']  = $fullName[0];
        $address['lastname']   = $fullName[1];
        $address['telephone']  = $address['phone'];
        $address['fax']        = $address['secondary_phone'];
        $address['country_id'] = $address['country'];

        $this->_cart->getBillingAddress()->addData($address);
        $this->_cart->getShippingAddress()->addData($address);
    }

    private function getCustomer($customerData)
    {
        $this->_newCustomer = false;
        $cpf = $this->helper->getFormatedTaxVat($customerData['vat_number']);

        $customerFactory = $this->_objectManager->get('\Magento\Customer\Model\CustomerFactory');
        $collection = $customerFactory->create()->getCollection()
            ->addAttributeToSelect("*")
            ->addAttributeToFilter("taxvat", array("eq" => $cpf))
            ->load();
        
        if($collection->getSize())
        {
            $customer = $collection->getFirstItem();
            return $customer;
        }
        else
        {
            try
            {
                $storeId   = $this->helper->getCartStore();
                $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();

                $customer = $customerFactory->create();
                $customer->setWebsiteId($websiteId);
                $customer->loadByEmail($customerData['email']);
                if(!$customer->getEntityId())
                {
                    $this->_newCustomer = true;

                    $name = explode(" ", $customerData['name']);
                    $customer = $customerFactory->create();

                    $customer->setWebsiteId($websiteId);
                    $customer->setEmail( $customerData['email'] ); 
                    $customer->setFirstname( $name[0] );
                    $customer->setLastname( $name[1] );
                    $customer->setTaxvat( $cpf );
                    $customer->setDob( $customerData['date_of_birth'] );
                    $customer->setPassword( "123456789password" );
        
                    $customer->save();
                }
                return $customer;
            }
            catch(\Exception $e)
            {
                echo 'Error! '. PHP_EOL;
                print_r($e->getMessage());
                $this->logger->critical($e);
            }
        }              
    }

    private function getOrderStatus($orderData)
    {
        $status = strtolower($orderData['status']['type']);

        switch ($status)
        {
            case 'new':
                return $this->helper->getStatusPending();   
                break;

            case 'shipped':
                return $this->helper->getStatusShipmed();   
                break;

            case 'approved':
                return $this->helper->getStatusInvoiced();   
                break;
            
            case 'delivered':
                return $this->helper->getStatusDelivered();   
                break;
            
            case 'canceled':
                return $this->helper->getStatusCanceled();   
                break;

            case 'refunded':
                return $this->helper->getStatusRefunded();   
                break;

            default:
                return $this->helper->getStatusPending();    
                break;
        }

    }
    
    private function getPaymentMethod($payment)
    {
        $paymentCode = array_shift($payment)['method'];

        switch (strtolower($paymentCode))
        {
            case 'debit_card':
                return 'marketplace_boleto';   
                break;

            case 'credit_card':
                return 'marketplace_cc';   
                break;
            
            case 'boleto':
                return 'marketplace_boleto';   
                break;
            
            case 'voucher':
                return 'marketplace_boleto';   
                break;

            default:
                return 'marketplace_boleto';    
                break;
        } 
    }
}
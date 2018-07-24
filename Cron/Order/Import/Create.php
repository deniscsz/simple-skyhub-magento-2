<?php 

namespace Resultate\Skyhub\Cron\Order\Import;

use Resultate\Skyhub\Cron\Order\Import\AbstractImportCron;
use Magento\Sales\Model\Order;

class Create extends AbstractImportCron
{
    private $_quote,
            $_newCustomer;
            
    protected function processOrder(array $orderData)
    {
        try{
            $orderCollection = $this->_objectManager->get('\Magento\Sales\Model\ResourceModel\Order\CollectionFactory')->create()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("skyhub_id", array("eq" => $orderData['code']));
                
            if($orderCollection->getSize())
            {
                $order = $orderCollection->getFirstItem();
            } else {

                $store     = $this->_storeManager->getStore();
                $websiteId = $this->_storeManager->getStore()->getWebsiteId();

                $customer = $this->getCustomer($orderData['customer']);
                if(!$customer)
                {
                    return;
                }

                $this->_quote = $this->_objectManager->get('\Magento\Quote\Model\QuoteFactory')->create();
                $this->_quote->setStore($store);
                $customer = $this->customerRepository->getById($customer->getEntityId());
            
                $this->_quote->setCurrency();
                $this->_quote->assignCustomer($customer);
                $this->addItems($orderData['items']);
                $this->setAddress($orderData['billing_address']);

                if($this->_registry->registry('shipping_price')){
                    $this->_registry->unregister('shipping_price');
                }
                $this->_registry->register('shipping_price', $orderData['shipping_cost']);
                // Collect Rates and Set Shipping & Payment Method
                $shippingAddress = $this->_quote->getShippingAddress();
                $shippingAddress->setCollectShippingRates(true)
                    ->collectShippingRates()
                    ->setShippingMethod('marketplace_marketplace');

                $this->_quote->setPaymentMethod('checkmo');
                $this->_quote->setInventoryProcessed(false);
                $this->_quote->save();
        
                /**
                 * @todo payment method
                 */
                $this->_quote->getPayment()->importData(['method' => 'checkmo']);

                $this->_quote->collectTotals()->save();
                $order = $this->_objectManager->get('\Magento\Quote\Model\QuoteManagement')->submit($this->_quote);
                
                $order->setEmailSent(0);
                $order->setSkyhubId($orderData['code']);
            }
            if($order->getEntityId()){
                echo 'Imported ' . $orderData['code'] . PHP_EOL;
                
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
        } catch(\Exception $e) {
            echo 'Error!' . PHP_EOL;
            print_r($e->getMessage());
            $this->logger->critical($e);
        }
    }

    private function addItems($items)
    {
        $product = $this->_objectManager->get('\Magento\Catalog\Model\ProductRepository');
        foreach($items as $item){
            $product = $product->get($item['product_id']);
            $product->setPrice($item['original_price']);
            $this->_quote->addProduct(
                $product,
                intval($item['qty'])
            );
        }
    }

    private function setAddress($address)
    {
        $fullName = explode(" ", $address['full_name']);
        $address['firstname']  = $fullName[0];
        $address['lastname']   = $fullName[1];
        $address['telephone']  = $address['phone'];
        $address['fax']        = $address['secondary_phone'];
        $address['country_id'] = $address['country'];

        $this->_quote->getBillingAddress()->addData($address);
        $this->_quote->getShippingAddress()->addData($address);
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
        } else {
            try{
                $websiteId  = $this->_storeManager->getWebsite()->getWebsiteId();

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
            } catch(\Exception $e) {
                echo 'Error! '. PHP_EOL;
                print_r($e->getMessage());
                $this->logger->critical($e);
            }
        }              
    }

    private function getOrderStatus($orderData)
    {
        $status = $orderData['status']['type'];

        switch ($status) {
            case 'NEW':
                return Order::STATE_PENDING_PAYMENT;   
                break;

            case 'SHIPPED':
            case 'APPROVED':
                return Order::STATE_PROCESSING;   
                break;
            
            case 'DELIVERED':
                return Order::STATE_COMPLETE;   
                break;
            
            case 'CANCELED':
                return Order::STATE_CANCELED;   
                break;

            default:
                return Order::STATE_PENDING_PAYMENT;    
                break;
        }

    }
}
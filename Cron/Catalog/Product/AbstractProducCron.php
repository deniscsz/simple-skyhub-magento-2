<?php

namespace Resultate\Skyhub\Cron\Catalog\Product;

use Resultate\Skyhub\Cron\Catalog\AbstractCatalogCron;
use Resultate\Skyhub\Model\SkyhubJob;
use Magento\Catalog\Model\Product;
use Magento\Framework\Phrase;

abstract class AbstractProducCron extends AbstractCatalogCron
{
    protected function processJob(SkyhubJob $job)
    {
        try{
            $storeId   = $this->helper->getCartStore();
            $productId = $job->getEntityId();
            $product = $this->_objectManager->create(\Magento\Catalog\Model\ProductRepository::class)
                ->getById($productId, false, $storeId);

            if(!$product->getId())
            {
                return false;
            }
            
            $sku            = $product->getSku();
            $attributes     = $this->getAttributes($product);
            $images         = $this->getImages($product);
            $categories     = $this->getCategorries($product);
            $specifications = $this->getAttributes($product, true);
            
            $response = $this->getRequestHandler()->create(
                $sku,
                $attributes,
                $images,
                $categories,
                $specifications,
                array(),
                array()
            );
            
            if ($response->success())
            {
                if( !$product->hasData('skyhub_control') )
                {
                    echo 'Set up skyhub_control!' . PHP_EOL;
                    $action = $this->_objectManager->get(\Magento\Catalog\Model\ResourceModel\Product\Action::class);
                    $action->updateAttributes([$product->getId()], ['skyhub_control' => 1], $storeId);    
                }

                echo 'Product Imported: '. $sku . PHP_EOL;
            }
        } catch (\Exception $e){
            if($sku)
            {
                echo PHP_EOL . 'Error: '. $sku . PHP_EOL;
            }
            print_r($e->getMessage());
            echo PHP_EOL;
            $this->logger->critical($e);
        }
    }

    protected function getCategorries(Product $product)
    {
        $response = array();
        $categoryIds = $product->getCategoryIds();
        if(count($categoryIds))
        {
            $categoryCollection = $this->_objectManager
                ->create(\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory::class)
                ->create()
                ->addAttributeToSelect('path')
                ->addAttributeToSelect('name')
                ->addFieldToFilter('entity_id', array('in' => $categoryIds));

            foreach($categoryCollection as $category)
            {
                $pathIds = explode('/', $category->getPath());
            
                $categoryCollection2 = $this->_objectManager
                    ->create(\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory::class)
                    ->create()
                    ->addAttributeToSelect('name')
                    ->addFieldToFilter('entity_id', array('in' => $pathIds))
                    ->addFieldToFilter('level', array('gt' => 1));

                $catNames = array();
                foreach($categoryCollection2 as $category2){
                    $catNames[] = $category2->getName();
                }

                $response[] = array(
                    'code' => strtolower($category->getName()),
                    'name' => implode(" > ", $catNames)
                );
            }
        }

        return $response;
    }

    protected function getImages(Product $product)
    {
        $response = array();
        if($product->hasData('media_gallery'))
        {
            $gallery  = $product->getData('media_gallery');
            $images   = $gallery['images'];
            $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, true);
            if(count($images))
            {
                foreach($images as $image)
                {
                    $response[] =  $mediaUrl."catalog/product". $image['file'];
                }
            }
        }
        
        return $response;
    }

    protected function getAttributes(Product $product, $specifications = false)
    {
        $response = array();
        $attrs = $product->getAttributes();
        $attributesToSync = $this->getAttributesToSync();

        foreach($attrs as $attr){
            $attrCode = $attr->getAttributeCode();
            if(in_array($attrCode, $attributesToSync) && $product->hasData($attrCode))
            {
                if( !is_array( $product->getData($attrCode) ) && $product->getData($attrCode) !== null )
                {
                    $value = $attr->getFrontend()->getValue($product);
                    if ($value instanceof Phrase) {
                        $value = $value->getText();
                    } 
                    if($specifications)
                    {
                        $response[] = array(
                            'key'   => $attrCode,
                            'value' => $value
                        );
                    }
                    else
                    {
                        $response[str_replace('ts_dimensions_','',$attrCode)] = $value;
                    }
                }
            }
        }

        if(count($response) && !$specifications)
        {
            $StockState                    = $this->_objectManager->get(\Magento\CatalogInventory\Api\StockStateInterface::class);
            $response['qty']               = $StockState->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
                     
            $prices                        = $this->helper->getPricesProduct( $product );
            $response['price']             = $prices['price'];
            $response['promotional_price'] = $prices['finalPrice'];
        }
        return $response;
    }

    protected function setRequestHandler()
    {
        return $this->requestHandler = $this->getApi()->product();
    }   
}
<?php

namespace Resultate\Skyhub\Observer\Cataloginventory;

use Resultate\Skyhub\Observer\AbstractObserver;
use Resultate\Skyhub\Model\SkyhubJob;

class StockItemSaveAfter extends AbstractObserver
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        if($observer->hasData('item'))
        {
            $stockItem = $observer->getData('item');
            $productId = $stockItem->getData('product_id');
            $storeId   = $this->helper->getCartStore();
            
            $skyhubControl   = $this->_objectManager->get(\Magento\Catalog\Model\ResourceModel\Product::class)
                ->getAttributeRawValue($productId, 'skyhub_control', $storeId);

            if($skyhubControl)
            {
                $this->createJob(
                    SkyhubJob::ENTITY_TYPE_CATALOGINVENTORY_STOCK_ITEM_SAVE, 
                    $productId
                );
            }
        }
        
        return $this;
    }
}
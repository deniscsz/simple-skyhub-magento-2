<?php

namespace Resultate\Skyhub\Observer\Catalog;

use Resultate\Skyhub\Observer\AbstractObserver;
use Resultate\Skyhub\Model\SkyhubJob;

class ProductSaveAfter extends AbstractObserver
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

        $product = $observer->getData('product');
        $this->createJob(
            SkyhubJob::ENTITY_TYPE_CATALOG_PRODUCT_SAVE, 
            $product->getId()
        );

        return $this;
    }
}

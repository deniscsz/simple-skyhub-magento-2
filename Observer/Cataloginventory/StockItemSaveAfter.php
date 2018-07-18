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

        $stockItem = $observer->getData('item');
        $this->createJob(
            SkyhubJob::ENTITY_TYPE_CATALOGINVENTORY_STOCK_ITEM_SAVE, 
            $stockItem->getId()
        );

        return $this;
    }
}

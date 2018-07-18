<?php

namespace Resultate\Skyhub\Observer\Order;

use Resultate\Skyhub\Observer\AbstractObserver;
use Resultate\Skyhub\Model\SkyhubJob;

class CancelAfter extends AbstractObserver
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

        $order = $observer->getData('order');
        $this->createJob(
            SkyhubJob::ENTITY_TYPE_ORDER_CANCEL, 
            $order->getId()
        );

        return $this;
    }
}

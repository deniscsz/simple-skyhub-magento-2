<?php

namespace Resultate\Skyhub\Observer\Sales\Order;

use Resultate\Skyhub\Observer\AbstractObserver;

abstract class AbstractOrderObserver extends AbstractObserver
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

        if( $order->hasData('skyhub_id') && $this->process($order->getStatus()) )
        {
            $this->createJob(
                $this->getOrderStatus(), 
                $order->getId()
            );
        }


        return $this;
    }

    protected abstract function process($status);
    protected abstract function getOrderStatus();
}
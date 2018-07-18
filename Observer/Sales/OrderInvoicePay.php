<?php


namespace Resultate\Skyhub\Observer\Sales;

use Resultate\Skyhub\Observer\AbstractObserver;
use Resultate\Skyhub\Model\SkyhubJob;

class OrderInvoicePay extends AbstractObserver
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

        $invoice = $observer->getData('invoice');
        $this->createJob(
            SkyhubJob::ENTITY_TYPE_SALES_ORDER_INVOICE_PAY, 
            $invoice->getData('order_id')
        );

        return $this;
    }
}

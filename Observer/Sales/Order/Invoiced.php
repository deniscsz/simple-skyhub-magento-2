<?php 

namespace Resultate\Skyhub\Observer\Sales\Order;

use Resultate\Skyhub\Observer\Sales\Order\AbstractOrderObserver;
use Resultate\Skyhub\Model\SkyhubJob;

class Invoiced extends AbstractOrderObserver
{
    protected function process($status)
    {
        return $status == $this->helper->getStatusInvoiced();
    }

    protected function getOrderStatus()
    {
        return SkyhubJob::ENTITY_TYPE_SALES_ORDER_INVOICED;
    }
}
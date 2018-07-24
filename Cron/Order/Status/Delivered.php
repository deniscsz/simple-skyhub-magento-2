<?php

namespace Resultate\Skyhub\Cron\Order\Status;

use Resultate\Skyhub\Cron\Order\AbstractOrderCron;
use Resultate\Skyhub\Model\SkyhubJob;

class Delivered extends AbstractOrderCron
{
    protected function processJob(SkyhubJob $job)
    {
        try{
            $orderId = $job->getEntityId();
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);

            if($order->hasData('skyhub_id'))
            {
                $skyhubId = $order->getData('skyhub_id');
                $response = $this->getRequestHandler()->delivery($skyhubId);
                if ($response->success())
                {
                    echo 'Order Delivered: ' . $skyhubId . PHP_EOL;
                }
            }
        }catch(\Exception $e){
            echo 'Error: ' . $orderId . PHP_EOL;
            print_r($e->getMessage());
            $this->logger->critical($e);
        }
    }

    protected function setJobType()
    {
        return $this->jobType = SkyhubJob::ENTITY_TYPE_SALES_ORDER_DELIVERED;
    }
}
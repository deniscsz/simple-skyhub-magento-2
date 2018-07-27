<?php

namespace Resultate\Skyhub\Cron\Order\Status;

use Resultate\Skyhub\Cron\Order\AbstractOrderCron;
use Resultate\Skyhub\Model\SkyhubJob;

class Shipmed extends AbstractOrderCron
{
    protected function processJob(SkyhubJob $job)
    {
        try{
            $orderId = $job->getEntityId();
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);

            if($order->hasData('skyhub_id'))
            {
                $skyhubId = $order->getData('skyhub_id');
                $items    = $this->getItems($order);
                
                /**
                 * @todo shipping data
                 */
                $code     = $order->getIncrementId();
                $carrier  = "";
                $method   = "";
                $url      = "";

                $response = $this->getRequestHandler()->shipment(
                    $skyhubId,
                    $items,
                    $code,
                    $carrier,
                    $method,
                    $url
                );

                if ($response->success())
                {
                    echo 'Order Shipmed: ' . $skyhubId . PHP_EOL;
                }
            }
        }catch(\Exception $e){
            echo 'Error: ' . $orderId . PHP_EOL;
            print_r($e->getMessage());
            $this->logger->critical($e);
        }
    }

    private function getItems($order)
    {
        $response = array();
        $orderItems = $order->getAllItems();
        foreach($orderItems as $item)
        {
            $response[] = array(
                "sku" => $item->getData('sku'),
                "qty" => $item->getData('qty_ordered')
            );
        }

        return $response;
    }

    protected function setJobType()
    {
        return $this->jobType = SkyhubJob::ENTITY_TYPE_SALES_ORDER_SHIPMED;
    }
}
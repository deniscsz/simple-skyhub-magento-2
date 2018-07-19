<?php

namespace Resultate\Skyhub\Cron\Order\Status;

use Resultate\Skyhub\Cron\Order\AbtractOrderCron;
use Resultate\Skyhub\Model\SkyhubJob;

class Refunded extends AbtractOrderCron
{
    protected function processJob(SkyhubJob $job)
    {
        
    }

    protected function setJobType()
    {
        return $this->jobType = SkyhubJob::ENTITY_TYPE_SALES_ORDER_PAYMENT_REFUND;
    }
}
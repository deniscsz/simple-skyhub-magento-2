<?php

namespace Resultate\Skyhub\Cron\Order\Status;

use Resultate\Skyhub\Model\SkyhubJob;
use Resultate\Skyhub\Cron\Order\Status\Canceled;

class Refunded extends Canceled
{
    protected function setJobType()
    {
        return $this->jobType = SkyhubJob::ENTITY_TYPE_SALES_ORDER_REFUNDED;
    }
}
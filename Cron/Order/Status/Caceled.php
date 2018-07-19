<?php

namespace Resultate\Skyhub\Cron\Order\Status;

use Resultate\Skyhub\Cron\Order\AbtractOrderCron;
use Resultate\Skyhub\Model\SkyhubJob;

class Canceled extends AbtractOrderCron
{
    protected function processJob(SkyhubJob $job)
    {
        
    }

    protected function setJobType()
    {
        return $this->jobType = SkyhubJob::ENTITY_TYPE_ORDER_CANCEL;
    }
}

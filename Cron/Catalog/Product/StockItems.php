<?php

namespace Resultate\Skyhub\Cron\Catalog\Product;

use Resultate\Skyhub\Cron\Catalog\Product\AbstractProducCron;
use Resultate\Skyhub\Model\SkyhubJob;

class StockItems extends AbstractProducCron
{
    protected function setJobType()
    {
        return $this->jobType = SkyhubJob::ENTITY_TYPE_CATALOGINVENTORY_STOCK_ITEM_SAVE;
    }
}

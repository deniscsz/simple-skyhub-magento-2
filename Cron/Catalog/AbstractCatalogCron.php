<?php

namespace Resultate\Skyhub\Cron\Catalog;

use Resultate\Skyhub\Cron\AbstractCron;

abstract class AbstractCatalogCron extends AbstractCron
{
    protected function getAttributesToSync()
    {
        return explode("," , $this->helper->getAttributesToSync());
    }
}
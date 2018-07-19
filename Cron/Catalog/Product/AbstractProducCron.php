<?php

namespace Resultate\Skyhub\Cron\Catalog\Product;

use Resultate\Skyhub\Cron\AbstractCron;

abstract class AbstractProducCron extends AbstractCron
{
    protected function setRequestHandler()
    {
        return $this->requestHandler = $this->getApi()->product();
    }
}
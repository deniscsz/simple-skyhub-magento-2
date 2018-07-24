<?php

namespace Resultate\Skyhub\Cron\Order;

use Resultate\Skyhub\Cron\AbstractCron;

abstract class AbstractOrderCron extends AbstractCron
{
    protected function setRequestHandler()
    {
        return $this->requestHandler = $this->getApi()->order();
    }
}
<?php

namespace Resultate\Skyhub\Cron\Catalog\Product;

use Resultate\Skyhub\Cron\Catalog\Product\AbstractProducCron;
use Resultate\Skyhub\Model\SkyhubJob;

class Products extends AbstractProducCron
{
    protected function processJob(SkyhubJob $job)
    {
        $productId = $job->getEntityId();
        $product = $this->_objectManager->create(\Magento\Catalog\Model\Product::class)->load($productId);

        print_r($product->debug());

        die;
    }

    protected function setJobType()
    {
        return $this->jobType = SkyhubJob::ENTITY_TYPE_CATALOG_PRODUCT_SAVE;
    }
}

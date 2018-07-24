<?php

namespace Resultate\Skyhub\Cron\Catalog;

use Resultate\Skyhub\Cron\Catalog\AbstractCatalogCron;
use Resultate\Skyhub\Model\SkyhubJob;

class Attributes extends AbstractCatalogCron
{
    protected function processJob(SkyhubJob $job)
    {
       try{
            foreach ($this->getAttributesToSync() as $attributeCode)
            {
                $attributeLabel = $this->getAttributeModel($attributeCode)->getLabel();
                $selectOptions = $this->getAttributeModel($attributeCode)->getSelectOptions();
                $attrOptionsLabel = $this->getAttrOptionsLabel($selectOptions);
                
                $response = $this->getRequestHandler()->create(
                    $attributeCode,
                    $attributeLabel,
                    $attrOptionsLabel
                );
                
                if ($response->success())
                {
                    echo 'Attribute Imported: '. $attributeLabel . PHP_EOL;
                }
            }

        }catch(\Exception $e){
            echo 'Error: '. $attributeLabel . PHP_EOL;
            print_r($e->getMessage());
            $this->logger->critical($e);
        }
    }

    private function getAttributeModel($attributeCode)
    {
        return $this->_objectManager->create(\Magento\Eav\Model\Entity\Attribute::class)
            ->loadByCode('catalog_product', $attributeCode)->getFrontend();
    }

    private function getAttrOptionsLabel($selectOptions)
    {
       $optionsLabels = array();
       
       if (count($selectOptions) > 1) 
       {
            foreach ($selectOptions as $option)
            {
                if (isset($option['value']) && $option['value'] && $option['value'] !== "")
                {
                    $optionsLabels[] = $option['label'];
                }
            }
        }
        return $optionsLabels;
    }

    protected function setJobType()
    {
        return $this->jobType = SkyhubJob::ENTITY_TYPE_SYNC_ATTRIBUTES;
    }

    protected function setRequestHandler()
    {
        return $this->requestHandler = $this->getApi()->productAttribute();
    }

}

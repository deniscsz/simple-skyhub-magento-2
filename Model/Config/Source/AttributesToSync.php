<?php


namespace Resultate\Skyhub\Model\Config\Source;

class AttributesToSync implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => '', 'label' => __('')]];
    }

    public function toArray()
    {
        return ['' => __('')];
    }
}

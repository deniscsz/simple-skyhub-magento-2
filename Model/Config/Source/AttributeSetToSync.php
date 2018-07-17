<?php


namespace Resultate\Skyhub\Model\Config\Source;

class AttributeSetToSync implements \Magento\Framework\Option\ArrayInterface
{
    private $_objectManager;
    private $_attributeSet = [];

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager
    ) {
        $this->_objectManager = $objectmanager;
        $this->_attributeSet = $this->_objectManager->create(\Magento\Catalog\Model\Product\AttributeSet\Options::class);
    }

    public function toOptionArray()
    {
        return $this->_attributeSet->toOptionArray();
    }

    public function toArray()
    {
        $options = [];
        foreach ($this->toOptionArray() as $attribute) {
            $options[$attribute['value']] = $attribute['label'];
        }
        return $options;
    }
}

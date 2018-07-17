<?php


namespace Resultate\Skyhub\Model\Config\Source;

class AttributesToSync implements \Magento\Framework\Option\ArrayInterface
{
    private $_objectManager;
    private $helper;
    private $_attributes = [];

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Resultate\Skyhub\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->_objectManager = $objectmanager;
        
        $attributeSetId = $this->helper->getAttributeSetToSync() ? $this->helper->getAttributeSetToSync() : 4;
        $collection = $this->_objectManager->create(\Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection::class);
        $collection->addFieldToFilter(\Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID, $attributeSetId);
        $this->_attributes = $collection->load()->getItems();
    }

    public function toOptionArray()
    {
        $options = [];
        foreach ($this->_attributes as $attribute) {
            $options[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getStoreLabel()
            ];
        }
        return $options;
    }

    public function toArray()
    {
        $options = [];
        foreach ($this->_attributes as $attribute) {
            $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        }
        return $options;
    }
}

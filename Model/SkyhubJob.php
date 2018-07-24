<?php


namespace Resultate\Skyhub\Model;

use Resultate\Skyhub\Api\Data\SkyhubJobInterface;

class SkyhubJob extends \Magento\Framework\Model\AbstractModel implements SkyhubJobInterface
{
    protected $_eventPrefix = 'resultate_skyhub_skyhubjob';

    const   ENTITY_TYPE_SYNC_ATTRIBUTES                  = 'catalog_product_sync_attributes',
            ENTITY_TYPE_CATALOG_PRODUCT_SAVE             = 'catalog_product_save',
            ENTITY_TYPE_CATALOGINVENTORY_STOCK_ITEM_SAVE = 'cataloginventory_stock_item_save',
            ENTITY_TYPE_SALES_ORDER_INVOICED             = 'sales_order_invoiced',
            ENTITY_TYPE_SALES_ORDER_CANCELED             = 'sales_order_canceled',
            ENTITY_TYPE_SALES_ORDER_REFUNDED             = 'sales_order_refunded',
            ENTITY_TYPE_SALES_ORDER_SHIPMED              = 'sales_order_shipmed',
            ENTITY_TYPE_SALES_ORDER_DELIVERED            = 'sales_order_delivered';
            
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Resultate\Skyhub\Model\ResourceModel\SkyhubJob');
    }

    /**
     * Get skyhubjob_id
     * @return string
     */
    public function getSkyhubjobId()
    {
        return $this->getData(self::SKYHUBJOB_ID);
    }

    /**
     * Set skyhubjob_id
     * @param string $skyhubjobId
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     */
    public function setSkyhubjobId($skyhubjobId)
    {
        return $this->setData(self::SKYHUBJOB_ID, $skyhubjobId);
    }

    /**
     * Get entity_type
     * @return string
     */
    public function getEntityType()
    {
        return $this->getData(self::ENTITY_TYPE);
    }

    /**
     * Set entity_type
     * @param string $entityType
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     */
    public function setEntityType($entityType)
    {
        return $this->setData(self::ENTITY_TYPE, $entityType);
    }

    /**
     * Get entity_id
     * @return string
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get created_at
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get executed_at
     * @return string
     */
    public function getExecutedAt()
    {
        return $this->getData(self::EXECUTED_AT);
    }

    /**
     * Set executed_at
     * @param string $executedAt
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     */
    public function setExecutedAt($executedAt)
    {
        return $this->setData(self::EXECUTED_AT, $executedAt);
    }
}

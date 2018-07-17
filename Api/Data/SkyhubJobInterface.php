<?php


namespace Resultate\Skyhub\Api\Data;

interface SkyhubJobInterface
{
    const EXECUTED_AT = 'executed_at';
    const SKYHUBJOB_ID = 'skyhubjob_id';
    const ENTITY_TYPE = 'entity_type';
    const CREATED_AT = 'created_at';
    const ENTITY_ID = 'entity_id';


    /**
     * Get skyhubjob_id
     * @return string|null
     */
    public function getSkyhubjobId();

    /**
     * Set skyhubjob_id
     * @param string $skyhubjobId
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     */
    public function setSkyhubjobId($skyhubjobId);

    /**
     * Get entity_type
     * @return string|null
     */
    public function getEntityType();

    /**
     * Set entity_type
     * @param string $entityType
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     */
    public function setEntityType($entityType);

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     */
    public function setEntityId($entityId);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get executed_at
     * @return string|null
     */
    public function getExecutedAt();

    /**
     * Set executed_at
     * @param string $executedAt
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     */
    public function setExecutedAt($executedAt);
}

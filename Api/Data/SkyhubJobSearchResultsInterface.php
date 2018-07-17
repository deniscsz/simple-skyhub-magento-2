<?php


namespace Resultate\Skyhub\Api\Data;

interface SkyhubJobSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get SkyhubJob list.
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface[]
     */
    public function getItems();

    /**
     * Set entity_type list.
     * @param \Resultate\Skyhub\Api\Data\SkyhubJobInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

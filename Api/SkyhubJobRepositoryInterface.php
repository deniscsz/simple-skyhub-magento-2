<?php


namespace Resultate\Skyhub\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SkyhubJobRepositoryInterface
{


    /**
     * Save SkyhubJob
     * @param \Resultate\Skyhub\Api\Data\SkyhubJobInterface $skyhubJob
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Resultate\Skyhub\Api\Data\SkyhubJobInterface $skyhubJob
    );

    /**
     * Retrieve SkyhubJob
     * @param string $skyhubjobId
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($skyhubjobId);

    /**
     * Retrieve SkyhubJob matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Resultate\Skyhub\Api\Data\SkyhubJobSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete SkyhubJob
     * @param \Resultate\Skyhub\Api\Data\SkyhubJobInterface $skyhubJob
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Resultate\Skyhub\Api\Data\SkyhubJobInterface $skyhubJob
    );

    /**
     * Delete SkyhubJob by ID
     * @param string $skyhubjobId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($skyhubjobId);
}

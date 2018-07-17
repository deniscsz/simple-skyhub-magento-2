<?php


namespace Resultate\Skyhub\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\DataObjectHelper;
use Resultate\Skyhub\Api\SkyhubJobRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Resultate\Skyhub\Model\ResourceModel\SkyhubJob\CollectionFactory as SkyhubJobCollectionFactory;
use Resultate\Skyhub\Api\Data\SkyhubJobInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Resultate\Skyhub\Api\Data\SkyhubJobSearchResultsInterfaceFactory;
use Resultate\Skyhub\Model\ResourceModel\SkyhubJob as ResourceSkyhubJob;

class SkyhubJobRepository implements SkyhubJobRepositoryInterface
{
    protected $dataSkyhubJobFactory;

    protected $dataObjectProcessor;

    protected $searchResultsFactory;

    private $storeManager;

    protected $resource;

    protected $dataObjectHelper;

    protected $skyhubJobFactory;

    protected $skyhubJobCollectionFactory;


    /**
     * @param ResourceSkyhubJob $resource
     * @param SkyhubJobFactory $skyhubJobFactory
     * @param SkyhubJobInterfaceFactory $dataSkyhubJobFactory
     * @param SkyhubJobCollectionFactory $skyhubJobCollectionFactory
     * @param SkyhubJobSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceSkyhubJob $resource,
        SkyhubJobFactory $skyhubJobFactory,
        SkyhubJobInterfaceFactory $dataSkyhubJobFactory,
        SkyhubJobCollectionFactory $skyhubJobCollectionFactory,
        SkyhubJobSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->skyhubJobFactory = $skyhubJobFactory;
        $this->skyhubJobCollectionFactory = $skyhubJobCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSkyhubJobFactory = $dataSkyhubJobFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Resultate\Skyhub\Api\Data\SkyhubJobInterface $skyhubJob
    ) {
        /* if (empty($skyhubJob->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $skyhubJob->setStoreId($storeId);
        } */
        try {
            $this->resource->save($skyhubJob);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the skyhubJob: %1',
                $exception->getMessage()
            ));
        }
        return $skyhubJob;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($skyhubJobId)
    {
        $skyhubJob = $this->skyhubJobFactory->create();
        $this->resource->load($skyhubJob, $skyhubJobId);
        if (!$skyhubJob->getId()) {
            throw new NoSuchEntityException(__('SkyhubJob with id "%1" does not exist.', $skyhubJobId));
        }
        return $skyhubJob;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->skyhubJobCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Resultate\Skyhub\Api\Data\SkyhubJobInterface $skyhubJob
    ) {
        try {
            $this->resource->delete($skyhubJob);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the SkyhubJob: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($skyhubJobId)
    {
        return $this->delete($this->getById($skyhubJobId));
    }
}

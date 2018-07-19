<?php 

namespace Resultate\Skyhub\Cron;

use Magento\Framework\Exception\LocalizedException;
use Resultate\Skyhub\Model\SkyhubJob;

abstract class AbstractCron
{
    
    protected   $logger,
                $helper,
                $skyhubJobFactory,
                $jobType,
                $api,
                $requestHandler,
                $date,
                $_objectManager;
    
    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Resultate\Skyhub\Helper\Data $helper
     * @param \Magento\Framework\App\ObjectManager $objectManager
     * @param \Resultate\Skyhub\Model\ResourceModel\SkyhubJob\CollectionFactory $skyhubJobFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Resultate\Skyhub\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Resultate\Skyhub\Model\ResourceModel\SkyhubJob\CollectionFactory $skyhubJobFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->_objectManager = $objectmanager;
        $this->skyhubJobFactory = $skyhubJobFactory;
        $this->date = $date;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }
        echo PHP_EOL;
        $this->processJobs();

        $this->logger->addInfo("Cronjob is executed.");
    }

    private function loadCollection()
    {
        try{
            $collection = $this->skyhubJobFactory->create();
            $collection
                ->addFieldToFilter(
                    SkyhubJob::ENTITY_TYPE, 
                    array('eq' => $this->getJobType()))
                ->addFieldToFilter(
                    SkyhubJob::EXECUTED_AT, 
                    array('null' => true))
                ->setPageSize(50)
                ->load();

            if($collection->count()){
                return $collection;    
            }
        }catch(\Exception $e){
            $this->logger->critical($e);
        }
        return false;
    }

    protected function processJobs()
    {
        $jobCollection = $this->loadCollection();
        if($jobCollection)
        {
            foreach ($jobCollection as $job) {
                $this->processJob($job);
            }
        } else {
            echo "Nothing to process.". PHP_EOL;
            $this->logger->addInfo("Nothing to process.");
        }        
    }

    protected function getJobType()
    {
        if($this->jobType !== null){
            return $this->jobType;
        }
        return $this->setJobType();
    }

    protected function getApi()
    {
        if($this->api !== null){
            return $this->api;
        }
        return $this->_setApi();
    }

    private function _setApi()
    {
        return $this->api = $this->helper->getSkyhubApi();
    }

    protected function getRequestHandler()
    {
        if($this->requestHandler !== null){
            return $this->requestHandler;
        }
        return $this->setRequestHandler();
    }

    protected abstract function processJob(SkyhubJob $job);

    protected abstract function setJobType();

    protected abstract function setRequestHandler();

}

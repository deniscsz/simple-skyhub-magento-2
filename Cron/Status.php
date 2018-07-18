<?php


namespace Resultate\Skyhub\Cron;

class Status
{
    protected $logger;
    protected $helper;
    protected $skyhubJobFactory;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Resultate\Skyhub\Helper\Data $helper
     * @param \Resultate\Skyhub\Model\ResourceModel\SkyhubJob\CollectionFactory $skyhubJobFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Resultate\Skyhub\Helper\Data $helper,
        \Resultate\Skyhub\Model\ResourceModel\SkyhubJob\CollectionFactory $skyhubJobFactory
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->skyhubJobFactory = $skyhubJobFactory;
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

        $collection = $this->skyhubJobFactory->create();
        $collection->addFieldToFilter("entity_type", array('eq' => 'status'))
            ->addFieldToFilter("executed_at", array('null' => true))
            ->setPageSize(50)
            ->load();
        echo $collection->count();
        foreach ($collection as $job) {
            print_r($job->getData());
        }
            
        $this->logger->addInfo("Cronjob Orders is executed.");
    }
}

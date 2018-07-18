<?php 

namespace Resultate\Skyhub\Observer;

use Resultate\Skyhub\Helper\Data;
use Magento\Framework\Event\ObserverInterface;

abstract class AbstractObserver implements ObserverInterface
{
    protected $logger;
    protected $helper;
    protected $_objectManager;

    
    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Resultate\Skyhub\Helper\Data $helper
     * @param \Magento\Framework\App\ObjectManager $objectManager
     */
    public function __construct(
        \Resultate\Skyhub\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper       = $helper;
        $this->logger       = $logger;
        $this->_objectManager = $objectManager;
    }

    protected function createJob($entityType, $entityId)
    {
        $this->logger->addInfo("Creatind Job");

        $skyhubJob = $this->_objectManager->create("Resultate\Skyhub\Model\SkyhubJob");
        $skyhubJob->setEntityType($entityType);
        $skyhubJob->setEntityId($entityId);
        $skyhubJob->save();

        $this->logger->addInfo("Job Created");
        
        return $this;
    }

}

<?php 

namespace Resultate\Skyhub\Cron\Order\Import;

use Magento\Framework\Exception\LocalizedException;
use Resultate\Skyhub\Model\SkyhubJob;

abstract class AbstractImportCron{

    const MAX_IMPORT_BY_CRON = 50;

    protected  $logger,
               $helper,
               $api,
               $requestHandler,
               $date,
               $_objectManager,
               $_storeManager,
               $customerRepository,
               $_registry;

    private    $_processedQueue = 0;
    
    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Resultate\Skyhub\Helper\Data $helper
     * @param \Magento\Framework\App\ObjectManager $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dates
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Resultate\Skyhub\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->helper             = $helper;
        $this->logger             = $logger;
        $this->_objectManager     = $objectmanager;
        $this->date               = $date;
        $this->_storeManager      = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->_registry          = $registry;
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
        echo PHP_EOL;
        $this->logger->addInfo("Cronjob is executed.");
    }

    protected function processJobs()
    {
        $queue = $this->getRequestHandler()->orders();
        if($queue->success())
        {
            $order = $queue->toArray();
            if( 
                ( $order && !empty($order) && isset( $order['code'] ) ) &&
                ( $this->_processedQueue < self::MAX_IMPORT_BY_CRON )
            ) {
                if($this->processOrder($order))
                {
                    $this->removeFromQueue($order['code']);
                }
                $this->_processedQueue++;

                $this->processJobs();
            }
        }       

    }

    protected function removeFromQueue($orderId)
    {
        $response = $this->getRequestHandler()->delete($orderId);
        if($response->success())
        {
            echo "Removed from Queue: " . $orderId . PHP_EOL;
        }
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

    protected function setRequestHandler()
    {
        return $this->requestHandler = $this->getApi()->queue();
    }

    protected abstract function processOrder(array $order);
}
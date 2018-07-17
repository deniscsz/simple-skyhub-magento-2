<?php


namespace Resultate\Skyhub\Cron;

class Products
{
    protected $logger;
    protected $helper;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Resultate\Skyhub\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
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
            
        $this->logger->addInfo("Cronjob Products is executed.");
    }
}

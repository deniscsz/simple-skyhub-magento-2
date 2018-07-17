<?php


namespace Resultate\Skyhub\Cron;

class Attributes
{
    protected $logger;
    protected $helper;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Resultate\Skyhub\Helper\Data $helper
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
        echo "Testing API SDK" . PHP_EOL;
        echo "Enabled: ". $helper->isEnabled() . PHP_EOL;
        echo "GetApiKey: ". $helper->getApiKey() . PHP_EOL;
        echo "GetApiEmail: ". $helper->getApiEmail() . PHP_EOL;
        echo "Get_SKYHUB_API: ". \get_class($helper->getSkyhubApi()) . PHP_EOL;
        echo "Testing API SDK COMPLETE" . PHP_EOL;
        $this->logger->addInfo("Cronjob Attributes is executed.");
    }
}

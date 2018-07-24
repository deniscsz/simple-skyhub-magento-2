<?php
namespace Resultate\Skyhub\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;

class UpgradeData implements UpgradeDataInterface
{
	/**
     * @var \Magento\Sales\Setup\SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @param \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     */
	public function __construct(
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritDoc}
     */
	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{

        $installer = $setup; 
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.2') < 0)
        {
            $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $installer]);
            $salesSetup->addAttribute(Order::ENTITY, 'skyhub_id', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'=> 255,
                'visible' => false,
                'nullable' => true
            ]);
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_grid'),
                'skyhub_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' =>'Skyhub Order Id'
                ]
            );
        }
 
        $installer->endSetup();
    }
}
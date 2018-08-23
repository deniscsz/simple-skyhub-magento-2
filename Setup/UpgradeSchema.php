<?php


namespace Resultate\Skyhub\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Resultate\Skyhub\Model\SkyhubJob;

class UpgradeSchema implements UpgradeSchemaInterface
{
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager
    ) {
        $this->_objectManager   = $objectmanager;
    }

    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;    
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0)
        {
            $installer->getConnection()->modifyColumn(
                $setup->getTable('resultate_skyhub_skyhubjob'),
                'created_at',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                ]
            );

            $installer->getConnection()->modifyColumn(
                $setup->getTable('resultate_skyhub_skyhubjob'),
                'executed_at',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'default' => null
                ]
            );
        }

        $installer->endSetup();
    }
}

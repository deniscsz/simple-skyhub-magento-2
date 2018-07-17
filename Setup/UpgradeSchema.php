<?php


namespace Resultate\Skyhub\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
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
}

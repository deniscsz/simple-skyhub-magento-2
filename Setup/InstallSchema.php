<?php


namespace Resultate\Skyhub\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_resultate_skyhub_skyhubjob = $setup->getConnection()->newTable($setup->getTable('resultate_skyhub_skyhubjob'));

        
        $table_resultate_skyhub_skyhubjob->addColumn(
            'skyhubjob_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_resultate_skyhub_skyhubjob->addColumn(
            'entity_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => false],
            'Job Skyhub Type'
        );
        

        
        $table_resultate_skyhub_skyhubjob->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            16,
            ['nullable' => false,'unsigned' => true],
            'Type of Entity to Sync'
        );
        

        
        $table_resultate_skyhub_skyhubjob->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'Created At'
        );
        

        
        $table_resultate_skyhub_skyhubjob->addColumn(
            'executed_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'Job executed at'
        );
        

        $setup->getConnection()->createTable($table_resultate_skyhub_skyhubjob);

        $setup->endSetup();
    }
}

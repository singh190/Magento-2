<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 3/31/2016
 * Time: 11:52 AM
 */

namespace Singh\Uigrid\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Get tutorial_simplenews table
        $tableName = $installer->getTable('singh_uigrid');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Entity Id')                
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Name')
                ->addColumn(
                    'email',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Email')
                ->addColumn(
                    'membership',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Membership'
                )
                ->addColumn(
                    'mobile',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Mobile Number'
                )
                ->addColumn(
                    'country_id',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Country'
                )
                ->addColumn(
                    'dob',
                    Table::TYPE_DATE,
                    null,
                    [],
                    'Date Of Birth'
                )
               
                ->setComment('News Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
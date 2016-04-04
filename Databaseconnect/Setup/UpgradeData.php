<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 3/31/2016
 * Time: 1:07 PM
 *
 * class to insert sample data in table
 */
namespace Singh\Databaseconnect\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            // Get tutorial_simplenews table
            $tableName = $setup->getTable('singh_database');
            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $data = [
                    [
                        'title' => 'How to create a simple module',
                        'summary' => 'The summary',
                        'description' => 'The description',
                        'created_at' => date('Y-m-d H:i:s'),
                        'status' => 1
                    ],
                    [
                        'title' => 'Create a module with custom database table',
                        'summary' => 'The summary',
                        'description' => 'The description',
                        'created_at' => date('Y-m-d H:i:s'),
                        'status' => 1
                    ]
                ];

                // Insert data to table
                foreach ($data as $item) {
                    $setup->getConnection()->insert($tableName, $item);
                }
            }
        }

        $setup->endSetup();
    }
}
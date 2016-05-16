<?php
namespace Camping\Auth\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Customer\Setup\CustomerSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    protected $customerSetupFactory;
    protected $indexerRegistry;
    protected $eavConfig;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        IndexerRegistry $indexerRegistry,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->indexerRegistry = $indexerRegistry;
        $this->eavConfig = $eavConfig;
    }

    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->removeAttribute(Customer::ENTITY,'wishlistprivacy');
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'wishlist_privacy',
            [
                'label' => 'Wishlist Privacy',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'required' => false,
                'sort_order' => 1000,
                'visible' => true,
                'system' => false,
                'position' => 1000,
                'default' => 0,
                'option' => ['values' => ['Enable', 'Disable']]
            ]
        );
        $customerSetup->getEavConfig()->getAttribute('customer', 'wishlist_privacy')
            ->setData('used_in_forms', ['adminhtml_customer'])
            ->save();

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            // Get tutorial_simplenews table
            $tableName = $setup->getTable('directory_country_region');
            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $data = [
                    [
                        'region_id' => '512',
                        'country_id' => 'MX',
                        'code' => 'CL',
                        'default_name' => 'Colima'
                    ],
                    [
                        'region_id' => '513',
                        'country_id' => 'MX',
                        'code' => 'DU',
                        'default_name' => 'Durango'
                    ],
                ];

                // Insert data to table
                foreach ($data as $item) {
                    $setup->getConnection()->insert($tableName, $item);
                }
            }
        }


        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            // Get tutorial_simplenews table
            $tableName = $setup->getTable('directory_country_region_name');
            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $data = [
                    [
                        'locale' => 'en_US',
                        'region_id' => '512',
                        'name' => 'Colima'
                    ],
                    [
                        'locale' => 'en_US',
                        'region_id' => '513',
                        'name' => 'Durango'
                    ],
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
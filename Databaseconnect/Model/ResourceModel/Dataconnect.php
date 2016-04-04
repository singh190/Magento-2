<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 3/31/2016
 * Time: 1:31 PM
 */

namespace Singh\Databaseconnect\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Dataconnect extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('singh_database', 'id');
    }
}
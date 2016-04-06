<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/7/2016
 * Time: 12:31 AM
 */

namespace Singh\Uigrid\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Grid extends AbstractDb{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('singh_uigrid','entity_id');
    }
}
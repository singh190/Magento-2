<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/4/2016
 * Time: 1:31 PM
 */

namespace Singh\Grid\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Grid extends AbstractDb{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('singh_database','id');
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/7/2016
 * Time: 12:32 AM
 */

namespace Singh\Uigrid\Model\ResourceModel\Grid;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection{
    /*
     * Define model and resource model
     */
    protected function _construct()
    {
        $this->_init('Singh\Uigrid\Model\Grid','Singh\Uigrid\Model\ResourceModel\Grid');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }
}
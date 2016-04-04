<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/4/2016
 * Time: 1:34 PM
 */

namespace Singh\Grid\Model\ResourceModel\Grid;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection{
    /*
     * Define model and resource model
     */
    protected function _construct()
    {
       $this->_init('Singh\Grid\Model\Grid','Singh\Grid\Model\ResourceModel\Grid');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }
}
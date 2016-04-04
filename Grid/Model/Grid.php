<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/4/2016
 * Time: 1:26 PM
 */
namespace Singh\Grid\Model;

use Magento\Framework\Model\AbstractModel;

class Grid extends AbstractModel{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Singh\Grid\Model\ResourceModel\Grid');
    }
}
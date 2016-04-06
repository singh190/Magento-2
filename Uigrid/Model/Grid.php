<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/7/2016
 * Time: 12:29 AM
 */

namespace Singh\Uigrid\Model;

use Magento\Framework\Model\AbstractModel;

class Grid extends AbstractModel{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Singh\Uigrid\Model\ResourceModel\Grid');
    }
}
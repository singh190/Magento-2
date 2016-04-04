<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 3/31/2016
 * Time: 1:29 PM
 */

namespace Singh\Databaseconnect\Model;

use Magento\Framework\Model\AbstractModel;

class Dataconnect extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Singh\Databaseconnect\Model\ResourceModel\Dataconnect');
    }
}
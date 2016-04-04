<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 3/31/2016
 * Time: 1:34 PM
 */

namespace Singh\Databaseconnect\Model\ResourceModel\Dataconnect;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Singh\Databaseconnect\Model\Dataconnect',
            'Singh\Databaseconnect\Model\ResourceModel\Dataconnect'
        );
    }
}
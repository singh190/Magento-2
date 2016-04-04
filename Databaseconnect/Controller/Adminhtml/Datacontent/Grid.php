<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/1/2016
 * Time: 4:10 PM
 * @pourpose: This is the grid action which is used for loading grid by ajax
 */

namespace Singh\Databaseconnect\Controller\Adminhtml\Datacontent;

use Singh\Databaseconnect\Controller\Adminhtml\Datacontent;

class Grid extends Datacontent
{
    /**
     * @return void
     */
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}
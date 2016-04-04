<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/1/2016
 * Time: 3:43 PM
 * @purpose: This is the block file of grid container
 */

namespace Singh\Databaseconnect\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Datacontent extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_datacontent';
        $this->_blockGroup = 'Singh_Databaseconnect';
        $this->_headerText = __('Manage Records');
        $this->_addButtonLabel = __('Add News Records');
        parent::_construct();
    }
}
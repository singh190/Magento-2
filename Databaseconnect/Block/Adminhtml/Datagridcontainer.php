<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/1/2016
 * Time: 6:53 PM
 *
 * this file is the block for grid container which we declared in layout file
 */

namespace Singh\Databaseconnect\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Datagridcontainer extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_datagrid';
        $this->_blockGroup = 'Singh_Databaseconnect';
        $this->_headerText = __('Manage News');
        $this->_addButtonLabel = __('Add News');
        parent::_construct();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/6/2016
 * Time: 10:25 PM
 */

namespace Singh\Uigrid\Block\Adminhtml;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_grid';
        $this->_blockGroup = 'Singh_Uigrid';
        $this->_headerText = __('Manage Grid');

        parent::_construct();

        if ($this->_isAllowedAction('Singh_Uigrid::save')) {
            $this->buttonList->update('add', 'label', __('Add New Record'));
        } else {
            $this->buttonList->remove('add');
        }
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}

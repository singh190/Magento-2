<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/4/2016
 * Time: 5:57 PM
 */

namespace Singh\Grid\Block\Adminhtml\Grid\Edit;

use Magento\Backend\Block\Widget\Tabs as CoreTabs;

/**
 * Admin page left menu
 */
class Tabs extends CoreTabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid_record');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Grid Information'));
    }
}
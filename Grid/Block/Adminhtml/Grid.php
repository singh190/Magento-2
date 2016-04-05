<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/4/2016
 * Time: 1:43 PM
 *
 * this class is grid container
 * calling grid from template : view.phtml
 */

namespace Singh\Grid\Block\Adminhtml;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;

class Grid extends Container
{
    /**
     * @var string
     */
    protected $_template = 'view.phtml';

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Prepare button and gridCreate Grid , edit/add grid row and installer in Magento2
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $this->buttonList->add('add_new',[
            'label' => __('Add New'),  //button label
            'class' => 'add',
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')"
        ]);

        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Singh\Grid\Block\Adminhtml\Grid\Grid', 'grid.view.grid')
        );      // for grid file/view
        return parent::_prepareLayout();
    }
    
    /**
     *
     *
     * @param string $type
     * @return string
     */
    protected function _getCreateUrl()
    {
        return $this->getUrl(
            'grid/*/new'
        );
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/4/2016
 * Time: 1:50 PM
 */

namespace Singh\Grid\Block\Adminhtml\Grid;

use Magento\Backend\Block\Widget\Grid\Extended;

class Grid extends Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Singh\Grid\Model\GridFactory
     */
    protected $_gridFactory;

    /**
     * @var \Singh\Grid\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Singh\Grid\Model\GridFactory $gridFactory
     * @param \Singh\Grid\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Singh\Grid\Model\GridFactory $gridFactory,
//        \Singh\Grid\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_gridFactory = $gridFactory;
//        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridGrid');
        $this->setDefaultSort('grid_record_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('grid_record');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_gridFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'grid_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'class' => 'xxx'
            ]
        );

        $this->addColumn(
            'summary',
            [
                'header' => __('Summary'),
                'index' => 'summary'
            ]
        );

        $this->addColumn(
            'description',
            [
                'header' => __('Description'),
                'index' => 'description'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at'
            ]
        );


       /* $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->_status->getOptionArray()
            ]
        );*/


        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit'
                        ],
                        'field' => 'grid_record_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
//    protected function _prepareMassaction()
//    {
//        $this->setMassactionIdField('grid_id');
//        $this->getMassactionBlock()->setFormFieldName('grid_id');
//
//        $this->getMassactionBlock()->addItem(
//            'delete',
//            [
//                'label' => __('Delete'),
//                'url' => $this->getUrl('grid/*/massDelete'),
//                'confirm' => __('Are you sure?')
//            ]
//        );
//
//        $statuses = $this->_status->getOptionArray();
//
//        array_unshift($statuses, ['label' => '', 'value' => '']);
//        $this->getMassactionBlock()->addItem(
//            'status',
//            [
//                'label' => __('Change status'),
//                'url' => $this->getUrl('blog/*/massStatus', ['_current' => true]),
//                'additional' => [
//                    'visibility' => [
//                        'name' => 'status',
//                        'type' => 'select',
//                        'class' => 'required-entry',
//                        'label' => __('Status'),
//                        'values' => $statuses
//                    ]
//                ]
//            ]
//        );
//
//
//        return $this;
//    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('grid/*/grid', ['_current' => true]);
    }


    public function getRowUrl($row)
    {
        return $this->getUrl(
            'grid/*/edit',
            ['grid_record_id' => $row->getId()]
        );
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/4/2016
 * Time: 5:35 PM
 */

namespace Singh\Grid\Block\Adminhtml\Grid;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize blog post edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'grid_record_id';
        $this->_blockGroup = 'singh_grid';
        $this->_controller = 'adminhtml_grid';

        parent::_construct();

        $this->buttonList->remove('save');

        $this->buttonList->update('delete', 'label', __('Delete Record'));
        //create button split for save
        $addButtonProps = [
            'id' => 'save-singh-grid-button',  //button id
            'label' => __('Save'),  //button label
            'button_class' => 'widget-button-save',
            'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton', //class for splitting button
            'options' => $this->_getAddSaveButtonOptions(),
        ];
        $this->buttonList->add('add_new', $addButtonProps);        
    }

    /**
     * method to create dropdown for save button
     * @return array
     */
    protected function _getAddSaveButtonOptions(){
        $buttonOptions = [];
        $buttonOptions[] = [
            'id' => 'save-edit-button',
            'label' => __('Save & Continue Edit'),
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                ],
            ],
            'default' => true,
        ];

        $buttonOptions[] = [
            'id' => 'save-new-button',
            'label' => __('Save & New'),
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndNew', 'target' => '#edit_form'],
                ],
            ],
        ];

        $buttonOptions[] = [
            'id' => 'save-close-button',
            'label' => __('Save & Close'),
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']],
            ],
        ];
        return $buttonOptions;
    }

    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('singh_form_data')->getId()) {
            return __("Edit Post '%s'", $this->escapeHtml($this->_coreRegistry->registry('singh_form_data')->getTitle()));
        } else {
            return __('New Grid');
        }
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('grid/*/save', ['_current' => true, 'back' => null]);
    }
    
    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('grid/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content');
                }
            };
        ";
        return parent::_prepareLayout();
    }

}
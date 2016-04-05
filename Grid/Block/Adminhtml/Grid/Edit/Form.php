<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/4/2016
 * Time: 5:58 PM
 */

namespace Singh\Grid\Block\Adminhtml\Grid\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Adminhtml grid record edit form block
 * preparing form
 * declaring form id and action
 */
class Form extends Generic
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
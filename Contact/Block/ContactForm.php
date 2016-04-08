<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Singh\Contact\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */
class ContactForm extends Template
{
    /*
     * set variables for subject select options
     */
    const SUBJECT_OPTION_NAME = 'contact_subject';
    const SUBJECT_OPTION_ID = 'contact_subject';
    const SUBJECT_OPTION_TITLE = 'Subject';
    const SUBJECT_OPTION_CLASS = 'form-control input-text required-subject';
    
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('contact/index/post', ['_secure' => true]);
    }

    /**
     * Returns subject array url for contact form
     *
     * @return array
     */
    public function getContactSubject(){
        $subjectArray = array(
            array(
                'value' => "Comments; Complaints & Resolution Hotline",
                'label' => "Comments; Complaints & Resolution Hotline",
            ),
            array(
                'value' => "For inquiries regarding job openings",
                'label' => "For inquiries regarding job openings",
            ),
            array(
                'value' => "Help us by telling us about your recent Camping World experience",
                'label' => "Help us by telling us about your recent Camping World experience",
            ),
            array(
                'value' => "Need help logging on or have any question concerning our site?",
                'label' => "Need help logging on or have any question concerning our site?",
            ),
            array(
                'value' => "Questions and comments about your experience at our store",
                'label' => "Questions and comments about your experience at our store",
            ),
            array(
                'value' => "Questions about RV Dealerships and RV Inventory",
                'label' => "Questions about RV Dealerships and RV Inventory",
            ),
            array(
                'value' => "Questions about your Good Sam Club membership",
                'label' => "Questions about your Good Sam Club membership",
            ),
            array(
                'value' => "Questions about your RV products or installation",
                'label' => "Questions about your RV products or installation",
            ),
            array(
                'value' => "Questions regarding or placing an order",
                'label' => "Questions regarding or placing an order",
            ),
            array(
                'value' => "Suggestions, general questions or comments about our company",
                'label' => "Suggestions, general questions or comments about our company",
            )
        );
        $subjectOptions = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setName(
            self::SUBJECT_OPTION_NAME
        )->setId(
            self::SUBJECT_OPTION_ID
        )->setTitle(
            __(self::SUBJECT_OPTION_TITLE)
        )->setOptions(
            $subjectArray
        )->setClass(
            self::SUBJECT_OPTION_CLASS
        )->setExtraParams(
            'data-validate="{\'validate-select\':true}"'
        )->getHtml();
        
        return $subjectOptions;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 3/31/2016
 * Time: 1:39 PM
 */

namespace Singh\Databaseconnect\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory;
    
    public function __construct(
        Context $context,  //created object of Context
        PageFactory $resultPageFactory   //created object of PageFactory; allow to render our page
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $resultPageFactory = $this->resultPageFactory->create();   //store created page to variable
        //Add page title
        $resultPageFactory->getConfig()->getTitle()->set(__("Page title from Controller"));
        //Add breadcrumb
        /** @var \Magento\Theme\Block\Html\Breadcrumbs */
        $breadcrumb = $resultPageFactory->getLayout()->getBlock('breadcrumbs');
        $breadcrumb->addCrumb('home',
            [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->_url->getUrl('')
            ]
        );
        $breadcrumb->addCrumb('data_content',
            [
                'label' => __('Data'),
                'title' => __('Data')
            ]
        );
        //return page object
        return $resultPageFactory;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 15-04-2016
 * Time: 12:06
 */

namespace Singh\Customcheckout\Controller\Index;
//declare core files require in this file
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class Index extends \Magento\Framework\App\Action\Action
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
        $resultPageFactory->getConfig()->getTitle()->set(__("Hello Module"));
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
        $breadcrumb->addCrumb('example_hello',
            [
                'label' => __('Example'),
                'title' => __('Example')
            ]
        );
        //return page object
        return $resultPageFactory;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 3/30/2016
 * Time: 4:08 PM
 */

//declare namespace
namespace Camping\Wishlist\Controller\Index;
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
        $resultPageFactory->getConfig()->getTitle()->set(__("Hello Custom Wishlist"));        
        //return page object
        return $resultPageFactory;
    }
}
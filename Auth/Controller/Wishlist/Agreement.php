<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 12-05-2016
 * Time: 13:35
 */

namespace Camping\Auth\Controller\Wishlist;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class Agreement extends \Magento\Framework\App\Action\Action{

    protected $resultPageFactory;
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPageFactory = $this->resultPageFactory->create();   //store created page to variable
//        $resultPageFactory->getLayout()->createBlock('Magento\Framework\View\Element\Template')->setTemplate('Camping_Auth::wishlist/agreement.phtml')->toHtml();
        //Add page title
        $resultPageFactory->getConfig()->getTitle()->set(__("Wishlist Agreement"));
        // TODO: Implement execute() method.
        return $resultPageFactory;
    }
}
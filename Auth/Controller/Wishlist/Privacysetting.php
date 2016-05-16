<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 12-05-2016
 * Time: 18:09
 */

namespace Camping\Auth\Controller\Wishlist;

use Magento\Framework\App\Action;
//use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Controller\Result\JsonFactory;

class Privacysetting extends Action\Action{

    protected $_customerSession;
//    protected $_customer;
//    protected $resultPageFactory;
    protected $_resultJsonFactory;
    protected $_customerFactory;
    protected $_customerResourceFcatory;

    public function __construct(
        Action\Context $context,
//        PageFactory $pageFactory,
        Session $session,
        JsonFactory $resultJsonFactory
    )
    {
        $this->_customerSession = $session;
        $this->_resultJsonFactory = $resultJsonFactory;
//        $this->resultPageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        // TODO: Implement execute() method.
//        $resultPage = $this->resultPageFactory->create();
        $resultJson = $this->_resultJsonFactory->create();
        $privacyId = $this->getRequest()->getParam('privacyId');
        if ($this->_customerSession->isLoggedIn()){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

//            $this->_customer->load($this->_customerSession->getCustomerId());
            try{
//                $this->_customer->setData('wishlist_privacy',$privacyId);
//                $this->_customer->save();
                $customer = $objectManager->create('Magento\Customer\Model\Customer')->load(1);
                $customer->setWishlistPrivacy($privacyId);
                $customer->save();
                $resultJson->setData(
                    ['success' => true]
                );
            }catch (InputException $e) {
                $this->messageManager->addException($e, __('Invalid input'));
                $resultJson->setData(
                    ['success' => false],
                    ['message' => $e.'Invalid input']
                );
            } catch (\Exception $e) {
                $message = __('We can\'t save the customer.')
                    . $e->getMessage()
                    . '<pre>' . $e->getTraceAsString() . '</pre>';
                $this->messageManager->addException($e, $message);
                $resultJson->setData(
                    ['success' => false],
                    ['message' => $message]
                );
            }
        }
        return $resultJson;
    }
}
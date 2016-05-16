<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Camping\Auth\Controller\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Catalog\Controller\Product\View\ViewInterface;

class Logout extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var Session	
     */
    protected $session;

	 protected $cart;
	
    /**
     * @param Context $context
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
		 CustomerCart $cart,
        Session $customerSession
    ) {
        $this->session = $customerSession;
		$this->cart = $cart;
        parent::__construct($context);
    }

    /**
     * Customer logout action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $lastCustomerId = $this->session->getId();
        $this->session->setcartFlag('set');      
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$objectManager->get('Camping\Auth\Helper\Cookie')->set('cart_flag', 3600);
        $cookieManager = $objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
        
        // set cookie value
        //$cookieManager->setPublicCookie('cart_flag', 'set');
        setcookie('cart_flag', 'set');
        var_dump($cookieManager->getCookie('cart_flag'));
        
        $this->session->logout()->setBeforeAuthUrl($this->_redirect->getRefererUrl())
            ->setLastCustomerId($lastCustomerId);

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/logoutSuccess');
        return $resultRedirect;
    }
}

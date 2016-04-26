<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 20-04-2016
 * Time: 10:43
 */

namespace Camping\Wishlist\Block\Customer;

class Wishlist extends \Magento\Wishlist\Block\Customer\Wishlist{

    /**
     * extending layout for setting title
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        //parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__($this->getCurrentCustomerName()."'s Wish List"));
    }
    
    /**
     * get current customer name
     * 
     * @return string
     */
    protected function getCurrentCustomerName(){
        return $this->currentCustomer->getCustomer()->getFirstname();
    }
}
<?php

namespace Camping\Auth\Block\Account;



use Magento\Framework\View\Element\Template;

class LogoutSuccess extends Template {
	
	
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}	
	
	public function getBaseUrl()
	{
		return $this->_storeManager->getStore()->getBaseUrl();
	}

}
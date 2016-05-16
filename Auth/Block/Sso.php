<?php
namespace Camping\Auth\Block;

use \Magento\Customer\Helper\View;
use Camping\Auth\Helper\Data;
use \Magento\Framework\App;


class Sso extends \Magento\Framework\View\Element\Template
{
	
	public function getSsoConfig(){
		return $this->_scopeConfig->getValue('campingworld/general/sso', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getBaseUrl(){
		return $this->_storeManager->getStore()->getBaseUrl();
	}
	
}
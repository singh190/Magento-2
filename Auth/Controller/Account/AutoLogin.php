<?php

namespace Camping\Auth\Controller\Account;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;

class AutoLogin extends \Magento\Framework\App\Action\Action {
	protected $resultPageFactory;
	protected $accountRedirect;
	protected $scopeConfig;
	/**
	 *
	 * @param \Magento\Framework\App\Action\Context $context        	
	 * @param
	 *        	\Magento\Framework\View\Result\PageFactory resultPageFactory
	 */
	public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, AccountRedirect $accountRedirect, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
		$this->resultPageFactory = $resultPageFactory;
		$this->accountRedirect = $accountRedirect;
		$this->_scopeConfig = $scopeConfig;
		parent::__construct ( $context );
	}
	
	
	/**
	 * Default customer account page
	 *
	 * @return void
	 */
	public function execute() { 
		
		if(isset($_COOKIE ['__CS'])){
			$token = $_COOKIE ['__CS'];	
		}else{
		$resultRedirect = $this->resultRedirectFactory->create ();
		$resultRedirect->setPath ( 'customer/account/' );
		return $resultRedirect;
		}
		
		if(isset($_COOKIE ['lastpage'])){
			$lastpage = $_COOKIE ['lastpage'];	
		}
		
		if (empty ( $token )) {
			die ( "No Token Received" );
		}
		$headers = array (
				"Authorization: $token" 
		);
		$url = $this->getSsoConfig () . "getprofile.php";
		
		$params = array (
				"dummy" => "dummy" 
		); 
		$response = $this->curlPost ( $url, $params, $headers );
		
		$email = $response ['data'] ['Username'];
		$websiteId = $this->_objectManager->create ( '\Magento\Store\Model\StoreManagerInterface' )->getWebsite ()->getWebsiteId ();
		$customerFactory = $this->_objectManager->create ( '\Magento\Customer\Model\Customer' );
		$customerFactory->setWebsiteId ( $websiteId );
		$customer = $customerFactory->loadByEmail ( $email );
		if (empty ( $customer->getId () )) {
			$customer = $this->createUser ( $response );
		}
		$customer = ($this->isGoodsamMember ( $response )) ? $customer->setGroupId ( \Camping\Auth\Controller\Account\LoginPost::GOODSAM_MEMBER_CUSTOMER_GROUP_ID )->save () : $customer->setGroupId ( \Camping\Auth\Controller\Account\LoginPost::DEFAULT_CUSTOMER_GROUP_ID )->save ();
		$session = $this->_objectManager->create ( '\Magento\Customer\Model\Session' );
		$session->loginById ( $customer->getId () );
		$session->regenerateId ();
		$resultRedirect = $this->resultRedirectFactory->create ();
		if(isset($lastpage)){
			//$resultRedirect->setPath ( $lastpage );
			 $resultRedirect = $this->resultRedirectFactory->create();            
			 $url = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(1)->getBaseUrl().'customercart/';
			 
			 if($lastpage == "https://qa.campingworld.com/customercart/"){
				$resultRedirect->setUrl($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(1)->getBaseUrl().'checkout/index/index');	
			 }else{
				 $resultRedirect->setPath ( $lastpage );
			 }
		}else{
			$resultRedirect->setPath ( 'customer/account/' );	
		}
		
		return $resultRedirect;
	}
	public function curlPost($url, $params, $headers = null) {
		$curl = curl_init ( $url );
		curl_setopt ( $curl, CURLOPT_HEADER, false );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		if (! empty ( $headers )) {
			curl_setopt ( $curl, CURLOPT_HTTPHEADER, $headers );
		}
		curl_setopt ( $curl, CURLOPT_POST, true );
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, $params );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
		$json_response = curl_exec ( $curl );
		$status = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
		if ($status != 200) {
			die ( "Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error ( $curl ) . ", curl_errno " . curl_errno ( $curl ) );
		}
		curl_close ( $curl );
		$response = json_decode ( $json_response, true );
		if ($response == null) {
			var_dump ( $json_response );
		}
		return $response;
	}
	public function createUser($ssfoResponse) {
		$password = mt_rand ( 100000, 999999 );
		$websiteId = $this->_objectManager->create ( '\Magento\Store\Model\StoreManagerInterface' )->getWebsite ()->getWebsiteId ();
		$customer = $this->_objectManager->create ( '\Magento\Customer\Model\Customer' );
		$customer->setWebsiteId ( $websiteId );
		$customer->setEmail ( $ssfoResponse ['data'] ["Email"] );
		$customer->setFirstname ( empty ( $ssfoResponse ['data'] ["FirstName"] ) ? array_shift ( explode ( "@", $ssfoResponse ['data'] ["Email"] ) ) : $ssfoResponse ['data'] ["FirstName"] );
		$customer->setLastname ( empty ( $ssfoResponse ['data'] ["LastName"] ) ? array_shift ( explode ( "@", $ssfoResponse ['data'] ["Email"] ) ) : $ssfoResponse ['data'] ["LastName"] );
		$customer->setPassword ( $password );
		$customer->save ();
		return $customer;
	}
	public function getSsoConfig() {
		return $this->_scopeConfig->getValue ( 'campingworld/general/sso', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
	}
	public function isGoodsamMember($profileInfo) {
		$isGoodsamClubMember = false;
		if (isset ( $profileInfo ['ContactId'] )) {
			$contactid = $profileInfo ['ContactId'];
			$dt = new \DateTime ();
			$dt->setTimeZone ( new \DateTimeZone ( 'UTC' ) );
			$timestamp = $dt->format ( 'Y-m-d\TH:i:s.\0\0\0\Z' );
			
			$request = '{
				 "EnterpriseSystemIdentifier": {
				    "IdentifierSystem": "Salesforce",
				    "IdentifierType": "Customer",
				    "Ids": [
				      "' . $contactid . '"
				    ]
				  },
				  "SourceApplicationTimestampUtc": "' . $timestamp . '",
				  "SourceApplicationName": "ConsumerWebsite"
				}';
			
			$helper = $this->_objectManager->get ( '\Camping\Auth\Helper\Data' );
			$url = $helper->serviceBaseUrl () . 'Membership/Search';
			$email = $login ['username'];
			$jsonarray = $helper->httpPost ( $url, $request );
			
			if (isset ( $jsonarray ['Memberships'] )) {
				foreach ( $jsonarray ['Memberships'] as $membership ) {
					if ($membership ['ClubCode'] == 'GoodSamClub') {
						$isGoodsamClubMember = true;
						$helper->gsc_membership_details ( $membership );
					}
					if ($membership ['ClubCode'] == 'RoadsideAssistanceGoodSam') {
						$isramember = 1;
						$helper->ra_membership_details ( $membership );
					}
					if ($membership ['ClubCode'] == 'TravelAssistGoodSam') {
						$istamember = 1;
						$helper->ta_membership_details ( $membership );
					}
					if ($membership ['ClubCode'] == 'ESPGoodSam') {
						$isespmember = 1;
						$helper->esp_membership_details ( $membership );
					}
				}
			}
		}
		return $isGoodsamClubMember;
	}
}

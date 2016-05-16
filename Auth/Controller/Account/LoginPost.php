<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Camping\Auth\Controller\Account;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory; 

	
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoginPost extends \Magento\Customer\Controller\AbstractAccount {
	
	const GOODSAM_MEMBER_CUSTOMER_GROUP_ID = '4';
	const DEFAULT_CUSTOMER_GROUP_ID = '1';
	
	

	/**
	 *
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
	
	/**
	 *
	 * @var \Magento\Customer\Model\CustomerFactory
	 */
	protected $customerFactory;
	
	/**
	 *
	 * @var AccountManagementInterface
	 */
	protected $customerAccountManagement;
	
	/**
	 *
	 * @var Validator
	 */
	protected $formKeyValidator;
	
	/**
	 *
	 * @var AccountRedirect
	 */
	protected $accountRedirect;
	
	/**
	 * 
	 * @var Session
	 */
	protected $session;
	
	
	protected $scopeConfig;
	
	
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $_customerSession;
	/**
	 *
	 * @param Context $context        	
	 * @param Session $customerSession        	
	 * @param AccountManagementInterface $customerAccountManagement        	
	 * @param CustomerUrl $customerHelperData        	
	 * @param Validator $formKeyValidator        	
	 * @param AccountRedirect $accountRedirect        	
	 */
	public function __construct(Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Model\CustomerFactory $customerFactory, Session $customerSession, AccountManagementInterface $customerAccountManagement, CustomerUrl $customerHelperData, Validator $formKeyValidator, AccountRedirect $accountRedirect,\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
		$this->storeManager = $storeManager;
		$this->customerFactory = $customerFactory;
		$this->session = $customerSession;
		$this->customerAccountManagement = $customerAccountManagement;
		$this->customerUrl = $customerHelperData;
		$this->formKeyValidator = $formKeyValidator;
		$this->accountRedirect = $accountRedirect;
		$this->_scopeConfig  = $scopeConfig;
		$this->_customerSession = $customerSession;
		parent::__construct ( $context );
	}
	
	/**
	 * Login post action
	 *
	 * @return \Magento\Framework\Controller\Result\Redirect @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public function execute() { 
		$url =  $this->getBaseUrl(); 
		$address = parse_url($url);
		$path = $address['path'];
		$login = $this->getRequest ()->getPost ( 'login' );
		$postData = $this->getRequest()->getPostValue();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$totalProducts = $objectManager->get('Magento\Checkout\Helper\Cart')->getItemsCount();

		 $url = $this->getSsoConfig()."loginsalesforce.php";  
		$params = array (
				"username" => $login ['username'],
				"password" => $login ['password'] 
		);
		$lasturl = $login['lasturl'];
		$curl = curl_init ( $url );
		curl_setopt ( $curl, CURLOPT_HEADER, false );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl, CURLOPT_POST, true );
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, $params );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
		$json_response = curl_exec ( $curl );
		
		$status = curl_getinfo ( $curl, CURLINFO_HTTP_CODE ); 
		
		if ($status != 200) {
			$this->_customerSession->setLoginErrMessage('1');
			$message = __ ( '<div class="signin_error col-lg-10 col-lg-offset-1 col-md-10">
                        <span class="errorDesk">
                            <span class="hidden-xs">Sorry, we could not find the email/password combination you entered.<br>If you require password assistance, please click the "Forgot Password" link below.</span>
                            <span class="hidden-sm hidden-md hidden-lg">Incorrect email address or password</span>
                        </span>
		
                    </div>' );
			$this->messageManager->addError ( $message );
			$this->session->setUsername ( $login ['username'] );
				
			$resultRedirect = $this->resultRedirectFactory->create();
			$resultRedirect->setUrl($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(1)->getBaseUrl().'customer/account/login/');
			return $resultRedirect;
		}
		$isGoodsamClubMember = false;
		curl_close ( $curl );
		$response = json_decode ( $json_response, true );
		
		if (isset ( $response ['data'] ['security_token'] )) {
			
			
			if(isset($postData['rememberme'])){
				$rememberme = $postData['rememberme'];
				if($rememberme == 1){  
					ini_set('session.cookie_lifetime',604800);
				}else{ 
					ini_set('session.cookie_lifetime',0);
				}
			}
			
			if ($this->getRequest ()->isPost ()) {
				if (! empty ( $login ['username'] ) && ! empty ( $login ['password'] )) {  
					try {
						$websiteId = $this->storeManager->getWebsite ()->getWebsiteId ();
						$customerFactory = $this->customerFactory->create ();
						$customerFactory->setWebsiteId ( $websiteId ); 
						$customer = $customerFactory->loadByEmail ( $login ['username'] );
						
						$token = $response ['data'] ['security_token'];
						$profileInfo = $this->getProfile ( $token );

						/**
						 * Goodsam member ship details
						 */

			
			$helper = $objectManager->get('Camping\Auth\Helper\Data');
									
			if (isset($profileInfo['ContactId'])) {
				$contactid = $profileInfo['ContactId'];

				$dt = new \DateTime();
				$dt->setTimeZone(new \DateTimeZone('UTC'));
				$timestamp = $dt->format('Y-m-d\TH:i:s.\0\0\0\Z');
						
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
								
				$url = $helper->serviceBaseUrl().'Membership/Search';
				$email = $login ['username'];
				$jsonarray = $helper->httpPost($url, $request);
						
							if (isset($jsonarray['Memberships'])) {
								foreach ($jsonarray['Memberships'] as $membership) {
									if ($membership['ClubCode'] == 'GoodSamClub') {
										$isGoodsamClubMember = true;
										$helper->gsc_membership_details($membership);
									}
									if ($membership['ClubCode'] == 'RoadsideAssistanceGoodSam') {
										$isramember = 1;
										$helper->ra_membership_details($membership);
									}
									if ($membership['ClubCode'] == 'TravelAssistGoodSam') {
										$istamember = 1;
										$helper->ta_membership_details($membership);
									}
									if ($membership['ClubCode'] == 'ESPGoodSam') {
										$isespmember = 1;
										$helper->esp_membership_details($membership);
									}
								}
							}
						}
						
						
						if (empty ( $customer->getId () )) {    
							 $token = $response ['data'] ['security_token'];  
							
							$profileInfo = $this->getProfile ( $token );
							
							if ( $totalProducts) { 
								echo "<script>
								require(['jquery'],function($){
									$(document).ready(function() {
										$('#success-msg').addClass('in');
										$('success-msg').css('display','block');
										$('body').addClass('modal-open');  
										$('body').append('<div class='modal-backdrop fade'></div>');
									});
								}); 
								</script>";
								echo "
								<div class='modal fade' id='success-msg' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
									<div class='modal-dialog modal-lg signin_success' role='document'>
										<div class='modal-content'>
											<div class='modal-body'>
												<h4 class='successmsg text-center'>Success!</h4>
												<p>Cart has been merged</p>
													<div class='form-group' align='center'>
														<a href='<?php echo $block->getUrl(''); ?>'><input type='button' value='Go to Home' class='btn yellow-btn'></a>
													</div>
											</div>
										</div>
									</div>
								</div>";

							}
							
							$newCustomer = $this->createUser ( $profileInfo, $login ['password'],$isGoodsamClubMember );
							$this->session->loginById ( $newCustomer->getId () );
							$this->session->regenerateId ();
						} else { 
							$this->session->loginById ( $customer->getId () );
							if ($isGoodsamClubMember) {
								$customer->setGroupId ( self::GOODSAM_MEMBER_CUSTOMER_GROUP_ID ); // for goodsam members only
								$customer->save ();
							}

							$this->session->regenerateId ();
						}
		
						$cookie_value = $response ['data'] ['security_token'];
						$profileInfo = $this->getProfile ( $cookie_value );
						$this->session->setProfileData($profileInfo);
						
						setcookie ( "__CS", $cookie_value, time () + (86400 * 30), "$path" );
						setcookie ( "newlogin", 1, time () + (86400 * 30), "$path" );
						
					} catch ( EmailNotConfirmedException $e ) {
						$value = $this->customerUrl->getEmailConfirmationUrl ( $login ['username'] );
						$message = __ ( 'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.', $value );
						$this->messageManager->addError ( $message );
						$this->session->setUsername ( $login ['username'] );
					} catch ( AuthenticationException $e ) {
						$this->messageManager->addError ( __ ( 'Authentications error.' ) );
					} catch ( \Exception $e ) {
						$this->messageManager->addError ( __ ( 'An unspecified error occurred. Please contact us for assistance.' ) );
					}
				} else { 
					$this->messageManager->addError ( __ ( 'A login and a password are required.' ) );
				}
			}
		} else { 
			
			$this->_customerSession->setLoginErrMessage('1');
			$message = __ ( '<div class="signin_error col-lg-10 col-lg-offset-1 col-md-10">
                        <span class="errorDesk">
                            <span class="hidden-xs">Sorry, we could not find the email/password combination you entered.<br>If you require password assistance, please click the "Forget Password" link below.</span>
                            <span class="hidden-sm hidden-md hidden-lg">Incorrect email address or password</span>
                        </span>
			
                    </div>' );
			$this->messageManager->addError ( $message );
			$this->session->setUsername ( $login ['username'] );
			$resultRedirect = $this->resultRedirectFactory->create();
		   $resultRedirect->setUrl($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(1)->getBaseUrl().'customer/account/login/');
		   //if from guest page 
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $baseUrl = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
           ->getStore(1)
           ->getBaseUrl(); 
      $guesturl= $baseUrl.'campingworld/account/guestlogin';
		
        $referer = $_SERVER['HTTP_REFERER'];
		 $referer = rtrim($referer,"/");
		 if ($guesturl == $referer)
		 {
	         $resultRedirect = $this->resultRedirectFactory->create();
            
 $resultRedirect->setUrl($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(1)->getBaseUrl().'campingworld/account/guestlogin');
           }   
       //$this->_objectManager->get('Psr\Log\LoggerInterface')->info($referer);
		return $resultRedirect;
		}
		 
		$resultRedirect = $this->resultRedirectFactory->create();
		$reUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(1)->getBaseUrl();
		if(isset($_SESSION['reviewDirectUrl']))  {
			$reUrl = $_SESSION['reviewDirectUrl'];
			$resultRedirect->setUrl($reUrl);
			return $resultRedirect;
		}
		
		if(isset($_SESSION['questionDirectUrl']))  {
			$reUrl = $_SESSION['questionDirectUrl'];
			$resultRedirect->setUrl($reUrl);
			return $resultRedirect;
		}
		if(isset($_SESSION['answerDirectUrl']))  {
			$reUrl = $_SESSION['answerDirectUrl'];
			$resultRedirect->setUrl($reUrl);
			return $resultRedirect;
		}
		
		$resultRedirect->setUrl($lasturl);
		//check for source controller url; if from guestlogin then redirect to checkout page
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$baseUrl = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
           ->getStore(1)
           ->getBaseUrl(); 
        $guesturl= $baseUrl.'campingworld/account/guestlogin';
	     $referer = $_SERVER['HTTP_REFERER'];
		 $referer = rtrim($referer,"/");
		 if ($guesturl == $referer)
		 {			 
	        $resultRedirect = $this->resultRedirectFactory->create();
            //$resultRedirect->setUrl('checkout/index/index');
			$resultRedirect->setUrl($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(1)->getBaseUrl().'checkout');
            return $resultRedirect;
		}
		
		$resultRedirect = $this->resultRedirectFactory->create();
		 $resultRedirect->setUrl($lasturl);
		 return $resultRedirect;
	}
	
	public function getProfile($accessToken) { 
		 $url = $this->getSsoConfig()."getprofile.php";
		
		$params = array (
				"username" => 'test' 
		);
		
		$curl = curl_init ( $url );
		curl_setopt ( $curl, CURLOPT_HEADER, false );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		
		curl_setopt ( $curl, CURLOPT_POST, true );
		
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, $params );
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, array (
				"Authorization: $accessToken" 
		) );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
		$json_response = curl_exec ( $curl );
		curl_close ( $curl );
		$response =  json_decode ( $json_response, true );
		
		if (! isset ( $response ['data'] ) || ($response ['status_code'] != 200)) {
			return false;
		}
		
		return $response['data'];
	}
	
	public function createUser($ssfoResponse, $password,$isGoodsamClubMember) {
		
		
		if(isset($ssfoResponse['Country'])){
			if($ssfoResponse['Country'] == 'United States'){
				$ssfoResponse['Country'] = 'US';
			}elseif($ssfoResponse['Country'] == 'Canada'){
				$ssfoResponse['Country'] = 'CA';
			}
		}
		
		$listedStates = $this->getRegiondId($ssfoResponse['Country']);
		$count = count($listedStates['available_regions']); 
		for($i=0;$i<$count;$i++){
			if($listedStates['available_regions'][$i]['name'] == 'Alabama'){
				$regionId =  $listedStates['available_regions'][$i]['id']; 
			}
		}
		
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$websiteId = $this->storeManager->getWebsite ()->getWebsiteId ();
		
		$customer = $this->customerFactory->create ();
		$customer->setWebsiteId ( $websiteId );
		$customer->setEmail ( $ssfoResponse ["Username"] );
		$customer->setFirstname ( $ssfoResponse ["FirstName"]?$ssfoResponse ["FirstName"]:'first');
		$customer->setLastname ( $ssfoResponse ["LastName"]?$ssfoResponse ["LastName"]:'last' );
		$customer->setPassword ( $password );

		$customer->save ();
		
		/**
		 * Address save Informations
  		 */
		$address = $objectManager->get('Magento\Customer\Model\Address');
		$address->setCustomerId($customer->getId())
        ->setFirstname($customer->getFirstname())
        ->setMiddleName($customer->getMiddlename())
        ->setLastname($customer->getLastname());
		if(!empty($ssfoResponse ["Country"])){
			$address->setCountryId($ssfoResponse ["Country"]?$ssfoResponse ["Country"]:'');
		}
		if(!empty($regionId)){
			$address->setRegionId($regionId ? $regionId:'');
		}
		if(!empty($ssfoResponse ["PostalCode"])){		
			$address->setPostcode($ssfoResponse ["PostalCode"]?$ssfoResponse ["PostalCode"]:'');
        }
		if(!empty($ssfoResponse ["City"])){
			$address->setCity($ssfoResponse ["City"]?$ssfoResponse ["City"]:'');
		}	
		if(!empty($ssfoResponse ["MobilePhone"])){
			$address->setTelephone($ssfoResponse ["MobilePhone"]?$ssfoResponse ["MobilePhone"]:'');
		}
		if(!empty($ssfoResponse ["Street"])){
		$address->setStreet(  array (
        	'0' => $ssfoResponse ["Street"]?$ssfoResponse ["Street"]:'',
        	//'1' => $ssfoResponse ["address2"]?$ssfoResponse ["address2"]:'',
    	 ) );
		
		$address->setIsDefaultBilling('1');
        $address->setIsDefaultShipping('1');
        $address->setSaveInAddressBook('1');
			try{
				$address->save();
			}
			catch (Exception $e) {
				$e->getMessage();
			}
		} 

		if ($isGoodsamClubMember) {
			$customer->setGroupId ( self::GOODSAM_MEMBER_CUSTOMER_GROUP_ID );
		}
		return $customer;
	}
	
	public function getSsoConfig(){
		return $this->_scopeConfig->getValue('campingworld/general/sso', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getRegiondId($countryId){
			$baseUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(1)->getBaseUrl();
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $baseUrl."rest/V1/directory/countries/".$countryId);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			$json_response = curl_exec($ch);
			curl_close($ch);
			
			$response = json_decode($json_response,true);
			if($response == null){
				return "No Response";
			}
			return $response;
	}
	
	/**
	 * Images Storage base URL
	 *
	 * @return string
	 */
	public function getBaseUrl(){
		return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(1)->getBaseUrl();
	}
}

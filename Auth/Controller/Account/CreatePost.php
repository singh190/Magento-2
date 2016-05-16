<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Camping\Auth\Controller\Account;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Helper\Address;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Customer\Model\Registration;
use Magento\Framework\Escaper;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreatePost extends \Magento\Customer\Controller\AbstractAccount
{
    /** @var AccountManagementInterface */
    protected $accountManagement;

    /** @var Address */
    protected $addressHelper;

    /** @var FormFactory */
    protected $formFactory;

    /** @var SubscriberFactory */
    protected $subscriberFactory;

    /** @var RegionInterfaceFactory */
    protected $regionDataFactory;

    /** @var AddressInterfaceFactory */
    protected $addressDataFactory;

    /** @var Registration */
    protected $registration;

    /** @var CustomerInterfaceFactory */
    protected $customerDataFactory;

    /** @var CustomerUrl */
    protected $customerUrl;

    /** @var Escaper */
    protected $escaper;

    /** @var CustomerExtractor */
    protected $customerExtractor;

    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;

    /** @var DataObjectHelper  */
    protected $dataObjectHelper;

    protected $_customerSession;
    
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var AccountRedirect
     */
    private $accountRedirect;
	
    protected $scopeConfig;
    
	/**
	 *
     * @var \Magento\Customer\Model\CustomerFactory
	 */
	protected $customerFactory;
	
    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param AccountManagementInterface $accountManagement
     * @param Address $addressHelper
     * @param UrlFactory $urlFactory
     * @param FormFactory $formFactory
     * @param SubscriberFactory $subscriberFactory
     * @param RegionInterfaceFactory $regionDataFactory
     * @param AddressInterfaceFactory $addressDataFactory
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param CustomerUrl $customerUrl
     * @param Registration $registration
     * @param Escaper $escaper
     * @param CustomerExtractor $customerExtractor
     * @param DataObjectHelper $dataObjectHelper
     * @param AccountRedirect $accountRedirect
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $accountManagement,
        Address $addressHelper,
        UrlFactory $urlFactory,
        FormFactory $formFactory,
        SubscriberFactory $subscriberFactory,
        RegionInterfaceFactory $regionDataFactory,
        AddressInterfaceFactory $addressDataFactory,
        CustomerInterfaceFactory $customerDataFactory,
        CustomerUrl $customerUrl,
        Registration $registration,
        Escaper $escaper,
        CustomerExtractor $customerExtractor,
        DataObjectHelper $dataObjectHelper,
    	\Magento\Customer\Model\Session $customerSession,
    	\Magento\Catalog\Model\Session $catalogSession,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
        AccountRedirect $accountRedirect
    ) {
        $this->session = $customerSession;
		$this->customerFactory = $customerFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->accountManagement = $accountManagement;
        $this->addressHelper = $addressHelper;
        $this->formFactory = $formFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->regionDataFactory = $regionDataFactory;
        $this->addressDataFactory = $addressDataFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerUrl = $customerUrl;
        $this->registration = $registration;
        $this->escaper = $escaper;
        $this->customerExtractor = $customerExtractor;
        $this->urlModel = $urlFactory->create();
        $this->dataObjectHelper = $dataObjectHelper;
        $this->accountRedirect = $accountRedirect;
        $this->scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;

        parent::__construct($context);
    }

    /**
     * Add address to customer during create account
     *
     * @return AddressInterface|null
     */
    protected function extractAddress()
    {
        if (!$this->getRequest()->getPost('create_address')) {
            return null;
        }

        $addressForm = $this->formFactory->create('customer_address', 'customer_register_address');
        $allowedAttributes = $addressForm->getAllowedAttributes();

        $addressData = [];

        $regionDataObject = $this->regionDataFactory->create();
        foreach ($allowedAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $value = $this->getRequest()->getParam($attributeCode);
            if ($value === null) {
                continue;
            }
            switch ($attributeCode) {
                case 'region_id':
                    $regionDataObject->setRegionId($value);
                    break;
                case 'region':
                    $regionDataObject->setRegion($value);
                    break;
                default:
                    $addressData[$attributeCode] = $value;
            }
        }
        $addressDataObject = $this->addressDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $addressDataObject,
            $addressData,
            '\Magento\Customer\Api\Data\AddressInterface'
        );
        $addressDataObject->setRegion($regionDataObject);

        $addressDataObject->setIsDefaultBilling(
            $this->getRequest()->getParam('default_billing', false)
        )->setIsDefaultShipping(
            $this->getRequest()->getParam('default_shipping', false)
        );
        return $addressDataObject;
    }

    /**
     * Create customer account action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(){ 
    
    	$postData = $this->getRequest()->getPostValue();
    	$resultRedirect = $this->resultRedirectFactory->create();
       
        $this->session->regenerateId();
       
        try {        	
            $password = $this->getRequest()->getParam('password');
            $confirmation = $this->getRequest()->getParam('password');
            $redirectUrl = $this->session->getBeforeAuthUrl();
            
             $this->checkPasswordConfirmation($password, $confirmation); 
           
             $gscLinkContact = $this->session->getGscLinkContact();
             $mailErrors = $this->session->getMailErrors();
             if(isset($mailErrors)){
             	$this->session->unsMailErrors();
             }
            
            if(isset($gscLinkContact)){
            	$postData['contact'] = $gscLinkContact;
            	//$this->session->unsGscLinkContact();
            }
           
            $response = $this->userRegister($postData);
           
          
            
            $data = $this->handleCurlResponse($response);
            $errordata = $data;
            $username = $this->getRequest()->getParam('email');
           
            $this->session->unsMailErrors();
			                               
            if(count($data)>0){
            	//foreach($data as $errordata){                                        
            		if(isset($errordata['error_code'])){
            			
            			if($errordata['error_code'] == 'OR001'){
            				$this->session->setMailErrors('Invalid email address.');                      
            			}
            			if($errordata['error_code'] == 'OR002'){
            				$this->session->setMailErrors('Email Id too large.');
            			}
            			if($errordata['error_code'] == 'OR003'){
            				$this->session->setMailErrors('Email address already registered.');
            				
            			}
            			if($errordata['error_code'] == 'OR004'){
            				$this->session->setMailErrors('Please enter your email address.');
            			}
            			if($errordata['error_code'] == 'OR005'){  
            				$this->session->setPassErrors('Password complexity not met.');
            			}
            			if($errordata['error_code'] == 'OR006'){
            				$this->session->setPassErrors('Password Field is mandatory.');
            			}
            			if($errordata['error_code'] == 'OR007'){
            				$this->session->setZipErrors('Zip code length not met.');
            			}
            			if($errordata['error_code'] == 'OR008'){
            				$this->session->setZipErrors('Please enter a valid ZIP code.');
            			}
            			if($errordata['error_code'] == 'OR009'){
            				$this->session->setZipErrors('Please enter a ZIP/Postal code.');
            			}
            			if($errordata['error_code'] == 'OR010'){
            				$this->session->setFirstNameErrors('First Name should have atleast one characters.');
            			}
            			if($errordata['error_code'] == 'OR012'){
            				$this->session->setFirstNameErrors('First Name should have atleast one characters.');
            			}
            			if($errordata['error_code'] == 'OR014'){
            				$this->session->setLastNameErrors('Address Line 1 is too long.');
            			}
            			if($errordata['error_code'] == 'OR015'){
            				$this->session->setLastNameErrors('Address Line 2 is too long.');
            			}
            			if($errordata['error_code'] == 'OR018'){
            				$this->session->setHomePhoneErrors('Should be a valid Home Phone Number.');
            			}
            			if($errordata['error_code'] == 'OR019'){
            				$this->session->setMobilePhoneErrors('Should be a valid Mobile Phone Number.');
            			}
            		}	
            	//}
            }
           
           
            /**
             * Goodsam member ship details
             */
            
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $objectManager->get('Camping\Auth\Helper\Data');
            
         if(isset($response['status_code'])){
	    	if($response['status_code'] == 200 ){
	    		$responseVal = $this->login($username,$password);
	    		
	    		/**
	    		 * Set Cookie Value
	    		 */
	    		$url =  $this->getBaseUrl();
	    		$address = parse_url($url);
	    		$path = $address['path'];
	    		
	    		if(isset($responseVal ['data']['security_token'])){
	    			$token = $responseVal ['data']['security_token'];
	    			setcookie ( "__CS", $token, time () + (86400 * 30), "$path" );
	    			setcookie ( "newlogin", 1, time () + (86400 * 30), "$path" );
	    		}
	    		
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	            $websiteId = $this->storeManager->getWebsite ()->getWebsiteId ();
					
					$customer = $this->customerFactory->create ();
					$customer->setWebsiteId ( $websiteId );
					$customer->setEmail ( $postData ["email"] );
					$customer->setFirstname ( $postData ["firstname"]?$postData ["firstname"]:'first');
					$customer->setLastname ( $postData ["lastname"]?$postData ["lastname"]:'last' );
					$customer->setPassword ( $postData["password"] );
					$customer->setPostcode ( $postData["postcode"] );
					
					$customer->save ();
					
					
							
					if(isset($postData['street'][0])){
						$street1 = $postData['street'][0];
					}
					
					if(isset($postData['region_id'])){
						if(is_numeric($postData['region_id'])){
							$region = $this->_objectManager->create('\Magento\Directory\Model\Region')->load($postData['region_id']);
							$data['state'] = $region->getName();
						}else{
							$regionModel = $this->_objectManager->create('\Magento\Directory\Model\Region')->loadByCode($postData['region_id'], $postData['country_id']);
							$regionId = $regionModel->getId();
							$region = $this->_objectManager->create('\Magento\Directory\Model\Region')->load($regionId);
							$data['state'] = $region->getName();
						}
					}
					
					/**
					 * address save Information
					 */
					$address = $objectManager->get('Magento\Customer\Model\Address');
					$address->setCustomerId($customer->getId())
			        ->setFirstname($customer->getFirstname())
			        ->setMiddleName($customer->getMiddlename())
			        ->setLastname($customer->getLastname());
					
					$address->setCountryId($postData ["country_id"]?$postData ["country_id"]:'');
					
					if(!empty($postData ["region_id"])){
						$address->setRegionId($postData ["region_id"]?$postData ["region_id"]:''); 
					}
					
					$address->setPostcode($postData ["postcode"]?$postData ["postcode"]:'');
			        
					if(!empty($postData ["city"])){
						$address->setCity($postData ["city"]?$postData ["city"]:'');
					}
					if(!empty($postData ["telephone"])){
						$address->setTelephone($postData ["telephone"]?$postData ["telephone"]:'');		
					}
					if(!empty($street1) || !empty($postData ["address2"])){
					$address->setStreet(  array (
			        	'0' => $street1?$street1:'',
			        	'1' => $postData ["address2"]?$postData ["address2"]:'',
			    	 ) );
					 
					$address->setIsDefaultBilling('1');
			        $address->setIsDefaultShipping('1');
					$address->setSaveInAddressBook('1');
					 
					try{
						$address->save();
					}catch (Exception $e) {
						echo $e->getMessage();
					}
					 
				}
				
             	//$customer = $this->accountManagement->createAccount($customer, $password, $redirectUrl);
             	
             	$profileInfo = $this->getProfile ( $token );
             	$this->session->setProfileData($profileInfo);
				
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
             		$email = $username;
             		$jsonarray = $helper->httpPost($url, $request);
             		/*
             		 if (!isset($jsonarray['Memberships'])) {
             		 $jsonarray = $helper->gsc_dummy_membership($email);
             		 }
             		*/
             		if (isset($jsonarray['Memberships'])) {
             			foreach ($jsonarray['Memberships'] as $membership) {
             				if ($membership['ClubCode'] == 'GoodSamClub') {
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
             	
             	if(isset($gscLinkContact)){
             		$this->session->unsGscLinkDetails();
             	}
             	
             	$this->session->setNoErrors('1');
             }else{

             	//$this->messageManager->addError($response['error'][0]['error_message']);
             	$url = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
             	$resultRedirect->setUrl($this->_redirect->error($url));
             	return $resultRedirect;
             }
        }else{
        	$this->session->setMailErrors('There was an error processing the request');
        	$defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
        	$resultRedirect->setUrl($this->_redirect->error($defaultUrl));
        	return $resultRedirect;
        }

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $this->subscriberFactory->create()->subscribeCustomerById($customer->getId());
            }
            
			
            $this->_eventManager->dispatch(
                'customer_register_success',
                ['account_controller' => $this, 'customer' => $customer]
            );
			
           
            
            $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
            if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                $email = $this->customerUrl->getEmailConfirmationUrl($customer->getEmail());
                // @codingStandardsIgnoreStart
                $this->messageManager->addSuccess(
                    __(
                        'You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.',
                        $email
                    )
                );
                // @codingStandardsIgnoreEnd
                $url = $this->urlModel->getUrl('*/*/index', ['_secure' => true]);
                $resultRedirect->setUrl($this->_redirect->success($url));
            } else {
				$this->session->loginById ( $customer->getId () );
				$this->session->regenerateId ();
                $resultRedirect = $this->accountRedirect->getRedirect();
			
            }
			  //if from guest page user 
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
		//end from guest page user 
            return $resultRedirect;
        } catch (StateException $e) {
            $url = $this->urlModel->getUrl('customer/account/forgotpassword');
            // @codingStandardsIgnoreStart
            $message = __(
                'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.',
                $url
            );
            // @codingStandardsIgnoreEnd
            $this->messageManager->addError($message);
        } catch (InputException $e) {
            $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addError($this->escaper->escapeHtml($error->getMessage()));
            }
        } catch (\Exception $e) {
        	echo $e->getMessage();
            $this->messageManager->addException($e, __('We can\'t save the customer.'));
        }

        
        $this->session->setCustomerFormData($this->getRequest()->getPostValue());
        $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
        $resultRedirect->setUrl($this->_redirect->error($defaultUrl));
        return $resultRedirect;
    }

    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password
     * @param string $confirmation
     * @return void
     * @throws InputException
     */
    protected function checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            throw new InputException(__('Please make sure your passwords match.'));
        }
    }

    /**
     * Retrieve success message
     *
     * @return string
     */
    protected function getSuccessMessage()
    {
        if ($this->addressHelper->isVatValidationEnabled()) {
            if ($this->addressHelper->getTaxCalculationAddressType() == Address::TYPE_SHIPPING) {
                // @codingStandardsIgnoreStart
                $message = __(
                    'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your shipping address for proper VAT calculation.',
                    $this->urlModel->getUrl('customer/address/edit')
                );
                // @codingStandardsIgnoreEnd
            } else {
                // @codingStandardsIgnoreStart
                $message = __(
                    'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your billing address for proper VAT calculation.',
                    $this->urlModel->getUrl('customer/address/edit')
                );
                
            }
        } 
    }
    
    
    public function userRegister($postData){
		
    	
    	
    	$data = array();

    	if(isset($postData['firstname'])){
    		$data['first_name'] = $postData['firstname'];
    	}
    	if(isset($postData['lastname'])){
    		$data['last_name'] = $postData['lastname'];
    	}
		
    	if(isset($postData['street'][0])){
    		$data['address1'] = $postData['street'][0];
    	}
    	if($postData['address2']){
    		$data['address2'] = $postData['address2'];
    	}
    	
    	if($postData['postcode']){
    		$data['zip_code'] = $postData['postcode'];
    	}
    	
    	if($postData['city']){
    		$data['city'] = $postData['city'];
    	}
		
		if(isset($postData['country_id'])){
			if($postData['country_id'] == 'US'){
				$data['country'] = 'United States';
			}elseif($postData['country_id'] == 'CA'){
				$data['country'] = 'Canada';
			}
		}
		/*
    	if($postData['country_id']){
    		$data['country'] = $postData['country_id'];
    	} 
    	*/
    	
    	if(isset($postData['region_id'])){

    		$region = $this->_objectManager->create('\Magento\Directory\Model\Region')->load($postData['region_id']);
    		$data['state'] = $region->getName();
    		
    	}
    	
    	if($postData['home_phone']){
    		$data['home_phone'] = $postData['home_phone'];
    	}
    	
    	if($postData['telephone']){
    		$data['mobile_phone'] = $postData['telephone'];
    	}
    	
    	if($postData['email']){
    		$data['email'] = $postData['email'];
    	}
    	
    	if($postData['password']){
    		$data['password'] = $postData['password'];
    	}
    	
    	
    	
    	if(!empty($postData['contact'])){
    		$data['contact_id'] = $postData['contact'];
    		$url = $this->getSsoConfig()."registercontactuser.php";
    	} else{
    		$url = $this->getSsoConfig()."registeruser.php";
    	}
    	
    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_HEADER, false);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($curl, CURLOPT_POST, true);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    	$json_response = curl_exec($curl);
    	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    	if ( $status != 200 ) {
    		return "Error";
    	}
    	curl_close($curl);
    	$response = json_decode($json_response, true);
    	if($response == null){
    		return "No Response";
    	}
    	
    	return $response;
    }
    

    
    public function login($username,$password){
    	
    	$url =  $this->getBaseUrl();
    	$address = parse_url($url);
    	$path = $address['path'];
    	    	 
    	$url = $this->getSsoConfig()."loginsalesforce.php"; 
    	$params = array (
    			"username" => $username,
    			"password" => $password
    	);
    	
    	$curl = curl_init ( $url );
    	curl_setopt ( $curl, CURLOPT_HEADER, false );
    	curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
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
    	/*
		if($response['status_code'] == 400 ){
			$this->session->setMailErrors('Invalid username, password, security token.');   
		}else{
		*/
    		if(isset($response ['data'] ['security_token'])){
				$cookie_value = $response ['data'] ['security_token'];
				setcookie ( "__CS", $cookie_value, time () + (86400 * 30), "$path" );
				setcookie ( "newlogin", 1, time () + (86400 * 30), "$path" );
    		}
			return $response;	
		//}
    }
    
    public function getSsoConfig(){
    	return $this->scopeConfig->getValue('campingworld/general/sso', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getMemberConfig(){
    	return $this->scopeConfig->getValue('campingworld/contact/member', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Images Storage base URL
     *
     * @return string
     */
    public function getBaseUrl(){
    	return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(1)->getBaseUrl();
    }
    
    
    private function httpPost($url, $postdata){
    	$username='BA7FF50C4D8849808F1524C88479DC52';
    	$password='B628DD6798834EDB94FD6C4D44C48C4A';
    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_HEADER, false);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    	$headers=$this->getHeader();
    	if(!empty($headers)){
    		curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
    	}
    	curl_setopt($curl, CURLOPT_POST, true);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    	curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
    	$json_response = curl_exec($curl);
    	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    	if ( $status != 200 ) {
    		die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    	}
    	curl_close($curl);
    	$response = json_decode($json_response, true);
    
    	if($response == null){var_dump($json_response);}
    	return $response;
    }
    
    function getHeader(){
    	$header = array('Content-Type: application/json','Accept: application/json');
    	return $header;
    }
    
    function curlPost($url,$params, $headers=null){
    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_HEADER, false);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    	if(!empty($headers)){
    		curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
    	}
    	curl_setopt($curl, CURLOPT_POST, true);
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    	$json_response = curl_exec($curl);
    	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    	if ( $status != 200 ) {
    		die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    	}
    	curl_close($curl);
    	$response = json_decode($json_response, true);
    	if($response == null){var_dump($json_response);}
    	return $response;
    }
    
    function handleCurlResponse($response){ 
		$resultRedirect = $this->resultRedirectFactory->create();
    	if(isset($response['error'])){
    		return $response['error'];
    	}
    	if(isset($response['status_code'])){
    		if($response['status_code']==200){
    			return $response['data'];
    		}
    	}else{
    		$this->session->setMailErrors('there was an error processing the request');
    		$defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
        	$resultRedirect->setUrl($this->_redirect->error($defaultUrl));
        	return $resultRedirect; 
    	}
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
}

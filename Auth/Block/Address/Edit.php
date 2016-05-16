<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Camping\Auth\Block\Address;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session;
use Photon\Hawk\Helper\DeviceDetect as deviceFactory;
/**
 * Customer address edit block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */	
class Edit extends \Magento\Directory\Block\Data
{
	/**
	 * @var \Camping\Usermanagement\Model\Post
	 */
	protected $_post;
	
    /**
     * @var \Magento\Customer\Api\Data\AddressInterface|null
     */
    protected $_address = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $_addressRepository;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    protected $addressDataFactory;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;
    
    
    protected $_objectManager;
    
    /**
     * @var Session
     */
    protected $session;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
    	\Camping\Usermanagement\Model\ResourceModel\Post\CollectionFactory $post,
    	\Magento\Framework\ObjectManagerInterface $objectManager,
    	Session $customerSession,
        array $data = [],
    	deviceFactory $deviceFactory
    ) {
    	
    	$prefix = "";
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    	
    		if ($deviceFactory->isMobile ()) {
    			$prefix = "mb_";
    			$template = "Camping_Auth::address/" . $prefix . "edit.phtml";
    			$this->setTemplate ( $template );
    		}else if ($deviceFactory->isTablet ()) {
				$prefix = "mb_";	
    			$template = "Camping_Auth::address/" . $prefix . "edit.phtml";
    			$this->setTemplate ( $template );
    		} else{
    			$template = "Camping_Auth::address/" . $prefix . "edit.phtml";
    			$this->setTemplate ( $template );
    		}
    	
			
  
        $this->_customerSession = $customerSession;
        $this->_addressRepository = $addressRepository;
        $this->addressDataFactory = $addressDataFactory;
        $this->currentCustomer = $currentCustomer;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_objectManager = $objectManager;
        $this->session = $customerSession;
        $this->_post = $post;
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $data
        );
    }

    /**
     * Prepare the layout of the address edit block.
     *
     * @return $this
     */
    

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        if ($addressId = $this->getRequest()->getParam('id')) {
            try {
                $this->_address = $this->_addressRepository->getById($addressId);
                if ($this->_address->getCustomerId() != $this->_customerSession->getCustomerId()) {
                    $this->_address = null;
                }
            } catch (NoSuchEntityException $e) {
                $this->_address = null;
            }
        }

        if ($this->_address === null || !$this->_address->getId()) {
            $this->_address = $this->addressDataFactory->create();
            $customer = $this->getCustomer();
           	
            $this->_address->setPrefix($customer->getPrefix());
            $this->_address->setFirstname($customer->getFirstname());
            $this->_address->setMiddlename($customer->getMiddlename());
            $this->_address->setLastname($customer->getLastname());
            $this->_address->setSuffix($customer->getSuffix());
            
        }

        $this->pageConfig->getTitle()->set($this->getTitle());

        if ($postedData = $this->_customerSession->getAddressFormData(true)) {
            if (!empty($postedData['region_id']) || !empty($postedData['region'])) {
                $postedData['region'] = [
                    'region_id' => $postedData['region_id'],
                    'region' => $postedData['region'],
                ];
            }
            $this->dataObjectHelper->populateWithArray(
                $this->_address,
                $postedData,
                '\Magento\Customer\Api\Data\AddressInterface'
            );
        }
        return $this;
    }

    /**
     * Generate name block html.
     *
     * @return string
     */
    public function getNameBlockHtml()
    {
        $nameBlock = $this->getLayout()
            ->createBlock('Magento\Customer\Block\Widget\Name')
            ->setObject($this->getAddress());

        return $nameBlock->toHtml();
    }

    /**
     * Return the title, either editing an existing address, or adding a new one.
     *
     * @return string
     */
    public function getTitle()
    {
        if ($title = $this->getData('title')) {
            return $title;
        }
        if ($this->getAddress()->getId()) {
            $title = __('Edit Address');
        } else {
            $title = __('Add New Address');
        }
        return $title;
    }

    /**
     * Return the Url to go back.
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getData('back_url')) {
            return $this->getData('back_url');
        }

        if ($this->getCustomerAddressCount()) {
            return $this->getUrl('customer/address');
        } else {
            return $this->getUrl('customer/account/');
        }
    }

    /**
     * Return the Url for saving.
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->_urlBuilder->getUrl(
            'customer/address/formPost',
            ['_secure' => true, 'id' => $this->getAddress()->getId()]
        );
    }

    /**
     * Return the associated address.
     *
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * Return the specified numbered street line.
     *
     * @param int $lineNumber
     * @return string
     */
    public function getStreetLine($lineNumber)
    {
    	
        $street = $this->_address->getStreet();
        return isset($street[$lineNumber - 1]) ? $street[$lineNumber - 1] : '';
    }

    /**
     * Return the country Id.
     *
     * @return int|null|string
     */
    public function getCountryId()
    {
        if ($countryId = $this->getAddress()->getCountryId()) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    /**
     * Return the name of the region for the address being edited.
     *
     * @return string region name
     */
    public function getRegion()
    {
        $region = $this->getAddress()->getRegion();
        return $region === null ? '' : $region->getRegion();
    }

    /**
     * Return the id of the region being edited.
     *
     * @return int region id
     */
    public function getRegionId()
    {
        $region = $this->getAddress()->getRegion();
        return $region === null ? 0 : $region->getRegionId();
    }

    /**
     * Retrieve the number of addresses associated with the customer given a customer Id.
     *
     * @return int
     */
    public function getCustomerAddressCount()
    {
        return count($this->getCustomer()->getAddresses());
    }
    
    
    public function getCustomerAddresscustom()
    {
    	return $this->getCustomer()->getAddresses();
    }
    

    /**
     * Determine if the address can be set as the default billing address.
     *
     * @return bool|int
     */
    public function canSetAsDefaultBilling()
    {
        if (!$this->getAddress()->getId()) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultBilling();
    }

    /**
     * Determine if the address can be set as the default shipping address.
     *
     * @return bool|int
     */
    public function canSetAsDefaultShipping()
    {
        if (!$this->getAddress()->getId()) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultShipping();
    }

    /**
     * Is the address the default billing address?
     *
     * @return bool
     */
    public function isDefaultBilling()
    {
        return (bool)$this->getAddress()->isDefaultBilling();
    }

    /**
     * Is the address the default shipping address?
     *
     * @return bool
     */
    public function isDefaultShipping()
    {
        return (bool)$this->getAddress()->isDefaultShipping();
    }

    /**
     * Retrieve the Customer Data using the customer Id from the customer session.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        return $this->currentCustomer->getCustomer();
    }


    /**
     * Return back button Url, either to customer address or account.
     *
     * @return string
     */
    public function getBackButtonUrl()
    {
        if ($this->getCustomerAddressCount()) {
            return $this->getUrl('customer/address');
        } else {
            return $this->getUrl('customer/account/');
        }
    }

    /**
     * Get config value.
     *
     * @param string $path
     * @return string|null
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    
    public function getProfileInfo(){
    
    	$token = $_COOKIE ['__CS'];
    	if (empty ( $token )) {
    		die ( "No Token Received" );
    	}
    	$headers = array (
    			"Authorization: $token"
    	);
    	
    	 $url = $this->getSsoConfig()."getprofile.php"; 
    	
    	$params = array (
    			"dummy" => "dummy"
    	);
    	return  $this->curlPost ( $url, $params, $headers );
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
    		//var_dump ( $json_response );
    	}
    	return $response;
    }
    
    /**
     * Images Storage base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
    	$baseurl =  $this->_storeManager->getStore()->getBaseUrl();
    	return $baseurl.'userinfo/edit/editpost/';
    }
    
    
    public function getFieldConfig(){
    	$FieldConfig = $this->_post->create()->addFieldToFilter('title','editaddress');
    	$item = $FieldConfig->load();
    	return $item->getData();
    }
    
    public function getSsoConfig(){
    	return $this->_scopeConfig->getValue('campingworld/general/sso', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getDefaultBaseUrl(){
    	return $this->_storeManager->getStore()->getBaseUrl();	
    }    
	public function getRegId($state,$country){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    	$region = $objectManager->create('\Magento\Directory\Model\Region')->loadByName($state,$country);
    	return $region->getId();
	}
	
    public function getEditSucessMessage(){
    	$editSuccessMessage = $this->session->getData();
    	if(isset($editSuccessMessage['edit_success_message'])){
    		return $editSuccessMessage['edit_success_message'];
    	}
    }
    

    
    public function unsEditSucessMessage(){
    	return  $this->session->unsEditSuccessMessage();
    }
    
    public function setUserName(){
    	
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    	$context = $objectManager->get('Magento\Framework\App\Http\Context');
    	/** @var bool $isLoggedIn */
    	$isLoggedIn = $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    	if($isLoggedIn) {
    			$customerName = $objectManager->get('Camping\Auth\Block\Account\Dashboard\Info');
    			return $customerName->getName();
    			//$this->session->setUserName($name);
    	}
	}
	
	public function getUserName(){
		$this->setUserName();
		$userName = $this->session->getData();
		if(isset($userName['user_name'])){
			return $userName['user_name'];
		}
	}



	public function getNoErrors(){
		$errors = $this->session->getData();
		if(isset($errors['no_errors'])){
			return $errors['no_errors'];
		}
	}
	
	
	public function sessionCheck(){

		$errors = $this->session->getNoErrors();
		if(isset($errors)){
			$this->session->unsNoErrors();
		}
		 
	}
	
	public function unsuserInfoErrors(){
		$this->session->getUserInfoErrors();
	}
	
	
	public function setUserInfoErrors(){
		$userInfoErrors = $this->session->getData();
		if(isset($userInfoErrors['user_info_errors'])){
			return $userInfoErrors['user_info_errors'];
		}
	}
	
	/*** Communication Preferences ***/
	public function getComPreference(){
		$preferences = $this->mainPreference();
		//echo "<pre>"; print_r($preferences['body']['Categories']);
		$m = 0;
		foreach ($preferences['body']['Categories'] as $p=>$pre)
		{
			$main_preference[$m]['MP']= $pre['CategoryName'];
			foreach ($pre['Preferences'] as $s=>$sub )
			{
				$main_preference[$m]['SP'][$s] = $sub['Description'];
			}
			$m++;
		}
		return $main_preference;
		//print_r($main_preference);
		//exit;
	}
	
	public function mainPreference()
	{
		$accesstoken = 'ACS '.$this->authToken();
		$url = "http://ky-web-svcs-cert.cwweb.local:52000/api/pages/39";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array('Content-Type: application/json','Accept: application/json','Authorization:' .$accesstoken);
		if(!empty($headers)){
			curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
		}
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_POST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		// curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ( $status != 200 ) {
			//die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
			return '';
		}
		curl_close($curl);
		$response = json_decode($json_response, true);
	
		if($response == null){ 
			//var_dump($json_response);
		}
		return $response;
	
	
	}
	
	public function authToken()
	{
		define ('identityProvider_name','ACS');
		define ('identityProvider_endpoint','https://cwiservices.accesscontrol.windows.net/WRAPv0.9');
		// Relying Party
		define ('relyingParty_realm','https://ws-cert.campingworld.com/ems/v1');
		define ('relyingParty_endpoint','http://ky-web-svcs-cert.cwweb.local:52000/api/');
		// Service Identity
		define ('serviceIdentity_name','prabu.kalaiselvam@photoninfotech.net');
		define ('serviceIdentity_key','L3en1FutzOSiiqTkA+3fqPJbXBRDhdrMkImlOV3Takk=');
		define ('serviceIdentity_signature', 'Cw9XGyZ4vVAxmZYSZhBT1y1Wa5sboJIPmJo1/7cqDw8=');
		$issuer = 'Issuer='.urlencode(serviceIdentity_name);
		$signature = serviceIdentity_signature;
		$authenticationRequestToken = $issuer.'&HMACSHA256='.urlencode($signature);
		// Post Data
		$postdata  = "wrap_assertion_format=SWT";
		$postdata .= "&wrap_scope=".relyingParty_realm;
		$postdata .= "&wrap_assertion=".urlencode($authenticationRequestToken);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, identityProvider_endpoint);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/x-www-form-urlencoded',
					'User-Agent: node.js EmsClient')
			);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			$output = curl_exec($ch);
			curl_close($ch);	
				
				if($output){
					$encodedSecurityToken = "";
					$securityToken = "";
					$responseArray = explode('&', $output);
					foreach ($responseArray as $value){
						$fieldParts = explode('=', $value);
						if (strtolower($fieldParts[0]) == "wrap_access_token") {
							$encodedSecurityToken = $fieldParts[1];
						}
						$securityToken = urldecode($encodedSecurityToken);
					}
					return $securityToken;
				}
				else {
					$response = curl_error($ch);
					return $response;
				}	
					
			
	}
	   /*** Communication Preferences ***/
	
	/** GSC Member ship starts ***/
	
	public function getGscMembership(){
		$gscMembership = $this->session->getData();
		if(isset($gscMembership['gsc_membership'])){
			return $gscMembership['gsc_membership'];
		}
	}
	
	
	public function getRaMembership(){
		$raMembership = $this->session->getData();
		if(isset($raMembership['ra_membership'])){
			return $raMembership['ra_membership'];
		}
	}
	
	
	public function getTaMembership(){
		$taMembership = $this->session->getData();
		if(isset($taMembership['ta_membership'])){
			return $taMembership['ta_membership'];
		}
	}
	
	public function getEspMembership(){
		$espMembership = $this->session->getData();
		if(isset($espMembership['esp_membership'])){
			return $espMembership['esp_membership'];
		}
	}
	
	
	public function setPassErrors(){
		$passErrors = $this->session->getData();
		if(isset($passErrors['pass_errors'])){
			return $passErrors['pass_errors'];
		}
	}
	
	function getHeader(){
		$header = array('Content-Type: application/json','Accept: application/json');
		return $header;
	}
	
	public function getEMSData(){
		$result = array();
		$profileInfo = $this->session->getProfileData();
		$userEmailAddressId = $profileInfo['UserEmailAddressId'];
		
		$preference_rv_club = $this->email_Get("https://services.stg.freedomroads.local:44401/EMS/api/"."Pages/31");
		$other_preferences =  $this->email_Get("https://services.stg.freedomroads.local:44401/EMS/api/"."Pages/41");
		if(isset($other_preferences["body"]["Categories"])){
			$preferences = array_merge($preference_rv_club["body"]["Categories"],$other_preferences["body"]["Categories"]);
			$user_preferences =  $this->email_Get("https://services.stg.freedomroads.local:44401/EMS/api/"."EmailAddresses/$userEmailAddressId"."/UserPreferences")['body'];
			
			$result = $this->map_user_preference($preferences, $user_preferences);
		}
	
		return $result;
	}
	
	public function email_Get($url){
		$username='BA7FF50C4D8849808F1524C88479DC52';
		$password='B628DD6798834EDB94FD6C4D44C48C4A';
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers=$this->getHeader();
		if(!empty($headers)){
			curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
		}
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$response['Status'] = $status;
		if ( $status != 200 ) {
			$response["body"] = "Error";
			return $response;
		}
		curl_close($curl);
		$response = json_decode($json_response, true);
		return $response;
	}
	
	/*
	 * Map the user preference with the preference list
	 */
	public function map_user_preference($preferences, $user_preferences){
		
		if(!is_array($user_preferences)){
			$user_preferences = array();
		}
		
		$preference_ids 	= array_column($user_preferences, 'PreferenceId');
		$preference_values 	= array_column($user_preferences, 'PreferenceValue');
		$user_preference_id = array_column($user_preferences, 'UserPreferenceId');
		$lastUpdateSource = array_column($user_preferences, 'LastUpdateSource');
		$final_preference_list = array();

		foreach($preferences as $category){
			foreach($category['Preferences'] as $preference){
				$prefId = '';
				$prefValue  = '';
				$last_update_s = '';
			    
				$key =  array_search($preference['PreferenceId'],$preference_ids );
				if($key) {
					$prefId    	 	= $user_preference_id[$key];
					$prefValue   	= $preference_values[$key];
					$last_update_s	= $lastUpdateSource[$key];
				}
				$final_preference_list[$category['CategoryName']][$preference['PreferenceId']] = array('desc'=>$preference['Description'],
											                                                           'UserPreferenceId'=>$prefId,
																									   'PreferenceValue' =>$prefValue,
																									   'LastUpdateSource'=>$last_update_s
																									   );
			}
		}
		return $final_preference_list;
	}
	
	public function getMenuFlag(){
		return $this->session->getMenuFlag();
	}
	
	public function unsMenuFlag(){
		return $this->session->unsMenuFlag();
	}
	
	public function unsNoErrors(){
		return $this->session->unsNoErrors();
	}
	

	
}	

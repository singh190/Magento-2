<?php 
namespace Camping\Auth\Helper;

use Magento\Framework\App\Action\Action;
use Camping\Usermanagement\Model\Post;
use Magento\Customer\Model\Session;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	
    /**
     * @var \Camping\Usermanagement\Model\Post
     */
    protected $_post;

    protected $scopeConfig;
    
    /**
     * @var Session
     */
    protected $session;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Camping\Usermanagement\Model\Post $post
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Post $post,\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, Session $customerSession
    )
    {
    	$this->session = $customerSession;
        $this->_post = $post;
        $this->_scopeConfig = $scopeConfig;
    }
    
    public function ssoDir(){
    	return $this->_scopeConfig->getValue('campingworld/general/sso', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    

    public function setChangePasswordError(){
    	$errors = $this->session->getData();
    	if(isset($errors['change_password_error'])){
    		return $errors['change_password_error'];
    	}
    }
    

	public function sessionCheck() {
		$errors = $this->session->getChangePasswordError();
		if(isset($errors)){
			$this->session->unsChangePasswordError();
		}
    }
    
	public function serviceBaseUrl(){
    	return $this->_scopeConfig->getValue('campingworld/contact/member', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getRVProfilePath(){
    	return $this->_scopeConfig->getValue('rvprofile/general/rvprofile_path', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
	
    public function getRVProfile(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $objectManager->get('Magento\Customer\Model\Session');
		$customer = $customerSession->getCustomer();
		$email = $customer->getEmail();
		$url= $this->getRVProfilePath()."rest/get_rv?email=".$email;
		
		return $this->curlPost($url);
	}
    
    public function curlPost($url, $headers = null) {
    	 
    	$curl = curl_init ( $url );
    	curl_setopt ( $curl, CURLOPT_HEADER, false );
    	curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
    	if (! empty ( $headers )) {
    		curl_setopt ( $curl, CURLOPT_HTTPHEADER, $headers );
    	}
    	//curl_setopt ( $curl, CURLOPT_POST, true );
    	curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
    	$json_response = curl_exec ( $curl );
    	$status = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
    	/*
    	if ($status != 200) {
    		die ( "Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error ( $curl ) . ", curl_errno " . curl_errno ( $curl ) );
    	}
    	*/
    	curl_close ( $curl );
    	$response = json_decode ( $json_response, true );
    	if ($response == null) {
    		//var_dump ( $json_response );
    	}
    	
    	return $response;
    }
    
    
    public function getRVType(){
    	$url= $this->getRVProfilePath()."rest/get_rv_terms";
    	return $this->curlPost($url);
    }
    
    
    public function httpPost($url, $postdata){
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
    	
    	if($response == null){
    		//var_dump($json_response);
    	}
    	return $response;
    }
    
    function getHeader(){
    	$header = array('Content-Type: application/json','Accept: application/json');
    	return $header;
    }
    
    /**
     * function used for dummy membership configuration
     */
    function gsc_dummy_membership($email){
    	$usermailid = $email; 
    	if ($usermailid == 'sggopalmohan@gmail.com') {
    		$json = '{
								"Memberships": [
								   {
									  "ClubName":"Good Sam",
									  "ClubCode":"GoodSamClub",
									  "MemberNumber":11111111,
									  "MemberType":"GS REGULAR",
									  "BenefitLevel":"Classic",
									  "MemberSince":"2010-12-07T00:00:00-07:00",
									  "ExpireDate":"2015-12-23T00:00:00-07:00",
									  "IsAutoRenew":true,
									  "ExtendedData":{
    
									  }
								   },
								   {
									  "ClubName": "Roadside Assistance - Good Sam",
									  "ClubCode": "RoadsideAssistanceGoodSam",
									  "SourceSystem": "GoodSam",
									  "MemberNumber": "222222222",
									  "MemberType": "AUTO + RV STANDARD",
									  "MemberTypeId": 605,
									  "BenefitLevel": "Elite",
									  "MemberSince": "2012-12-15T00:00:00-07:00",
									  "ExpireDate": "2016-01-13T00:00:00-07:00",
									  "IsAutoRenew": true,
									  "ExtendedData": {}
									}
								]
							}
							';
    		$isgscmember = 1;
    		$expiryday = "2015-12-23T00:00:00-07:00";
    	} else if ($usermailid == 'deluxemember1@gmail.com') {
    		$json = '{
								"Memberships": [
								   {
									  "ClubName":"Good Sam",
									  "ClubCode":"GoodSamClub",
									  "MemberNumber":22222222,
									  "MemberType":"GS REGULAR",
									  "BenefitLevel":"Deluxe",
									  "MemberSince":"2010-12-07T00:00:00-07:00",
									  "ExpireDate":"2016-01-23T00:00:00-07:00",
									  "IsAutoRenew":true,
									  "ExtendedData":{
    
									  }
								   },
								   {
									  "ClubName": "Roadside Assistance - Good Sam",
									  "ClubCode": "RoadsideAssistanceGoodSam",
									  "SourceSystem": "GoodSam",
									  "MemberNumber": "222222222",
									  "MemberType": "AUTO + RV STANDARD",
									  "MemberTypeId": 605,
									  "BenefitLevel": "Elite",
									  "MemberSince": "2012-12-15T00:00:00-07:00",
									  "ExpireDate": "2016-03-13T00:00:00-07:00",
									  "IsAutoRenew": true,
									  "ExtendedData": {}
									},
									{
									  "ClubName": "Travel Assistance - Good Sam",
									  "ClubCode": "TravelAssistanceGoodSam",
									  "SourceSystem": "GoodSam",
									  "MemberNumber": "55555555",
									  "MemberType": "TA-GS Base",
									  "MemberTypeId": 605,
									  "BenefitLevel": "BASIC",
									  "MemberSince": "2012-12-15T00:00:00-07:00",
									  "ExpireDate": "2016-02-20T00:00:00-07:00",
									  "IsAutoRenew": true,
									  "ExtendedData": {}
									}
								]
							}
							';
    		$isgscmember = 1;
    		$expiryday = "2016-01-23T00:00:00-07:00";
    	} else if ($usermailid == 'elite@gmail.com') {
    		$json = '{
								"Memberships": [
								   {
									  "ClubName":"Good Sam",
									  "ClubCode":"GoodSamClub",
									  "MemberNumber":33333333,
									  "MemberType":"GS REGULAR",
									  "BenefitLevel":"Elite",
									  "MemberSince":"2010-12-07T00:00:00-07:00",
									  "ExpireDate":"2025-12-23T00:00:00-07:00",
									  "IsAutoRenew":true,
									  "ExtendedData":{
    
									  }
								   },
								   {
									  "ClubName": "Roadside Assistance - Good Sam",
									  "ClubCode": "RoadsideAssistanceGoodSam",
									  "SourceSystem": "GoodSam",
									  "MemberNumber": "222222222",
									  "MemberType": "AUTO + RV STANDARD",
									  "MemberTypeId": 605,
									  "BenefitLevel": "Elite",
									  "MemberSince": "2012-12-15T00:00:00-07:00",
									  "ExpireDate": "2026-01-13T00:00:00-07:00",
									  "IsAutoRenew": true,
									  "ExtendedData": {}
									}
								]
							}
							';
    		$isgscmember = 1;
    		$expiryday = "2025-12-23T00:00:00-07:00";
    	} else if ($usermailid == 'life@gmail.com') {
    		$json = '{
								"Memberships": [
								   {
									  "ClubName":"Good Sam",
									  "ClubCode":"GoodSamClub",
									  "MemberNumber":44444444,
									  "MemberType":"GS REGULAR",
									  "BenefitLevel":"Life",
									  "MemberSince":"2010-12-07T00:00:00-07:00",
									  "ExpireDate":"2025-12-23T00:00:00-07:00",
									  "IsAutoRenew":true,
									  "ExtendedData":{
    
									  }
								   },
								   {
									  "ClubName": "Roadside Assistance - Good Sam",
									  "ClubCode": "RoadsideAssistanceGoodSam",
									  "SourceSystem": "GoodSam",
									  "MemberNumber": "222222222",
									  "MemberType": "AUTO + RV STANDARD",
									  "MemberTypeId": 605,
									  "BenefitLevel": "Elite",
									  "MemberSince": "2012-12-15T00:00:00-07:00",
									  "ExpireDate": "2025-01-13T00:00:00-07:00",
									  "IsAutoRenew": true,
									  "ExtendedData": {}
									},
									{
									  "ClubName": "Travel Assistance - Good Sam",
									  "ClubCode": "TravelAssistanceGoodSam",
									  "SourceSystem": "GoodSam",
									  "MemberNumber": "55555555",
									  "MemberType": "TA-GS Premier",
									  "MemberTypeId": 605,
									  "BenefitLevel": "PREMIER",
									  "MemberSince": "2012-12-15T00:00:00-07:00",
									  "ExpireDate": "2025-01-28T00:00:00-07:00",
									  "IsAutoRenew": true,
									  "ExtendedData": {}
									},
									{
									  "ClubName": "Extended Service Plan",
									  "ClubCode": "ESPGoodSam",
									  "SourceSystem": "GoodSam",
									  "MemberNumber": "55555555",
									  "MemberType": "Elite",
									  "MemberTypeId": 605,
									  "BenefitLevel": "Elite",
									  "MemberSince": "2012-12-15T00:00:00-07:00",
									  "ExpireDate": "2025-01-28T00:00:00-07:00",
									  "IsAutoRenew": true,
									  "ExtendedData": {}
									}
								]
							}
							';
    		$isgscmember = 1;
    		$expiryday = "2025-12-23T00:00:00-07:00";
    	} else if ($usermailid == 'nongsc@gmail.com') {
    		$json = '';
    		$widgetmembericon = 'https://images3.campingworld.com/CampingWorld/images/V2/GS/common/gsmember.png';
    		$isgscmember = 0;
    	}
    	$gscjsonarray = json_decode($json, true);
    	return $gscjsonarray;
    }
    
    
    /**
     * To fetch the Goodsam membership details
     */
    function gsc_membership_details($membership){
    	
    	$expiryday = "0001-01-01T00:00:00-00:00";
    	$isgscmember = 0;
    	$gscjsonarray = $membership;
    	$isgscmember = 1;
    	$expiryday = $membership['ExpireDate'];
    
    	$now = time(); // or current datetime
    	$your_date = strtotime($expiryday);
    	$datediff = $your_date - $now;
    	$expiresindays = (floor($datediff / 86400));
    	$gscstatus = "expired";
    	if (isset($gscjsonarray)) {
    		$json = json_encode($gscjsonarray);
    	} else {
    		$json = '';
    	}
    	/**
    	 * in GRACE PERIOD
    	 */ 
    	if ($expiresindays < 0 && $expiresindays >= -60) {
    		$gscstatus = "renew"; 
    	} else if ($expiresindays > 0) { // in Active
    		$gscstatus = "active";
    	}
    	
    	
    	$gsc_membership = $this->session->getGscMembership();
    	if (isset($gsc_membership)) {
    		$this->session->unsGscMembership();
    	}
    	$this->session->setGscMembership($json);
    	
    	
    	$isgscclubmember = $this->session->getIsGscClubMember();
    	if (isset($isgscclubmember)) {
    		$this->session->unsIsGscClubMember();
    	}
    	$this->session->setIsGscClubMember($isgscmember);
    	
    	

    	$gsc_status = $this->session->getGscStatus();
    	if (isset($gsc_status)) {
    		$this->session->unsGscStatus();
    	}
    	$this->session->setGscStatus($gscstatus);
    	
    }
    
    
    /**
     * To fetch the Roadside Assistance membership details
     */
    function ra_membership_details($membership) {
    	$expiryday = "0001-01-01T00:00:00-00:00";
    	$isramember = 0;
    	$rajsonarray = $membership;
    	$isramember = 1;
    	$expiryday = $membership['ExpireDate'];
    	$ra_membertypeid = $membership['MemberTypeId'];
    	$now = time(); // or current datetime
    	$your_date = strtotime($expiryday);
    	$datediff = $your_date - $now;
    	$expiresindays = (floor($datediff / 86400));
    	$rastatus = "expired";
    	if (isset($rajsonarray)) {
    		$json = json_encode($rajsonarray);
    	} else {
    		$json = '';
    	}
    
    	if ($expiresindays < 0 && $expiresindays >= -60) { // in GRACE PERIOD
    		$rastatus = "renew";
    	} else if ($expiresindays > 0) { // in Active
    		$rastatus = "active";
    	}
    
    	

    	$ra_membership = $this->session->getRaMembership();
    	if (isset($ra_membership)) {
    		$this->session->unsRaMembership();
    	}
    	$this->session->setRaMembership($json);
    	

    	$israclubmember = $this->session->getIsRaClubMember();
    	if (isset($israclubmember)) {
    		$this->session->unsIsRaClubMember();
    	}
    	$this->session->setIsRaClubMember($isramember);

    
    	$ra_status = $this->session->getRaStatus();
    	if (isset($ra_status)) {
    		$this->session->unsRaStatus();
    	}
    	$this->session->setRaStatus($rastatus);

    

    	$ramembertypeid = $this->session->getRaMemberTypeId();
    	if (isset($ramembertypeid)) {
    		$this->session->unsRaMemberTypeId();
    	}
    	$this->session->setRaMemberTypeId($ra_membertypeid);
  
    }
    
    
    /**
     * To fetch the Travel Assistance membership details
     */
    function ta_membership_details($membership) {
    	$expiryday = "0001-01-01T00:00:00-00:00";
    	$istamember = 0;
    	$tajsonarray = $membership;
    	$istamember = 1;
    	$expiryday = $membership['ExpireDate'];
    	$ta_membertypeid = $membership['MemberTypeId'];
    	$now = time(); // or current datetime
    	$your_date = strtotime($expiryday);
    	$datediff = $your_date - $now;
    	$expiresindays = (floor($datediff / 86400));
    	$tastatus = "expired";
    	if (isset($tajsonarray)) {
    		$json = json_encode($tajsonarray);
    	} else {
    		$json = '';
    	}
    
    	if ($expiresindays < 0 && $expiresindays >= -60){ // in GRACE PERIOD
    		$tastatus = "renew";
    	} else if ($expiresindays > 0){ // in Active
    		$tastatus = "active";
    	}
    
    	
    	$ta_membership = $this->session->getTaMembership();
    	if (isset($ta_membership)) {
    		$this->session->unsTaMembership();
    	}
    	$this->session->setTaMembership($json);
    	
    	$istaclubmember = $this->session->getIsTaClubMember();
    	if (isset($istaclubmember)) {
    		$this->session->unsIsTaClubMember();
    	}
    	$this->session->setIsTaClubMember($istamember);


    	$ta_status = $this->session->getTaStatus();
    	if (isset($ta_status)) {
    		$this->session->unsTaStatus();
    	}
    	$this->session->setTaStatus($tastatus);
    


    	$tamembertypeid = $this->session->getTaMemberTypeId();
    	if (isset($$tamembertypeid)) {
    		$this->session->unsTaMemberTypeId();
    	}
    	$this->session->setTaMemberTypeId($ta_membertypeid);
    	
    }
    
    /**
     * To fetch the Extended Service Plan details
     */
    function esp_membership_details($membership) {
    	$expiryday = "0001-01-01T00:00:00-00:00";
    	$isespmember = 0;
    	$espjsonarray = $membership;
    	$isespmember = 1;
    	$expiryday = $membership['ExpireDate'];
    	$esp_membertypeid = $membership['MemberTypeId'];
    	$now = time(); // or current datetime
    	$your_date = strtotime($expiryday);
    	$datediff = $your_date - $now;
    	$expiresindays = (floor($datediff / 86400));
    	$espstatus = "expired";
    	if (isset($espjsonarray)) {
    		$json = json_encode($espjsonarray);
    	} else {
    		$json = '';
    	}
    
    	if ($expiresindays < 0 && $expiresindays >= -60){ // in GRACE PERIOD
    		$espstatus = "renew";
    	} else if ($expiresindays > 0){ // in Active
    		$espstatus = "active";
    	}
    
    	
    	$esp_membership = $this->session->getEspMembership();
    	if (isset($esp_membership)) {
    		$this->session->unsEspMembership();
    	}
    	$this->session->setEspMembership($json);
    	
    	
    	$isespclubmember = $this->session->getIsEspClubMember();
    	if (isset($isespclubmember)) {
    		$this->session->unsIsEspClubMember();
    	}
    	$this->session->setIsEspClubMember($isespmember);
    	

    	$esp_status = $this->session->getEspStatus();
    	if (isset($esp_status)) {
    		$this->session->unsEspStatus();
    	}
    	$this->session->setEspStatus($espstatus);
    
    	$espmembertypeid = $this->session->getEspMemberTypeId();
    	if (isset($espmembertypeid)) {
    		$this->session->unsEspMemberTypeId();
    	}
    	$this->session->setEspMemberTypeId($esp_membertypeid);
    }
    
    
    /*
     * Function to get the Tertiary Menu Url When Plan Details is send
     */
    function get_rsa_plan_detail_url($plan_title){
    	/*
    	$mp = \Drupal::menuTree()->getCurrentRouteMenuTreeParameters("rsa-membership");
    	$test = \Drupal::menuTree()->load("rsa-membership", $mp);
    	$render = \Drupal::menuTree()->build($test);
    	if (!empty($render['#items'])) {
    		foreach ($render['#items'] As $item) {
    			if ($item['title'] == $plan_title) {
    				return $item['url'];
    			}
    		}
    	}
    	return null;
    	*/
    }
    
    /*
     * Function to get RA membership mapping name
     */
    function get_ra_membership_mapping_title($member_type_key) {
    	switch ($member_type_key) {
    		case "Platinum RA":
    			return  "AUTO + RV PLATINUM";
    		case "Standard":
    			return "AUTO + RV STANDARD";
    		case "Auto Platinum RA (CVP)":
    			return "AUTO PLATINUM";
    		default:
    			return $member_type_key;
    	}
    }
    
    /*
     * Function to get TA membership mapping name
     */
    function get_ta_membership_mapping_title($member_type_key) {
    	switch ($member_type_key) {
    		case "TA-GS Base":
    			return  "BASIC PLAN";
    		case "TA-GS Premier":
    			return "PREMIER PLAN";
    		default:
    			return $member_type_key;
    	}
    }
    
    
    /*
     * Function to get the Tertiary Menu Url When Plan Details is send
     */
    function get_dashboard_ta_plan_detail_url($plan_title) {
		/*
    	$mp = \Drupal::menuTree()->getCurrentRouteMenuTreeParameters("travel-asssit");
    	$test = \Drupal::menuTree()->load("travel-asssit", $mp);
    	$render = \Drupal::menuTree()->build($test);
    	if (!empty($render['#items'])) {
    		foreach ($render['#items'] As $item) {
    			if (stripos($item['title'], $plan_title) !== false) {
    				return $item['url'];
    			}
    		}
    	}
    	return null;
		*/
    }
    
    /**
     * Get the Current Time Stamp.
     */
    function get_current_timestamp() {
    	$dt = new \DateTime();
    	$dt->setTimeZone(new \DateTimeZone('UTC'));
    	$timestamp = $dt->format('Y-m-d\TH-i-s.\0\0\0\Z');
    	return $timestamp;
    }
    
}

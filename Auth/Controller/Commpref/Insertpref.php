<?php
namespace Camping\Auth\Controller\Commpref;

class Insertpref extends \Magento\Customer\Controller\Address
{
	public function execute()
	{
		$postdata 			= $this->getRequest()->getPost();
		$profileInfo = $this->_objectManager->get('Magento\Customer\Model\Session')->getProfileData();
		$userEmailAddressId = $profileInfo['UserEmailAddressId'];
		
		$url 	 			= "https://services.stg.freedomroads.local:44401/EMS/api/EmailAddresses/$userEmailAddressId/UserPreferences";
		$username			= 'BA7FF50C4D8849808F1524C88479DC52';
		$password			=  'B628DD6798834EDB94FD6C4D44C48C4A';
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = $this->getHeader();
		if(!empty($headers)){
			curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
		}
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postdata));
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ( $status != 200 ) {
			$response["body"] = "Error";
		}
		curl_close($curl);
		$response = json_decode($json_response, true);
		
		if(!empty($response['error'])){
			$msg = 'unable to find';
			/* foreach($response['error'] as $key=>$value)
			{
				if(is_array($value)) {
					$msg .= implode(" ",$value);
				} else {
					$msg .= $value;
				}
			} */
			print_r($response['error']);
			echo json_encode(array('status'=>'fail','message'=>$msg));
			return;
		}
		echo json_encode(array('st'=>$status,'status'=>'success','id'=>$postdata['PreferenceId'],'upid'=>$response['body']['UserPreferenceId'],'lps'=>$response['body']['LastUpdateSource']));
	}
	
	function getHeader(){
		$header = array('Content-Type: application/json','Accept: application/json');
		return $header;
	}
}
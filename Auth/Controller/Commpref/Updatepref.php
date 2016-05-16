<?php
namespace Camping\Auth\Controller\Commpref;

class Updatepref extends \Magento\Customer\Controller\Address
{
	public function execute()
	{
		$postdata 			= $this->getRequest()->getPost();
		$profileInfo = $this->_objectManager->get('Magento\Customer\Model\Session')->getProfileData();
		$userEmailAddressId = $profileInfo['UserEmailAddressId'];
		
		$url 	 			= "https://services.stg.freedomroads.local:44401/EMS/api/userpreferences";
		$username			= 'BA7FF50C4D8849808F1524C88479DC52';
		$password			= 'B628DD6798834EDB94FD6C4D44C48C4A';
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = $this->getHeader();
		if(!empty($headers)){
			curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
		}
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postdata));
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ( $status != 200 ) {
			$response["body"] = "Error";
			//echo json_encode($response);
		}
		curl_close($curl);
		//$response['Status'] = $status;
		$response = json_decode($json_response, true);
		if(!empty($response['error'])){
			$msg = '';
			foreach($response['error'] as $key=>$value)
			{
				$msg .= $value;
			}
			echo json_encode(array('status'=>'fail','message'=>$msg));
			return;
		}
		echo json_encode(array('status'=>'success','message'=>$json_response));
	}
	
	function getHeader(){
		$header = array('Content-Type: application/json','Accept: application/json');
		return $header;
	}
}
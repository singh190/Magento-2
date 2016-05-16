<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Camping\Auth\Controller\Account;

use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditPost extends \Magento\Customer\Controller\AbstractAccount
{
    /** @var AccountManagementInterface */
    protected $customerAccountManagement;

    /** @var CustomerRepositoryInterface  */
    protected $customerRepository;

    /** @var Validator */
    protected $formKeyValidator;

    /** @var CustomerExtractor */
    protected $customerExtractor;

    /**
     * @var Session
     */
    protected $session;
	
    protected $scopeConfig;
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param Validator $formKeyValidator
     * @param CustomerExtractor $customerExtractor
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository,
        Validator $formKeyValidator,
        CustomerExtractor $customerExtractor,
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    	
    ) {
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerExtractor = $customerExtractor;
        $this->_scopeConfig  = $scopeConfig;
        
        parent::__construct($context);
    }

    /**
     * Change customer password action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {   
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if ($this->getRequest()->isPost()) {
            $customerId = $this->session->getCustomerId();
            $currentCustomer = $this->customerRepository->getById($customerId);

            /** Prepare new customer data  */
            $customer = $this->customerExtractor->extract('customer_account_edit', $this->_request);
            $customer->setId($customerId);
            if ($customer->getAddresses() == null) {
                $customer->setAddresses($currentCustomer->getAddresses());
            }
            
            /** Change customer password */
            if ($this->getRequest()->getParam('change_password')) {
                $this->changeCustomerPassword($currentCustomer->getEmail());
            }
				
            try {
                $this->customerRepository->save($customer);
            } catch (AuthenticationException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (InputException $e) {
               // $this->messageManager->addException($e, __('Invalid input'));
            } catch (\Exception $e) {
                $message = __('We can\'t save the customer.')
                    . $e->getMessage()
                    . '<pre>' . $e->getTraceAsString() . '</pre>';
                $this->messageManager->addException($e, $message);
            }

            if ($this->messageManager->getMessages()->getCount() > 0) {
                $this->session->setCustomerFormData($this->getRequest()->getPostValue());
                return $resultRedirect->setPath('*/*/edit');
            }
			
            $this->messageManager->addSuccess(__('You saved the account information.'));
            return $resultRedirect->setPath('userinfo/edit/success/');
        }

        return $resultRedirect->setPath('*/*/edit');
    }

    /**
     * Change customer password
     *
     * @param string $email
     * @return $this
     */
    protected function changeCustomerPassword($email)
    {
    	 
    	 $token = $_COOKIE ['__CS']; 
    	if (empty ( $token )) {
    		die ( "No Token Received" );
    	}
    	$headers = array (
    			"Authorization: $token"
    	);
    	
    	$url = $this->getSsoConfig()."resetpassword.php";
    	
        $currPass = $this->getRequest()->getPost('password');
        $newPass = $this->getRequest()->getPost('password');
        $confPass = $this->getRequest()->getPost('password_confirmation');


        
        if (!strlen($newPass)) {
            $this->messageManager->addError(__('Please enter new password.'));
            return $this;
        }

        if ($newPass !== $confPass) {
            $this->messageManager->addError(__('Confirm your new password.'));
            return $this;
        }

        try {
        	$params = array (
        			"newpassword" => $newPass
        	);

        	$response = $this->curlPost ( $url, $params, $headers );
        	
        	if ($response ['status_code'] != 200) {
        		$this->session->setChangePasswordError($response['error']['error_message']);
        		$this->messageManager->addException($e, __( $response['error']['error_message'].'Something went wrong while changing the password.'));
        	}else{
        		$this->customerAccountManagement->changePassword($email, $currPass, $newPass);
        	}
        	
        } catch (AuthenticationException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while changing the password.'));
        }

        return $this;
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
    
    public function getSsoConfig(){
    	return $this->_scopeConfig->getValue('campingworld/general/sso', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
}


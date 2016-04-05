<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/5/2016
 * Time: 4:31 PM
 */

namespace Singh\Grid\Controller\Adminhtml\Grid;

use Singh\Grid\Controller\Adminhtml\Grid;

class Delete extends Grid
{    
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $isPost = $this->getRequest()->isPost();
        
//        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());                
//        if (!$formKeyIsValid || !$isPost) {
//            $this->messageManager->addError(__('Grid record could not be deleted.'));
//            return $resultRedirect->setPath('grid/*/');
//        }

        /**
         * need to check for magento form key for validating form key
         */
        if (!$isPost) {
            $this->messageManager->addError(__('Grid record could not be deleted.'));
            return $resultRedirect->setPath('grid/*/');
        }
        $gridId = $this->getRequest()->getParam('grid_record_id');
        if ($gridId){
            try{
                $gridModel = $this->_gridFactory->create();
                $gridModel->load($gridId);
                //check if grid id is in model
                if($gridModel->getId()){
                    //delete that record
                    $gridModel->delete();
                    $this->messageManager->addSuccess(__('Grid record deleted.'));                    
                }
            }catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());                
            }
        }
        return $resultRedirect->setPath('grid/*/');
    }
}
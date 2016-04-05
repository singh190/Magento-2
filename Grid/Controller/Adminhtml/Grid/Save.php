<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/5/2016
 * Time: 1:32 PM
 */

namespace Singh\Grid\Controller\Adminhtml\Grid;

use Singh\Grid\Controller\Adminhtml\Grid;

class Save extends Grid
{
    public function execute()
    {
        $gridId = $this->getRequest()->getParam('grid_record_id');
        //for save-continue and save-new action;
        $redirectBack = $this->getRequest()->getParam('back', false);
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data){
            //unset data variable
            try{
                $gridModel = $this->_gridFactory->create();                
                //if post has grid id; load it
                if ($gridId)
                    $gridModel->load($gridId);                
                //set values to model
                $formData = $this->getRequest()->getParam('page');
//                unset($formData['grid_record_id']);
                
                $gridModel->addData($formData)->save();
                $gridId = $gridModel->getId();
                $this->messageManager->addSuccess(__('Grid record is saved.'));

            }catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setFormData($data);
                $redirectBack = $gridId ? true : 'new';
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError($e->getMessage());
                $this->_session->setFormData($data);
                $redirectBack = $gridId ? true : 'new';
            }
        } else {
            $resultRedirect->setPath('grid/*/', ['id' => $gridId]);
            $this->messageManager->addError('No data to save');
            return $resultRedirect;
        }
        if ($redirectBack === 'new') {
            $resultRedirect->setPath('grid/*/new');
        } elseif ($redirectBack === 'edit') {
            $resultRedirect->setPath(
                'grid/*/edit',
                ['id' => $gridId, '_current' => true]
            );
        } else {
            $resultRedirect->setPath('grid/*/');
        }
        return $resultRedirect;
    }
}
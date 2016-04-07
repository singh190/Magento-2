<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/6/2016
 * Time: 10:15 PM
 */

namespace Singh\Uigrid\Controller\Adminhtml\Grid;

use Magento\TestFramework\ErrorLog\Logger;

class Save extends \Singh\Uigrid\Controller\Adminhtml\Grid
{
    /**
     * {@inheritdoc}
     * check if allowed in admin resource
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Singh_Uigrid::grid');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $id = $this->getRequest()->getParam('entity_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectBack = $this->getRequest()->getParam('back', false);
        if ($data) {
            try {
//                $uigridModel = $this->_objectManager->create('Singh\Uigrid\Model\Grid');
                $uigridModel = $this->_gridFactory;
                if ($id) {
                    $uigridModel->load($id);
                }
                $uigridModel->setData($data);
                /*$this->_eventManager->dispatch(
                    'uigrid_prepare_save',
                    ['uigrid' => $uigridModel, 'request' => $this->getRequest()]
                );*/
                $uigridModel->save();
                $id = $uigridModel->getId();
                $this->messageManager->addSuccess(__('Grid record is saved.'));

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setFormData($data);
                $redirectBack = $id ? true : 'new';
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError($e->getMessage());
                $this->_session->setFormData($data);
                $redirectBack = $id ? true : 'new';
            }
        }else {
            $resultRedirect->setPath('uigrid/*/', ['entity_id' => $id]);
            $this->messageManager->addError('No data to save');
            return $resultRedirect;
        }
        if ($redirectBack === 'new') {
            $resultRedirect->setPath('uigrid/*/new');
        } elseif ($redirectBack === 'edit') {
            $resultRedirect->setPath(
                'uigrid/*/edit',
                ['entity_id' => $id, '_current' => true]
            );
        } else {
            $resultRedirect->setPath('uigrid/*/');
        }
        return $resultRedirect;
    }
}

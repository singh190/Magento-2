<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/5/2016
 * Time: 1:32 PM
 */

namespace Singh\Grid\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;
use Singh\Grid\Model\GridFactory;
use Magento\Framework\View\Result\PageFactory;

class Save extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_gridFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        GridFactory $gridFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_gridFactory = $gridFactory;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectBack = $this->getRequest()->getParam('back', false);
        $gridId = $this->getRequest()->getParam('grid_record_id');
        $data = $this->getRequest()->getPostValue();
        if ($data){
            try{

                $gridModel = $this->_gridFactory->create();
                //if got grid id; load it
                if ($gridId)
                    $gridModel->load($gridId);
                //set values to model
                $formData = $this->getRequest()->getParam('page');
                $gridModel->setData($formData);
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
        }else {
            $resultRedirect->setPath('grid/*/', ['id' => 'grid_record_id']);
            $this->messageManager->addError('No data to save');
//            return $resultRedirect;
        }
        if ($redirectBack === 'new') {
            $resultRedirect->setPath('grid/*/newaction');
        }elseif ($redirectBack) {
            $resultRedirect->setPath(
                'grid/*/edit',
                ['id' => $gridId]
            );
        } else {
            $resultRedirect->setPath('grid/*/');
        }
//        return $resultRedirect;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/5/2016
 * Time: 4:31 PM
 */

namespace Singh\Grid\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;
use Singh\Grid\Model\GridFactory;
use Magento\Framework\View\Result\PageFactory;

class Delete extends Action
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
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
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
                    return $resultRedirect->setPath('grid/*/');
                }
            }catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath(
                    'grid/*/edit',
                    ['id' => $gridId]
                );
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath(
                    'grid/*/edit',
                    ['id' => $gridId]
                );
            }
        }else {
            $resultRedirect->setPath('grid/*/', ['id' => 'grid_record_id']);
            $this->messageManager->addError('No data to save');
        }
        return $resultRedirect;
    }
}
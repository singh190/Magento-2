<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/4/2016
 * Time: 6:27 PM
 */

namespace Singh\Grid\Controller\Adminhtml\Grid;

use Singh\Grid\Controller\Adminhtml\Grid;

class Edit extends Grid
{
    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Singh_Grid::grid')
            ->addBreadcrumb(__('Grid'), __('Grid'))
            ->addBreadcrumb(__('Manage Grid'), __('Manage Grid'));
        return $resultPage;
    }

    /**
     * Edit grid record
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('grid_record_id');
        $model = $this->_objectManager->create('Singh\Grid\Model\Grid');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This grid record no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->resultRegistry->register('singh_form_data', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Post') : __('New Grid'),
            $id ? __('Edit Post') : __('New Grid')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Grids'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Grid'));

        return $resultPage;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/6/2016
 * Time: 6:45 PM
 */

namespace Singh\Uigrid\Controller\Adminhtml\Grid;

class Index extends \Singh\Uigrid\Controller\Adminhtml\Grid
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Singh_Uigrid::grid');
        $resultPage->addBreadcrumb(__('Example Grid'), __('Example Grid'));
        $resultPage->addBreadcrumb(__('Manage Grid'), __('Manage Grid'));
        $resultPage->getConfig()->getTitle()->prepend(__('Grid'));

        return $resultPage;
    }

    /**
     * Is the user allowed to view the blog post grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Singh_Uigrid::grid');
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/4/2016
 * Time: 2:02 PM
 */

namespace Singh\Grid\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Singh_Grid::grid');
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        $resultPage->addBreadcrumb(__('Manage Admin Grid View'), __('Manage Admin Grid View'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Admin Grid'));

        return $resultPage;
    }
}
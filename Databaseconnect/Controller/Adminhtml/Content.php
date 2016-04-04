<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/1/2016
 * Time: 3:52 PM
 * @purpose :  this file is used as a root controller and the action classes will be extended this controller
 */

namespace Singh\Databaseconnect\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Singh\Databaseconnect\Model\DataconnectFactory;

abstract class Content extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * News model factory
     *
     * @var \Singh\Databaseconnect\Model\DataconnectFactory;
     */
    protected $_newsFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param DataconnectFactory $newsFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        DataconnectFactory $newsFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_newsFactory = $newsFactory;
    }

    /**
     * News access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Singh_Databaseconnect::main_menu');
    }
}

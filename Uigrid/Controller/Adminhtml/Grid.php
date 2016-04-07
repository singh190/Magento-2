<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/6/2016
 * Time: 6:45 PM
 */

namespace Singh\Uigrid\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

abstract class Grid extends Action{
    
    /**
     * @var \Singh\Uigrid\Model\Grid
     */
    protected $_gridFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $resultRegistry;
    /**
     * @var \Magento\Framework\View\Result\Page
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultForwardFactory;
    
   
    public function __construct(
        Action\Context $context,
        ForwardFactory $resultForwardFactory,
        Registry $resultRegistry,
        PageFactory $resultPageFactory 
    )
    {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRegistry = $resultRegistry;
        $this->_gridFactory = $this->_objectManager->create('Singh\Uigrid\Model\Grid');
    }

    /**
     * {@inheritdoc}
     * check if allowed in admin resource
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Singh_Uigrid::grid');
    }
}
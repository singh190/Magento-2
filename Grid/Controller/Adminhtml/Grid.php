<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/5/2016
 * Time: 5:05 PM
 */

namespace Singh\Grid\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Singh\Grid\Model\GridFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

abstract class Grid extends Action{
    protected $resultPageFactory;
    protected $resultForwardFactory;
    protected $_gridFactory;
    protected $resultRegistry;

    public function __construct(
        Action\Context $context,
        GridFactory $gridFactory,
        Registry $resultRegistry,
        PageFactory $resultPageFactory,        
        ForwardFactory $resultForwardFactory
        
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRegistry = $resultRegistry;
        $this->_gridFactory = $gridFactory;        
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Singh_Grid::grid');
    }

}
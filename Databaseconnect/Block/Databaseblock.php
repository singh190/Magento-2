<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 3/31/2016
 * Time: 2:09 PM
 */

namespace Singh\Databaseconnect\Block;

use Singh\Databaseconnect\Model\DataconnectFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Databaseblock extends Template {

    protected $_modelDatabaseFactory;

    /**
     * @param Context $context
     * @param DataconnectFactory $modelDatabaseFactory
     */
    public function __construct(
        Context $context,
        DataconnectFactory $modelDatabaseFactory,
        array $data = []
    ) {
        parent::__construct($context,$data);
        $this->_modelDatabaseFactory = $modelDatabaseFactory;
    }

    protected function _prepareLayout()
    {
        $this->setMessage('calling from block prepare layout to template');
    }

    public function display(){
        $dataModel = $this->_modelDatabaseFactory->create();
        // Get all data collection
        $data = $dataModel->getCollection()->getData();
        return $data;
    }
    
}
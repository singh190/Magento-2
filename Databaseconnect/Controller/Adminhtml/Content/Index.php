<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/1/2016
 * Time: 12:23 PM
 */

namespace Singh\Databaseconnect\Controller\Adminhtml\Content;

use Singh\Databaseconnect\Controller\Adminhtml\Content;

class Index extends Content
{
    /**
     * @return void
     */
   public function execute()
   {
       // TODO: Implement execute() method.
       if ($this->getRequest()->getQuery('ajax')) {
           $this->_forward('grid');
           return;
       }

       $resultPage = $this->_resultPageFactory->create();
       $resultPage->setActiveMenu("Singh_Databaseconnect::main_menu");
       $resultPage->getConfig()->getTitle()->prepend(__("Sample data"));

       return $resultPage;
   }
}
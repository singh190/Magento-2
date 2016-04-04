<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 3/30/2016
 * Time: 3:47 PM
 */

namespace Singh\Hello\Block;

class Hello extends \Magento\Framework\View\Element\Template 
{
    public function displayMsg(){
        return "displaying message";
    }
    
}

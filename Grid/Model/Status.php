<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 4/1/2016
 * Time: 3:41 PM
 * @purpose : This file is used to get News status options
 */

namespace Singh\Grid\Model;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    const ENABLED  = 1;
    const DISABLED = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled')
        ];

        return $options;
    }
}
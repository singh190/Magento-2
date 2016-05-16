<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 11-05-2016
 * Time: 15:17
 */
namespace Camping\Auth\Block\Wishlist\Customer;

use Magento\Framework\Option\ArrayInterface;
class Privacyoption implements ArrayInterface
{
    const ENABLED  = 1;
    const DISABLED = 0;
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::ENABLED => __('Public'),
            self::DISABLED => __('Private')
        ];
        return $options;
    }
}
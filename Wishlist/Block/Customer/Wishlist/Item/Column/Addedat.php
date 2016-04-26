<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 21-04-2016
 * Time: 13:48
 */

namespace Camping\Wishlist\Block\Customer\Wishlist\Item\Column;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Block\Product\Context;
class Addedat extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
{
    protected $timeZoneInterface;
    public function __construct(
        Context $context,
        TimezoneInterface $timezoneInterface,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data)
    {
        parent::__construct($context, $httpContext, $data);
        $this->timeZoneInterface = $timezoneInterface;
    }

    /**
     * @param $addedDate
     * @return string
     */
    public function getWishlistAddedAt($addedDate){
        return $this->timeZoneInterface->formatDate($addedDate,\IntlDateFormatter::LONG,false);
    }
}
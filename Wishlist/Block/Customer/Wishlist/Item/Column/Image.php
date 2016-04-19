<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Wishlist block customer item cart column
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Singh\Wishlist\Block\Customer\Wishlist\Item\Column;

use Magento\Framework\Stdlib\DateTime\DateTimeFormatter;

class Image extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
{
    protected $timeZoneInterface;
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        array $data)
    {
        parent::__construct($context, $httpContext, $data);
        $this->timeZoneInterface = $timezoneInterface;
    }

    public function getWishlistAddedAt($addedDate){
        return $this->timeZoneInterface->formatDate($addedDate,\IntlDateFormatter::LONG,false);
    }
}

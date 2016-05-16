<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 27-04-2016
 * Time: 12:48
 */

namespace Camping\Auth\Block\Wishlist\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Model\Product;
use Magento\Paypal\Model\Payflow\Pro;
//use Magento\Theme\Block\Html\Pager;

class Wishlist extends \Magento\Framework\View\Element\Template
{
    /**
     * constant for percentage
     */
    const percentage = 100;
    /**
     * constant for wishlist item limit, per page
     */
    const wishlistPageLimit = 1;

    /**
     * @var Session
     */
    protected $_currentCustomer;
    /**
     * @var TimezoneInterface
     */
    protected $timeZoneInterface;
    /**
     * @var Product
     */
    protected $_productModel;
    /**
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_wishlistItemModel;
    /**
     * @var
     */
    protected $_wishlistItemCollection;
    /**
     * @var
     */
    protected $_customerWishlist;
    /**
     * @var \Magento\Wishlist\Block\Customer\Wishlist
     */
    protected $_wishlistMageBlock;

    /**
     * Wishlist constructor.
     * @param Session $currentCustomer
     * @param TimezoneInterface $timezoneInterface
     * @param Product $product
     * @param \Magento\Wishlist\Model\Wishlist $wishlistItemModel
     * @param \Magento\Wishlist\Block\Customer\Wishlist $wishlistMageBlock
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Session $currentCustomer,
        TimezoneInterface $timezoneInterface,
        Product $product,
        \Magento\Wishlist\Model\Wishlist $wishlistItemModel,
        \Magento\Wishlist\Block\Customer\Wishlist $wishlistMageBlock,
        Template\Context $context,
        array $data = []
    )
    {
        $this->_currentCustomer = $currentCustomer;
        $this->_productModel = $product;
        $this->timeZoneInterface = $timezoneInterface;
        $this->_wishlistItemModel = $wishlistItemModel;
        $this->_wishlistMageBlock = $wishlistMageBlock;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @uses get pager 
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getWishlistItemCollectionCount()) {
            // create pager block for collection
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'camping.wishlist.pager'
            );
            $pager->setLimit(self::wishlistPageLimit)
                ->setShowAmounts(false)
                ->setCollection($this->getWishlistItemCollection());
            $this->setChild('wishlist_pager', $pager);
            $this->getWishlistItemCollection()->load();
        }
        return $this;
    }

    /**
     * @param $addedDate
     * @return string
     */
    public function getWishlistAddedAt($addedDate){
        return $this->timeZoneInterface->formatDate($addedDate,\IntlDateFormatter::LONG,false);
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer(){
        return $this->_currentCustomer->getCustomer();
    }

    /**
     * @return current customer id
     */
    public function getCustomerId(){
        return $this->getCustomer()->getId();
    }

    /**
     * @return customer first name
     */
    public function getCustomerFirstName(){
        return $this->getCustomer()->getFirstname();
    }

    /**
     * @return get wishlist by cutomer id
     */
    public function getWishlistCustomerItem(){
        return $this->_wishlistItemModel->loadByCustomerId($this->getCustomerId());
    }

    /**
     * @return get wishlist collection
     */
    public function getWishlistItemCollection(){
        return $this->getWishlistCustomerItem()->getItemCollection();
    }

    /**
     * @return count wishlist collection
     */
    public function getWishlistItemCollectionCount(){
        return $this->getWishlistCustomerItem()->getItemsCount();
    }

    /**
     * @param $id
     * @return set product final price as null
     * else if one product has special price, it will be applied to all
     */
    public function getItemProduct($id){
        return $this->getProductModel()->load($id)->setFinalPrice(null);
        //return $this->getWishlistItemCollection()->getItems();
    }

    /**
     * @return Product Model
     */
    public function getProductModel(){
        return $this->_productModel;
    }

    /**
     * @param $oldPrice
     * @param $newPrice
     * @return float
     */
    public function getPriceOff($oldPrice,$newPrice){
        return (($oldPrice - $newPrice) / $oldPrice) * self::percentage;
    }

    /**
     * @param $item
     * @return string; get url for add item to cart
     */
    public function getItemAddtoCartParams($item){
        return $this->_wishlistMageBlock->getItemAddToCartParams($item);
    }

    /**
     * @param $item
     * @return string; get url for remove item from wishlist
     */
    public function getWishlistItemRemoveParams($item){
        return $this->_wishlistMageBlock->getItemRemoveParams($item);
    }

    /**
     * @return string; controller url for sharing wishlist
     */
    public function getWishlistShareSendUrl(){
        return $this->getUrl('campingauth/wishlist/send');
    }

    /**
     * @return string; controller url for adding item to wishlist
     */
    public function getWishlistAddSendUrl(){
        return $this->getUrl('campingauth/wishlist/add');
    }

    /**
     * @return get wishlits id
     */
    public function getWishlistId(){
        return $this->getWishlistCustomerItem()->getId();
    }

    /**
     * @return string; get wishlist pager block
     */
    public function getPagerHtml(){
        return $this->getChildHtml('wishlist_pager');
    }
}
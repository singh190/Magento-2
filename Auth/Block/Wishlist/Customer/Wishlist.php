<?php
/**
 * Created by PhpStorm.
 * User: abhimanyu_s
 * Date: 20-04-2016
 * Time: 10:43
 */

namespace Camping\Auth\Block\Wishlist\Customer;

use Photon\Hawk\Helper\DeviceDetect as deviceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Data;
use Camping\Product\Block\Product\AbstractProduct;
use Magento\Eav\Model\Config as eavConfig;
use Camping\Auth\Block\Wishlist\Customer\Privacyoption;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
//my custom use class

use Magento\Wishlist\Model\Wishlist as MageWishlist;

class Wishlist extends AbstractProduct implements \Magento\Framework\DataObject\IdentityInterface
{

    CONST TEMPLATE_PATH = "Camping_Auth::wishlist/";
    CONST TEMPLATE = "view.phtml";
    /**
     * constant for percentage
     */
    const percentage = 100;
    /**
     * constant for wishlist item limit, per page
     */
    const wishlistPageLimit = 1;
    static $hasProductAttribute = false;
    //public $whichView;

    protected $urlEncoder;
    protected $_jsonEncoder;
    protected $_productHelper;
    protected $_localeFormat;
    protected $_customerSession;
    protected $productRepository;
    protected $priceCurrency;
    protected $_catalogData;
    protected $_wishlistItemModel;
    protected $_productModel;
    protected $eavConfig;
    protected $privacyOption;
    protected $timeZoneInterface;
    protected $_wishlistMageBlock;

    public function __construct
    (
        \Magento\Backend\Block\Template\Context $template,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Wishlist\Model\Wishlist $wishlistItemModel,
        \Magento\Wishlist\Block\Customer\Wishlist $wishlistMageBlock,
        TimezoneInterface $timezoneInterface,
        deviceFactory $deviceFactory,
        Product $product,
        eavConfig $config,
        Data $catalogData,
        Privacyoption $privacyoption,
        array $data = []
    )
    {
        $this->_productHelper = $productHelper;
        $this->urlEncoder = $urlEncoder;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_localeFormat = $localeFormat;
        $this->_customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->timeZoneInterface = $timezoneInterface;
        $this->priceCurrency = $priceCurrency;
        $this->_wishlistItemModel = $wishlistItemModel;
        $this->_wishlistMageBlock = $wishlistMageBlock;
        $this->_productModel = $product;
        $this->_catalogData = $catalogData;
        $this->eavConfig = $config;
        $this->privacyOption = $privacyoption;

        $prefix = '';
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'];
        if ($deviceFactory->isTablet(null, null, $http_user_agent)) {            
            $prefix = "mb_";
            //$this->whichView = "tablet";
            
        } else if ($deviceFactory->isMobile(null, null, $http_user_agent)) {
            $prefix = "mb_";
            //$this->whichView = "mobile";
        } else {
            //$this->whichView = "desktop";
            $prefix = "";
        }
        $template = self::TEMPLATE_PATH . $prefix . self::TEMPLATE;
        $this->setTemplate($template);
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @uses get pager
     */

    /*public function getView(){
        return $this->whichView;
    }*/
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
    
     /** Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities() {
        $identities = array();
        return $identities;
    }

    /**
     * @param $addedDate
     * @return string
     */
    public function getWishlistAddedAt($addedDate){
        $this->hasProductAttribute();
        return $this->timeZoneInterface->formatDate($addedDate,\IntlDateFormatter::LONG,false);
    }

    public function hasProductAttribute(){
        $this->eavConfig->getAttribute("catalog_product", "skuPrimaryImage");
        self::$hasProductAttribute = true;
    }

    protected function getCustomer(){
        $this->hasProductAttribute();
        return $this->_customerSession->getCustomer();
    }

    /**
     * @return current customer id
     */
    public function getCustomerId(){
        return $this->_customerSession->getCustomer()->getId();
    }
    /**
     * @return customer first name
     */
    public function getCustomerFirstName(){
        return $this->getCustomer()->getFirstname();
    }
    /**
     * @return Product Model
     */
    public function getProductModel(){
        return $this->_productModel;
    }
    /**
     * @param $id
     * @return set product final price as null
     * else if one product has special price, it will be applied to all
     */
    public function getItemProduct($id){
        $this->_coreRegistry->unregister('product');
        $product = $this->getProductModel()->load($id);//->setFinalPrice(null);
        $this->_coreRegistry->register ( 'product',$product);
        return $this->getProduct();
        //return $this->getWishlistItemCollection()->getItems();
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
     * @return  wishlist by cutomer id
     */
    public function getWishlistCustomerItem(){
        return $this->_wishlistItemModel->loadByCustomerId($this->getCustomerId());
    }
    /**
     * @return  wishlist collection
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
     * @param $oldPrice
     * @param $newPrice
     * @return float
     */
    public function getPriceOff($oldPrice,$newPrice){
        return (($oldPrice - $newPrice) / $oldPrice) * self::percentage;
    }
    /**
     * @param $item
     * @return string; get url for remove item from wishlist
     */
    public function getWishlistItemRemoveParams($item){
        return $this->_wishlistMageBlock->getItemRemoveParams($item);
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
    
    public function getCustomerPrivacyValue(){
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $customerRepository = $objectManager
//            ->get('Magento\Customer\Block\Widget\AbstractWidget');
//        $customer = $customerRepository->getById($this->getCustomerId());
//         $this->getCustomer()->setData('wishlist_privacy',0)->save();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load(1);
        return $customer->getData('wishlist_privacy');
//        return $customerRepository->_getAttribute('gender');
//        return $this->getCustomer()->getData();
    }
    /**
     * customer wishlist privacy setting
     */
    public function getPrivacyOptions(){
        $html = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setName(
            'wishlistprivacy'
        )->setId(
            'wishlistprivacy'
        )->setTitle(
            __('Privacy Setting')
        )->setValue(
            $this->getCustomerPrivacyValue()
        )->setOptions(
            $this->privacyOption->toOptionArray()
        )->setExtraParams(
            'data-validate="{\'validate-select\':true}"'
        )->getHtml();

        \Magento\Framework\Profiler::stop('TEST: ' . __METHOD__);
        return $html;
    }


    /**
     * Get Product Images
     *
     * @return array of image_url
     */
    public function getProductImage() {
        $image_url = $this->getViewFileUrl('Camping_Product::images/no-image.png');
        $product = $this->getProduct();
        $images = $product->getMediaGalleryImages();
        if (self::$hasProductAttribute) {
            $image = '';
            //$image = $product->getResource()->getAttribute('skuPrimaryImage')->getFrontend()->getValue($product);
            if ($image) {
                $image_aray = explode("/", $image);
                $image_url = "https://images4.campingworld.com/CampingWorld/images/products/" . $image_aray[0] . "/425x425/" . $image_aray[1];
            }
        }
        return $image_url;
    }

    /**
     * Get Product Inventory Service
     *
     * @return Store Quantity based on store list
     */
    public function getServiceInventory($ProductSku) {
        $url = 'https://BA7FF50C4D8849808F1524C88479DC52:B628DD6798834EDB94FD6C4D44C48C4A@dmzservicesstg.campingworld.com/CW/CAMPINGWORLD/item';
        $data["currentLocation"] = '';
        $data["itemCodes"] = [$ProductSku];
        $data["storeList"] = [ 20, 21];
        $data_json = json_encode($data);
        $username = 'BA7FF50C4D8849808F1524C88479DC52';
        $password = 'B628DD6798834EDB94FD6C4D44C48C4A';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array('Content-Type: application/json', 'Accept: application/json');
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = json_decode($json_response, true);
        $inventoryInfos_array = $response["inventoryInfos"];
        $locationlevel_array = $inventoryInfos_array[0]["locationLevels"];
        if( count($locationlevel_array) ) {
            if ($locationlevel_array[0]["onHand"] != 0)
                return $locationlevel_array[0]["onHand"];
            else
                return $locationlevel_array[1]["onHand"];
        }
        else {
            return 0;
        }
    }
}
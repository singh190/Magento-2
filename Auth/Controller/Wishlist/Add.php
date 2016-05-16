<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Camping\Auth\Controller\Wishlist;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Model\Product;
use Magento\Paypal\Model\Payflow\Pro;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Wishlist\Controller\Index\Add
{

    /**
     * Adding new item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        $productSku = $this->getRequest()->getPost('item_sku');
        $wishlist = $this->wishlistProvider->getWishlist();
        
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }

        $session = $this->_customerSession;

        $requestParams = $this->getRequest()->getParams();

        if ($session->getBeforeWishlistRequest()) {
            $requestParams = $session->getBeforeWishlistRequest();
            $session->unsBeforeWishlistRequest();
        }

//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
//        $productId = null;
//        $collection = $productCollection->create()
//        ->addAttributeToFilter('sku',$productSku);
//        foreach ($collection as $item){
//            $productId = $item->getId();
//
//        }
//        echo "product id".$productId;die;
        //$productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$productSku) {
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }
        try {
            $product = $this->productRepository->get($productSku);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        if (!$product || !$product->isVisibleInCatalog()) {
            $this->messageManager->addErrorMessage(__('We can\'t specify a product.'));
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }

        try {
            $buyRequest = new \Magento\Framework\DataObject($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                throw new \Magento\Framework\Exception\LocalizedException(__($result));
            }
            $wishlist->save();

            $this->_eventManager->dispatch(
                'wishlist_add_product',
                ['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_redirect->getRefererUrl();
            }

            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();

            $this->messageManager->addComplexSuccessMessage(
                'addProductSuccessMessage',
                [
                    'product_name' => $product->getName(),
                    'referer' => $referer
                ]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t add the item to Wish List right now: %1.', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t add the item to Wish List right now.')
            );
        }

        $resultRedirect->setPath('customer/account', ['wishlist_id' => $wishlist->getId()]);
        return $resultRedirect;
    }
}

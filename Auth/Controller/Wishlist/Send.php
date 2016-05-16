<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Camping\Auth\Controller\Wishlist;

use Magento\Framework\App\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Session\Generic as WishlistSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Layout as ResultLayout;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Send extends \Magento\Wishlist\Controller\AbstractIndex
{
    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerHelperView;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Wishlist\Model\Config
     */
    protected $_wishlistConfig;

    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var WishlistSession
     */
    protected $wishlistSession;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Customer\Helper\View $customerHelperView
     * @param WishlistSession $wishlistSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Wishlist\Model\Config $wishlistConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Customer\Helper\View $customerHelperView,
        WishlistSession $wishlistSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->_wishlistConfig = $wishlistConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->_customerHelperView = $customerHelperView;
        $this->wishlistSession = $wishlistSession;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        //$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }

        $sharingLimit = $this->_wishlistConfig->getSharingEmailLimit();
        $textLimit = $this->_wishlistConfig->getSharingTextLimit();
        $emailsLeft = $sharingLimit - $wishlist->getShared();

        $emails = $this->getRequest()->getPost('emails');
        $wishlistId = $this->getRequest()->getPost('wishlist_id');
        $emails = empty($emails) ? $emails : explode(',', $emails);


        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $this->addLayoutHandles($resultLayout);
        $this->inlineTranslation->suspend();

        $sent = 0;
        $result = $this->_resultJsonFactory->create();

        try {
            $customer = $this->_customerSession->getCustomerDataObject();
            $customerName = $this->_customerHelperView->getCustomerName($customer);

            $message = $this->getRssLink($wishlistId, $resultLayout);
            $emails = array_unique($emails);
            $sharingCode = $wishlist->getSharingCode();

            try {
                foreach ($emails as $email) {
                    $transport = $this->_transportBuilder->setTemplateIdentifier(
                        $this->scopeConfig->getValue(
                            'wishlist/email/email_template',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getStoreId(),
                        ]
                    )->setTemplateVars(
                        [
                            'customer' => $customer,
                            'customerName' => $customerName,
                            'salable' => $wishlist->isSalable() ? 'yes' : '',
                            'items' => $this->getWishlistItems($resultLayout),
                            'viewOnSiteLink' => $this->_url->getUrl('*/shared/index', ['code' => $sharingCode]),
                            'message' => $message,
                            'store' => $this->storeManager->getStore(),
                        ]
                    )->setFrom(
                        $this->scopeConfig->getValue(
                            'wishlist/email/email_identity',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo(
                        $email
                    )->getTransport();

                    $transport->sendMessage();

                    $sent++;
                }
            } catch (\Exception $e) {
                $wishlist->setShared($wishlist->getShared() + $sent);
                $wishlist->save();
                throw $e;
            }
            $wishlist->setShared($wishlist->getShared() + $sent);
            $wishlist->save();

            $this->inlineTranslation->resume();
            $result->setData(
                ['success' => true]);
            return $result;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $result->setData(
                ['success' => false]);
            $this->wishlistSession->setSharingForm($this->getRequest()->getPostValue());
            return $result;
        }
    }

    /**
     * Prepare to load additional email blocks
     *
     * Add 'wishlist_email_rss' layout handle.
     * Add 'wishlist_email_items' layout handle.
     *
     * @param \Magento\Framework\View\Result\Layout $resultLayout
     * @return void
     */
    protected function addLayoutHandles(ResultLayout $resultLayout)
    {
        if ($this->getRequest()->getParam('rss_url')) {
            $resultLayout->addHandle('wishlist_email_rss');
        }
        $resultLayout->addHandle('wishlist_email_items');
    }

    /**
     * Retrieve RSS link content (html)
     *
     * @param int $wishlistId
     * @param \Magento\Framework\View\Result\Layout $resultLayout
     * @return mixed
     */
    protected function getRssLink($wishlistId, ResultLayout $resultLayout)
    {
        if ($this->getRequest()->getParam('rss_url')) {
            return $resultLayout->getLayout()
                ->getBlock('wishlist.email.rss')
                ->setWishlistId($wishlistId)
                ->toHtml();
        }
    }

    /**
     * Retrieve wishlist items content (html)
     *
     * @param \Magento\Framework\View\Result\Layout $resultLayout
     * @return string
     */
    protected function getWishlistItems(ResultLayout $resultLayout)
    {
        return $resultLayout->getLayout()
            ->getBlock('wishlist.email.items')
            ->toHtml();
    }
}

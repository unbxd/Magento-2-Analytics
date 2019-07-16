<?php
/**
 * Copyright (c) 2019 Unbxd Inc.
 */

/**
 * Init development:
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace Unbxd\Analytics\Model\EventDataProvider;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\HTTP\Header as HTTPHeader;
use Unbxd\Analytics\Model\ResourceModel\EventDataProvider as ResourceEventDataProvider;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\Math\Random;
use Unbxd\Analytics\Helper\Data as HelperData;
use Magento\Catalog\Model\Session as CatalogSession;
use Unbxd\Analytics\Model\Config;
use Unbxd\ProductFeed\Model\Serializer;

/**
 * Class AbstractProvider
 * @package Unbxd\Analytics\Model\EventDataProvider
 */
abstract class AbstractProvider
{
    /**
     * Product view layout handler
     */
    const PRODUCT_VIEW_LAYOUT_HANDLE = 'catalog_product_view';

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $requestHttp;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var HTTPHeader
     */
    private $httpHeader;

    /**
     * @var ResourceEventDataProvider
     */
    private $resourceEventDataProvider;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var OrderCollectionFactory
     */
    protected $salesOrderCollection;

    /**
     * @var Random
     */
    private $random;

    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * @var CatalogSession
     */
    private $catalogSession;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * AbstractProvider constructor.
     * @param LayoutInterface $layout
     * @param RedirectInterface $redirect
     * @param RequestInterface $request
     * @param UrlInterface $url
     * @param HTTPHeader $httpHeader
     * @param ResourceEventDataProvider $resourceEventDataProvider
     * @param Registry $registry
     * @param CookieManagerInterface $cookieManager
     * @param OrderCollectionFactory $salesOrderCollection
     * @param Random $random
     * @param HelperData $helperData
     * @param CatalogSession $catalogSession
     * @param $registryKey
     * @param Serializer|null $serializer
     */
    public function __construct(
        LayoutInterface $layout,
        RedirectInterface $redirect,
        RequestInterface $request,
        UrlInterface $url,
        HTTPHeader $httpHeader,
        ResourceEventDataProvider $resourceEventDataProvider,
        Registry $registry,
        CookieManagerInterface $cookieManager,
        OrderCollectionFactory $salesOrderCollection,
        Random $random,
        HelperData $helperData,
        CatalogSession $catalogSession,
        Serializer $serializer = null
    ) {
        $this->layout = $layout;
        $this->redirect = $redirect;
        $this->request = $request;
        $this->url = $url;
        $this->httpHeader = $httpHeader;
        $this->resourceEventDataProvider = $resourceEventDataProvider;
        $this->registry = $registry;
        $this->cookieManager = $cookieManager;
        $this->salesOrderCollection = $salesOrderCollection;
        $this->random = $random;
        $this->helperData = $helperData;
        $this->catalogSession = $catalogSession;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Serializer::class);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    private function isAjaxRequest()
    {
        return $this->getRequest()->isAjax();
    }

    /**
     * @return string
     */
    public function getReferrerUrl()
    {
        $referrerUrl = $this->redirect->getRefererUrl();
        if ($this->isAjaxRequest()) {
            $referrerUrl = $this->httpHeader->getHttpReferer();
        }

        return $referrerUrl;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        $currentUrl = $this->url->getCurrentUrl();
        if ($this->isAjaxRequest()) {
            $currentUrl = $this->httpHeader->getHttpReferer();
        }

        return $currentUrl;
    }

    /**
     * Generate random string
     *
     * @param bool $useDigits
     * @param bool $useLowers
     * @param bool $useUppers
     * @param int $length
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateRandomString($useDigits = true, $useLowers = false, $useUppers = false, $length = 10)
    {
        $chars = '';
        if ($useDigits) {
            $chars .= Random::CHARS_DIGITS;
        }
        if ($useLowers) {
            $chars .= Random::CHARS_LOWERS;
        }
        if ($useUppers) {
            $chars .= Random::CHARS_UPPERS;
        }

        return $this->random->getRandomString($length, $chars);
    }

    /**
     * @return mixed
     */
    private function getSiteKey()
    {
        return $this->helperData->getSiteKey();
    }

    /**
     * Retrieve frontend cookie value generated by unbxdAnalytics.js
     *
     * @param null $name
     * @param null $default
     * @return string|null
     */
    public function getCookie($name = null, $default = null)
    {
        if (null === $name) {
            return $_COOKIE;
        }

        $cookie = $this->cookieManager->getCookie($name);
        if (!$cookie) {
            $cookie = isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
        }

        return $cookie;
    }

    /**
     * Build customer id request parameter
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerIdParameter()
    {
        return sprintf('uid-%s-%s', time(), $this->generateRandomString());
    }

    /**
     * Build time request parameter
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getTimeParameter()
    {
        return sprintf('%s|%s', time(), $this->generateRandomString());
    }

    /**
     * Get default request parameters which are included in main data
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDefaultDataParameters()
    {
        return [
            Config::PARAM_CURRENT_URL => $this->getCurrentUrl(),
            Config::PARAM_REFERRER_URL => $this->getReferrerUrl(),
            Config::PARAM_VISIT_TYPE => $this->getCookie(Config::COOKIE_VISIT_TYPE, 'first_time'),
            Config::PARAM_VERSION => Config::ANALYTICS_VERSION,
            Config::PARAM_VISIT_ID => $this->getCookie(Config::COOKIE_VISIT_ID, $this->generateRandomString())
        ];
    }

    /**
     * Get default request parameters which are not included in main data
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDefaultInlineParameters()
    {
        return [
            Config::PARAM_SITE_KEY => $this->getSiteKey(),
            Config::PARAM_CUSTOMER_ID => $this->getCookie(Config::COOKIE_USER_ID, $this->getCustomerIdParameter()),
            Config::PARAM_TIME => $this->getTimeParameter()
        ];
    }

    /**
     * Get current page ID
     *
     * @return mixed
     */
    private function getPageId()
    {
        return $this->getRequest()->getParam('id') ?: $this->getRequest()->getParam('page_id');
    }

    /**
     * Retrieve last visited category id
     * Uses in events 'product click' and 'add to cart'
     *
     * @return int|null
     */
    private function getLastVisitedCategoryId()
    {
        $categoryId = $this->catalogSession->getLastVisitedCategoryId();
        if (!$categoryId) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->registry->registry('current_category');
            $categoryId = null;
            if ($category instanceof \Magento\Catalog\Model\Category) {
                $categoryId = $category->getCategoryId();
            }
        }

        return $categoryId;
    }

    /**
     * In case if product was processed from category or search results page - add additional parameters:
     * 'query' - search query (search results page)
     * 'page' - unique identifier for the page (category page)
     *
     * @param array $params
     * @return $this
     */
    public function addExtraParameters(array &$params)
    {
        $queryParams = $this->getRequest()->getQueryValue();
        $referrerUrl = $this->getReferrerUrl();
        // if query params for current request are empty, try to detect query params from referrer url (if any)
        if (empty($queryParams) && (strlen($referrerUrl) > 0)) {
            parse_str(parse_url($referrerUrl, PHP_URL_QUERY), $queryParams);
        }

        if (!empty($queryParams)) {
            // search results page
            if (isset($queryParams['q'])) {
                $query = trim($queryParams['q']);
            } else {
                $query = http_build_query($queryParams);
            }
            $params[Config::PARAM_QUERY] = $query;
        } else {
            // category view page
            if ($pageId = $this->getLastVisitedCategoryId()) {
                $params[Config::PARAM_PAGE] = $pageId;
            }
        }

        return $this;
    }

    /**
     * Retrieve product SKU by related ID
     *
     * @param $entityId
     * @return string
     * @throws \Exception
     */
    public function getSkuById($entityId)
    {
        return $this->resourceEventDataProvider->getSkuById($entityId);
    }
}
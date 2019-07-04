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
     * @var string
     */
    private $registryKey;

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
        $registryKey,
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
        $this->registryKey = $registryKey;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Serializer::class);

        if (!$this->getRegistryData()) {
            $this->setRegistryData([
                Config::PARAM_CURRENT_URL => $this->url->getCurrentUrl(),
                Config::PARAM_REFERRER_URL => $this->redirect->getRefererUrl(),
                Config::PARAM_PAGE => $this->getPageId()
            ]);
        }
    }

    /**
     * @param null $key
     * @return mixed
     */
    public function getRegistryData($key = null)
    {
        if (null === $key) {
            $key = $this->registryKey;
        }

        return $this->registry->registry($key);
    }

    /**
     * @param $data
     * @param null $key
     */
    private function setRegistryData($data, $key = null)
    {
        if (null === $key) {
            $key = $this->registryKey;
        }

        $this->registry->register($key, $data);
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
     * @return array
     */
    public function getDefaultDataParameters()
    {
        return [
            Config::PARAM_CURRENT_URL => $this->getCurrentUrl(),
            Config::PARAM_REFERRER_URL => $this->getReferrerUrl(),
            Config::PARAM_VISIT_TYPE => $this->getCookie(Config::COOKIE_VISIT_TYPE, 'first_time')
        ];
    }

    /**
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
     * In case if product was added from search results page - add additional parameters:
     * 'query' - search query
     * 'page' - unique identifier for the page
     *
     * @param array $params
     * @return $this
     */
    public function addExtraParameters(array &$params)
    {
        $queryParams = $this->getRequest()->getQueryValue();
        if (!empty($queryParams)) {
            $query = http_build_query($queryParams);
            $params[Config::PARAM_QUERY] = $query;
            $params[Config::PARAM_PAGE] = $this->getPageId();
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
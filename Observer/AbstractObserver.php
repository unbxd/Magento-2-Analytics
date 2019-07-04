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
namespace Unbxd\Analytics\Observer;

use Unbxd\Analytics\Model\EventDataProvider;
use Unbxd\ProductFeed\Model\Feed\Api\ConnectorFactory;
use Unbxd\ProductFeed\Model\Feed\Api\Connector as ApiConnector;
use Unbxd\ProductFeed\Model\CustomerIp;
use Magento\Framework\HTTP\Header as HTTPHeader;
use Unbxd\Analytics\Helper\Data as HelperData;
use Unbxd\Analytics\Model\Config;

/**
 * Class AbstractObserver
 * @package Unbxd\ProductFeed\Observer
 */
abstract class AbstractObserver
{
    /**
     * @var EventDataProvider
     */
    private $eventDataProvider;

    /**
     * @var ConnectorFactory
     */
    private $connectorFactory;

    /**
     * @var CustomerIp
     */
    protected $customerIp;

    /**
     * @var HTTPHeader
     */
    private $httpHeader;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Local cache for feed API connector manager
     *
     * @var null
     */
    private $connectorManager = null;

    /**
     * AbstractObserver constructor.
     * @param EventDataProvider $eventDataProvider
     * @param ConnectorFactory $connectorFactory
     * @param CustomerIp $customerIp
     * @param HTTPHeader $httpHeader
     * @param HelperData $helperData
     */
    public function __construct(
        EventDataProvider $eventDataProvider,
        ConnectorFactory $connectorFactory,
        CustomerIp $customerIp,
        HTTPHeader $httpHeader,
        HelperData $helperData
    ) {
        $this->eventDataProvider = $eventDataProvider;
        $this->connectorFactory = $connectorFactory;
        $this->customerIp = $customerIp;
        $this->httpHeader = $httpHeader;
        $this->helperData = $helperData;
    }

    /**
     * @param $type
     * @return \Unbxd\Analytics\Model\EventDataProviderInterface|null
     */
    public function getEventDataProvider($type)
    {
        return $this->eventDataProvider->getEventDataReader($type);
    }

    /**
     * Retrieve connector manager instance. Init if needed
     *
     * @return ApiConnector|null
     */
    public function getConnectorManager()
    {
        if (null == $this->connectorManager) {
            /** @var ApiConnector */
            $this->connectorManager = $this->connectorFactory->create();
        }

        return $this->connectorManager;
    }

    /**
     * @param array $params
     * @return string
     */
    private function getApiEndpoint(array $params)
    {
        return sprintf(
            '%s?%s',
            $this->helperData->getAnalyticsApiEndpoint(),
            urldecode(http_build_query($params))
        );
    }

    /**
     * @return array
     */
    private function getHeaders()
    {
        return [
            "X-Forwarded-For: {$this->customerIp->getCurrentIp()}",
            "user-agent: {$this->httpHeader->getHttpUserAgent()}"
        ];
    }

    /**
     * @param array $params
     * @return $this
     */
    public function sendAnalytics(array $params)
    {
        /** @var ApiConnector $connectorManager */
        $connectorManager = $this->getConnectorManager();
        try {
            $connectorManager->resetHeaders()
                ->resetParams()
                ->setApiUrl($this->getApiEndpoint($params))
                ->execute(
                    Config::API_REQUEST_TYPE_ANALYTICS,
                    \Zend_Http_Client::POST,
                    $this->getHeaders()
                );
        } catch (\Exception $e) {
            return $this;
        }

        $connectorManager->resetResponse();

        return $this;
    }
}
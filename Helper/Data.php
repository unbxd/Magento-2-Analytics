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
namespace Unbxd\Analytics\Helper;

use Unbxd\ProductFeed\Helper\Data as FeedHelperData;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Unbxd\Analytics\Helper
 */
class Data extends FeedHelperData
{
    /**
     * XML paths
     *
     * analytics section
     */
    const XML_PATH_ANALYTICS_ENABLED = 'unbxd_analytics/general/enabled';

    /**
     * API endpoints
     */
    const XML_PATH_ANALYTICS = 'unbxd_analytics/general/api_endpoint';

    /**
     * @param null $store
     * @return string
     */
    public function getIsAnalyticsEnabled($store = null)
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_ANALYTICS_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return bool
     */
    public function getIsAnalyticsAvailable($store = null)
    {
        return $this->getIsAnalyticsEnabled($store) && (bool) $this->getSiteKey($store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getAnalyticsApiEndpoint($store = null)
    {
        return trim($this->scopeConfig->getValue(
            self::XML_PATH_ANALYTICS,
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }
}
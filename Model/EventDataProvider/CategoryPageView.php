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

use Unbxd\Analytics\Model\EventDataProvider\AbstractProvider;
use Unbxd\Analytics\Model\EventDataProviderInterface;
use Unbxd\Analytics\Model\Config;

/**
 * Class CategoryPageView
 * @package Unbxd\Analytics\Model\EventDataProvider
 */
class CategoryPageView extends AbstractProvider implements EventDataProviderInterface
{
    /**
     * Event type code
     */
    const EVENT_TYPE_CODE = 'categoryPage';

    /**
     * Category page types
     */
    const CATEGORY_PAGE_TYPE_PATH = 'CATEGORY_PATH'; // default state
    const CATEGORY_PAGE_TYPE_TAXONOMY_NODE = 'TAXONOMY_NODE'; // if category ID was passed to unbxd service
    const CATEGORY_PAGE_TYPE_URL = 'URL'; // for landing page

    /**
     * @param array $params
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function buildEventData($params = [])
    {
        $data = [
            Config::PARAM_CATEGORY_PAGE_TYPE => self::CATEGORY_PAGE_TYPE_PATH,
            Config::PARAM_CATEGORY_PAGE => $this->getRequest()->getRequestString()
        ];

        $data = array_merge($data, $this->getDefaultDataParameters());

        $serializedData = $this->serializer->serialize($data);
        $eventData = [
            Config::PARAM_DATA => $serializedData,
            Config::PARAM_ACTION => self::EVENT_TYPE_CODE
        ];

        $eventData = array_merge($eventData, $this->getDefaultInlineParameters());

        return $eventData;
    }
}
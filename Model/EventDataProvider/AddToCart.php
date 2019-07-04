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

use Unbxd\Analytics\Model\EventDataProviderInterface;
use Unbxd\Analytics\Model\Config;

/**
 * Class AddToCart
 * @package Unbxd\Analytics\Model\EventDataProvider
 */
class AddToCart extends AbstractProvider implements EventDataProviderInterface
{
    /**
     * Event type code
     */
    const EVENT_TYPE_CODE = 'cart';

    /**
     * @param array $params
     * @return array|mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function buildEventData($params = [])
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = isset($params['quote_item']) ? $params['quote_item'] : null;
        if (!$quoteItem) {
            return null;
        }

        $quoteItemId = $quoteItem->getProduct()->getId();
        // try to get child product ID if any
        if ($option = $quoteItem->getOptionByCode('simple_product')) {
            $quoteItemId = $option->getProduct()->getId();
        }

        $parentProductId = $this->getRequest()->getParam('product') ?: $quoteItem->getProduct()->getId();
        $data = [
            Config::PARAM_PRODUCT_ID => $parentProductId,
            Config::PARAM_PRODUCT_QTY => $quoteItem->getQty() ?: 1
        ];
        $data = array_merge($data, $this->getDefaultDataParameters());

        if ($quoteItemId != $parentProductId) {
            $data[Config::PARAM_VARIANT_ID] = $quoteItemId;
        }

        $this->addExtraParameters($data);

        $serializedData = $this->serializer->serialize($data);
        $eventData = [
            Config::PARAM_DATA => $serializedData,
            Config::PARAM_ACTION => self::EVENT_TYPE_CODE
        ];

        $eventData = array_merge($eventData, $this->getDefaultInlineParameters());

        return $eventData;
    }
}
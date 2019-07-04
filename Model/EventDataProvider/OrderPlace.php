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
 * Class OrderPlace
 * @package Unbxd\Analytics\Model\EventDataProvider
 */
class OrderPlace extends AbstractProvider implements EventDataProviderInterface
{
    /**
     * Event type code
     */
    const EVENT_TYPE_CODE = 'order';

    /**
     * @param array $params
     * @return array|mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function buildEventData($params = [])
    {
        $orderIds = isset($params['order_ids']) ? $params['order_ids'] : [];
        if (empty($orderIds)) {
            return null;
        }

        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collection */
        $collection = $this->salesOrderCollection->create();
        $collection->addFieldToFilter('entity_id', ['in' => $orderIds]);
        $output = [];
        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            foreach ($order->getAllVisibleItems() as $item) {
                /** @var \Magento\Sales\Model\Order\Item $item */
                $orderItemData = [
                    Config::PARAM_PRODUCT_ID => $item->getProductId(),
                    Config::PARAM_PRODUCT_PRICE => $item->getPrice(),
                    Config::PARAM_PRODUCT_QTY => $item->getQtyOrdered() * 1,
                ];
                $orderItemData = array_merge($orderItemData, $this->getDefaultDataParameters());
                $serializedOrderItemData = $this->serializer->serialize($orderItemData);
                $eventData = [
                    Config::PARAM_DATA => $serializedOrderItemData,
                    Config::PARAM_ACTION => self::EVENT_TYPE_CODE
                ];
                $finalEventData = array_merge($eventData, $this->getDefaultInlineParameters());
                $output[] = $finalEventData;
            }
        }

        return $output;
    }
}
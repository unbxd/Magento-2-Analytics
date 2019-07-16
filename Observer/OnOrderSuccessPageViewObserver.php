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

use Unbxd\Analytics\Model\EventDataProvider\OrderPlace;
use Unbxd\Analytics\Observer\AbstractObserver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class OnOrderSuccessPageViewObserver
 * @package Unbxd\Analytics\Observer
 */
class OnOrderSuccessPageViewObserver extends AbstractObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        // check if analytics is available
        if (!$this->helperData->getIsAnalyticsAvailable()) {
            return $this;
        }

        /** @var OrderPlace $eventDataProvider */
        $eventDataProvider = $this->getEventDataProvider(OrderPlace::EVENT_TYPE_CODE);
        if (null === $eventDataProvider) {
            return $this;
        }

        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return $this;
        }

        $eventDataCalls = $eventDataProvider->buildEventData(['order_ids' => $orderIds]);
        if (empty($eventDataCalls)) {
            return $this;
        }

        // according to Unbxd doc.: if a user buys multiple products in a single order,
        // then multiple order events should be fired.
        foreach ($eventDataCalls as $eventData) {
            $this->sendAnalytics($eventData);
        }

        return $this;
    }
}
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

use Unbxd\Analytics\Model\EventDataProvider\RemoveFromCart;
use Unbxd\Analytics\Observer\AbstractObserver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class OnRemoveFromCartObserver
 * @package Unbxd\Analytics\Observer
 */
class OnRemoveFromCartObserver extends AbstractObserver implements ObserverInterface
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

        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = $observer->getEvent()->getQuoteItem();

        /** @var RemoveFromCart $eventDataProvider */
        $eventDataProvider = $this->getEventDataProvider(RemoveFromCart::EVENT_TYPE_CODE);
        if (null === $eventDataProvider) {
            return $this;
        }

        $eventData = $eventDataProvider->buildEventData(['quote_item' => $quoteItem]);
        if (empty($eventData)) {
            return $this;
        }

        $this->sendAnalytics($eventData);

        return $this;
    }
}
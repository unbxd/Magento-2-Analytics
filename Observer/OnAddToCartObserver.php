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

use Unbxd\Analytics\Model\EventDataProvider\AddToCart;
use Unbxd\Analytics\Observer\AbstractObserver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class OnProductPageViewObserver
 * @package Unbxd\ProductFeed\Observer
 */
class OnAddToCartObserver extends AbstractObserver implements ObserverInterface
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

        /** @var AddToCart $eventDataProvider */
        $eventDataProvider = $this->getEventDataProvider(AddToCart::EVENT_TYPE_CODE);
        if (null === $eventDataProvider) {
            return $this;
        }

        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = $observer->getEvent()->getQuoteItem();

        $eventData = $eventDataProvider->buildEventData(['quote_item' => $quoteItem]);
        if (empty($eventData)) {
            return $this;
        }

        $this->sendAnalytics($eventData);

        return $this;
    }
}
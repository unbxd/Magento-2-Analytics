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

use Unbxd\Analytics\Observer\AbstractObserver;
use Unbxd\Analytics\Model\EventDataProvider\ProductPageClick;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class OnProductPageClickObserver
 * @package Unbxd\Analytics\Observer
 */
class OnProductPageClickObserver extends AbstractObserver implements ObserverInterface
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

        /** @var ProductPageClick $eventDataProvider */
        $eventDataProvider = $this->getEventDataProvider(ProductPageClick::EVENT_TYPE_CODE);
        if (null === $eventDataProvider) {
            return $this;
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();

        $eventData = $eventDataProvider->buildEventData(['product' => $product]);
        if (empty($eventData)) {
            return $this;
        }

        $this->sendAnalytics($eventData);

        return $this;
    }
}
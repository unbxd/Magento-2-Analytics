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

use Unbxd\Analytics\Model\EventDataProvider\ProductPageView;
use Unbxd\Analytics\Observer\AbstractObserver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class OnProductPageViewObserver
 * @package Unbxd\ProductFeed\Observer
 */
class OnProductPageViewObserver extends AbstractObserver implements ObserverInterface
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

        $fullActionName = $observer->getEvent()->getFullActionName();
        if ($fullActionName != ProductPageView::PRODUCT_VIEW_LAYOUT_HANDLE) {
            // tracking only product page view
            return $this;
        }

        /** @var ProductPageView $eventDataProvider */
        $eventDataProvider = $this->getEventDataProvider(ProductPageView::EVENT_TYPE_CODE);
        if (null === $eventDataProvider) {
            return $this;
        }

        $eventData = $eventDataProvider->buildEventData();
        if (empty($eventData)) {
            return $this;
        }

        $this->sendAnalytics($eventData);

        return $this;
    }
}
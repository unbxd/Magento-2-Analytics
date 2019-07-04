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
use Magento\Catalog\Api\Data\ProductInterface;
use Unbxd\Analytics\Model\Config;

/**
 * Class ProductPageView
 * @package Unbxd\Analytics\Model\EventDataProvider
 */
class ProductPageView extends AbstractProvider implements EventDataProviderInterface
{
    /**
     * Event type code
     */
    const EVENT_TYPE_CODE = 'product_view';

    /**
     * @param array $params
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function buildEventData($params = [])
    {
        $data = [
            Config::PARAM_PRODUCT_ID => $this->getProductIdentifier()
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

    /**
     * @param bool $isSku
     * @return mixed|string
     * @throws \Exception
     */
    private function getProductIdentifier($isSku = false)
    {
        $identifier = $this->getRequest()->getParam('id');
        if (!$identifier) {
            $identifier = $this->retrieveProductIdentifierByLayoutHandles($isSku);
        }

        return $identifier;
    }

    /**
     * @param bool $isSku
     * @return mixed|null
     */
    private function retrieveProductIdentifierByLayoutHandles($isSku = false)
    {
        $identifier = null;
        $needle = $isSku ? ProductInterface::SKU : 'id';
        foreach ($this->layout->getUpdate()->getHandles() as $handle) {
            if (strpos($handle, $needle)) {
                $identifier = str_replace(
                    sprintf('%s_%s_', self::PRODUCT_VIEW_LAYOUT_HANDLE, $needle),
                    '',
                    $handle
                );
                break;
            }
        }

        return $identifier;
    }
}
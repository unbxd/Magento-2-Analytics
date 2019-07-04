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
namespace Unbxd\Analytics\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Unbxd\Analytics\Helper\Data as HelperData;

/**
 * Class Analytics
 * @package Unbxd\Analytics\Block
 */
class Analytics extends Template
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Analytics constructor.
     * @param Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function isAnalyticsAvailable()
    {
        return $this->helperData->getIsAnalyticsAvailable();
    }

    /**
     * @return mixed
     */
    public function getSiteKey()
    {
        return $this->helperData->getSiteKey();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->isAnalyticsAvailable()) {
            return '';
        }

        return parent::_toHtml();
    }
}
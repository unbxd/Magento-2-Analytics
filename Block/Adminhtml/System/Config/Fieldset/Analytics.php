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
namespace Unbxd\Analytics\Block\Adminhtml\System\Config\Fieldset;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Unbxd\Analytics\Helper\Module as ModuleHelper;
use Magento\Framework\View\Asset\Repository as AssetRepository;

/**
 * Class Analytics
 * @package Unbxd\Analytics\Block\Adminhtml\System\Config\Fieldset
 */
class Analytics extends Template implements RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'Unbxd_Analytics::system/config/fieldset/analytics.phtml';

    /**
     * Static resources
     *
     * @var array
     */
    protected static $unbxdReferenceUrls = [
        'base' => '//unbxd.com',
        'search_doc' => '//unbxd.com/documentation/site-search/v2-search-analytics-integration-browser/'
    ];

    /**
     * @var ModuleHelper
     */
    private $moduleHelper;

    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * Search constructor.
     * @param Context $context
     * @param ModuleHelper $moduleHelper
     * @param AssetRepository $assetRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ModuleHelper $moduleHelper,
        AssetRepository $assetRepository,
        array $data = []
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->assetRepository = $assetRepository;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return mixed
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '';
        if ($element->getData('group')['id'] == 'analytics_header') {
            $html = $this->toHtml();
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getLogoSrc()
    {
        return $this->assetRepository->getUrl("Unbxd_ProductFeed::images/unbxd_logo.png");
    }

    /**
     * @return string
     */
    public function getModuleInformation()
    {
        $moduleFullName = $this->moduleHelper->getModuleName();
        $moduleShortName = substr($moduleFullName, strpos($moduleFullName, '_') + 1);

        return __(sprintf('%s v. %s', $moduleShortName, $this->getModuleVersion()));
    }

    /**
     * @return mixed
     */
    public function getModuleVersion()
    {
        return $this->moduleHelper->getModuleInfo()->getVersion();
    }

    /**
     * @param string $type
     * @return mixed|string
     */
    public static function getUnbxdReferenceUrl($type = null)
    {
        return isset(self::$unbxdReferenceUrls[$type]) ? self::$unbxdReferenceUrls['base'] : '';
    }
}
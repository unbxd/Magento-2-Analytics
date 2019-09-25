# Unbxd Analytics Module For Magento 2

[![Latest Stable Version](https://poser.pugx.org/unbxd/magento2-analytics/v/stable)](https://packagist.org/packages/unbxd/magento2-analytics)
[![Total Downloads](https://poser.pugx.org/unbxd/magento2-analytics/downloads)](https://packagist.org/packages/unbxd/magento2-analytics)
[![Monthly Downloads](https://poser.pugx.org/unbxd/magento2-analytics/d/monthly)](https://packagist.org/packages/unbxd/magento2-analytics)
[![Daily Downloads](https://poser.pugx.org/unbxd/magento2-analytics/d/daily)](https://packagist.org/packages/unbxd/magento2-analytics)
[![License](https://poser.pugx.org/unbxd/magento2-analytics/license)](https://packagist.org/packages/unbxd/magento2-analytics)
[![composer.lock](https://poser.pugx.org/unbxd/magento2-analytics/composerlock)](https://packagist.org/packages/unbxd/magento2-analytics)

This module provide possibility tracking analytics for Unbxd service.

Support Magento 2.1.\* || 2.2.\* || 2.3.\*

# Installation Guide

### Install by composer

```
composer require unbxd/magento2-analytics
php bin/magento module:enable Unbxd_ProductFeed
php bin/magento module:enable Unbxd_Analytics
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

### Manual installation

1. Download module Unbxd_ProductFeed [Link](https://github.com/unbxd/Magento-2-Extension/archive/1.0.27.zip)
2. Download this module [Link](https://github.com/unbxd/Magento-2-Analytics/archive/1.0.5.zip)
3. Unzip modules in the folders:

    app\code\Unbxd\ProductFeed  
    app\code\Unbxd\Analytics

4. Access the root of you Magento 2 instance from command line and run the following commands:

```
php bin/magento module:enable Unbxd_ProductFeed
php bin/magento module:enable Unbxd_Analytics
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

5. Configure module in backend


 


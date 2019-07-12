# Unbxd Analytics Module For Magento 2

This module provide possibility tracking analytics for Unbxd service.

Support Magento 2.2.\* || 2.3.\*

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

1. Download module Unbxd_ProductFeed [Link](https://github.com/unbxd/Magento-2-Extension/archive/1.0.13.zip)
2. Download this module [Link](https://github.com/unbxd/Magento-2-Analytics/archive/1.0.1.zip)
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


 


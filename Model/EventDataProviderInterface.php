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
namespace Unbxd\Analytics\Model;

/**
 * Interface EventDataProviderInterface
 * @package Unbxd\Analytics\Model
 */
interface EventDataProviderInterface
{
    /**
     * Retrieve needed data for specific event type (add to cart, place order, etc...)
     *
     * @param array $params
     * @return mixed
     */
    public function buildEventData($params = []);
}

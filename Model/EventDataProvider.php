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

use Unbxd\Analytics\Model\EventDataProviderInterface;

/**
 * Class EventDataProvider
 * @package Unbxd\Analytics\Model
 */
class EventDataProvider
{
    /**
     * @var EventDataProviderInterface[]
     */
    private $eventDataReaderPool = [];

    /**
     * EventDataProvider constructor.
     * @param array $eventDataReaderPool
     */
    public function __construct(
        $eventDataReaderPool = []
    ) {
        $this->eventDataReaderPool = $eventDataReaderPool;
    }

    /**
     * Retrieve event data readers list.
     *
     * @return EventDataProviderInterface[]
     */
    public function getEventDataReaders()
    {
        return $this->eventDataReaderPool;
    }

    /**
     * Retrieve a specific event data reader by event type.
     *
     * @param string $type
     * @return EventDataProviderInterface|null
     */
    public function getEventDataReader($type)
    {
        return isset($this->eventDataReaderPool[$type]) ? $this->eventDataReaderPool[$type] : null;
    }
}
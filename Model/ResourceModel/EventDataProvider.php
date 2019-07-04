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
namespace Unbxd\Analytics\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class EventDataProvider
 * @package Unbxd\Analytics\Model\ResourceModel
 */
class EventDataProvider
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * AbstractProvider constructor.
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resource
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resource
    ) {
        $this->metadataPool = $metadataPool;
        $this->resource = $resource;
    }

    /**
     * Get resource connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection();
    }

    /**
     * Get table name using the adapter.
     *
     * @param $tableName
     * @return string
     */
    private function getTable($tableName)
    {
        return $this->resource->getTableName($tableName);
    }

    /**
     * Retrieve Metadata for an entity by entity type
     *
     * @param $entityType
     * @return EntityMetadataInterface
     * @throws \Exception
     */
    private function getEntityMetaData($entityType)
    {
        return $this->metadataPool->getMetadata($entityType);
    }

    /**
     * Retrieve product SKU by related ID
     *
     * @param $entityId
     * @return string
     * @throws \Exception
     */
    public function getSkuById($entityId)
    {
        $metadata = $this->getEntityMetaData(ProductInterface::class);
        $entityTable = $this->getTable($metadata->getEntityTable());
        $select = $this->getConnection()->select()
            ->from(['e' => $entityTable])
            ->where(sprintf('e.%s = ?', $metadata->getIdentifierField()), $entityId)
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(ProductInterface::SKU);

        return $this->getConnection()->fetchOne($select);
    }
}
<?php

namespace TemplateMonster\Parallax\Model\ResourceModel\Block\Relation\Store;

use TemplateMonster\Parallax\Api\Data\BlockInterface;
use TemplateMonster\Parallax\Model\ResourceModel\Block;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SaveHandler.
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Block
     */
    protected $resourceBlock;

    /**
     * @param MetadataPool $metadataPool
     * @param Block        $resourceBlock
     */
    public function __construct(
        MetadataPool $metadataPool,
        Block $resourceBlock
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBlock = $resourceBlock;
    }

    /**
     * @param object $entity
     * @param array  $arguments
     *
     * @return object
     *
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(BlockInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldStores = $this->resourceBlock->lookupStoreIds((int) $entity->getId());
        $newStores = (array) $entity->getStores();
        if (empty($newStores)) {
            $newStores = (array) $entity->getStoreId();
        }

        $table = $this->resourceBlock->getTable('parallax_block_store');

        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                $linkField.' = ?' => (int) $entity->getData($linkField),
                'store_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    $linkField => (int) $entity->getData($linkField),
                    'store_id' => (int) $storeId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}

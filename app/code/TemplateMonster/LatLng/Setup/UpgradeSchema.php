<?php

namespace TemplateMonster\LatLng\Setup;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();


        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('quote_address'),
                'lat',
                [
                    'type' => 'text',
                    'length' => 255,
                    'comment' => 'Latitude',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote_address'),
                'lon',
                [
                    'type' => 'text',
                    'length' => 255,
                    'comment' => 'Longitude',
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_address'),
                'lat',
                [
                    'type' => 'text',
                    'length' => 255,
                    'comment' => 'Latitude',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_address'),
                'lon',
                [
                    'type' => 'text',
                    'length' => 255,
                    'comment' => 'Longitude',
                ]
            );
        }


        $setup->endSetup();
    }
}
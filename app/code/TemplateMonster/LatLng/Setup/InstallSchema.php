<?php

namespace TemplateMonster\LatLng\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'lat',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Latitude',
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'lon',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Longitude',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'lat',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Latitude',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'lon',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Longitude',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'lat',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Latitude',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'lon',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Longitude',
            ]
        );

        $setup->endSetup();
    }
}
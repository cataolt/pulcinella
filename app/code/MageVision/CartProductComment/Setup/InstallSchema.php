<?php
/**
 * MageVision Cart Product Comment Extension
 *
 * @category     MageVision
 * @package      MageVision_CartProductComment
 * @author       MageVision Team
 * @copyright    Copyright (c) 2017 MageVision (http://www.magevision.com)
 * @license      LICENSE_MV.txt or http://www.magevision.com/license-agreement/
 */
namespace MageVision\CartProductComment\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

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

        if ($installer->tableExists('quote_item')) {
            $installer->getConnection()
                    ->addColumn(
                        $installer->getTable('quote_item'),
                        'comment',
                        [
                            'type' => Table::TYPE_TEXT,
                            'length' => 255,
                            'default' => null,
                            'comment' => 'Comment'
                        ]
                    );
        }
        if ($installer->tableExists('sales_order_item')) {
            $installer->getConnection()
                    ->addColumn(
                        $installer->getTable('sales_order_item'),
                        'comment',
                        [
                            'type' => Table::TYPE_TEXT,
                            'length' => 255,
                            'default' => null,
                            'comment' => 'Comment'
                        ]
                    );
        }
        $installer->endSetup();
    }
}

<?php

namespace TemplateMonster\ThemeOptions\Plugin\Catalog\Block\Product;

use \Magento\Catalog\Block\Product\AbstractProduct;
use \TemplateMonster\ThemeOptions\Helper\Data;

/**
 * Config edit plugin.
 *
 * @package TemplateMonster\ThemeOptions\Plugin\Catalog\Block\Product
 */
class AbstractProductPlugin
{
    /**
     * ThemeOptions helper.
     *
     * @var helper
     */
    protected $_helper;

    /**
     * Construct
     *
     * @param \TemplateMonster\ThemeOptions\Helper\Data $helper
     *
     */
    public function __construct(
        Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * Get if it is necessary to show product stock status
     *
     * @return bool
     */
    public function aroundDisplayProductStockStatus(AbstractProduct $subject, callable $proceed)
    {
        return $this->_helper->isProductShowStock() ? $proceed() : '';
    }
}
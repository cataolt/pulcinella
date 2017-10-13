<?php

/**
 *
 * Copyright © 2015 TemplateMonster. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace TemplateMonster\FeaturedProduct\Block\FeaturedProduct\Widget;

use Magento\Customer\Model\Context as CustomerContext;

class Product extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{

    /**
     * Default cache time
     */
    const CACHE_TIME = 86400;

    protected $_template = 'TemplateMonster_FeaturedProduct::widget/products.phtml';
    protected $_productFactory;
    protected $_imageBuilder;
    protected $_httpContext;


    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \TemplateMonster\FeaturedProduct\Model\ProductFactory $productFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = [])
    {
        $this->_imageBuilder = $context->getImageBuilder();
        $this->_productFactory = $productFactory;
        $this->_httpContext = $httpContext;
        parent::__construct($context, $data);
    }


    public function _construct()
    {
        $this->addData(
            ['cache_lifetime' => self::CACHE_TIME, 'cache_tags' => [\Magento\Catalog\Model\Product::CACHE_TAG]]
        );
        parent::_construct();
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyFromData = "TM_";
        $configData = $this->getData();

        foreach($configData as $value) {
            if(is_string($value)) {
                $cacheKeyFromData .= $value;

            }
        }
        return [
            $cacheKeyFromData,
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->_httpContext->getValue(CustomerContext::CONTEXT_GROUP),
            'template' => $this->getTemplate()
        ];
    }

    public function getCacheLifetime()
    {
        if(!$this->getData('cache_lifetime')) {
            return self::CACHE_TIME;
        }
        return $this->getData('cache_lifetime');
    }

    public function getProductIds()
    {
        $productArr = [];
        $productIdsStr = $this->getData('product_ids');
        if($productIdsStr && is_string($productIdsStr)) {
            $productArr = explode(',',$productIdsStr);
        }
        return $productArr;
    }

    public function getItemWidth()
    {
        $productItemWidth = '';
        $productPerRow = $this->getNumberPerRow();
        if ($productPerRow) {
            if (!$this->getShowCarousel()) {
                $productItemWidth = 'style="width: ' . 100 / $productPerRow . '%;"';
            }
        }
        return $productItemWidth;
    }

    /**
     * Truncate product name
     *
     * @param string $name
     * @return string
     */
    public function truncateProductName($name)
    {
        $name = $this->escapeHtml($name);
        $truncate = (int) $this->getProductNameLength();
        $nameLength = strlen($name);
        if($truncate != 0 && is_int($truncate) && $nameLength > $truncate){
            $name = substr($name, 0, $truncate) . '...';
        }
        return $name;
    }

    /**
     * Return Product Collection
     * @return bool
     */
    protected function _getProductCollection()
    {
        $productIds = $this->getProductIds();
        $numberPerView = (int)$this->getNumberPerView();
        $productType = $this->getProductTypes();
        try {
            $productFactory = $this->_productFactory->create($productType);
        } catch(\Exception $e) {
            return false;
        }
        $productCollection = $productFactory->getPreparedCollection($numberPerView,$productIds);
        return $productCollection;
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }

    /**
     * Prepare collection with new products
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->_getProductCollection());
        return parent::_beforeToHtml();
    }

}
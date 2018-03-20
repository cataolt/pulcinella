<?php

namespace TemplateMonster\ThemeOptions\Plugin;

use Magento\Quote\Model\Quote\Item;

class DefaultItem
{
    public function aroundGetItemData($subject, \Closure $proceed, Item $item)
    {
        $data = $proceed($item);
        $product = $item->getProduct();

        $showAddIngredient = true;
        if(in_array((int)$product->getAttributeSetId(), array(11,12))){
            $showAddIngredient = false;
        }
        $showProduct = true;
        if(in_array($product->getAttributeSetId(), array(10))) {
            $showProduct = false;
        }
        $atts = [
            "show_add_ingredient" => $showAddIngredient,
            "show_product_cart"   => $showProduct
        ];

        return array_merge($data, $atts);
    }
}
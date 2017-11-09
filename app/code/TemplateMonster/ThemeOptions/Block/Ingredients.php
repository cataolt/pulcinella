<?php

namespace TemplateMonster\ThemeOptions\Block;

use TemplateMonster\ThemeOptions\Helper\Data as ThemeOptionsHelper;
use Magento\Framework\View\Element\Template;

/**
 * Social links block.
 *
 * @method string getPosition()
 *
 * @package TemplateMonster\ThemeOptions\Block
 */
class Ingredients extends Template
{
    /**
     * @var string
     */
    protected $_template = 'ingredient.phtml';

    protected $_attributeSetCollection;
    protected $productCollectionFactory;

    /**
     * SocialLinks constructor.
     *
     * @param Template\Context   $context
     * @param array              $data
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection,
        Template\Context $context,
        array $data
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_attributeSetCollection = $attributeSetCollection;
        parent::__construct($context, $data);
    }

    public function getFormAction() {
        return $this->_storeManager->getStore()->getBaseUrl() . '/theme_options/ingredients/index';
    }

    public function getAllIngredients(){
        $attributeSet = $this->_attributeSetCollection->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'attribute_set_name',
            'Ingredient'
        );

        $attributeSetId = 0;
        foreach($attributeSet as $attr):
            $attributeSetId = $attr->getAttributeSetId();
        endforeach;

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('attribute_set_id', $attributeSetId);
        $collection->addAttributeToSort('weight');

        $result = array();

        foreach ($collection->getItems() as $item) {
            $result[$item->getId()] = array(
                'name' => $item->getName(),
                'price' => $item->getPrice()
            );
        }

        return $result;
    }

}
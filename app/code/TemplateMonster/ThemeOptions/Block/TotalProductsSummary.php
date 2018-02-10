<?php

namespace TemplateMonster\ThemeOptions\Block;

use Magento\Framework\View\Element\Template;

/**
 * Closed Pop-Up block.
 *
 * @package TemplateMonster\ThemeOptions\Block
 */
class TotalProductsSummary extends Template
{

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    protected $_productRepository;

    /**
     * Closed constructor.
     *
     * @param PopupHelper       $helper
     * @param Template\Context  $context
     * @param array             $data
     */
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        Template\Context $context,
        array $data
    )
    {
        $this->cart = $cart;
        $this->_productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    public function getSummary () {

        $quote = $this->cart->getQuote();
        $summary = array();
        foreach ($quote->getAllItems() as $item){
            $product = $this->_productRepository->getById($item->getData('product_id'));
            if(!in_array($product->getAttributeSetId(), array(10))) {
                if (is_null($item->getData('additional_data'))) {
                    $found = false;
                    foreach ($summary as $key => $value) {
                        if ($item->getSku() == $key) {
                            $found = true;
                            break;
                        }
                    }
                    if ($found) {
                        $summary[$item->getSku()]['qty'] = $summary[$item->getSku()]['qty'] + 1;
                    } else {
                        $summary[$item->getSku()] = array(
                            'name' => $item->getName(),
                            'qty' => 1
                        );
                    }
                } else {
                    $id = $item->getSku();
                    $productsArray = unserialize($item->getData('additional_data'));
                    $extra = array();

                    foreach ($productsArray as $key=>$value){
                        $ingredient = $this->_productRepository->getById($value);
                        if(!array_key_exists($ingredient->getId(), $extra)){
                            $extra[$ingredient->getId()] = array(
                                'name' => $ingredient->getName(),
                                'qty' => 1
                            );
                        } else {
                            $extra[$ingredient->getId()]['qty'] = $extra[$ingredient->getId()]['qty'] + 1;
                        }
                        $id = $id . $value;
                    }

                    $found = false;
                    foreach ($summary as $key => $value) {
                        if ($id == $key) {
                            $found = true;
                            break;
                        }
                    }
                    if ($found) {
                        $summary[$id]['qty'] = $summary[$id]['qty'] + 1;
                    } else {
                        $extraText = ' ';
                        foreach ($extra as $key=>$value){
                            $extraText = $extraText . $value['qty'] . ' X ' .  $value['name'] . ' ';
                        }

                        $summary[$id] = array(
                            'name' => $item->getName() . ' extra:' . $extraText,
                            'qty' => 1
                        );
                    }
                }
            }
        }

        $orderSummaryItems = array();
        foreach ($summary as $summaryValue){
            $orderSummaryItems[] = $summaryValue['qty'] . ' ' .  $summaryValue['name'];
        }

        return $orderSummaryItems;
    }
}
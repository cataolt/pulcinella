<?php

namespace TemplateMonster\ThemeOptions\Block\Order\Item\Renderer;

class DefaultRenderer extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer
{
    protected $_productRepository;

    protected $_template = 'Magento_Sales::order/items/renderer/default.phtml';

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        array $data = []
    ) {
        parent::__construct($context, $string, $productOptionFactory, $data);
        $this->_productRepository = $productRepository;
    }

    public function getOptions(){
        $item = $this->getItem();
        $result = array();

        $additionalData = $item->getAdditionalData();
        if($additionalData) {
            $productsArray = unserialize($additionalData);
            foreach ($productsArray as $key=>$value){
                $productId = $value;
                $product = $this->_productRepository->getById($productId);
                $result[] = array(
                    'name' => $product->getName(),
                    'price' => $product->getPrice()
                );
            }
        }
        return $result;

    }
}
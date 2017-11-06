<?php
namespace TemplateMonster\ThemeOptions\Observer;

class RemoveProducts implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    public function __construct(
        \Magento\Checkout\Model\Cart $cart
    ){
        $this->cart = $cart;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quoteItem = $observer->getQuoteItem();
        $additionalData = $quoteItem->getAdditionalData();
        if($additionalData) {
            $productsArray = unserialize($additionalData);
            $quote = $quoteItem->getQuote();
            $allItems = $quote->getAllItems();

            foreach ($allItems as $item) {
                if (in_array($item->getProductId(), $productsArray)) {
                    foreach ($productsArray as $key=>$value){
                        if ($value === $item->getProductId()) {
                            unset($productsArray[$key]);
                            break;
                        }
                    }
                    $item->delete();
                }
            }
        }
    }
}
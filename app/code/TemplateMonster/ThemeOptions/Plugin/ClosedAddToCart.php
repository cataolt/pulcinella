<?php

namespace TemplateMonster\ThemeOptions\Plugin;

use Magento\Framework\Exception\LocalizedException;

class ClosedAddToCart
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    protected $request;

    /**
     * @var \TemplateMonster\ThemeOptions\Helper|Closed
     */
    protected $helper;

    /**
     * Plugin constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Request\Http $request,
        \TemplateMonster\ThemeOptions\Helper\Closed $helper
    ) {
        $this->quote = $checkoutSession->getQuote();
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * beforeAddProduct
     *
     * @param      $subject
     * @param      $productInfo
     * @param null $requestInfo
     *
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {
        $productId = (int)$this->request->getParam('product', 0);
        $qty = (int)$this->request->getParam('qty', 1);

        // do something
        // your code goes here

        $isClosed = false;
        $isClosed = $this->helper->isClosingHour();

        if($isClosed){
//            throw new LocalizedException(__('Ne pare rau magazinul este inchis'));
        }
        return [$productInfo, $requestInfo];
    }
}
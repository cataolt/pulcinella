<?php

namespace TemplateMonster\ThemeOptions\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Response\Http as responseHttp;
use Magento\Framework\UrlInterface;

class ClosedCart
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    protected $request;
    protected $response;
    protected $_url;

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
        \TemplateMonster\ThemeOptions\Helper\Closed $helper,
        responseHttp $response,
        UrlInterface $url
    ) {
        $this->quote = $checkoutSession->getQuote();
        $this->request = $request;
        $this->helper = $helper;
        $this->response = $response;
        $this->_url = $url;
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
    public function beforeExecute($subject)
    {
        // do something
        // your code goes here

        $isClosed = false;
        $isClosed = $this->helper->isClosingHour();

        if($isClosed){
            $url = $this->_url->getUrl();
            $this->response->setRedirect($url);
            return $subject;
        }
    }
}
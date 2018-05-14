<?php
namespace TemplateMonster\NewShipping\Plugin;

use Magento\Framework\App\Config\DataFactory as ConfigDataFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;



class Method {

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;
    /**
     * @var ConfigDataFactory
     */
    protected $configDataFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    protected $_request;

    protected $_helper;

    public function __construct(
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        ConfigDataFactory $configDataFactory,
        \TemplateMonster\NewShipping\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->configDataFactory = $configDataFactory;
        $this->_helper = $helper;
        $this->_request = $request;
    }

    public function aroundCollectRates($subject, callable  $proceed, RateRequest $request)
    {

        if (!$subject->getConfigFlag('active')) {
            return false;
        }
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        $this->_updateFreeMethodQuote($request);

        if ($request->getFreeShipping() || $request->getBaseSubtotalInclTax() >= $subject->getConfigData(
                'free_shipping_subtotal'
            )
        ) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();
            $minimumAmount = $this->_helper->getMinimumAmount($request->getDestPostcode());

            $method->setCarrier('freeshipping');
            $method->setCarrierTitle($subject->getConfigData('title') . (($minimumAmount)?' Comanda minima: ' . $minimumAmount . ' RON':''));

            $method->setMethod('freeshipping');
            $method->setMethodTitle($subject->getConfigData('name'));

            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        }


        return $result;
    }

    /**
     * Allows free shipping when all product items have free shipping (promotions etc.)
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return void
     */
    protected function _updateFreeMethodQuote($request)
    {
        $freeShipping = false;
        $items = $request->getAllItems();
        $c = count($items);
        for ($i = 0; $i < $c; $i++) {
            if ($items[$i]->getProduct() instanceof \Magento\Catalog\Model\Product) {
                if ($items[$i]->getFreeShipping()) {
                    $freeShipping = true;
                } else {
                    return;
                }
            }
        }
        if ($freeShipping) {
            $request->setFreeShipping(true);
        }
    }
}
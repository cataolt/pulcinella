<?php

namespace TemplateMonster\ThemeOptions\Plugin;

class UnmergeQuote
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'sales_quote';

    /**
     * @var string
     */
    protected $_eventObject = 'quote';

    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;



    public function __construct(
        \Magento\Framework\Model\Context $context,
        array $data = []
    ){
        $this->_eventManager = $context->getEventDispatcher();
    }

    public function aroundMerge($subject, \Closure $proceed, \Magento\Quote\Model\Quote $quote)
    {
        $this->_eventManager->dispatch(
            $this->_eventPrefix . '_merge_before',
            [$this->_eventObject => $subject, 'source' => $quote]
        );

        foreach ($quote->getAllVisibleItems() as $item) {
            $found = false;
            if (!$found) {
                $newItem = clone $item;
                $subject->addItem($newItem);
                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        $newChild = clone $child;
                        $newChild->setParentItem($newItem);
                        $subject->addItem($newChild);
                    }
                }
            }
        }

        /**
         * Init shipping and billing address if quote is new
         */
        if (!$subject->getId()) {
            $subject->getShippingAddress();
            $subject->getBillingAddress();
        }

        if ($quote->getCouponCode()) {
            $subject->setCouponCode($quote->getCouponCode());
        }

        $this->_eventManager->dispatch(
            $this->_eventPrefix . '_merge_after',
            [$this->_eventObject => $subject, 'source' => $quote]
        );

        return $subject;
    }
}
<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace TemplateMonster\ThemeOptions\Observer\Webapi;

class SubmitObserver extends  \Magento\Quote\Observer\Webapi\SubmitObserver
{

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var  \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        /**
         * a flag to set that there will be redirect to third party after confirmation
         */
        $order->setDoNotSendEmail(true);
        $redirectUrl = $quote->getPayment()->getOrderPlaceRedirectUrl();
        if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
            try {
                $this->orderSender->send($order);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }
}

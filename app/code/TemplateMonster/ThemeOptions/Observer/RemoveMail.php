<?php
namespace TemplateMonster\ThemeOptions\Observer;

class RemoveMail implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $order->setDoNotSendEmail(true);
        return $order;
    }
}
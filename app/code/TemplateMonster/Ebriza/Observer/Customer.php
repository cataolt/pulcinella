<?php
namespace TemplateMonster\Ebriza\Observer;

class Customer implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();

        if($customer->getSkipEbriza() != true) {
            if ($customer->getData('need_ebriza_sync') != 1) {
                $customer->setData('need_ebriza_sync', 1);
                $customer->save();
            }
        }
    }
}
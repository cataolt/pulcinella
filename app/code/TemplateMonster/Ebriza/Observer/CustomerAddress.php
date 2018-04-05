<?php
namespace TemplateMonster\Ebriza\Observer;

class CustomerAddress implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerAddress = $observer->getEvent()->getCustomerAddress();
        $customer = $customerAddress->getCustomer();

        if($customer->getNeedEbrizaSync() != 1){
            $customer->setNeedEbrizaSync(1);
            $customer->save();
        }
    }
}
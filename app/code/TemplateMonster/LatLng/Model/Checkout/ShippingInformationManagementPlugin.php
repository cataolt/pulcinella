<?php

namespace TemplateMonster\LatLng\Model\Checkout;

class ShippingInformationManagementPlugin
{

    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $lat = $extAttributes->getLat();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setLat($lat);

        $lon = $extAttributes->getLon();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setLon($lon);

        $shippingAddress = $addressInformation->getShippingAddress();
        $shippingAddress->setLat($lat);
        $shippingAddress->setLon($lon);
    }
}
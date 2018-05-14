<?php
namespace TemplateMonster\NewShipping\Plugin;

use Magento\Framework\Exception\StateException;

class ShippingInformation
{

    protected $_helper;

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    public function __construct(
        \TemplateMonster\NewShipping\Helper\Data $helper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->_helper = $helper;
        $this->quoteRepository = $quoteRepository;
    }

    public function beforeSaveAddressInformation (
        $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation)
    {
        $quote = $this->quoteRepository->getActive($cartId);
        $minimumAmount = $this->_helper->getMinimumAmount($addressInformation->getShippingAddress()->getCity());
        if ($addressInformation->getShippingMethodCode() == 'freeshipping' && $quote->getGrandTotal() < $minimumAmount){
            throw new StateException(__('Pentru aceasta zona comanda este de ' . $minimumAmount . ' RON!'));
        }

        return array($cartId, $addressInformation);
    }
}
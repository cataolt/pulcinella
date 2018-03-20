<?php

namespace TemplateMonster\ThemeOptions\Plugin;

use Magento\Quote\Model\ResourceModel\Quote\Item;
use Magento\Catalog\Model\ProductRepository;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;


class RemoveProductSummary
{
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item
     */
    protected $quoteItem;

    protected $_productRepository;

    /**
     * @var CartTotalRepositoryInterface
     */
    protected $cartTotalRepository;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;


    public function __construct(
        \Magento\Quote\Model\ResourceModel\Quote\Item $quoteItem,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        CheckoutSession $checkoutSession,
        CartTotalRepositoryInterface $cartTotalRepository
    ){
        $this->quoteItem = $quoteItem;
        $this->_productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
        $this->cartTotalRepository = $cartTotalRepository;
    }

    public function aroundGetConfig($subject, \Closure $proceed)
    {
        $data = $proceed();
        $data['totalsData'] = $this->getNewTotalsData();
        return $data;
    }

    private function getNewTotalsData() {
        /** @var \Magento\Quote\Api\Data\TotalsInterface $totals */
        $totals = $this->cartTotalRepository->get($this->checkoutSession->getQuote()->getId());
        $items = [];
        /** @var  \Magento\Quote\Model\Cart\Totals\Item $item */
        foreach ($totals->getItems() as $item) {
            $quoteItemId = $item->getItemId();
            $quote = $this->checkoutSession->getQuote();
            $quoteItem = $quote->getItemById($quoteItemId);
            $product = $this->_productRepository->getById($quoteItem->getData('product_id'));
            if(!in_array($product->getAttributeSetId(), array(10))) {
                $items[] = $item->__toArray();
            }
        }
        $totalSegmentsData = [];
        /** @var \Magento\Quote\Model\Cart\TotalSegment $totalSegment */
        foreach ($totals->getTotalSegments() as $totalSegment) {
            $totalSegmentArray = $totalSegment->toArray();
            if (is_object($totalSegment->getExtensionAttributes())) {
                $totalSegmentArray['extension_attributes'] = $totalSegment->getExtensionAttributes()->__toArray();
            }
            $totalSegmentsData[] = $totalSegmentArray;
        }
        $totals->setItems($items);
        $totals->setTotalSegments($totalSegmentsData);
        $totalsArray = $totals->toArray();
        if (is_object($totals->getExtensionAttributes())) {
            $totalsArray['extension_attributes'] = $totals->getExtensionAttributes()->__toArray();
        }

        return $totalsArray;
    }
}
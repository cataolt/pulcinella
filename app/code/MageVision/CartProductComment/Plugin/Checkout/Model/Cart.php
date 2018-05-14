<?php
/**
 * MageVision Cart Product Comment Extension
 *
 * @category     MageVision
 * @package      MageVision_CartProductComment
 * @author       MageVision Team
 * @copyright    Copyright (c) 2017 MageVision (http://www.magevision.com)
 * @license      LICENSE_MV.txt or http://www.magevision.com/license-agreement/
 */
namespace MageVision\CartProductComment\Plugin\Checkout\Model;

use MageVision\CartProductComment\Helper\Data as Helper;

class Cart
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Cart items comment update
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param \Closure $proceed
     * @param array $data
     * @return $this
     */
    public function aroundUpdateItems(
        \Magento\Checkout\Model\Cart $subject,
        \Closure $proceed,
        $data = []
    ) {
        $result = $proceed($data);
        if ($this->helper->isEnabled()) {
            foreach ($data as $itemId => $itemInfo) {
                $item = $subject->getQuote()->getItemById($itemId);
                if (isset($itemInfo['comment']) && $item) {
                    $item->setComment($itemInfo['comment']);
                }
            }
        }
        return $result;
    }
}

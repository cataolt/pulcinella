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
namespace MageVision\CartProductComment\Plugin\Sales\Block\Adminhtml\Order\View\Items\Renderer;

use MageVision\CartProductComment\Helper\Data as Helper;

class DefaultRenderer
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
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\DataObject|Item $item
     * @param string $column
     * @param null $field
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function aroundGetColumnHtml(
        \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer $subject,
        \Closure $proceed,
        \Magento\Framework\DataObject $item,
        $column,
        $field = null
    ) {
        $result = $proceed($item, $column, $field);
        if ($column == 'comment') {
            return $item->getComment();
        }
        return $result;
    }
}

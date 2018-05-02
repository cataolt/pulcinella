<?php

namespace TemplateMonster\ThemeOptions\Block;

use TemplateMonster\ThemeOptions\Helper\Closed as PopupHelper;
use Magento\Framework\View\Element\Template;

/**
 * Closed Pop-Up block.
 *
 * @package TemplateMonster\ThemeOptions\Block
 */
class ClosedHours extends Template
{
    /**
     * @string
     */
    protected $_template = 'popup_closed.phtml';

    /**
     * @var PopupHelper
     */
    protected $_helper;

    /**
     * Closed constructor.
     *
     * @param PopupHelper       $helper
     * @param Template\Context  $context
     * @param array             $data
     */
    public function __construct(
        PopupHelper $helper,
        Template\Context $context,
        array $data
    )
    {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }
    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (!$this->_helper->isClosingHour()) {
            return '';
        }
        return parent::_toHtml();
    }

    public function isClosingHour(){
        return $this->_helper->isClosingHour();
    }

    public function getOpenStartHour(){
        return $this->_helper->getOpenStartHour();
    }

    public function getOpenStartEnd(){
        return $this->_helper->getOpenStartEnd();
    }

}
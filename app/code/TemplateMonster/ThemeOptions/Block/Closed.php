<?php

namespace TemplateMonster\ThemeOptions\Block;

use TemplateMonster\ThemeOptions\Helper\Closed as PopupHelper;
use Magento\Framework\View\Element\Template;

/**
 * Closed Pop-Up block.
 *
 * @package TemplateMonster\ThemeOptions\Block
 */
class Closed extends Template
{
    /**
     * @string
     */
    protected $_template = 'popup.phtml';

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
        if (!$this->_helper->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }

    public function getContent(){
        return $this->_helper->getContent();
    }

    public function isEnabled(){
        return $this->_helper->isEnabled();
    }

    public function getTitle(){
        return $this->_helper->getTitle();
    }
}
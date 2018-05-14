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
namespace MageVision\CartProductComment\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use MageVision\CartProductComment\Helper\Data as Helper;
use Magento\Backend\Block\AbstractBlock;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Info extends AbstractBlock implements RendererInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Constructor
     * @param Context $context
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Render form element as HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $version  = $this->helper->getExtensionVersion();
        $name = $this->helper->getExtensionName();
        $logoUrl = 'https://www.magevision.com/pub/media/logo/default/magevision.png';
        
        $html  = <<<HTML
<div style="background: url('$logoUrl') no-repeat scroll 15px 15px #fff;
border:1px solid #e3e3e3; min-height:100px; display;block;
padding:15px 15px 15px 130px;">
<p>
<strong>$name Extension v$version</strong> by <strong><a href="https://www.magevision.com" target="_blank">MageVision</a></strong><br />
Allow your customers to leave a comment to each product in cart.
</p>
<p>
Check more extensions you might be interested in our <a href="https://www.magevision.com/magento-2-extensions" target="_blank">website</a>.
<br />Like and follow us on 
<a href="https://www.facebook.com/magevision" target="_blank">Facebook</a>,
<a href="https://www.linkedin.com/company/magevision" target="_blank">LinkedIn</a> and
<a href="https://twitter.com/magevision" target="_blank">Twitter</a>.<br />
If you need support or have any questions, please contact us at
<a href="mailto:support@magevision.com">support@magevision.com</a>.
</p>
</div>
HTML;
        return $html;
    }
}

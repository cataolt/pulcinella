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
namespace MageVision\CartProductComment\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED               = 'cartproductcomment/general/enabled';
    const XML_PATH_DISPLAY_UPDATE_BUTTON = 'cartproductcomment/general/display_update_button';
    const MODULE_NAME                    = 'Cart Product Comment';

    /**
     * @var ModuleListInterface;
     */
    protected $moduleList;

    /**
     * @param Context $context
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
        parent::__construct($context);
    }
    
    /**
     * Check is enabled Module
     *
     * @param int $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Display update button per cart item
     *
     * @param int $store
     * @return bool
     */
    public function displayUpdateButton($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_UPDATE_BUTTON,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns extension version.
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        $moduleInfo = $this->moduleList->getOne($this->getModuleName());
        return $moduleInfo['setup_version'];
    }

    /**
     * Returns module's name
     *
     * @return string
     */
    public function getModuleName()
    {
        $classArray = explode('\\', get_class($this));

        return count($classArray) > 2 ? "{$classArray[0]}_{$classArray[1]}" : '';
    }

    /**
     * Returns extension name.
     *
     * @return string
     */
    public function getExtensionName()
    {
        return self::MODULE_NAME;
    }
}

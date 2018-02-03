<?php

namespace TemplateMonster\ThemeOptions\Helper;

use Magento\Framework\App\Config\DataFactory as ConfigDataFactory;
use Magento\Framework\App\Config\Initial as InitialConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Data helper
 *
 * @package TemplateMonster\NewsletterPopup\Helper
 */
class Closed extends AbstractHelper
{
    /**
     * Enabled config option.
     */
    const XML_PATH_ENABLED = 'closed_popup/general/enabled';

    /**
     * Show on startup config option.
     */
    const XML_PATH_CONTENT = 'closed_popup/general/content';

    /**
     * Show on startup config option.
     */
    const XML_PATH_TITLE = 'closed_popup/general/title';

    /**
     * @var InitialConfig
     */
    protected $initialConfig;

    /**
     * @var ConfigDataFactory
     */
    protected $configDataFactory;

    /**
     * @var null|\Magento\Framework\App\Config\Data
     */
    protected $initialConfigData = null;

    /**
     * Data constructor.
     *
     * @param InitialConfig     $initialConfig
     * @param ConfigDataFactory $configDataFactory
     * @param Context           $context
     */
    public function __construct(
        InitialConfig $initialConfig,
        ConfigDataFactory $configDataFactory,
        Context $context
    )
    {
        $this->initialConfig = $initialConfig;
        $this->configDataFactory = $configDataFactory;
        parent::__construct($context);
    }

    /**
     * Check is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONTENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getTitle()
    {
        $result = $this->scopeConfig->getValue(
            self::XML_PATH_TITLE,
            ScopeInterface::SCOPE_STORE
        );
        if(!$result){
            $result = "Atentie";
        }

        return $result;
    }

    /**
     * Get initial config data.
     *
     * @return \Magento\Framework\App\Config\Data|\Magento\Framework\App\Config\DataInterface|null
     */
    protected function getInitialConfigData()
    {
        if (null === $this->initialConfigData) {
            $data = $this->initialConfig->getData(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
            $this->initialConfigData = $this->configDataFactory->create([
                'data' => $data
            ]);
        }

        return $this->initialConfigData;
    }

    /**
     * Get config value.
     *
     * @param string $path
     *
     * @return array|mixed|null
     */
    protected function getConfigValue($path)
    {
        if ($this->isUseDefaultIconColors()) {
            return $this->getInitialConfigData()->getValue($path);
        }

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
}
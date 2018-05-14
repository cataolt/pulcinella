<?php

namespace TemplateMonster\NewShipping\Helper;

use Magento\Framework\App\Config\DataFactory as ConfigDataFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Data helper
 *
 * @package TemplateMonster\NewsletterPopup\Helper
 */
class Data extends AbstractHelper
{

    const XML_ZONE_1 = 'sales/minimum_order/region_1';
    const XML_ZONE_1_AMOUNT = 'sales/minimum_order/region_1_amount';
    const XML_ZONE_2 = 'sales/minimum_order/region_2';
    const XML_ZONE_2_AMOUNT = 'sales/minimum_order/region_2_amount';

    /**
     * @var ConfigDataFactory
     */
    protected $configDataFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Data constructor.
     *
     * @param InitialConfig     $initialConfig
     * @param ConfigDataFactory $configDataFactory
     * @param Context           $context
     */

    protected $date;
    public function __construct(
        ConfigDataFactory $configDataFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        Context $context
    )
    {
        $this->configDataFactory = $configDataFactory;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * Check is enabled.
     *
     * @return bool
     */
    public function getMinimumAmount($city = null)
    {
        $zone1Cities = $this->scopeConfig->getValue(self::XML_ZONE_1);
        $zone1Amount = $this->scopeConfig->getValue(self::XML_ZONE_1_AMOUNT);
        $zone1Array = explode(';',$zone1Cities);

        $zone2Cities = $this->scopeConfig->getValue(self::XML_ZONE_2);
        $zone2Amount = $this->scopeConfig->getValue(self::XML_ZONE_2_AMOUNT);
        $zone2Array = explode(';',$zone2Cities);

        if(!$city) {
            $city = $this->_checkoutSession->getQuote()->getBillingAddress()->getCity();
        }
        $amount = $zone1Amount;

        if(in_array($city, $zone2Array)){
            $amount = $zone2Amount;
        }


        return $amount;
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

    public function getOpenStartHour(){
        $result = $this->scopeConfig->getValue(
            self::XML_PATH_START_HOUR,
            ScopeInterface::SCOPE_STORE
        );
        if(!$result){
            $result = "10:00";
        }

        return $result;
    }

    public function getOpenStartEnd(){
        $result = $this->scopeConfig->getValue(
            self::XML_PATH_END_HOUR,
            ScopeInterface::SCOPE_STORE
        );
        if(!$result){
            $result = "20:00";
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

    public function isClosingHour() {
        $hour = (int)$this->date->date('H');
        $minute = (int)$this->date->date('i');

        $openString = $this->getOpenStartHour();
        $openArray = explode(':',$openString);
        $openHour = (int)$openArray[0];
        $openMinute = (int)$openArray[1];

        $closeString = $this->getOpenStartEnd();
        $closeArray = explode(':',$closeString);
        $closeHour = (int)$closeArray[0];
        $closeMinute = (int)$closeArray[1];
//        var_dump($hour);die();
        if($hour > $openHour && $hour < $closeHour){
            return false;
        } else {
            if($hour == $openHour){
                if($minute >= $openMinute){
                    return false;
                }
            }
            if($hour == $closeHour){
                if($minute <= $closeMinute){
                    return false;
                }
            }
        }
        return true;

    }
}
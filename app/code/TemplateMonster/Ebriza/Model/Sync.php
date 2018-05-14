<?php

namespace TemplateMonster\Ebriza\Model;

use Magento\Framework\Model\AbstractModel;
use TemplateMonster\SocialLogin\Model\Exception;

class Sync  extends AbstractModel {

    protected $_token;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $_curl;

    protected $jsonHelper;

    protected $datetime;

    protected $_expire;

    protected $scopeConfig;
    /**
     * @param Context                             $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_curl = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->datetime = $datetime;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function getToken()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $url = $this->scopeConfig->getValue("ebriza/general/auth_url", $storeScope);
        $public = $this->scopeConfig->getValue("ebriza/general/public", $storeScope);;
        $secret = $this->scopeConfig->getValue("ebriza/general/private", $storeScope);;
        $clientId = $this->scopeConfig->getValue("ebriza/general/client_id", $storeScope);;

        $params = array(
            'grant_type' => 'client_credentials'
        );

        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Cache-control' => 'no-cache',
            'ebriza-clientid' => $clientId
        );

        try {
            $this->_curl->get($url);
            $this->_curl->setHeaders($headers);
            $this->_curl->setCredentials($public, $secret);
            //if the method is post
            $this->_curl->post($url, $params);
            //response will contain the output in form of JSON string
            $response = $this->_curl->getBody();

            $arrayResponse = $this->jsonHelper->jsonDecode($response);
            $this->_token = $arrayResponse['access_token'];
            $curentTimestamp = $this->datetime->timestamp();
            $expireDate = $curentTimestamp + $arrayResponse['expires_in'];
            $this->_expire = $expireDate;
        } catch (Exception $e){
            $this->log($e->getMessage());
            echo 'unable to get access token';
        }

    }

    protected function log($message){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ebriza.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $logger->info($message);
    }
}
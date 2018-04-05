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

    /**
     * @param Context                             $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime
    ) {
        $this->_curl = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->datetime = $datetime;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function getToken()
    {
        $url = 'https://www.ebrizademo.com/api/token';
        $public = '731A38D0-B071-4E47-ACD1-682DB30DBC86';
        $secret = 'EHAJ5qNG8GSWG5E6X0MMUqgshXDvD4AQf8NTd2x7FSJM7cWbv3pqFvzMJNGZ2wNCrcI3RMLwhIRWhLrJ';
        $clientId = 'D6739DC4-A3F8-4FF4-B7D5-1A715520A026';

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
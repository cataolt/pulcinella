<?php

namespace TemplateMonster\Ebriza\Model;

use DateTime;


class SendEmail  extends Sync {
    protected $data;

    protected $_orderCollectionFactory;

    protected $_orderSender;

    public function __construct(

        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->_orderCollectionFactory  = $orderCollectionFactory;
        $this->_orderSender  = $orderSender;
        parent::__construct($curl, $jsonHelper, $datetime, $scopeConfig);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function sendEmail()
    {
        $this->getToken();
        $orders = $this->getOrdersToSync();
        foreach ($orders as $order){
            $this->log('Send email for order with id ' . $order->getId());
            $this->syncOrder($order);
        }
    }

    private function syncOrder($order){
        if($order->getData('ebriza_id')) {
            $data = array('id' => $order->getData('ebriza_id'));
            $this->data = $data;
            $result = $this->execute();
            $this->updateOrder($order, $result);
        }
    }

    private function getOrdersToSync(){
        $collection = $this->_orderCollectionFactory->create();
        $collection->addFieldToSelect("*");
        $collection->addFieldToFilter("status", 'processing');
        $collection->addFieldToFilter("ebriza_delivery_time",array('null' => true));
        return $collection;
    }

    private function execute(){
        $arrayResponse = array();
        $curentTimestamp = $this->datetime->timestamp();
        if($this->_expire >= $curentTimestamp) {
            $token = $this->_token;
        } else {
            $token = $this->getToken();
        }


        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $url = $this->scopeConfig->getValue("ebriza/general/get_bill_url", $storeScope);
        $url = $url . $this->data['id'];
        $clientId = $this->scopeConfig->getValue("ebriza/general/client_id", $storeScope);

        $headers = array(
            'Authorization' => 'bearer ' . $token . '',
            'ebriza-clientid' => '' . $clientId . '',
            'Content-Type' => 'application/json',
            'Cache-control' => 'no-cache'
        );
        try {
//            $this->log($this->data);
            $this->_curl->setHeaders($headers);
            //if the method is post
            $this->_curl->get($url);
            //response will contain the output in form of JSON string
            $response = $this->_curl->getBody();
            $arrayResponse = $this->jsonHelper->jsonDecode($response);
//            $this->log($arrayResponse);

        } catch (Exception $e){
            $this->log($e->getMessage());
            echo 'unable to get update order';
        }
        return $arrayResponse;
    }

    protected function updateOrder($order, $result){
        try {
            if(!empty($result) && array_key_exists('id', $result)){
                if(array_key_exists('deliveryDate', $result)){
                    if(!is_null($result['deliveryDate'])){
                        if($result['deliveryDate'] != $result['timestamp']) {
                            $start_date = new DateTime($result['deliveryDate']);
                            $end_date = new DateTime($this->datetime->date('Y-m-d H:i:s'));
                            $interval = $start_date->diff($end_date);
                            $hours = $interval->format('%h');
                            $minutes = $interval->format('%i');
                            $min = ($hours * 60 + $minutes);
                            $order->setData('ebriza_delivery_time', $min);
                            $order->save();
                            $this->_orderSender->send($order);
                        }
                    } else {
                        $this->log('order not yet updated');
                    }

                }
            }
        }  catch (Exception $e){
            $this->log($e->getMessage());
            echo 'unable to save order';
        }
    }
}
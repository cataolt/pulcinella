<?php

namespace TemplateMonster\Ebriza\Model;

class UpdateOrders  extends Sync {
    protected $data;

    protected $_orderCollectionFactory;

    public function __construct(

        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->_orderCollectionFactory  = $orderCollectionFactory;
        parent::__construct($curl, $jsonHelper, $datetime, $scopeConfig);
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function syncOrders()
    {
        $this->getToken();
        $orders = $this->getOrdersToSync();
        foreach ($orders as $order){
            $this->log('Try to update order with with id ' . $order->getId());
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
        $collection->addFieldToFilter("ebriza_delivery_time",array('neq' => NULL));
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
        $url = $this->scopeConfig->getValue("ebriza/general/orders_url", $storeScope);
        $url = $url . $this->data['id'];
        $clientId = $this->scopeConfig->getValue("ebriza/general/client_id", $storeScope);

        $headers = array(
            'Authorization' => 'bearer ' . $token . '',
            'ebriza-clientid' => '' . $clientId . '',
            'Content-Type' => 'application/json',
            'Cache-control' => 'no-cache'
        );
        try {
            $this->log($this->data);
            $this->_curl->setHeaders($headers);
            //if the method is post
            $this->_curl->get($url);
            //response will contain the output in form of JSON string
            $response = $this->_curl->getBody();
            $arrayResponse = $this->jsonHelper->jsonDecode($response);
            $this->log($arrayResponse);

        } catch (Exception $e){
            $this->log($e->getMessage());
            echo 'unable to get update order';
        }
        return $arrayResponse;
    }

    protected function updateOrder($order, $result){
        try {
            if(!empty($result) && array_key_exists('id', $result)){
                $order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE, true);
                $order->setStatus(\Magento\Sales\Model\Order::STATE_COMPLETE);
                $order->addStatusToHistory($order->getStatus(), 'Order complete in Ebriza with ' . $result['id']);
                $order->save();
            }
        }  catch (Exception $e){
            $this->log($e->getMessage());
            echo 'unable to save order';
        }
    }
}
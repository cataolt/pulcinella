<?php

namespace TemplateMonster\Ebriza\Model;

use DateTime;


class CancelOrders  extends Sync {
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
    public function syncOrders()
    {
        $this->getToken();
        $orders = $this->getOrdersToSync();
        foreach ($orders as $order){
            $this->log('Cancel for order with id ' . $order->getId());
            $this->syncOrder($order);
        }
    }

    private function syncOrder($order){
        $this->updateOrder($order);
    }

    private function getOrdersToSync(){
        $collection = $this->_orderCollectionFactory->create();
        $collection->addFieldToSelect("*");
        $collection->addFieldToFilter("status", array('in',array('pending','processing')));

        $to = date("Y-m-d h:i:s"); // current date
        $from = strtotime('-30 minutes', strtotime($to));
        $from = date('Y-m-d h:i:s', $from); // 30 mintues before

        $collection->addFieldToFilter('created_at', array('to'=>$from));

        return $collection;
    }

    private function execute(){
        return true;
    }

    protected function updateOrder($order){
        try {
            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
            $order->addStatusToHistory($order->getStatus(), 'Order canceld due to long time waiting in Ebriza with ');
            $order->save();
        }  catch (Exception $e){
            $this->log($e->getMessage());
            echo 'unable to save order';
        }
    }
}
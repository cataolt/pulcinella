<?php

namespace TemplateMonster\Ebriza\Model;

class NewOrders  extends Customers {

    protected $_orderCollectionFactory;
    protected $_customerRepositoryInterface;
    protected $_productRepository;
    protected $_countryFactory;

    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\CountryFactory $countryFactory
    ){
        $this->_orderCollectionFactory  = $orderCollectionFactory;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_productRepository = $productRepository;
        $this->_countryFactory = $countryFactory;
        parent::__construct($customerCollectionFactory, $addressRepository, $addressDataFactory, $curl, $jsonHelper, $datetime, $scopeConfig);
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
            $this->log('Sync order with id ' . $order->getId());
            $this->syncOrder($order);
        }
    }

    private function syncOrder($order){
        $items = $this->generateOrderData($order);
        $customerEbrizaId = $this->syncOrderCustomer($order);
        $data = array(
            "clientID"        => $customerEbrizaId,
            "items"           => $items,
            "payments"        => array(
                                    "type" => ($order->getPayment()->getMethod() === 'cashondelivery') ? 1 : 2,
                                ),
            'isforPickup'     => ($order->getShippingMethod() === 'freeshipping_freeshipping') ? "false" : "true",
            'externalorderNo' => $order->getIncrementId()
        );
        try {
            $result = $this->executeOrderSync($data);
            $this->updateOrder($order, $result);
        }catch (Exception $e){
            $this->log($e->getMessage());
            echo 'unable to sync order with id ' . $order->getId();
        }

    }

    private function getOrdersToSync(){
        $collection = $this->_orderCollectionFactory->create();
        $collection->addFieldToSelect("ebriza_id");
        $collection->addFieldToSelect("*");
        $collection->addFieldToFilter("ebriza_id",array('null' => true));
        $collection->addFieldToFilter("status", 'pending');
        return $collection;
    }

    private function generateOrderData($order){
        $orderItems = $order->getAllItems();

        $comments = $order->getStatusHistoryCollection();
        $globalComment = '';
        if($comments->getSize() > 0) {
            $comment = $comments->getFirstItem();
            $globalComment = $comment->getData('comment');
        }
        $paymentInfo = ($order->getPayment()->getMethod() === 'cashondelivery') ? '' : ' plata: POS';
        $globalComment = $globalComment . $paymentInfo;

        $items = array();
        foreach($orderItems as $orderItem){
            $productId = $orderItem->getProductId();
            try {
                $product = $this->_productRepository->getById($productId);
                $additionalData = $orderItem->getAdditionalData();

                if (!(in_array($product->getAttributeSetId(), array(10)))) {
                    if ($additionalData) {
                        $productsArray = unserialize($additionalData);
                        $extra = ' Extra: ';
                        $extraItems = array();
                        $items[] = array(
                            "id" => $product->getData('ebriza_id'),
                            "quantity" => 1,
                            'Lock' => 'Lock' . $orderItem->getId(),
                            'IsMod' => "false",
                            'note' => $extra . $orderItem->getData('comment') . ' ' . $globalComment
                        );
                        foreach ($productsArray as $key=>$value){
                            $productId = $value;
                            $product = $this->_productRepository->getById($productId);
                            $extra = $extra . $product->getName() . ', ';
                            $extraItems[] = array(
                                "id" => $product->getData('ebriza_id'),
                                "quantity" => 1,
                                'Lock' => 'Lock' . $orderItem->getId(),
                                'IsMod' => "true"
                            );
                        }
                        foreach ($extraItems as $extraItem){
                            $items[] = $extraItem;
                        }
                    } else {
                        $items[] = array(
                            "id" => $product->getData('ebriza_id'),
                            "quantity" => 1,
                            "note" => $orderItem->getData('comment') . ' ' . $globalComment
                        );
                    }
                }
            } catch (Exception $e){
                $this->log($e->getMessage());
                echo 'product with id ' . $productId . ' does no longer exist';
            }
        }
        return $items;
    }

    private function syncOrderCustomer($order){
        $customerId = $order->getCustomerId();
        try {
            if ($order->getCustomerId()) {
                $customer = $this->_customerRepositoryInterface->getById($customerId);
                $data = $this->generateCustomerData($customer);
                $customerAddressId = $order->getShippingAddress()->getCustomerAddressId();
                if($customerAddressId) {
                    $address = $this->addressRepository->getById($customerAddressId);
                    $addressData = $this->generateAddressData($address);
                } else {
                    $addressData = $this->generateUnsavedAddressData($order);
                }
            } else {
                $data = $this->generateCustomerGuestData($order);
                $addressData = $this->generateUnsavedAddressData($order);
            }
            $data['address'] = $addressData;
            $this->data = $data;
            $this->log('Sync customer for order');
            $result = $this->execute();
            $customerId = $result['id'];
        } catch (Exception $e){
            $this->log($e->getMessage());
            echo 'customer with id ' . $customer->getId() . ' unable to sync';
        }
        return $customerId;
    }

    protected function generateCustomerData($customer){
        $data = array(
            "email"      => $customer->getEmail(),
            "firstName"  => $customer->getFirstname(),
            "lastName"   => $customer->getLastname(),
            "phone"      => $customer->getCustomAttribute('telephone')->getValue(),
            "company"    => null
        );
        return $data;
    }

    protected function generateAddressData($address){
        $country = $this->_countryFactory->create()->loadByCode($address->getCountryId());
        $data = array(
            "postalCode"        => '1234',
            "street"            => implode(' ', $address->getStreet()),
            "city"              => $address->getCity(),
            "county"            => $address->getRegion()->getRegion(),
            "country"           => $country->getName(),
            "long"              => ($address->getCustomAttribute('lon')) ? $address->getCustomAttribute('lon')->getValue() : '',
            "lat"               => ($address->getCustomAttribute('lat')) ? $address->getCustomAttribute('lat')->getValue() : '',
            "isDeliveryAddress" => "true",
            "isInvoiceAddress"  => "true",
            "details"           => ""
        );
        return $data;
    }

    private function generateUnsavedAddressData($order){
        $address = $order->getShippingAddress();
        $country = $this->_countryFactory->create()->loadByCode($address->getCountryId());
        $data = array(
            "postalCode"        => '1234',
            "street"            => implode(' ', $address->getStreet()),
            "city"              => $address->getCity(),
            "county"            => $address->getRegion(),
            "country"           => $country->getName(),
            "long"              => $order->getLon(),
            "lat"               => $order->getLat(),
            "isDeliveryAddress" => "true",
            "isInvoiceAddress"  => "true",
            "details"           => ""
        );
        return $data;
    }

    private function generateCustomerGuestData($order){
        $address = $order->getShippingAddress();
        $data = array(
            "email"      => $order->getCustomerEmail(),
            "firstName"  => $address->getFirstname(),
            "lastName"   => $address->getLastname(),
            "phone"      => $address->getTelephone(),
            "company"    => null
        );
        return $data;
    }

    private function executeOrderSync($data) {
        $arrayResponse = array();
        $curentTimestamp = $this->datetime->timestamp();
        if($this->_expire >= $curentTimestamp) {
            $token = $this->_token;
        } else {
            $token = $this->getToken();
        }

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $url = $this->scopeConfig->getValue("ebriza/general/open_bill_url", $storeScope);
        $clientId = $this->scopeConfig->getValue("ebriza/general/client_id", $storeScope);
        $headers = array(
            'Authorization' => 'bearer ' . $token . '',
            'ebriza-clientid' => '' . $clientId . '',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Cache-control' => 'no-cache'
        );
        try {
            $this->log($data);
            $this->_curl->setHeaders($headers);
            //if the method is post
            $this->_curl->post($url,$data);
            //response will contain the output in form of JSON string
            $response = $this->_curl->getBody();
            $arrayResponse = $this->jsonHelper->jsonDecode($response);
            $this->log($arrayResponse);

        } catch (Exception $e){
            $this->log($e->getMessage());
            echo 'unable to get sync customer';
        }
        return $arrayResponse;
    }

    private function updateOrder($order, $result){
        if(!empty($result) && array_key_exists('id', $result)){
            $order->setData('ebriza_id', $result['id']);
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->addStatusToHistory($order->getStatus(), 'Order sent in Ebriza with ' . $result['id']);
            $order->save();
        }
    }
}
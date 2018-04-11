<?php

namespace TemplateMonster\Ebriza\Model;

class Customers  extends Sync {

    protected $data;

    protected $customerCollectionFactory;

    protected $_addressFactory;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    protected $addressDataFactory;

    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime
    ){
        $this->customerCollectionFactory  = $customerCollectionFactory;
        $this->addressRepository = $addressRepository;
        $this->addressDataFactory = $addressDataFactory;
        parent::__construct($curl, $jsonHelper, $datetime);
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function syncCustomers()
    {
        $this->getToken();
        $customers = $this->getCustomersToSync();
        foreach ($customers as $customer){
            $this->log('Sync customer with id ' . $customer->getId());
            $this->syncCustomer($customer);
        }
    }

    private function syncCustomer($customer){
        $data = $this->generateCustomerData($customer);
        if(count($customer->getAddresses()) > 0) {
            foreach ($customer->getAddresses() as $address) {
                $addressData = $this->generateAddressData($address);
                $data['address'] = $addressData;
                $this->data = $data;
                $result = $this->execute();
            }
        } else {
            $data['address'] = null;
            $this->data = $data;
            $result = $this->execute();
        }
        $this->updateCustomer($customer, $result);
    }

    private function getCustomersToSync(){
        $collection = $this->customerCollectionFactory->create();
        $collection->addAttributeToSelect('telephone');
        $collection->addAttributeToFilter("need_ebriza_sync",1);
        return $collection;
    }

    protected function generateCustomerData($customer){
        $data = array(
            "email"      => $customer->getEmail(),
            "firstName"  => $customer->getFirstname(),
            "lastName"   => $customer->getLastname(),
            "phone"      => $customer->getTelephone(),
            "company"    => null
        );
        return $data;
    }

    protected function generateAddressData($address){
        $data = array(
            "postalCode"        => '1234',
            "street"            => implode(' ', $address->getStreet()),
            "city"              => $address->getCity(),
            "county"            => $address->getRegion(),
            "country"           => $address->getCountryModel()->getName(),
            "long"              => $address->getLon(),
            "lat"               => $address->getLat(),
            "isDeliveryAddress" => "true",
            "isInvoiceAddress"  => "true",
            "details"           => ""
        );
        return $data;
    }

    protected function execute(){
        $arrayResponse = array();
        $curentTimestamp = $this->datetime->timestamp();
        if($this->_expire >= $curentTimestamp) {
            $token = $this->_token;
        } else {
            $token = $this->getToken();
        }

        $url = 'https://www.ebrizademo.com/api/clients/getoradd';
        $clientId = 'D6739DC4-A3F8-4FF4-B7D5-1A715520A026';
        $headers = array(
            'Authorization' => 'bearer ' . $token . '',
            'ebriza-clientid' => '' . $clientId . '',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Cache-control' => 'no-cache'
        );
        try {
            $this->log($this->data);
            $this->_curl->setHeaders($headers);
            //if the method is post
            $this->_curl->post($url,$this->data);
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

    protected function updateCustomer($customer, $data){
        try {
            if (!empty($data) && array_key_exists('id', $data)) {
                $customer->setEmail($data['email']);
                $customer->setFirsname($data['firstName']);
                $customer->setLastname($data['lastName']);
                $customer->setTelephone($data['phone']);
                $customer->setEbrizaId($data['id']);
                $customer->setNeedEbrizaSync(0);
                $customer->setSkipEbriza(true);
                $customer->save();

                $addreses = $data['addresses'];
                if (!empty($addreses)) {
                    $customerAddresses = $customer->getAddresses();
                    foreach ($addreses as $address) {
                        $found = false;
                        foreach ($customerAddresses as $customerAddress) {
                            if ($customerAddress->getStreet() == $address['street']) {
                                $found = true;
                            }
                        }

                        if (!$found) {
                            $addressDataObject = $this->addressDataFactory->create();
                            $addressDataObject->setCustomerId($customer->getId());
                            $addressDataObject->setStreet(array($address['street']));
                            $addressDataObject->setCountryId('RO');
                            $addressDataObject->setRegionId('Timiş');
                            $addressDataObject->setPostcode('1234');
                            $addressDataObject->setCustomAttribute('lat', $address['lat']);
                            $addressDataObject->setCustomAttribute('lon', $address['long']);
                            $addressDataObject->setCustomAttribute('skip_ebriza', 1);
                            $newAddress = $this->addressRepository->save($addressDataObject);
                        }
                    }
                }
            }
        }  catch (Exception $e){
            $this->log($e->getMessage());
            echo 'unable to save customer';
        }
    }
}
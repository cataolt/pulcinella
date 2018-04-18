<?php

namespace TemplateMonster\Ebriza\Model;

class Products  extends Sync {

    protected $data;

    protected $searchCriteriaBuilder;

    protected $filterBuilder;

    protected $productRepository;

    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ){
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        parent::__construct($curl, $jsonHelper, $datetime);
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function syncProducts()
    {
        $this->getToken();
        $products = $this->getAllProducts();
        $syncProducts = array();
        foreach ($products as $product) {
            $result = $this->syncProductPrice($product);
            if(!empty($result)) {
                $syncProducts[] = $result;
            }
        }
        return $syncProducts;
    }

    private function syncProductPrice($product){
        $result = array();
        if(array_key_exists('id',$product)) {
            $filters[] = $this->filterBuilder
                ->setField('ebriza_id')
                ->setConditionType('eq')
                ->setValue($product['id'])
                ->create();

            $this->searchCriteriaBuilder->addFilters($filters);
            $productCriteria = $this->searchCriteriaBuilder->create();

            $products = $this->productRepository->getList($productCriteria);

            $items = $products->getItems();
            if(!empty($items)) {
                foreach($items as $item){
                    if((int)$item->getPrice() != (int)$product['price']){
                        $this->log('Setting price for ' . $item->getSku() . ' ' . $product['price']);
                        $result = array(
                            'product_name' =>  $item->getName(),
                            'product_price' => $product['price']
                        );
                        try {
                            $item->setPrice((int)$product['price']);
                            $item->save();
                            $result ['error'] = false;
                            $result['message'] = 'success';
                        } catch (Exception $e){
                            $result['error'] = true;
                            $result['message'] = $e->getMessage();
                            $this->log($e->getMessage());
                        }
                    }
                }
            }
        }

        return $result;
    }

    private function getAllProducts(){
        $arrayResponse = $this->execute();
        $products = array();
        if(!empty($arrayResponse)) {
            foreach ($arrayResponse as $product) {
                $products[] = array(
                    'id' =>  $product['id'],
                    'price' =>  $product['price'],
                    'name' =>  $product['name'],
                );
            }
        }
        return $products;
    }

    protected function execute(){
        $arrayResponse = array();
        $curentTimestamp = $this->datetime->timestamp();
        if($this->_expire >= $curentTimestamp) {
            $token = $this->_token;
        } else {
            $token = $this->getToken();
        }
        $url = 'https://www.ebrizademo.com/api/items';
        $clientId = 'D6739DC4-A3F8-4FF4-B7D5-1A715520A026';
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
            echo 'unable to get sync product';
        }
        return $arrayResponse;
    }
}
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
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        parent::__construct($curl, $jsonHelper, $datetime, $scopeConfig);
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
                    if((int)$item->getPrice() != (int)round($product['price'])){
                        $this->log('Setting price for ' . $item->getSku() . ' ' . reound($product['price']));
                        $result = array(
                            'product_name' =>  $item->getName(),
                            'product_price' => round($product['price'])
                        );
                        try {
                            $item->setPrice((int)reound($product['price']));
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
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $url = $this->scopeConfig->getValue("ebriza/general/products_url", $storeScope);
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
            echo 'unable to get sync product';
        }
        return $arrayResponse;
    }
}
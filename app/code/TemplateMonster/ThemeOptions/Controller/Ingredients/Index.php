<?php

namespace TemplateMonster\ThemeOptions\Controller\Ingredients;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

/**
 * CSS Index Controller.
 *
 * @package TemplateMonster\ThemeOptions\Controller\Ingredients
 */
class Index extends Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultFactory;

    protected $quoteItemFactory;

    protected $_productRepository;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    protected $_cart;

    protected $quoteItemRepository;

    /**
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $configurationHelper;

//    protected $_quoteRepo;

    /**
     * @param \Magento\Framework\View\Result\PageFactory $resultFactory
     * @param \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\Json\Helper|Data $jsonHelper
     * @param \Magento\Quote\Api\CartItemRepositoryInterface $quoteitemRepository
     * @param \Magento\Catalog\Helper\Product\Configuration $configurationHelper
//     * @param \Magento\Quote\Model\QuoteRepository $quoteRepo

     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultFactory,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Quote\Api\CartItemRepositoryInterface $quoteItemRepository,
        \Magento\Catalog\Helper\Product\Configuration $configurationHelper,
//        \Magento\Quote\Model\QuoteRepository $quoteRepo,
        Context $context
    )
    {
        $this->_cart = $cart;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->resultFactory = $resultFactory;
        $this->_productRepository = $productRepository;
        $this->jsonHelper = $jsonHelper;
        $this->quoteItemRepository = $quoteItemRepository;
        $this->configurationHelper = $configurationHelper;
//        $this->_quoteRepo = $quoteRepo;
        parent::__construct($context);
    }
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/json');
        $result = array();
        $result['success'] = true;
        $result['message'] = '';

        $requestParams = $this->getRequest()->getParams();

        $ingredients = array();
        $products = array();
        if($requestParams){
            if(array_key_exists('item_id',$requestParams)){
                $quoteId = $requestParams['item_id'];
                unset($requestParams['item_id']);

                foreach ($requestParams as $productId){
                    $_product = $this->_productRepository->getById($productId);
                    if($_product->getId()) {
                        $products[] = $productId;
                        $ingredients[] = array(
                            'label'=>'Extra',
                            'value'=>$_product->getName() . '   ' . (int)$_product->getPrice() . ' RON'
                        );
                    }
                }
                $quote = $this->_cart->getQuote();
                $quoteItem =  $quote->getItemById($quoteId);

//                $quoteItem->removeOption('additional_options');
                $quoteItem->addOption(array(
                    'code' => 'additional_options',
                    'value' => serialize($ingredients)
                ));
//                $quoteItem->re
//                var_dump($this->configurationHelper->getCustomOptions($quoteItem));die();

                $quoteItem->saveItemOptions();
                $quoteItem->setAdditionalData(serialize($requestParams));
                $quoteItem->save();

                foreach ($products as $ingredient){
                    $params = array(
                        'product' => $ingredient,
                        'qty' => 1
                    );
                    $_product = $this->_productRepository->getById($ingredient);
                    $this->_cart->addProduct($_product, $params);
                    $this->_cart->save();
                }

            } else {
                $result['success'] = false;
                $result['message'] = 'Something Went Wrong';
            }
        } else {
            $result['success'] = false;
            $result['message'] = 'Something Went Wrong';
        }

        echo $this->jsonHelper->jsonEncode(
            $result
        );
    }

}

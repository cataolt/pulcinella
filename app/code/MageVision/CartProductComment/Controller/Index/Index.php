<?php
namespace MageVision\CartProductComment\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_checkoutSession;
    protected $quoteItem;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItem,
        \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        $this->_checkoutSession = $_checkoutSession;
        $this->quoteItem = $quoteItem;
        return parent::__construct($context);
    }

    public function execute()
    {
        $quote = $this->_checkoutSession->getQuote();
        $quoteId = $quote->getId();

        $data = $this->getRequest()->getParams();

        $item = $this->_checkoutSession->getQuote()->getItemById($data['id']);

        if ($item && $item->getId()) {
            $item->setComment($data['comment']);
            $item->save();
        }

    }
}

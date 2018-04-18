<?php
namespace TemplateMonster\Ebriza\Controller\Adminhtml\Catalog;

class Sync extends \Magento\Backend\App\Action
{
    protected $ebrizaProductsSync;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \TemplateMonster\Ebriza\Model\Products $ebrizaProductsSync
    ) {
        $this->ebrizaProductsSync  = $ebrizaProductsSync;
        parent::__construct($context);
    }

    /**
     * Hello test controller page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $data = $this->ebrizaProductsSync->syncProducts();
        if(!empty($data)) {
            foreach ($data as $dat) {
                if ($dat['error']) {
                    $this->messageManager->addError(__($dat['product_name'] . ' price was NOT set to ' . $dat['product_price'] . ': ' . $dat['message']));
                } else {
                    $this->messageManager->addSuccess(__($dat['product_name'] . ' price was set to ' . $dat['product_price']));
                }
            }

        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('catalog/product/index');
        return $resultRedirect;
    }

    /**
     * Check Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::categories');
    }
}
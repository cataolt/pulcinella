<?php
namespace TemplateMonster\OfflinePaymentMethods\Model;
class Invoice30 extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_CUSTOM_INVOICE_CODE = 'invoice30';
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_CUSTOM_INVOICE_CODE;
}
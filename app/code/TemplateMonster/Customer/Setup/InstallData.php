<?php
namespace TemplateMonster\Customer\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\UpgradeDataInterface;


class InstallData implements InstallDataInterface
{
    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;
    /**
     * Init
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }
    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $installer = $setup;
        $installer->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "telephone");
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "customer_code");

        $customerSetup->addAttribute(Customer::ENTITY, 'telephone', [
            'type' => 'varchar',
            'label' => 'Telephone',
            'input' => 'text',
            'required' => true,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 90,
            'position' => 90,
            'system' => 0,
        ]);
        $used_in_forms[]="adminhtml_customer";
        $used_in_forms[]="checkout_register";
        $used_in_forms[]="customer_account_create";
        $used_in_forms[]="customer_account_edit";
        $used_in_forms[]="adminhtml_checkout";
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'telephone')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => $used_in_forms,
            ]);

        $attribute->save();

        $customerSetup->updateAttribute('customer_address' ,'telephone', 'is_required', false);
        $customerSetup->updateAttribute('customer_address' ,'city', 'default_value', "Timisoara");
        $customerSetup->updateAttribute('customer_address' ,'region_id', 'default_value', "315");
        $customerSetup->updateAttribute('customer_address' ,'region', 'default_value', "Timis");
        $customerSetup->updateAttribute('customer_address' ,'postcode', 'is_required', false);
        $customerSetup->updateAttribute('customer_address' ,'country_id', 'default_value', "RO");

        $installer->endSetup();
    }
}
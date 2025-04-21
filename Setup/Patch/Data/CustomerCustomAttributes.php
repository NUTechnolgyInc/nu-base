<?php
/**
 * @author NuLogic Team
 * @package Nulogic_Base
 */
declare(strict_types=1);

namespace Nulogic\Base\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Add Custom Customer Attributes
 */
class CustomerCustomAttributes implements DataPatchInterface
{
    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $attributes = [
            'otp' => [
                'type' => 'text',
                'label' => 'OTP',
                'input' => 'text',
                'required' => false,
                'sort_order' => 19,
                'visible' => false, // Hidden from all forms
                'position' => 19,
                'system' => false,
                'user_defined' => false,
            ],
            'country_code' => [
                'type' => 'varchar',
                'label' => 'Country Code',
                'input' => 'text',
                'required' => false,
                'sort_order' => 20,
                'visible' => true,
                'position' => 20,
                'system' => false,
                'user_defined' => true,
            ],
            'mobile_number' => [
                'type' => 'varchar',
                'label' => 'Mobile Number',
                'input' => 'text',
                'required' => false,
                'sort_order' => 21,
                'visible' => true,
                'position' => 21,
                'system' => false,
                'unique' => true,
                'user_defined' => true,
            ],
        ];

        foreach ($attributes as $attributeCode => $attributeOptions) {
            $customerSetup->addAttribute(Customer::ENTITY, $attributeCode, $attributeOptions);
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $attributeCode);

            $usedInForms = $attributeCode === 'otp' ? [] : [
                'adminhtml_customer',
                'customer_account_create',
                'customer_account_edit'
            ];

            $attribute->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => $usedInForms,
            ]);
            $attribute->save();
        }
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}

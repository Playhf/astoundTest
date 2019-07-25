<?php

namespace AstoundDRudenko\Install\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Downloadable\Model\Product\Type as TypeDownloadable;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use \Magento\Eav\Setup\EavSetupFactory;
use \AstoundDRudenko\Install\Model\TestAttribute\Config;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class AddProductTestDataAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $productTypes = [
            Type::TYPE_SIMPLE,
            Type::TYPE_VIRTUAL,
            Type::TYPE_BUNDLE,
            TypeDownloadable::TYPE_DOWNLOADABLE
        ];
        $productTypes = join(',', $productTypes);

        $eavSetup->addAttribute(
            Product::ENTITY,
            Config::TEST_ATTRIBUTE_CODE,
            [
                'type' => 'varchar',
                'label' => 'Test Product Attribute Data',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'required' => false,
                'apply_to' => $productTypes
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}

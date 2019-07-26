<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Setup\Patch\Data;

use AstoundDRudenko\Badge\Model\Attribute\Badge\Backend;
use AstoundDRudenko\Badge\Model\Attribute\Badge\Config;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use AstoundDRudenko\Badge\Model\Attribute\Badge\Source;

/**
 * Class AddBadgeProductAttribute
 * @package AstoundDRudenko\Badge\Setup\Patch\Data
 */
class AddBadgeProductAttribute implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * Default sort order
     */
    public const DEFAULT_OPTION_SORT_ORDER = 0;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var array
     */
    private $productTypes = [
        Type::TYPE_SIMPLE,
        Configurable::TYPE_CODE
    ];

    /**
     * AddBadgeProductAttribute constructor.
     * @param EavConfig $eavConfig
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavConfig $eavConfig,
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Setup badge attribute
     *
     * @return void
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $this->createBadgeAttribute($eavSetup);
    }

    /**
     * Create attribute
     * @param EavSetup $eavSetup
     * @return $this
     */
    private function createBadgeAttribute(EavSetup $eavSetup)
    {
        $productTypes = implode(',', $this->productTypes);

        $eavSetup->addAttribute(
            Product::ENTITY,
            Config::BADGE_ATTRIBUTE_CODE,
            [
                'type' => 'text',
                'label' => 'Product Badge',
                'input' => 'multiselect',
                'source' => Source::class,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'sort_order' => 85,
                'user_defined' => true,
                'backend' => Backend::class,
                'searchable' => true,
                'filterable' => true,
                'used_in_product_listing' => true,
                'required' => false,
                'apply_to' => $productTypes
            ]
        );

        return $this;
    }

    /**
     * Revert attribute
     */
    public function revert()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->removeAttribute(
            Product::ENTITY,
            Config::BADGE_ATTRIBUTE_CODE
        );
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }
}

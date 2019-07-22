<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Setup\Patch\Data;

use AstoundDRudenko\Badge\Model\Attribute\Badge\Backend;
use AstoundDRudenko\Badge\Model\Attribute\Badge\Config;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use \AstoundDRudenko\Badge\Model\Attribute\Badge\Source;

/**
 * Class AddBadgeProductAttribute
 * @package AstoundDRudenko\Badge\Setup\Patch\Data
 */
class AddBadgeProductAttribute implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var array
     */
    private $productTypes = [
        Type::TYPE_SIMPLE,
        Configurable::TYPE_CODE
    ];

    /**
     * AddBadgeProductAttribute constructor.
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
     * Setup badge attribute
     *
     * @return void
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $productTypes = implode(',', $this->productTypes);

        $eavSetup->addAttribute(
            Product::ENTITY,
            Config::BADGE_ATTRIBUTE_CODE,
            [
                'type' => 'varchar',
                'label' => 'Product Badge',
                'input' => 'multiselect',
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'source' => Source::class,
                'sort_order' => 85,
                'backend' => Backend::class,
                'searchable' => true,
                'filterable' => true,
                'used_in_product_listing' => true,
                'required' => false,
                'apply_to' => $productTypes
            ]
        );
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

<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Backend\Price;
use Magento\Catalog\Model\Product\Type;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;

/**
 * Add previous price attribute
 *
 * Class AddPreviousPriceAttribute
 * @package AstoundDRudenko\PriceBadge\Setup\Patch\Data
 */
class AddPreviousPriceAttribute implements DataPatchInterface, PatchRevertableInterface
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
     * Do upgrade
     *
     * @return void
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            Config::PREVIOUS_PRICE_ATTRIBUTE_CODE,
            [
                'type' => 'decimal',
                'label' => 'Previous Price',
                'input' => 'price',
                'backend' => Price::class,
                'user_defined' => true,
                'sort_order' => 8,
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'searchable' => true,
                'filterable' => true,
                'visible' => true,
                'visible_in_advanced_search' => true,
                'used_in_product_listing' => true,
                'used_for_sort_by' => true,
                'apply_to' => Type::TYPE_SIMPLE,
                'group' => 'Prices'
            ]
        );
    }

    /**
     * Revert data
     */
    public function revert()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->removeAttribute(Product::ENTITY, Config::PREVIOUS_PRICE_ATTRIBUTE_CODE);
    }

    /**
     * {@inheritDoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}

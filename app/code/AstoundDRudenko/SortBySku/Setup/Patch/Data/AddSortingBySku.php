<?php

declare(strict_types=1);
namespace AstoundDRudenko\SortBySku\Setup\Patch\Data;

use Magento\Catalog\Api\Data\EavAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use \Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddSortingBySku
 * @package AstoundDRudenko\SortBySku\Setup\Patch\Data
 */
class AddSortingBySku implements DataPatchInterface
{
    /**
     * Enabled value of attribute
     */
    public const SORT_BY_ENABLED = 1;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * AddSortingBySku constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * Add possibility to use sku in sort by
     *
     * @return void
     */
    public function apply()
    {
        /** @var CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
        $entityTypeId = $categorySetup->getEntityTypeId(Product::ENTITY);

        $categorySetup->updateAttribute(
            $entityTypeId,
            ProductInterface::SKU,
            EavAttributeInterface::USED_FOR_SORT_BY,
            self::SORT_BY_ENABLED
        );
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

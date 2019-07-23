<?php

declare(strict_types=1);
namespace AstoundDRudenko\Install\Setup\Patch\Data;

use AstoundDRudenko\Install\Model\TestAttribute\InstallReindexer;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\ConfigFactory as EavConfigFactory;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use \AstoundDRudenko\Install\Model\TestAttribute\Config;
use \Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use \Magento\Eav\Api\Data\AttributeInterface;

/**
 * Relocate test attribute data
 * Class RelocateTestAttributeData
 * @package AstoundDRudenko\Install\Setup\Patch\Data
 */
class RelocateTestAttributeData implements DataPatchInterface
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
     * @var EavConfigFactory
     */
    private $eavConfigFactory;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var InstallReindexer
     */
    private $installReindexer;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var AbstractAttribute
     */
    private $testDataAttribute;

    /**
     * RelocateTestAttributeData constructor.
     * @param InstallReindexer $installReindexer
     * @param ProductResource $productResource
     * @param EavConfigFactory $eavConfigFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        InstallReindexer $installReindexer,
        ProductResource $productResource,
        EavConfigFactory $eavConfigFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfigFactory = $eavConfigFactory;
        $this->productResource = $productResource;
        $this->installReindexer = $installReindexer;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $this->connection = $this->moduleDataSetup->getConnection();
        $oldTable = $this->getTestDataAttribute()->getBackendTable();

        $eavSetup->updateAttribute(
            Product::ENTITY,
            Config::TEST_ATTRIBUTE_CODE,
            'backend_type',
            'text'
        );

        $fields = [
            AttributeInterface::ATTRIBUTE_ID,
            Product::STORE_ID,
            'value',
            $this->productResource->getLinkField()
        ];

        $this->testDataAttribute = $this->getTestDataAttribute();
        $newTable = $this->testDataAttribute->getBackendTable();

        $select = $this->getAttributeDataSelect($oldTable, $fields);

        $this->insertAttributeData($select, $newTable, $fields);
        $this->deleteAttributeData($select, $oldTable);

        $productIds = $this->getUpdatedProductIds($newTable);

        $this->installReindexer->reindexProductAttributes($productIds);
    }

    /**
     * Query to update attribute data
     * @param string $newTable
     * @return array
     */
    private function getUpdatedProductIds(string $newTable) :array
    {
        $productIdsSelect = $this->connection->select()
            ->from(
                $this->moduleDataSetup->getTable($newTable),
                [$this->productResource->getLinkField()]
            )->where(
                AttributeInterface::ATTRIBUTE_ID . ' = ?', $this->testDataAttribute->getId()
            );

        return $this->connection->fetchCol($productIdsSelect);
    }

    /**
     * Query to delete attribute data
     * @param Select $select
     * @param string $oldTable
     * @return $this
     */
    private function deleteAttributeData(Select $select, string $oldTable)
    {
        $deleteQuery = $this->connection->deleteFromSelect($select, $oldTable);
        $this->connection->query($deleteQuery);

        return $this;
    }

    /**
     * Get test attribute data select object
     * @param string $oldTable
     * @param array $fields
     * @return Select
     */
    private function getAttributeDataSelect(string $oldTable, array $fields) : Select
    {
        $attributeId = $this->testDataAttribute->getId();

        return $this->connection->select()
            ->from(
                $this->moduleDataSetup->getTable($oldTable),
                $fields
            )->where(
                AttributeInterface::ATTRIBUTE_ID . ' = ?', $attributeId
            );
    }

    /**
     * Insert data in text table
     * @param Select $select
     * @param string $newTable
     * @param array $fields
     * @return $this;
     */
    private function insertAttributeData(Select $select, string $newTable, array $fields)
    {
        $insertQuery = $this->connection->insertFromSelect(
            $select,
            $newTable,
            $fields
        );
        $this->connection->query($insertQuery);

        return $this;
    }

    /**
     * Retrieves test data attribute
     * @return AbstractAttribute
     */
    private function getTestDataAttribute()
    {
        return $this->eavConfigFactory
            ->create()
            ->getAttribute(
            Product::ENTITY,
            Config::TEST_ATTRIBUTE_CODE
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
        return [AddProductTestDataAttribute::class, FillTestAttributeData::class];
    }
}

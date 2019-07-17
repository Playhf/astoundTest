<?php

declare(strict_types=1);
namespace AstoundDRudenko\Install\Setup\Patch\Data;

use Magento\Catalog\Model\Indexer\Product\Eav\Processor as AttributeProcessor;
use Magento\Catalog\Model\Indexer\Product\Flat\Processor as ProductFlatProcessor;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\ConfigFactory as EavConfigFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use \AstoundDRudenko\Install\Model\TestAttribute\Config;
use \Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use \Magento\Eav\Api\Data\AttributeInterface;
use Psr\Log\LoggerInterface;

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
     * @var AttributeProcessor
     */
    private $attributeProcessor;

    /**
     * @var ProductFlatProcessor
     */
    private $productFlatProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RelocateTestAttributeData constructor.
     * @param LoggerInterface $logger
     * @param AttributeProcessor $attributeProcessor
     * @param ProductFlatProcessor $productFlatProcessor
     * @param ProductResource $productResource
     * @param EavConfigFactory $eavConfigFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        LoggerInterface $logger,
        AttributeProcessor $attributeProcessor,
        ProductFlatProcessor $productFlatProcessor,
        ProductResource $productResource,
        EavConfigFactory $eavConfigFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfigFactory = $eavConfigFactory;
        $this->productResource = $productResource;
        $this->attributeProcessor = $attributeProcessor;
        $this->productFlatProcessor = $productFlatProcessor;
        $this->logger = $logger;
    }

    /**
     * @return DataPatchInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        try {
            $oldTable = $this->getTestDataAttribute()->getBackendTable();

            $eavSetup->updateAttribute(
                Product::ENTITY,
                Config::TEST_ATTRIBUTE_CODE,
                'backend_type',
                'text'
            );

            $testDataAttribute = $this->getTestDataAttribute();
            $attributeId = $testDataAttribute->getId();
            $newTable = $testDataAttribute->getBackendTable();

            $connection = $this->moduleDataSetup->getConnection();

            $fields = [
                AttributeInterface::ATTRIBUTE_ID,
                Product::STORE_ID,
                'value',
                $this->productResource->getLinkField()
            ];

            $select = $connection->select()
                ->from(
                    $this->moduleDataSetup->getTable($oldTable),
                    $fields
                )->where(
                    AttributeInterface::ATTRIBUTE_ID . ' = ?', $attributeId
                );

            $insertQuery = $connection->insertFromSelect(
                $select,
                $newTable,
                $fields
            );
            $connection->query($insertQuery);

            $deleteQuery = $connection->deleteFromSelect($select, $oldTable);
            $connection->query($deleteQuery);

            $productIdsSelect = $connection->select()
                ->from(
                    $this->moduleDataSetup->getTable($newTable),
                    [$this->productResource->getLinkField()]
                )->where(
                    AttributeInterface::ATTRIBUTE_ID . ' = ?', $attributeId
                );

            $productIds = $connection->fetchCol($productIdsSelect);

            $this->productFlatProcessor->reindexList($productIds);
            $this->attributeProcessor->reindexList($productIds);

        } catch (LocalizedException $e) {
            $this->logger->critical(
                $e->getMessage(),
                ['exception' => $e]
            );
            throw $e;
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     * @throws \Magento\Framework\Exception\LocalizedException
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
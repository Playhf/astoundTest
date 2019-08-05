<?php

namespace AstoundDRudenko\Install\Setup\Patch\Data;

use AstoundDRudenko\Install\Model\TestAttribute\Config;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\State;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use \Magento\Catalog\Model\Product\Type;
use \Magento\Catalog\Api\Data\ProductInterface;
use \AstoundDRudenko\Install\Model\TestAttribute\InstallReindexer;

/**
 * Fill product values for test attribute data
 * Class FillTestAttributeData
 * @package AstoundDRudenko\Install\Setup\Patch\Data
 */
class FillTestAttributeData implements DataPatchInterface
{
    /**
     * Default count coefficient for collection
     */
    public const COUNT_COEFFICIENT = 0.05;

    /**
     * Default string length used for test data generation
     */
    public const STRING_LENGTH = 10;

    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var Random
     */
    private $randomString;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var InstallReindexer
     */
    private $installReindexer;

    /**
     * @param InstallReindexer $installReindexer
     * @param EavConfig $eavConfig
     * @param CollectionFactory $collectionFactory
     * @param State $appState
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreManagerInterface $storeManager
     * @param ProductResource $productResource
     * @param Random $randomString
     */
    public function __construct(
        InstallReindexer $installReindexer,
        EavConfig $eavConfig,
        CollectionFactory $collectionFactory,
        State $appState,
        ModuleDataSetupInterface $moduleDataSetup,
        StoreManagerInterface $storeManager,
        ProductResource $productResource,
        Random $randomString
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeManager = $storeManager;
        $this->productResource = $productResource;
        $this->randomString = $randomString;
        $this->appState = $appState;
        $this->collectionFactory = $collectionFactory;
        $this->eavConfig = $eavConfig;
        $this->installReindexer = $installReindexer;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $this->connection = $this->moduleDataSetup->getConnection();

        $collectionSize = $this->calculateCollectionSize();

        $productCollection = $this->collectionFactory->create();
        $productIds = $productCollection
            ->addFieldToFilter(ProductInterface::TYPE_ID, TYPE::TYPE_SIMPLE)
            ->getAllIds($collectionSize);

        $this->fillTestAttributeData($productIds);

        $this->installReindexer->reindexProductAttributes($productIds);
    }

    /**
     * Calculate count of products to insert data
     * @return float
     */
    private function calculateCollectionSize() :float
    {
        $countSelect = $this->connection->select()
            ->from(
                $this->productResource->getEntityTable(),
                'COUNT(*)'
            )->where(
                ProductInterface::TYPE_ID . ' = ?', TYPE::TYPE_SIMPLE
            );

        $count = $this->connection->fetchOne($countSelect);
        return floor($count * self::COUNT_COEFFICIENT);
    }


    /**
     * Fill product values for test attribute data
     * @param array $productIds
     */
    private function fillTestAttributeData(array $productIds)
    {
        $testDataAttribute = $this->eavConfig->getAttribute(
            Product::ENTITY,
            Config::TEST_ATTRIBUTE_CODE
        );

        $insertData = [];
        $stores = $this->storeManager->getStores(true);
        $linkField = $this->productResource->getLinkField();
        $attributeId = $testDataAttribute->getId();

        foreach ($productIds as $productId) {
            $storeId = array_rand($stores, 1);
            $testData = $this->randomString->getRandomString(self::STRING_LENGTH);

            $insertData[] = [
                $linkField => $productId,
                AttributeInterface::ATTRIBUTE_ID => $attributeId,
                Product::STORE_ID => $storeId,
                'value' => $testData
            ];
        }

        $this->connection->insertOnDuplicate(
            $this->connection->getTableName($testDataAttribute->getBackendTable()),
            $insertData,
            array_keys($insertData)
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
        return [AddProductTestDataAttribute::class];
    }
}

<?php

namespace AstoundDRudenko\Install\Setup\Patch\Data;

use AstoundDRudenko\Install\Model\TestAttribute\Config;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\App\State;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Math\Random;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use \Magento\Catalog\Model\Product\Type;
use \Magento\Catalog\Api\Data\ProductInterface;
use Psr\Log\LoggerInterface;
use \Magento\Catalog\Model\Indexer\Product\Eav\Processor as EavProcessor;
use \Magento\Catalog\Model\Indexer\Product\Flat\Processor as ProductFlatProcessor;
use \Magento\Catalog\Model\Indexer\Product\Eav\Processor as AttributeProcessor;

class FillTestAttributeData implements DataPatchInterface
{
    public const COUNT_COEFFICIENT = 0.05;

    public const STRING_LENGTH = 10;

    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

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
     * @var LoggerInterface
     */
    private $logger;

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
     * @var ProductFlatProcessor
     */
    private $productFlatProcessor;

    /**
     * @var AttributeProcessor
     */
    private $attributeProcessor;

    /**
     * @param AttributeProcessor $attributeProcessor
     * @param ProductFlatProcessor $productFlatProcessor
     * @param EavConfig $eavConfig
     * @param CollectionFactory $collectionFactory
     * @param State $appState
     * @param LoggerInterface $logger
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreManagerInterface $storeManager
     * @param ProductResource $productResource
     * @param Random $randomString
     */
    public function __construct(
        AttributeProcessor $attributeProcessor,
        ProductFlatProcessor $productFlatProcessor,
        EavConfig $eavConfig,
        CollectionFactory $collectionFactory,
        State $appState,
        LoggerInterface $logger,
        ModuleDataSetupInterface $moduleDataSetup,
        StoreManagerInterface $storeManager,
        ProductResource $productResource,
        Random $randomString
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeManager = $storeManager;
        $this->productResource = $productResource;
        $this->randomString = $randomString;
        $this->logger = $logger;
        $this->appState = $appState;
        $this->collectionFactory = $collectionFactory;
        $this->eavConfig = $eavConfig;
        $this->productFlatProcessor = $productFlatProcessor;
        $this->attributeProcessor = $attributeProcessor;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $connection = $this->moduleDataSetup->getConnection();

        $countSelect = $connection->select()
            ->from(
                $this->productResource->getEntityTable(),
                'COUNT(*)'
            )->where(
                ProductInterface::TYPE_ID . ' = ?', TYPE::TYPE_SIMPLE
            );

        $count = $connection->fetchOne($countSelect);
        $collectionSize = floor($count * self::COUNT_COEFFICIENT);

        try {
            $productCollection = $this->collectionFactory->create();
            $productIds = $productCollection
                ->addFieldToFilter(ProductInterface::TYPE_ID, TYPE::TYPE_SIMPLE)
                ->getAllIds($collectionSize);

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

            $connection->insertOnDuplicate(
                $connection->getTableName($testDataAttribute->getBackendTable()),
                $insertData,
                array_keys($insertData)
            );

            $this->productFlatProcessor->reindexList($productIds);
            $this->attributeProcessor->reindexList($productIds);

        } catch (\Exception $e) {
            $this->logger->critical(
                $e->getMessage(),
                ['exception' => $e]
            );
            throw $e;
        }

        $this->moduleDataSetup->endSetup();
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
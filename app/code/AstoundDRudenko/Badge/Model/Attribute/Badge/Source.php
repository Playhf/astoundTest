<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Model\Attribute\Badge;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Source
 * @package AstoundDRudenko\Badge\Model\Attribute\Badge
 */
class Source extends AbstractSource
{
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var CollectionFactory
     */
    private $optionsCollectionFactory;

    /**
     * Source constructor.
     * @param ProductAttributeRepositoryInterface $attributeRepository
     * @param CollectionFactory $optionsCollectionFactory
     */
    public function __construct(
        ProductAttributeRepositoryInterface $attributeRepository,
        CollectionFactory $optionsCollectionFactory
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->optionsCollectionFactory = $optionsCollectionFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllOptions() :array
    {
        if (!$this->_options) {
            $options = [];

            $dbOptions = $this->getOptionsDbValues();
            $allOptions = array_merge($dbOptions, Config::DEFAULT_OPTIONS);

            foreach ($allOptions as $value => $label) {
                $options[] = [
                    'value' => $value,
                    'label' => $label
                ];
            }

            $this->_options = $options;
        }

        return $this->_options;
    }

    /**
     * Retrieves db options values
     * @return array
     */
    private function getOptionsDbValues() :array
    {
        $options = [];

        if ($attribute = $this->getBadgeAttribute()) {
            $dbOptions = $this->optionsCollectionFactory
                ->create()
                ->setAttributeFilter($attribute->getAttributeId())
                ->setPositionOrder(
                    'asc',
                    true
                )->getItems();

            foreach ($dbOptions as $option) {
                $options[$option->getValue()] = $option->getValue();
            }
        }

        return $options;
    }

    /**
     * Retrieves an attribute
     * @return ProductAttributeInterface|null
     */
    private function getBadgeAttribute() : ?ProductAttributeInterface
    {
        try {
            $attribute = $this->attributeRepository->get(Config::BADGE_ATTRIBUTE_CODE);
        } catch (NoSuchEntityException $e) {
            $attribute = null;
        }

        return $attribute;
    }
}

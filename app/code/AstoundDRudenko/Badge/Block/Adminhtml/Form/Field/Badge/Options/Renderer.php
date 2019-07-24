<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Block\Adminhtml\Form\Field\Badge\Options;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use \Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\View\Element\Html\Select;
use \AstoundDRudenko\Badge\Model\Attribute\Badge\Config;
use \Magento\Framework\View\Element\Context;

/**
 * Class Renderer
 * @package AstoundDRudenko\Badge\Block\Adminhtml\Form\Field\Badge\Options
 */
class Renderer extends Select
{
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * Renderer constructor.
     * @param ProductAttributeRepositoryInterface $attributeRepository
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ProductAttributeRepositoryInterface $attributeRepository,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Set input name
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Retrieves an options
     * @param ProductAttributeInterface $attribute
     * @return array
     */
    public function getConfigOptions(ProductAttributeInterface $attribute) :array
    {
        return $attribute->getSource()->getAllOptions();
    }

    /**
     * {@inheritDoc}
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            try {
                $attribute = $this->attributeRepository->get(Config::BADGE_ATTRIBUTE_CODE);
                $options = $this->getConfigOptions($attribute);
            } catch (NoSuchEntityException $e) {
                $options = $this->getDefaultOptions();
            }

            foreach ($options as $option) {
                $this->addOption($option['value'], addslashes($option['label']));
            }
        }

        return parent::_toHtml();
    }



    /**
     * Retrieves default options
     * @return array
     */
    private function getDefaultOptions() :array
    {
        $result = [];

        foreach (Config::DEFAULT_OPTIONS as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $result;
    }
}

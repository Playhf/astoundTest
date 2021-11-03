<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Pricing\Render\Configurable\FinalPriceBox;

use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;
use AstoundDRudenko\PriceBadge\Model\PreviousPrice\Discount\Calculator;
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\ConfigurableProduct\Pricing\Price\ConfigurableOptionsProviderInterface;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\ConfigurableProduct\Pricing\Render\FinalPriceBox;
use Magento\Framework\View\Element\Template\Context;

/**
 * Extended configurable final price model with possibility for checking previous price availability
 *
 * Class Extended
 * @package AstoundDRudenko\PriceBadge\Pricing\Render
 */
class Extended extends FinalPriceBox
{
    /**
     * @var LowestPriceOptionsProviderInterface
     */
    private $lowestPriceOptionsProvider;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * ConfigurablePriceBox constructor.
     * @param Calculator $calculator
     * @param LowestPriceOptionsProviderInterface|null $lowestPriceOptionsProvider
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param ConfigurableOptionsProviderInterface $configurableOptionsProvider
     * @param array $data
     * @param SalableResolverInterface|null $salableResolver
     * @param MinimalPriceCalculatorInterface|null $minimalPriceCalculator
     */
    public function __construct(
        Calculator $calculator,
        LowestPriceOptionsProviderInterface $lowestPriceOptionsProvider,
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        ConfigurableOptionsProviderInterface $configurableOptionsProvider,
        array $data = [],
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minimalPriceCalculator = null
    ) {
        $this->lowestPriceOptionsProvider = $lowestPriceOptionsProvider;
        $this->calculator = $calculator;
        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $configurableOptionsProvider,
            $data,
            $lowestPriceOptionsProvider,
            $salableResolver,
            $minimalPriceCalculator
        );
    }

    /**
     * Check does previous price exist in child products
     *
     * @return bool
     */
    public function hasPreviousPrice(): bool
    {
        $product = $this->getSaleableItem();
        $hasPreviousPrice = false;
        $previousPrice = 0;

        foreach ($this->lowestPriceOptionsProvider->getProducts($product) as $subProduct) {
            $previousPrice = $subProduct->getPriceInfo()->getPrice(Config::PREVIOUS_PRICE_ATTRIBUTE_CODE)->getValue();
            $hasPreviousPrice = $previousPrice > 0;
        }

        if ($hasPreviousPrice && $previousPrice) {
            $this->calculator->calculate($product, $previousPrice);
        }

        return $hasPreviousPrice;
    }
}

<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Pricing\Render\Simple\FinalPriceBox;

use AstoundDRudenko\PriceBadge\Model\PreviousPrice\Discount\Calculator;
use AstoundDRudenko\PriceBadge\Pricing\Price\Simple\PreviousPrice;
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\Catalog\Pricing\Render\FinalPriceBox;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Extended final price model with possibility for checking previous price availability
 *
 * Class Extended
 * @package AstoundDRudenko\PriceBadge\Pricing\Render\Simple\FinalPriceBox
 */
class Extended extends FinalPriceBox
{
    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * Extended constructor.
     * @param Calculator $calculator
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param array $data
     * @param SalableResolverInterface|null $salableResolver
     * @param MinimalPriceCalculatorInterface|null $minimalPriceCalculator
     */
    public function __construct(
        Calculator $calculator,
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        array $data = [],
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minimalPriceCalculator = null
    ) {
        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $data,
            $salableResolver,
            $minimalPriceCalculator
        );
        $this->calculator = $calculator;
    }

    /**
     * Check does previous price exist in child products
     *
     * @return bool
     */
    public function hasPreviousPrice(): bool
    {
        $value = $this->getPriceType(PreviousPrice::PRICE_CODE)
            ->getAmount()
            ->getValue();

        $hasPreviousPrice = $value > 0;
        if ($hasPreviousPrice) {
            $this->calculator->calculate($this->getSaleableItem(), $value);
        }

        return $hasPreviousPrice;
    }
}

<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Pricing\Render;

use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\PriceBox as BasePriceBox;
use AstoundDRudenko\PriceBadge\Pricing\Price\Simple\PreviousPrice;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\View\Element\Template;
use AstoundDRudenko\PriceBadge\Model\PreviousPrice\Discount\Calculator;

/**
 * Class for previous_price rendering
 * @package AstoundDRudenko\PriceBadge\Pricing\Render
 */
class SimplePriceBox extends BasePriceBox
{
    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * SimplePriceBox constructor.
     * @param Calculator $calculator
     * @param Template\Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param array $data
     */
    public function __construct(
        Calculator $calculator,
        Template\Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        array $data = []
    ) {
        parent::__construct($context, $saleableItem, $price, $rendererPool, $data);
        $this->calculator = $calculator;
    }

    /**
     * Check if salable item has previous price
     * @return bool
     */
    public function hasPreviousPrice() :bool
    {
        $value = $this->getPriceType(PreviousPrice::PRICE_CODE)
            ->getAmount()
            ->getValue();

        $hasPreviousPrice = $value > 0;
        if ($hasPreviousPrice) {
            $this->calculator->calculate($this, $value);
        }

        return $hasPreviousPrice;
    }
}

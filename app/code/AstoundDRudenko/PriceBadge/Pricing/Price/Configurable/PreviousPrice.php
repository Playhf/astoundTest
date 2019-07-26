<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Pricing\Price\Configurable;

use AstoundDRudenko\PriceBadge\Pricing\Price\Simple\PreviousPrice as SimplePreviousPrice;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;
use Magento\ConfigurableProduct\Pricing\Price\PriceResolverInterface;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Price\AbstractPrice;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\SaleableInterface;

/**
 * Configurable previous price model
 * Class PreviousPrice
 * @package AstoundDRudenko\PriceBadge\Pricing\Price\Configurable
 */
class PreviousPrice extends AbstractPrice
{
    /**
     * Price code
     */
    public const PRICE_CODE = SimplePreviousPrice::PRICE_CODE;

    /**
     * @var PriceResolverInterface
     */
    private $priceResolver;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * PreviousPrice constructor.
     * @param PriceResolverInterface $priceResolver
     * @param SaleableInterface $saleableItem
     * @param $quantity
     * @param CalculatorInterface $calculator
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        PriceResolverInterface $priceResolver,
        SaleableInterface $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        PriceCurrencyInterface $priceCurrency
    ) {
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency);
        $this->priceResolver = $priceResolver;
    }

    public function getValue()
    {
        if (!isset($this->values[$this->product->getId()])) {
            $this->values[$this->product->getId()] = $this->priceResolver->resolvePrice($this->product);
        }

        return $this->values[$this->product->getId()];
    }
}

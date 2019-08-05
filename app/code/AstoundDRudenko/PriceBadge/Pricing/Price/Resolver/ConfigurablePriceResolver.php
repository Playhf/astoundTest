<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Pricing\Price\Resolver;

use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;
use Magento\ConfigurableProduct\Pricing\Price\PriceResolverInterface;
use Magento\Framework\Pricing\SaleableInterface;

/**
 * Previous price resolver
 *
 * Class ConfigurablePriceResolver
 * @package AstoundDRudenko\PriceBadge\Pricing\Price\Resolver
 */
class ConfigurablePriceResolver implements PriceResolverInterface
{
    /**
     * @var LowestPriceOptionsProviderInterface
     */
    private $lowestPriceOptionsProvider;
    /**
     * @var PriceResolverInterface
     */
    private $priceResolver;

    /**
     * ConfigurablePriceResolver constructor.
     *
     * @param PriceResolverInterface $priceResolver
     * @param LowestPriceOptionsProviderInterface $lowestPriceOptionsProvider
     */
    public function __construct(
        PriceResolverInterface $priceResolver,
        LowestPriceOptionsProviderInterface $lowestPriceOptionsProvider
    ) {
        $this->lowestPriceOptionsProvider = $lowestPriceOptionsProvider;
        $this->priceResolver = $priceResolver;
    }

    /**
     * @inheritDoc
     */
    public function resolvePrice(SaleableInterface $product): float
    {
        $price = null;

        foreach ($this->lowestPriceOptionsProvider->getProducts($product) as $subProduct) {
            $productPrice = $this->priceResolver->resolvePrice($subProduct);
            $price = isset($price) ? min($price, $productPrice) : $productPrice;
        }

        return $price;
    }
}

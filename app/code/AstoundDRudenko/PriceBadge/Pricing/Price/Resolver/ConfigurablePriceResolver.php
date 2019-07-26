<?php


namespace AstoundDRudenko\PriceBadge\Pricing\Price\Resolver;

use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;
use Magento\ConfigurableProduct\Pricing\Price\PriceResolverInterface;

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

    public function resolvePrice(\Magento\Framework\Pricing\SaleableInterface $product)
    {
        $price = null;

        foreach ($this->lowestPriceOptionsProvider->getProducts($product) as $subProduct) {
            $productPrice = $this->priceResolver->resolvePrice($subProduct);
            $price = isset($price) ? min($price, $productPrice) : $productPrice;
        }

        return (float)$price;
    }
}

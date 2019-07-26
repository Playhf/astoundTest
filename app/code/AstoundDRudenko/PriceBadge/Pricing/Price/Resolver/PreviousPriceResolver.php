<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Pricing\Price\Resolver;

use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;
use Magento\ConfigurableProduct\Pricing\Price\PriceResolverInterface;

class PreviousPriceResolver implements PriceResolverInterface
{
    /**
     * Resolve price
     * @param \Magento\Framework\Pricing\SaleableInterface $product
     * @return float
     */
    public function resolvePrice(\Magento\Framework\Pricing\SaleableInterface $product) :float
    {
        return $product->getPriceInfo()->getPrice(Config::PREVIOUS_PRICE_ATTRIBUTE_CODE)->getValue();
    }
}

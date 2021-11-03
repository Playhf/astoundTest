<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Pricing\Price\Resolver;

use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;
use Magento\ConfigurableProduct\Pricing\Price\PriceResolverInterface;
use Magento\Framework\Pricing\SaleableInterface;

/**
 * Previous price resolve model
 *
 * Class PreviousPriceResolver
 * @package AstoundDRudenko\PriceBadge\Pricing\Price\Resolver
 */
class PreviousPriceResolver implements PriceResolverInterface
{
    /**
     * Resolve price
     *
     * @param SaleableInterface $product
     * @return float
     */
    public function resolvePrice(SaleableInterface $product): float
    {
        return $product->getPriceInfo()->getPrice(Config::PREVIOUS_PRICE_ATTRIBUTE_CODE)->getValue();
    }
}

<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Model\PreviousPrice\Discount;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;

/**
 * Discount calculator of difference between final and previous price
 *
 * @package AstoundDRudenko\PriceBadge\Model\PreviousPrice\Discount
 */
class Calculator
{
    /**
     * @var Provider
     */
    private $discountProvider;

    /**
     * Calculator constructor.
     * @param Provider $discountProvider
     */
    public function __construct(Provider $discountProvider)
    {
        $this->discountProvider = $discountProvider;
    }

    /**
     * Calculate discount
     *
     * @param Product $product
     * @param float $previousPrice
     * @return $this
     */
    public function calculate(Product $product, float $previousPrice)
    {
        if (!$this->discountProvider->getDiscount($product)) {
            $finalPrice = $product->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)
                ->getAmount()
                ->getValue();

            $subtract = $previousPrice - $finalPrice;
            $discount = 0;
            if ($subtract > 0) {
                $discount = round(($subtract / $previousPrice) * 100);
            }

            $this->discountProvider->setDiscount($product, $discount);
        }

        return $this;
    }
}

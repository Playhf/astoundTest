<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Model\PreviousPrice\Discount;

use AstoundDRudenko\PriceBadge\Pricing\Price\Simple\PreviousPrice;
use AstoundDRudenko\PriceBadge\Pricing\Render\SimplePriceBox;
use Magento\Catalog\Pricing\Price\FinalPrice;

/**
 * Discount calculator of difference between final and previous price
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
     * @param SimplePriceBox $simplePriceBox
     * @param float $previousPrice
     * @return $this
     */
    public function calculate(SimplePriceBox $simplePriceBox, float $previousPrice)
    {
        $saleableItem = $simplePriceBox->getSaleableItem();
        if (!$this->discountProvider->getDiscount($saleableItem)) {
            $finalPrice = $simplePriceBox->getPriceType(FinalPrice::PRICE_CODE)
                ->getAmount()
                ->getValue();

            $subtract = $previousPrice - $finalPrice;
            $discount = 0;
            if ($subtract > 0) {
                $discount = round(($subtract / $previousPrice) * 100);
            }

            $this->discountProvider->setDiscount($saleableItem, $discount);
        }
        return $this;
    }
}

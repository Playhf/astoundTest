<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Pricing\Price\Simple;

use \Magento\Framework\Pricing\Price\AbstractPrice;
use \AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;

/**
 * Previous price model
 *
 * Class PreviousPrice
 * @package AstoundDRudenko\PriceBadge\Pricing\Price\Simple
 */
class PreviousPrice extends AbstractPrice
{
    /**
     * Price type previous
     */
    const PRICE_CODE = Config::PREVIOUS_PRICE_ATTRIBUTE_CODE;

    /**
     * Get previous price value
     *
     * @return float
     */
    public function getValue(): float
    {
        if ($this->value === null) {
            $price = $this->product->getPreviousPrice();
            $priceInCurrentCurrency = $this->priceCurrency->convertAndRound($price);
            $this->value = $priceInCurrentCurrency ? (float)$priceInCurrentCurrency : 0;
        }
        return $this->value;
    }
}

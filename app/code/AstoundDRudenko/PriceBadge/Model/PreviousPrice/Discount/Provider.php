<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Model\PreviousPrice\Discount;

use \Magento\Catalog\Model\Product;

class Provider
{
    /**
     * Default discount
     */
    public const DEFAULT_DISCOUNT = 0;

    /**
     * @var array
     */
    private $discounts = [];

    /**
     * Set discount of product
     * @param Product $product
     * @param float $value
     * @return $this
     */
    public function setDiscount(Product $product, float $value)
    {
        $this->discounts[$product->getId()] = $value;

        return $this;
    }

    /**
     * Get discount of product
     * @param Product $product
     * @return float
     */
    public function getDiscount(Product $product) :float
    {
        return $this->discounts[$product->getId()] ?? self::DEFAULT_DISCOUNT;
    }
}

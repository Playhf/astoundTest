<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Plugin\Badge\Provider;

use \AstoundDRudenko\Badge\Model\Product\View\Badge\Provider;
use \AstoundDRudenko\PriceBadge\Model\PreviousPrice\Discount\Provider as DiscountProvider;
use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;

/**
 * Add price badge labels
 * Class ExtendLabels
 * @package AstoundDRudenko\PriceBadge\Plugin\Badge\Provider
 */
class ExtendLabels
{
    /**
     * Default discount format
     */
    public const DEFAULT_DISCOUNT_FORMAT = '%s Saved!';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var DiscountProvider
     */
    private $discountProvider;

    /**
     * ExtendLabels constructor.
     * @param Config $config
     * @param DiscountProvider $discountProvider
     */
    public function __construct(
        Config $config,
        DiscountProvider $discountProvider
    ) {
        $this->config = $config;
        $this->discountProvider = $discountProvider;
    }

    /**
     * Add price badge
     * @param Provider $badgeProvider
     * @param array $badges
     * @return array
     */
    public function beforeGetBadgesLabels(Provider $badgeProvider, array $badges) :array
    {
        $product = $badgeProvider->getCurrentProduct();
        if ($this->config->previousPriceEnabled() && $product && $product->hasPreviousPrice()) {
            $badges[] = Config::PRICE_BADGE_OPTION_LABEL;
        }

        return [$badges];
    }

    /**
     * Set price badge label if exists
     * @param Provider $badgeProvider
     * @param array $badges
     * @return array
     */
    public function afterGetBadgesLabels(Provider $badgeProvider, array $badges) :array
    {
        if (isset($badges[Config::PRICE_BADGE_OPTION_LABEL])) {
            $product = $badgeProvider->getCurrentProduct();
            $discount = $this->discountProvider->getDiscount($product);

            if ($product && $discount) {
                $labelValue = Config::PRICE_BADGE_OPTION_LABEL;
                $labelFormat = $this->config->getPriceLabelFormat();
                $argument = $discount . '%';

                try {
                    $badges[$labelValue] = sprintf($labelFormat, $argument);
                } catch (\Throwable $e) {
                    $badges[$labelValue] = sprintf(self::DEFAULT_DISCOUNT_FORMAT, $argument);
                }
            }
        }

        return $badges;
    }
}

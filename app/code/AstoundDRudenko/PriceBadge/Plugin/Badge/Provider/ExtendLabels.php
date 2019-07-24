<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Plugin\Badge\Provider;

use \AstoundDRudenko\Badge\Model\Product\View\Badge\Provider;
use \AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;
use \AstoundDRudenko\PriceBadge\Model\PreviousPrice\Discount\Provider as DiscountProvider;

/**
 * Add price label
 * Class ExtendLabels
 * @package AstoundDRudenko\PriceBadge\Plugin\Badge\Provider
 */
class ExtendLabels
{
    /**
     * @var DiscountProvider
     */
    private $discountProvider;

    /**
     * @var Config
     */
    private $config;

    /**
     * ExtendLabels constructor.
     * @param Config $config
     * @param DiscountProvider $discountProvider
     */
    public function __construct(
        Config $config,
        DiscountProvider $discountProvider
    ) {
        $this->discountProvider = $discountProvider;
        $this->config = $config;
    }

    /**
     * Extends product badge labels
     * @param Provider $subject
     * @param Provider $result
     * @return array
     */
    public function afterInitBadgeOptions(Provider $subject, Provider $result) :Provider
    {
        if ($this->config->previousPriceEnabled()) {
            $labelFormat = $this->config->getPriceLabelFormat();

            $badge = [
                'value' => Config::PRICE_BADGE_OPTION_LABEL,
                'label' => $labelFormat
            ];

            $subject->pushBadge($badge);
        }

        return $result;
    }
}

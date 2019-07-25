<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Plugin\Badge\Provider;

use \AstoundDRudenko\Badge\Model\Product\View\Badge\Provider;
use \AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;

/**
 * Add price label
 * Class ExtendLabels
 * @package AstoundDRudenko\PriceBadge\Plugin\Badge\Provider
 */
class ExtendBadgeOptions
{
    /**
     * @var Config
     */
    private $config;

    /**
     * ExtendLabels constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
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

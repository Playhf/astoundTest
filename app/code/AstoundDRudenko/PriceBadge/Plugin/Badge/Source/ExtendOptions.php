<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Plugin\Badge\Source;

use \AstoundDRudenko\Badge\Model\Attribute\Badge\Source;
use \AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;

/**
 * Extend existing options by adding price badge
 * @package AstoundDRudenko\PriceBadge\Plugin\Badge\Source
 */
class ExtendOptions
{
    /**
     * Extend attribute options
     * @param Source $subject
     * @param array $options
     * @return array
     */
    public function afterGetAllOptions(Source $subject, array $options) :array
    {
        $options[] = [
            'value' => Config::PRICE_BADGE_OPTION_VALUE,
            'label' => Config::PRICE_BADGE_OPTION_LABEL
        ];

        return $options;
    }
}

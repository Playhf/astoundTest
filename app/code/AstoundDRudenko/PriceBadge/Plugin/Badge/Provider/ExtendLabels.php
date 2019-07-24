<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Plugin\Badge\Provider;

use \AstoundDRudenko\Badge\Model\Product\View\Badge\Provider;

/**
 * Add price label
 * Class ExtendLabels
 * @package AstoundDRudenko\PriceBadge\Plugin\Badge\Provider
 */
class ExtendLabels
{
    public function afterInitBadgeOptions(Provider $subject, array $options) :array
    {


        return $options;
    }
}

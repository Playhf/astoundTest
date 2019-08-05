<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Plugin\Badge\Options;

use AstoundDRudenko\Badge\Block\Adminhtml\Form\Field\Badge\Options\Renderer;
use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;

/**
 * Plugin responsible for adding price option
 *
 * @package AstoundDRudenko\PriceBadge\Plugin\Badge\Options
 */
class ExtendConfig
{
    /**
     * Add price badge option
     *
     * @param Renderer $subject
     * @param array $options
     * @return array
     */
    public function afterGetConfigOptions(Renderer $subject, array $options): array
    {
        $options[] = [
            'value' => Config::PRICE_BADGE_OPTION_LABEL,
            'label' => Config::PRICE_BADGE_OPTION_LABEL
        ];

        return $options;
    }
}

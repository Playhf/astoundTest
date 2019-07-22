<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Model\Attribute\Badge;

/**
 * Simple config provider
 *
 * Class Config
 * @package AstoundDRudenko\Badge\Model\Attribute\Badge
 */
class Config
{
    /**
     * Attribute code
     */
    public const BADGE_ATTRIBUTE_CODE = 'badge_label';

    /**
     * An array of attribute for soruce model
     */
    public const DEFAULT_OPTIONS = [
        self::SALE_OPTION_VALUE => self::SALE_OPTION_LABEL,
        self::NEW_OPTION_VALUE => self::NEW_OPTION_LABEL,
        self::EXCLUSIVE_OPTION_VALUE => self::EXCLUSIVE_OPTION_LABEL
    ];

    /**
     * Sale option label
     */
    public const SALE_OPTION_LABEL = 'Sale';

    /**
     * New option label
     */
    public const NEW_OPTION_LABEL = 'New';

    /**
     * Exclusive option label
     */
    public const EXCLUSIVE_OPTION_LABEL = 'Exclusive';

    /**
     * Sale option value
     */
    public const SALE_OPTION_VALUE = 'sale';

    /**
     * New option value
     */
    public const NEW_OPTION_VALUE = 'new';

    /**
     * Exclusive option value
     */
    public const EXCLUSIVE_OPTION_VALUE = 'exclusive';
}

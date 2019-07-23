<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Simple config provider for attribute
 * Class Config
 * @package AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice
 */
class Config
{
    /**#@+
     * Constants config path
     */
    public const CONFIG_PATH_ENABLED = 'catalog/price_badge/active';
    public const CONFIG_PATH_LABEL   = 'catalog/price_badge/label';
    /**#@-*/

    /**#@+
     * Constants option price badge
     */
    public const PRICE_BADGE_OPTION_VALUE = 'price';
    public const PRICE_BADGE_OPTION_LABEL   = 'Price';
    /**#@-*/

    /**
     * Attribute code
     */
    public const PREVIOUS_PRICE_ATTRIBUTE_CODE = 'previous_price';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Is previous price output enabled
     * @return bool
     */
    public function previousPriceEnabled() :bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}

<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Model\Attribute\Badge;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

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
     * An array of attribute for source model
     */
    public const DEFAULT_OPTIONS = [
        self::SALE_OPTION_LABEL => self::SALE_OPTION_LABEL,
        self::NEW_OPTION_LABEL => self::NEW_OPTION_LABEL,
        self::EXCLUSIVE_OPTION_LABEL => self::EXCLUSIVE_OPTION_LABEL
    ];

    /**#@+
     * Badge option labels
     */
    public const SALE_OPTION_LABEL = 'Sale';
    public const NEW_OPTION_LABEL = 'New';
    public const EXCLUSIVE_OPTION_LABEL = 'Exclusive';
    /**#@-*/

    /**#@+
     * Badge option values
     */
    public const SALE_OPTION_VALUE = 'sale';
    public const NEW_OPTION_VALUE = 'new';
    public const EXCLUSIVE_OPTION_VALUE = 'exclusive';
    /**#@-*/

    /**#@+
     * Badge config xml paths
     */
    public const CONFIG_PATH_BADGES_ENABLED = 'catalog/product_badges/active';
    public const CONFIG_PATH_BADGES_PRIORITY = 'catalog/product_badges/priority';
    public const CONFIG_PATH_BADGES_MULTIPLE_ENABLED = 'catalog/product_badges/enable_multiple';
    /**#@-*/

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $badgesPriority;

    /**
     * Data constructor.
     * @param SerializerInterface $serializer
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        SerializerInterface $serializer,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Is badged enabled
     * @return bool
     */
    public function isBadgesEnabled() :bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_BADGES_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Multiple badges enabled
     * @return bool
     */
    public function isMultipleBadgesEnabled() :bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_BADGES_MULTIPLE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieves badges priority config
     * @return array
     */
    public function getBadgesPriority() :array
    {
        if (null === $this->badgesPriority) {
            $result = [];

            $configPriority = $this->scopeConfig->getValue(
                self::CONFIG_PATH_BADGES_PRIORITY,
                ScopeInterface::SCOPE_STORE
            );

            if ($configPriority) {
                try {
                    $result = $this->serializer->unserialize($configPriority);
                } catch (\InvalidArgumentException $e) {
                    $result = [];
                }

                asort($result);
            }

            $this->badgesPriority = $result;
        }

        return $this->badgesPriority;
    }
}

<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Simple helper for work with config values
 *
 * Class Data
 * @package AstoundDRudenko\Badge\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Badge config path enabled
     */
    public const CONFIG_PATH_BADGES_ENABLED = 'catalog/product_badges/active';

    /**
     * Badge config path priority
     */
    public const CONFIG_PATH_BADGES_PRIORITY = 'catalog/product_badges/priority';

    /**
     * Badge config path multiple
     */
    public const CONFIG_PATH_BADGES_MULTIPLE_ENABLED = 'catalog/product_badges/enable_multiple';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Data constructor.
     * @param SerializerInterface $serializer
     * @param Context $context
     */
    public function __construct(
        SerializerInterface $serializer,
        Context $context
    ) {
        parent::__construct($context);
        $this->serializer = $serializer;
    }

    /**
     * Is badged enabled
     *
     * @return mixed
     */
    public function isBadgesEnabled()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_BADGES_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Multiple badges enabled
     * @return mixed
     */
    public function isMultipleBadgesEnabled()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_BADGES_MULTIPLE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieves badges priority config
     * @return array
     */
    public function getBadgesPriority()
    {
        $result = [];

        $configPriority = $this->scopeConfig->getValue(
            self::CONFIG_PATH_BADGES_PRIORITY,
            ScopeInterface::SCOPE_STORE
        );

        if ($configPriority) {
            $result = $this->serializer->unserialize($configPriority);
            asort($result);
        }

        return $result;
    }
}

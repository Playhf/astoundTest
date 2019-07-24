<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\ViewModel\Category\Product;

use AstoundDRudenko\Badge\Model\Attribute\Badge\Config;
use \AstoundDRudenko\Badge\Model\Product\View\Badge\Provider;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * PLP view model responsible for badge providing
 *
 * Class Badge
 * @package AstoundDRudenko\Badge\ViewModel\Category\Product
 */
class Badge implements ArgumentInterface
{
    /**
     * @var Provider
     */
    private $badgeProvider;

    /**
     * @var Config
     */
    private $badgeConfig;

    /**
     * Badge constructor.
     * @param Config $badgeConfig
     * @param Provider $badgeProvider
     */
    public function __construct(
        Config $badgeConfig,
        Provider $badgeProvider
    ) {
        $this->badgeProvider = $badgeProvider;
        $this->badgeConfig = $badgeConfig;
    }

    /**
     * @param Product $product
     * @return array
     */
    public function getProductBadges(Product $product) :array
    {
        return $this->badgeProvider->getProductBadges($product);
    }

    /**
     * Is badges enabled
     * @return bool
     */
    public function productBadgesEnabled() :bool
    {
        return $this->badgeConfig->isBadgesEnabled();
    }
}

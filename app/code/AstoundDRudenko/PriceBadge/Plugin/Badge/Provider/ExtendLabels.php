<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Plugin\Badge\Provider;

use AstoundDRudenko\Badge\Model\Product\View\Badge\Provider;
use AstoundDRudenko\Badge\Model\Attribute\Badge\Config as BadgeConfig;
use AstoundDRudenko\PriceBadge\Model\PreviousPrice\Discount\Provider as DiscountProvider;
use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;
use AstoundDRudenko\Badge\Model\Attribute\Badge\Source as BadgeSource;
use Magento\Catalog\Model\Product;

/**
 * Add price badge labels
 *
 * Class ExtendLabels
 * @package AstoundDRudenko\PriceBadge\Plugin\Badge\Provider
 */
class ExtendLabels
{
    /**
     * Default discount format
     */
    public const DEFAULT_DISCOUNT_FORMAT = '%s Saved!';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var DiscountProvider
     */
    private $discountProvider;

    /**
     * @var BadgeConfig
     */
    private $badgeConfig;

    /**
     * @var array
     */
    private $priority;

    /**
     * @var BadgeSource
     */
    private $badgeSource;

    /**
     * Badges options
     *
     * @var array
     */
    private $badgeOptions;

    /**
     * ExtendLabels constructor.
     * @param Config $config
     * @param BadgeSource $badgeSource
     * @param BadgeConfig $badgeConfig
     * @param DiscountProvider $discountProvider
     */
    public function __construct(
        Config $config,
        BadgeSource $badgeSource,
        BadgeConfig $badgeConfig,
        DiscountProvider $discountProvider
    ) {
        $this->config = $config;
        $this->discountProvider = $discountProvider;
        $this->badgeConfig = $badgeConfig;
        $this->badgeSource = $badgeSource;
        $this->initConfig();
    }

    /**
     * Add price badge to badge labels
     *
     * @param Provider $provider
     * @param callable $proceed
     * @param Product $product
     * @return array
     */
    public function aroundGetBadgesLabels(Provider $provider, callable $proceed, Product $product): array
    {
        $discount = $this->discountProvider->getDiscount($product);

        if ($this->config->previousPriceEnabled() && $discount) {
            $badges = $product->getBadges();
            $badges[] = Config::PRICE_BADGE_OPTION_LABEL;
            usort($badges, [$this, 'sortBadgeLabels']);

            $labels = $this->formatBadgeLabels($badges, $discount);

            if (!$this->badgeConfig->isMultipleBadgesEnabled()) {
                $labels = [array_shift($labels)];
            }

            return $labels;
        }

        return $proceed($product);
    }

    /**
     * Format badge labels
     *
     * @param array $badges
     * @param float $discount
     * @return array
     */
    private function formatBadgeLabels(array $badges, float $discount): array
    {
        $labels = [];
        foreach ($badges as $badge) {
            $labels[$badge] = $this->badgeOptions[$badge];
        }

        $labelValue = Config::PRICE_BADGE_OPTION_LABEL;
        $argument = $discount . '%';

        try {
            $labels[$labelValue] = sprintf($labels[$labelValue], $argument);
        } catch (\Throwable $e) {
            $labels[$labelValue] = sprintf(self::DEFAULT_DISCOUNT_FORMAT, $argument);
        }

        return $labels;
    }

    /**
     * Initialize configuration
     *
     * @return $this
     */
    private function initConfig()
    {
        $this->priority = $this->badgeConfig->getBadgesPriority();
        $this->initBadgeOptions();

        return $this;
    }

    /**
     * Initialize badge options
     *
     * @return $this
     */
    private function initBadgeOptions()
    {
        $badgeOptions = $this->badgeSource->getAllOptions();

        foreach ($badgeOptions as $badgeOption) {
            $this->badgeOptions[$badgeOption['value']] = $badgeOption['label'];
        }

        $labelValue = Config::PRICE_BADGE_OPTION_LABEL;
        $this->badgeOptions[$labelValue] = $this->config->getPriceLabelFormat();

        return $this;
    }

    /**
     * Sort badge labels
     *
     * @param string $first
     * @param string $second
     * @return int
     */
    private function sortBadgeLabels(string $first, string $second): int
    {
        $firstPriority  = $this->priority[$first] ?? Provider::DEFAULT_SORT_PRIORITY;
        $secondPriority = $this->priority[$second] ?? Provider::DEFAULT_SORT_PRIORITY;

        if ($firstPriority == $secondPriority) {
            return 0;
        }

        return ($firstPriority > $secondPriority) ? 1 : -1;
    }
}

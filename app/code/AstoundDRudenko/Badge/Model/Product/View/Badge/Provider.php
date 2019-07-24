<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Model\Product\View\Badge;

use AstoundDRudenko\Badge\Helper\Data as Helper;
use AstoundDRudenko\Badge\Model\Attribute\Badge\Config;
use AstoundDRudenko\Badge\Model\Attribute\Badge\Source;
use Magento\Catalog\Model\Product;

/**
 * Model responsible for badge providing
 *
 * Class Provider
 * @package AstoundDRudenko\Badge\Model\Product\View\Badge
 */
class Provider
{
    /**
     * Default sort priority
     */
    public const DEFAULT_SORT_PRIORITY = 99999;

    /**
     * Sorted badges with priority
     * @var array
     */
    private $configPriority;

    /**
     * Badges options
     * @var array
     */
    private $badgeOptions;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Source
     */
    private $badgesSource;

    /**
     * @var array
     */
    private $badges = [];

    /**
     * @var Product
     */
    private $currentProduct;

    /**
     * @var bool
     */
    private $multipleBadges;

    /**
     * Badge constructor.
     * @param Source $badgesSource
     * @param Config $config
     */
    public function __construct(
        Source $badgesSource,
        Config $config
    ) {
        $this->config = $config;
        $this->badgesSource = $badgesSource;
        $this->initConfiguration();
    }

    /**
     * Is badges enabled
     * @return bool
     */
    public function isBadgesEnabled() :bool
    {
        return $this->config->isBadgesEnabled();
    }

    /**
     * Retrieves product badges
     * @param Product $product
     * @return array
     */
    public function getProductBadges(Product $product) :array
    {
        if (!isset($this->badges[$product->getId()])) {
            $this->setCurrentProduct($product);
            $badges = $product->getBadgeLabel();

            if (is_string($badges) && !empty($badges)) {
                $badges = explode(',', $badges);
            } elseif (null === $badges) {
                $badges = [];
            }

            $labels = $this->getBadgesLabels($badges);
            $this->badges[$product->getId()] = $labels;
            $this->unsetCurrentProduct();
        }

        return $this->badges[$product->getId()] ?? [];
    }

    /**
     * Initialize badge options
     * @return $this
     */
    public function initBadgeOptions()
    {
        $options = $this->badgesSource->getAllOptions();
        foreach ($options as $option) {
            $this->pushBadge($option);
        }

        return $this;
    }

    /**
     * Set current product
     * @param Product $product
     * @return $this
     */
    public function setCurrentProduct(Product $product)
    {
        $this->currentProduct = $product;

        return $this;
    }

    /**
     * Retrieves current product
     * @return Product|null
     */
    public function getCurrentProduct() : ?Product
    {
        return $this->currentProduct ?? null;
    }

    /**
     * Unset current product
     * @return $this
     */
    public function unsetCurrentProduct()
    {
        $this->currentProduct = null;

        return $this;
    }

    /**
     * Push new badge option
     * @param array $option
     * @return $this
     */
    public function pushBadge(array $option)
    {
        $this->badgeOptions[$option['value']] = $option['label'];

        return $this;
    }

    /**
     * Generate an array of badges in convenient format
     * @param array $badges
     * @return array
     */
    private function getBadgesLabels(array $badges) :array
    {
        $labels = [];

        usort($badges, [$this, 'sortBadgeLabels']);
        foreach ($badges as $badge) {
            $labels[$badge] = $this->badgeOptions[$badge];
        }

        if (!$this->multipleBadges) {
            $labels = [array_shift($labels)];
        }

        return $labels;
    }

    /**
     * Initialize config
     * @return $this
     */
    private function initConfiguration()
    {
        $this->configPriority = $this->config->getBadgesPriority();
        $this->initBadgeOptions();
        $this->multipleBadges = $this->config->isMultipleBadgesEnabled();

        return $this;
    }

    /**
     * Sort an array by priority
     * @param string $first
     * @param string $second
     * @return int
     */
    private function sortBadgeLabels(string $first, string $second) :int
    {
        $firstPriority  = $this->configPriority[$first] ?? self::DEFAULT_SORT_PRIORITY;
        $secondPriority = $this->configPriority[$second] ?? self::DEFAULT_SORT_PRIORITY;

        if ($firstPriority == $secondPriority) {
            return 0;
        }

        return ($firstPriority > $secondPriority) ? 1 : -1;
    }
}

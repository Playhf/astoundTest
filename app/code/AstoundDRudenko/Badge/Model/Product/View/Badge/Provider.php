<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Model\Product\View\Badge;

use AstoundDRudenko\Badge\Helper\Data as Helper;
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
     * @var Helper
     */
    private $helper;

    /**
     * @var Source
     */
    private $badgesSource;

    /**
     * @var array
     */
    private $badges = [];

    /**
     * @var bool
     */
    private $multipleBadges;

    /**
     * Badge constructor.
     * @param Source $badgesSource
     * @param Helper $helper
     */
    public function __construct(
        Source $badgesSource,
        Helper $helper
    ) {
        $this->helper = $helper;
        $this->badgesSource = $badgesSource;
        $this->initConfiguration();
    }

    /**
     * Is badges enabled
     * @return bool
     */
    public function isBadgesEnabled()
    {
        return (bool)$this->helper->isBadgesEnabled();
    }

    /**
     * Retrieves product badges
     * @param Product $product
     * @return array|null
     */
    public function getProductBadges(Product $product)
    {
        if (!isset($this->badges[$product->getId()])) {
            $badges = $product->getBadgeLabel();

            if (is_string($badges) && !empty($badges)) {
                $badges = explode(',', $badges);
            } elseif (null === $badges) {
                $badges = [];
            }

            $labels = $this->getBadgesLabels($badges);
            $this->badges[$product->getId()] = $labels;
        }

        return $this->badges[$product->getId()] ?? null;
    }

    /**
     * Generate an array of badges in convenient format
     * @param array $badges
     * @return array
     */
    private function getBadgesLabels(array $badges)
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
     * @return void
     */
    private function initConfiguration()
    {
        $this->configPriority = $this->helper->getBadgesPriority();

        $this->initBadgeOptions();

        $this->multipleBadges = (bool)$this->helper->isMultipleBadgesEnabled();
    }

    /**
     * Initialize badge options
     */
    private function initBadgeOptions()
    {
        $options = $this->badgesSource->getAllOptions();
        foreach ($options as $option) {
            $this->badgeOptions[$option['value']] = $option['label'];
        }
    }

    /**
     * Sort an array by priority
     * @param $first
     * @param $second
     * @return int
     */
    private function sortBadgeLabels($first, $second)
    {
        $firstPriority  = $this->configPriority[$first] ?? self::DEFAULT_SORT_PRIORITY;
        $secondPriority = $this->configPriority[$second] ?? self::DEFAULT_SORT_PRIORITY;

        if ($firstPriority == $secondPriority) {
            return 0;
        }

        return ($firstPriority > $secondPriority) ? 1 : -1;
    }
}

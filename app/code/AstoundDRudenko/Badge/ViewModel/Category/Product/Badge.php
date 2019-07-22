<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\ViewModel\Category\Product;

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
     * Badge constructor.
     * @param Provider $badgeProvider
     */
    public function __construct(Provider $badgeProvider)
    {
        $this->badgeProvider = $badgeProvider;
    }

    /**
     * @param Product $product
     * @return array|null
     */
    public function getProductBadges(Product $product)
    {
        return $this->badgeProvider->getProductBadges($product);
    }
}

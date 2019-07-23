<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\ViewModel\Product;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;
use AstoundDRudenko\PriceBadge\Pricing\Price\Simple\PreviousPrice as PreviousPriceModel;
use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;

/**
 * Previous price product list view model
 * @package AstoundDRudenko\PriceBadge\ViewModel\Product
 */
class PreviousPrice implements ArgumentInterface
{
    /**
     * @var Render
     */
    private $priceRenderer;

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Render previous price html
     * @param Product $product
     * @param Template $block
     * @return string
     */
    public function getPreviousPriceHtml(Product $product, Template $block) :string
    {
        if (null === $this->priceRenderer) {
            try {
                /** @var Render $priceRenderer */
                $this->priceRenderer = $block->getLayout()->getBlock('product.price.render.default')
                    ->setData('is_product_list', true);
            } catch (LocalizedException $e) {
                $this->priceRenderer = null;
            }
        }

        $price = '';
        if ($this->priceRenderer) {
            $price = $this->priceRenderer->render(
                PreviousPriceModel::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }

    /**
     * Is previous price output enabled
     * @return bool
     */
    public function previousPriceEnabled() :bool
    {
        return $this->config->previousPriceEnabled();
    }
}

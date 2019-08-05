<?php

declare(strict_types=1);
namespace AstoundDRudenko\Badge\Block\Product\View\Info;

use AstoundDRudenko\Badge\Model\Attribute\Badge\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Json\EncoderInterface as JsonEncoder;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Url\EncoderInterface;
use AstoundDRudenko\Badge\Model\Product\View\Badge\Provider;

/**
 * Class of block which responsible for badges rendering on pdp
 * Class Badges
 * @package AstoundDRudenko\Badge\Block\Product\View\Info
 */
class Badges extends View
{
    /**
     * @var Provider
     */
    private $badgesProvider;

    /**
     * @var Config
     */
    private $badgeConfig;

    /**
     * Badges constructor.
     * @param Config $badgeConfig
     * @param Provider $badgesProvider
     * @param Context $context
     * @param EncoderInterface $urlEncoder
     * @param JsonEncoder $jsonEncoder
     * @param StringUtils $string
     * @param Product $productHelper
     * @param ConfigInterface $productTypeConfig
     * @param FormatInterface $localeFormat
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Config $badgeConfig,
        Provider $badgesProvider,
        Context $context,
        EncoderInterface $urlEncoder,
        JsonEncoder $jsonEncoder,
        StringUtils $string,
        Product $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
        $this->badgesProvider = $badgesProvider;
        $this->badgeConfig = $badgeConfig;
    }

    /**
     * Retrieves product badges
     * @return array
     */
    public function getProductBadges() :array
    {
        return $this->badgesProvider->getProductBadges($this->getProduct());
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

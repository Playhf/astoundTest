<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Plugin\Product\Configurable\Price\Options;

use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;
use Magento\Catalog\Block\Product\View;
use Magento\Framework\Locale\Format;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Add previous price option amount
 *
 * Class AddConfigPricesPdp
 * @package AstoundDRudenko\PriceBadge\Plugin\Product\Configurable\Price\Options
 */
class AddConfigPricesPdp
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Format
     */
    private $localeFormat;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * AssignPreviousPriceOption constructor.
     * @param SerializerInterface $serializer
     * @param Format $localeFormat
     * @param Config $config
     */
    public function __construct(
        SerializerInterface $serializer,
        Format $localeFormat,
        Config $config
    ) {
        $this->config = $config;
        $this->localeFormat = $localeFormat;
        $this->serializer = $serializer;
    }

    /**
     * Add previous price option amount
     *
     * @param View $subject
     * @param string $jsonConfig
     * @return string
     */
    public function afterGetJsonConfig(View $subject, string $jsonConfig): string
    {
        if ($this->config->previousPriceEnabled()) {
            $config = $this->serializer->unserialize($jsonConfig);
            $product = $subject->getProduct();
            $previousPrice = $product->getPriceInfo()->getPrice(Config::PREVIOUS_PRICE_ATTRIBUTE_CODE);

            $config['prices']['previousPrice'] = [
                'amount' => $previousPrice->getAmount()->getValue(),
                'adjustments' => []
            ];

            $jsonConfig = $this->serializer->serialize($config);
        }

        return $jsonConfig;
    }
}

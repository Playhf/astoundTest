<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Plugin\Product\Configurable;

use AstoundDRudenko\Badge\Model\Product\View\Badge\Provider;
use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;
use AstoundDRudenko\PriceBadge\Model\PreviousPrice\Discount\Calculator;
use JsonSchema\Exception\InvalidArgumentException;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Locale\Format;

/**
 * Plugin responsible for adding previous prices
 *
 * Class FillPreviousPrices
 * @package AstoundDRudenko\PriceBadge\Plugin\Product\Configurable
 */
class FillPreviousPrices
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Format
     */
    private $localeFormat;

    /**
     * @var Provider
     */
    private $badgeProvider;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * FillPreviousPrices constructor.
     * @param Calculator $calculator
     * @param Provider $badgeProvider
     * @param Format $localeFormat
     * @param SerializerInterface $serializer
     * @param Config $config
     */
    public function __construct(
        Calculator $calculator,
        Provider $badgeProvider,
        Format $localeFormat,
        SerializerInterface $serializer,
        Config $config
    ) {
        $this->config = $config;
        $this->serializer = $serializer;
        $this->localeFormat = $localeFormat;
        $this->badgeProvider = $badgeProvider;
        $this->calculator = $calculator;
    }

    /**
     * Add previous prices
     *
     * @param Configurable $subject
     * @param string $jsonConfig
     * @return string
     */
    public function afterGetJsonConfig(Configurable $subject, string $jsonConfig): string
    {
        if ($this->config->previousPriceEnabled()) {
            $defaultConfig = $jsonConfig;
            $config = $this->serializer->unserialize($jsonConfig);

            $pricesData = $this->getPreviousPricesData($subject);
            $previousPrices = $pricesData['previous_prices'];

            foreach ($config['optionPrices'] as $key => &$optionPrice) {
                if (isset($previousPrices[$key])) {
                    $optionPrice['previousPrice'] = [
                        'amount' => $previousPrices[$key]
                    ];
                }
            }

            $config['optionPriceBadges'] = $pricesData['price_badges'];

            try {
                $jsonConfig = $this->serializer->serialize($config);
            } catch (InvalidArgumentException $e) {
                $jsonConfig = $defaultConfig;
            }
        }

        return $jsonConfig;
    }

    /**
     * Retrieve previous prices and badges
     *
     * @param Configurable $configurableBlock
     * @return array
     */
    private function getPreviousPricesData(Configurable $configurableBlock): array
    {
        $previousPrices = [];
        $priceBadges = [];

        foreach ($configurableBlock->getAllowProducts() as $product) {
            $priceInfo = $product->getPriceInfo();
            $previousPriceModel = $priceInfo->getPrice(Config::PREVIOUS_PRICE_ATTRIBUTE_CODE);
            $amount = $previousPriceModel->getAmount()->getValue();

            $previousPrices[$product->getId()] = $this->localeFormat->getNumber($amount);

            $this->calculator->calculate($product, $amount);
            $badges = $this->badgeProvider->getProductBadges($product);

            $priceBadges[$product->getId()] = [
                'label' => $badges['Price'] ?? ''
            ];
        }

        return [
            'previous_prices' => $previousPrices,
            'price_badges' => $priceBadges
        ];
    }
}

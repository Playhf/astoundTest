<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Plugin\Product\Configurable;

use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;
use JsonSchema\Exception\InvalidArgumentException;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;
use Magento\Framework\Serialize\SerializerInterface;
use \Magento\Framework\Locale\Format;

/**
 * Plugin responsible for adding previous prices
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
     * FillPreviousPrices constructor.
     * @param Format $localeFormat
     * @param SerializerInterface $serializer
     * @param Config $config
     */
    public function __construct(
        Format $localeFormat,
        SerializerInterface $serializer,
        Config $config
    ) {
        $this->config = $config;
        $this->serializer = $serializer;
        $this->localeFormat = $localeFormat;
    }

    /**
     * Add previous prices
     * @param Configurable $subject
     * @param string $jsonConfig
     * @return string
     */
    public function afterGetJsonConfig(Configurable $subject, string $jsonConfig) :string
    {
        if ($this->config->previousPriceEnabled()) {

            $defaultConfig = $jsonConfig;
            $config = $this->serializer->unserialize($jsonConfig);
            $previousPrices = $this->getPreviousPrices($subject);

            foreach ($config['optionPrices'] as $key => &$optionPrice) {
                if ($previousPrices[$key]) {
                    $optionPrice[$key] = $previousPrices[$key];
                }
            }

            try {
                $jsonConfig = $this->serializer->serialize($config);
            } catch (InvalidArgumentException $e) {
                $jsonConfig = $defaultConfig;
            }
        }

        return $jsonConfig;
    }

    /**
     * Retrieve previous prices
     * @param Configurable $configurableBlock
     * @return array
     */
    private function getPreviousPrices(Configurable $configurableBlock) :array
    {
        $prices = [];

        foreach ($configurableBlock->getAllowProducts() as $product) {
            $priceInfo = $product->getPriceInfo();
            $previousPriceModel = $priceInfo->getPrice(Config::PREVIOUS_PRICE_ATTRIBUTE_CODE);

            $prices[$product->getId()] = [
                'previousPrice' => [
                    'amount' => $this->localeFormat->getNumber(
                        $previousPriceModel->getAmount()->getValue()
                    )
                ]
            ];
        }

        return $prices;
    }
}

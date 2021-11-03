<?php

declare(strict_types=1);
namespace AstoundDRudenko\PriceBadge\Plugin\Product\Configurable\Price\Options;

use AstoundDRudenko\PriceBadge\Model\Attribute\PreviousPrice\Config;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Variations\Prices;
use Magento\Framework\Locale\Format;
use Magento\Framework\Pricing\PriceInfo\Base;

/**
 * Plugin responsible for adding a previous price value
 *
 * Class AssignPreviousPriceOption
 * @package AstoundDRudenko\PriceBadge\Plugin\Product\Configurable\Price\Options
 */
class AssignPreviousPriceOption
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
     * AssignPreviousPriceOption constructor.
     * @param Format $localeFormat
     * @param Config $config
     */
    public function __construct(
        Format $localeFormat,
        Config $config
    ) {
        $this->config = $config;
        $this->localeFormat = $localeFormat;
    }

    /**
     * Add previous price option amount
     *
     * @param Prices $subject
     * @param array $prices
     * @param Base $beforeArgument
     * @return array
     */
    public function afterGetFormattedPrices(Prices $subject, array $prices, Base $beforeArgument): array
    {
        if ($this->config->previousPriceEnabled()) {
            $priceInfo = $beforeArgument;
            $previousPrice = $priceInfo->getPrice(Config::PREVIOUS_PRICE_ATTRIBUTE_CODE);

            $prices['previousPrice'] = [
                'amount' => $this->localeFormat->getNumber($previousPrice->getValue())
            ];
        }

        return $prices;
    }
}

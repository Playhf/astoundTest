define([
    'jquery',
    'mage/template',
    'Magento_Catalog/js/price-utils'
], function ($, mageTemplate, utils) {
    'use strict';

    return function (widget) {
        $.widget('mage.priceBox', widget, {
            /**
             * Hide previous price element if we don't have this one.
             */
            reloadPrice: function reDrawPrices() {
                var priceFormat = (this.options.priceConfig && this.options.priceConfig.priceFormat) || {},
                    priceTemplate = mageTemplate(this.options.priceTemplate);

                _.each(this.cache.displayPrices, function (price, priceCode) {
                    price.final = _.reduce(price.adjustments, function (memo, amount) {
                        return memo + amount;
                    }, price.amount);

                    price.formatted = utils.formatPrice(price.final, priceFormat);

                    var template = priceTemplate({
                        data: price
                    });

                    if (priceCode === 'previousPrice') {
                        if (price.final === 0) {
                            $('.price-previous', this.element).hide();
                        } else {
                            $('.price-previous', this.element).show();
                            $('[data-price-type="' + priceCode + '"]', this.element).html(template);
                        }
                    } else {
                        $('[data-price-type="' + priceCode + '"]', this.element).html(template);
                    }

                }, this);
            }
        });
    }
});

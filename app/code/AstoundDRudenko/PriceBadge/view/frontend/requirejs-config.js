var config = {
    config: {
        mixins: {
            'Magento_Catalog/js/price-box': {
                'AstoundDRudenko_PriceBadge/js/product/mixin/hide-previous-price': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'AstoundDRudenko_PriceBadge/js/product/mixin/price-badges': true
            }
        }
    }
};

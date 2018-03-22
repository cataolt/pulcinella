define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'TemplateMonster_OfflinePaymentMethods/payment/invoice30'
            },
            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.invoice30.mailingAddress;
            },
            /** Returns payable to info */
            /*getPayableTo: function() {
             return window.checkoutConfig.payment.checkmo.payableTo;
             }*/
        });
    }
);
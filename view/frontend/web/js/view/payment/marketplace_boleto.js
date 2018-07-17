define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'marketplace_boleto',
                component: 'Resultate_Skyhub/js/view/payment/method-renderer/marketplace_boleto-method'
            }
        );
        return Component.extend({});
    }
);
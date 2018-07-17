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
                type: 'marketplace_cc',
                component: 'Resultate_Skyhub/js/view/payment/method-renderer/marketplace_cc-method'
            }
        );
        return Component.extend({});
    }
);
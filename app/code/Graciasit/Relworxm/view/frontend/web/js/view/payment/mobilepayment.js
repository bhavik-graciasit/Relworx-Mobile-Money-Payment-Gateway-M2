/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList )
    {
        'use strict';
        rendererList.push(
            {
                type: 'mobilepayment',
                component: 'Graciasit_Relworxm/js/view/payment/method-renderer/mobilepayment-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
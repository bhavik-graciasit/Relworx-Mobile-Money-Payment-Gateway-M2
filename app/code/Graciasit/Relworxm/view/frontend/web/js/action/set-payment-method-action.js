/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function ($, quote, urlBuilder, url, storage, errorProcessor, customer, fullScreenLoader) {
        'use strict';
        return function (messageContainer) {
            jQuery(function ($) {
                $.ajax({
                    url: url.build('relworxm/checkout/start'),
                    type: 'get',
                    dataType: 'json',
                    cache: false,
                    processData: false, // Don't process the files
                    contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                    success: function (data) {
                        if(data['paymentUrl'] == 'Success')
                        {
                            $.mage.redirect(url.build('relworxm/checkout/prosuccess'));
                        }
                        else if(data['paymentUrl'] == 'Failure')
                        {
                            $.mage.redirect(url.build('relworxm/checkout/profailure'));
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            });
        };
    }
);
define([
    'uiComponent',
    'jquery',
    'Magento_Ui/js/modal/modal'
], function (Component, $, modal) {
    'use strict';

    window.addEventListener('message', function (message) {
        // Only messages from the Econt form in the checkout modal (its window and the origin of its URL)
        var frame = document.querySelector('#place_iframe_here iframe');
        if (!frame || message.source !== frame.contentWindow || message.origin !== new URL(frame.src, window.location.href).origin) {
            return;
        }
        
        var data = message.data
        var modal = $('#econt-iframe-modal')
        var proceed = false;
        var content;
        
        // With shipment_error_cod Econt does not allow cash on delivery for this order: no price with it, no surcharge
        econtModalHelper.shipping_price_cod = data['shipping_price_cod'] != null
            ? Math.round((data['shipping_price_cod'] - data['shipping_price']) * 100) / 100
            : 0;
        econtModalHelper.shipping_data = data

        if ( data.shipment_error ) {
            content = data.shipment_error
        } else if ( data.shipment_error_cod ) {
            econtModalHelper.updateShippingAddress(data);
            proceed = true
            content = $.mage.__('Delivery cost') + ': '
                + data['shipping_price'] + ' ' + data['shipping_price_currency_sign'] + '. '
                + $.mage.__('Cash on delivery is not possible for this order: %1 Please choose another payment method.').replace('%1', data.shipment_error_cod)
        } else {
            econtModalHelper.updateShippingAddress(data);
            proceed = true
            content = $.mage.__('Delivery cost') + ': '
                + data['shipping_price'] + ' ' + data['shipping_price_currency_sign']
                + " ( + " + econtModalHelper.shipping_price_cod + ' ' + data['shipping_price_currency_sign']
                + ' ' + $.mage.__('cash on delivery') + ').'
        }

        modal.modal('toggleModal')
        
        econtModalHelper.showAlert(content, data, proceed, modal)
    });

    return Component.extend({
        defaults: {
            template: {
                name: 'Oxl_Delivery/shipping/additional-block',
                afterRender: function () {
                    const button = document.getElementById('econtGetModalData');

                    if (button) {
                        button.addEventListener('click', function () {
                            econtModalHelper.loadModal();
                        });
                    }
                }
            }
        }
    });
});
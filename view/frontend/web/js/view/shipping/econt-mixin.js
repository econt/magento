define([
  'jquery',
  'underscore',
  'Magento_Ui/js/form/form',
  'ko',
  'Magento_Customer/js/model/customer',
  'Magento_Customer/js/model/address-list',
  'Magento_Checkout/js/model/address-converter',
  'Magento_Checkout/js/model/quote',
  'Magento_Checkout/js/action/create-shipping-address',
  'Magento_Checkout/js/action/select-shipping-address',
  'Magento_Checkout/js/model/shipping-rates-validator',
  'Magento_Checkout/js/model/shipping-address/form-popup-state',
  'Magento_Checkout/js/model/shipping-service',
  'Magento_Checkout/js/action/select-shipping-method',
  'Magento_Checkout/js/model/shipping-rate-registry',
  'Magento_Checkout/js/action/set-shipping-information',
  'Magento_Checkout/js/model/step-navigator',
  'Magento_Ui/js/modal/modal',
  'Magento_Ui/js/modal/alert',
  'Magento_Checkout/js/model/checkout-data-resolver',
  'Magento_Checkout/js/checkout-data',
  'uiRegistry',
  'mage/translate',
  'Magento_Checkout/js/model/shipping-rate-service'
], function (
    $,
    _,
    Component,
    ko,
    customer,
    addressList,
    addressConverter,
    quote,
    createShippingAddress,
    selectShippingAddress,
    shippingRatesValidator,
    formPopUpState,
    shippingService,
    selectShippingMethodAction,
    rateRegistry,
    setShippingInformationAction,
    stepNavigator,
    modal,
    alert,
    checkoutDataResolver,
    checkoutData,
    registry,
    $t
) {
  
    'use strict';
    var mixin = {

      /**
       * Set shipping information handler
       */
        setShippingInformation: function () {
            if (this.validateShippingInformation()) {
                quote.billingAddress(null);
                checkoutDataResolver.resolveBillingAddress();
                setShippingInformationAction().done(
                    function () {
                        if ( quote.shippingMethod().carrier_code != "econtdelivery" ) {
                            stepNavigator.next();
                        } else {
                            if (mixin.checkShippingPrice(quote.getTotals()())) {
                                stepNavigator.next();
                            } else {
                                alert({
                                    title: $.mage.__('Delivery with Econt'),
                                    content: $.mage.__("You need to calculate the delivery price! Please use the 'Calculate Price' button."),
                                });
                            }
                        }
                    }
                );
            }
        },
        checkShippingPrice: function (obj) {
            if (obj.base_shipping_incl_tax === 0) {
                return false;
            }

            if (obj.base_shipping_amount === 0) {
                return false;
            }
      
            return true;
        },
        myClickEvent: function ( data, event ) {
            console.log(event, data);
            return
        }
    };
  
    return function (target) {
        return target.extend(mixin);
    }
});
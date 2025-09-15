var config = {
    config: {
        mixins: {
           'Magento_Checkout/js/view/shipping': {
               'Oxl_Delivery/js/view/shipping/econt-mixin': true
           },
        //    'Magento_Checkout/js/view/payment': {
        //        'Vendor_Module/js/view/shipping-payment-mixin': true
        //    }
       },       
    },
    map: {
        "*": {
            econtjs: "Oxl_Delivery/js/view/cms-block"
        }
    }
}

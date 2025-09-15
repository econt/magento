define([
    'jquery',
    'underscore',
    'mage/storage',
    'mage/template',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/sidebar',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/action/set-shipping-information'
    ], function (
        $,
        _,
        storage,
        template,
        alert,
        modal,
        quote,
        customerData,
        sidebarModel,
        stepNavigator,
        errorProcessor,
        shippingService,
        resourceUrlManager,
        rateRegistry,
        setShippingInformationAction,
    ) {
        'use strict'
        var baseUrl;
        var econtModalHelper;
        econtModalHelper = {
            shipping_data: {},
            shipping_price_cod: null,
            main: function (config, element) {                                
                var url = config.AjaxUrl;
                baseUrl = url;                

                quote.shippingMethod.subscribe( function (val) {                    
                    econtModalHelper.toggleCalculateshippingButton(val);
                    // if( ! stepNavigator.isProcessed( 'shipping' ) && val['carrier_code'] === "econtdelivery" && shipping_price_cod === null ) {
                    //     stepNavigator.navigateTo('shipping', 'opc-shipping_method');
                    //     sidebarModel.hide();
                    // }
                })
                quote.paymentMethod.subscribe( function (method) {                    
                    econtModalHelper.blah(method);
                })
                           
            },
            toggleCalculateshippingButton: function (object) {                
                setTimeout(function() {
                    if (object.carrier_code != 'econtdelivery') {
                        $('#block-custom').hide()
                    } else if (object.carrier_code === 'econtdelivery') {
                        $('#block-custom').show()
                    }
                }, 800)
            },
            loadModal : function() {
                if (quote.shippingMethod().carrier_code != 'econtdelivery') return;
                var data;
                var footer;
                let cdata
                cdata = customerData.get('checkout-data') ();                
                
                if ( !this.checkCustomerData(cdata) ) {                        
                    this.showAlert($.mage.__('Моля попълнете всички задължителни полета!'));
                    return
                }

                data = {
                    'type': "popup",
                    'title': $.mage.__('Доставка с Еконт'),
                    'responsive': true,
                    'showLoader': true,
                    // 'buttons': [{
                    //     text: jQuery.mage.__('Submit'),
                    //     class: 'action'
                    // }],
                    opened: this.prepareIframe(baseUrl, cdata)
                }
                
                $('#econt-iframe-modal').modal(data);
                $('#econt-iframe-modal').modal('openModal');
                footer = $('.modal-footer')        
                footer.css('display', 'none');   
            },
            prepareIframe: function ( url, cdata ) {            
                var iframe;
                var orderParams = {};
                var items = quote.getItems();                

                orderParams.order_total = checkoutConfig.totalsData.subtotal_with_discount
                orderParams.order_currency = checkoutConfig.totalsData.quote_currency_code
                orderParams.customer_name = cdata.shippingAddressFromData.firstname + ' ' + cdata.shippingAddressFromData.lastname
                orderParams.customer_company = cdata.shippingAddressFromData.company
                orderParams.customer_address = ''   
                orderParams.order_weight = 0            
                orderParams.customer_city_name = cdata.shippingAddressFromData.city
                orderParams.customer_post_code = cdata.shippingAddressFromData.postcode
                orderParams.customer_phone = cdata.shippingAddressFromData.telephone
                orderParams.ignore_history = 1
                orderParams.customer_email = cdata.validatedEmailValue

                _.forEach( items, function(item) {
                    orderParams.order_weight += item.weight
                } )
    
                _.forEach( cdata.shippingAddressFromData.street, function(str, index) {
                    if ( index > 0 && str.length > 0 && index <= (_.size(cdata.shippingAddressFromData.street) - 1) ) {
                        orderParams.customer_address += ', ';
                    }
                    orderParams.customer_address += str;
                })
    
                _.forEach( items, (item, index) => {
                    orderParams.order_weight += Number(item.weight)                    
                } )
                
                $.ajax({
                    // showLoader: true,
                    url: url + 'rest/V1/econt/delivery/get-iframe-data',
                    // data: param,
                    type: "GET",
                    dataType: 'json'
                }).done(function (data) {
                    $( '#place_iframe_here' ).empty(); 
                    orderParams.id_shop = data.econt_shop_id                     
                    iframe = '<iframe src="' + data.econt_customer_info_url + jQuery.param(orderParams) + '" scrolling="yes" id="delivery_with_econt_iframe"></iframe>'                    
                    // append the generated iframe in the div
                    $( '#place_iframe_here' ).append(iframe);   
                });           
            },            
            storeSessionPriceData: function ( data ) {
                storage.post(
                    baseUrl + 'rest/V1/econt/delivery/set-payment-data',
                    JSON.stringify({
                        econt_id: data.id,
                        shipping_price: data.shipping_price,
                        shipping_price_cod: data.shipping_price_cod
                    }),
                    false
                ).done(function (result) {
   
                }).fail(function (response) {
                    console.log( "It's Fucked - " + response );
                })
            },    
            updateShippingAddress: function ( data ) {
                var full_name = [];
                var company = '';
                var updateBilling;

                updateBilling = quote.billingAddress() === null ? false : true;

                if ( data['face'] != null ) {
                    full_name = data['face'].split( ' ' );
                    company = data['name'];
                } else {
                    full_name = data['name'].split( ' ' );
                }
    
                if ( quote.shippingAddress().firstname != full_name[0] ) {
                    quote.shippingAddress().firstname = full_name[0];
                    if (updateBilling)
                        quote.billingAddress().firstname = full_name[0];
                }
    
                if ( quote.shippingAddress().lastname != full_name[1] ) {
                    quote.shippingAddress().lastname = full_name[1];  
                    if (updateBilling)
                        quote.billingAddress().lastname = full_name[1];    
                }
    
                if ( quote.shippingAddress().company != company ) {
                    quote.shippingAddress().company = company;    
                    if (updateBilling)
                        quote.billingAddress().company = company;    
                }
    
                quote.shippingAddress().street[0] = data['address'] != '' ? data['address'] : data['office_name'];
                if (updateBilling)
                    quote.billingAddress().street[0] = data['address'] != '' ? data['address'] : data['office_name'];
    
                if ( quote.shippingAddress().telephone != data['phone'] ) {
                    quote.shippingAddress().telephone = data['phone'];
                    if (updateBilling)
                        quote.billingAddress().telephone = data['phone'];
                }
                
                if ( quote.shippingAddress().postcode != data['post_code'] ) {
                    quote.shippingAddress().postcode = data['post_code'];
                    if (updateBilling)
                        quote.billingAddress().postcode = data['post_code'];
                }
    
                if ( quote.shippingAddress().city != data['city_name'] ){
                    quote.shippingAddress().city = data['city_name'];
                    if (updateBilling)
                        quote.billingAddress().city = data['city_name'];
                }                
    
                if ( quote.guestEmail != data['email'] ) {
                    quote.guestEmail = data['email'];
                }
            },    
            updateShippingPrice: function ( data ) {
                var address = quote.shippingAddress();
                var _that = this;
                shippingService.isLoading(true);
                storage.post(
                    resourceUrlManager.getUrlForEstimationShippingMethodsForNewAddress(quote),
                    JSON.stringify({
                        address: address
                    }),
                    false
                ).done(function (result) {
                    var r;
                    r = _.each(result, function(res) {
                        if(res.carrier_code === 'econtdelivery') {
                            res.amount = data['shipping_price_cod'];
                            res.base_amount = data['shipping_price_cod'];
                            res.price_excl_tax = data['shipping_price_cod'];
                            res.price_incl_tax = data['shipping_price_cod'];
                        }
    
                        return res;
                    })
                    
                    rateRegistry.set(address.getKey(), r);
                    shippingService.setShippingRates(r);
                    _that.updateQuoteShippingTotals( data['shipping_price_cod'] );
                }).fail(function (response) {
                    shippingService.setShippingRates([]);
                    errorProcessor.process(response);
                }).always(function () {
                    shippingService.isLoading(false);
                    
                    // if ( ! stepNavigator.isProcessed( 'shipping' ) ) {
                    //     console.log('hims');
                        
                    //     // stepNavigator.next();
                    //     // setShippingInformationAction().done(
                    //     //     function () {                                      
                    //     //     }
                    //     // );
                    // } else {        
                                    
                    //     var chkd = $( 'input[type="radio"][name="payment[method]"]:checked' )                                        
                    //     console.log('hams');
                    //     if ( chkd.length && chkd[0].value === 'cashondelivery' ) {
                    //         
                    //     }
    
                    //     $('input[type="radio"][name="payment[method]"]').on('change', _that.blah )
                    // }
                });
            },
            blah: function (data) {    
                var totals = quote.getTotals() ();
                if (Object.keys(this.shipping_data).length === 0) return;
                if ( quote.shippingMethod() && quote.shippingMethod().carrier_code != "econtdelivery" ) return;

                if( this.shipping_price_cod === null ) {
                    setTimeout(function(){
                        stepNavigator.navigateTo('shipping', 'opc-shipping_method');
                        sidebarModel.hide();
                    }, 1000)
                }

                if ( data.method === 'cashondelivery' ) {                    
                    if ( totals.base_shipping_incl_tax < this.shipping_data.shipping_price_cod )
                        this.updateQuoteShippingTotals( this.shipping_price_cod, true );
                }
                else {                    
                    if ( totals.base_shipping_incl_tax > this.shipping_data.shipping_price  ){
                        this.updateQuoteShippingTotals( this.shipping_price_cod, false, true );
                    }
                }
            },    
            updateQuoteShippingTotals: function ( data, add_cod = false, sub_cod = false ) {                      
                var t = quote.getTotals() ();
                var shipping_fields = [
                    'base_shipping_amount',
                    'base_shipping_incl_tax',
                    'shipping_amount',
                    'shipping_incl_tax',
                ];
                var subtotal_fields = [
                    'subtotal_with_discount'
                ];                
    
                _.each( shipping_fields, function( field ) {
                    if ( add_cod ) {
                        t[field] += data;
                    } else if ( sub_cod ) {
                        t[field] -= data;
                    } else {
                        t[field] = data;
                    }
                });
    
                _.each( subtotal_fields, function( field ) {                
                    if ( add_cod )
                        t[field] += data;
                    else if ( sub_cod )
                        t[field] -= data;
                    else 
                        t[field] = t.subtotal + data;
                } );
                
                _.each( t.total_segments, function( segment ) {
                    if( segment.code === 'shipping' ) {
                        if ( add_cod ) 
                            segment.value += data;
                        else if ( sub_cod )
                            segment.value -= data;
                        else
                            segment.value = data;
    
                        if ( segment.title.indexOf( '(Deliver With Econt - Econt Shipping)' )  === -1 ) {
                            segment.title += ' (Deliver With Econt - Econt Shipping)'
                        }
                    // } else if ( segment.code === 'subtotal' ) {
                    //     if ( add_cod ) 
                    //         segment.value += data;
                    //     else if ( sub_cod )
                    //         segment.value -= data;
                    //     else
                    //         segment.value = t.subtotal + data;
                    } else if ( segment.code === 'grand_total' ) {
                        if ( add_cod ) 
                            segment.value += data;
                        else if ( sub_cod )
                            segment.value -= data;
                        else
                            segment.value = t.grand_total + data;
                    }
                    
                    return segment;
                } );

                quote.setTotals( t );
            },
            showAlert: function (content, data = null, proceed = false, modal = null) {
                var _that = this;
                alert({
                    title: $.mage.__('Доставка с Еконт'),
                    content: content,
                    actions: {
                        always: function(){
                            if ( proceed && data ) {
                                _that.updateShippingPrice( data );
                                $( '#place_iframe_here' ).empty();
                                _that.storeSessionPriceData( data );                
                            }
                            else {
                                if (modal)
                                    modal.modal('toggleModal');
                            }
                        }
                    }
                });
            },
            checkCustomerData: function (data) {
                var succss = false;
                _.forEach(data.shippingAddressFromData, function (value, key) {                              
                    if (key != 'region' && value != "") {
                        succss = true;
                    } else if (key != 'company' && value != "") {
                        succss = true;
                    } else if (key === 'region' || key === 'company') {
                        succss = true;
                    } else {                        
                        succss = false;
                    }
                })

                return succss;
            }
        }

        if ( quote.shippingMethod() && quote.shippingMethod().carrier_code != "econtdelivery" ) return;                  

        window.econtModalHelper = econtModalHelper;

        return econtModalHelper.main
});
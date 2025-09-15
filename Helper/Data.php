<?php

namespace Oxl\Delivery\Helper;

use Oxl\Delivery\Model\OxlDelivery;
use \Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function __construct ( 
        Context $context
    )
    {
        parent::__construct($context);    
    }

    /**
     * Get the module config data
     * 
     * @return string
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if we using Demo service
     * 
     * @return bool
     */
    public function is_demo()
    {
        $options = $this->getConfig('carriers/econtdelivery/demo_service');

        return boolval($options);
    }

    /**
     * Based on the demo setting returns the appropiate url
     * 
     * @return string URL
     */
    public function get_service_url()
    {
        $url = '';
        
        if ( $this->is_demo() ) {
            $url = OxlDelivery::DEMO_URL;
        } else {
            $url = OxlDelivery::REAL_URL;
        }
        // return ( is_ssl() ? 'https:' : 'http:' ) . $url;
        return $url;
    }

    /**
     * Retrieve the stored in database setting
     * 
     * @param bool $encrypt Encrypt the string or not
     * 
     * @return string
     */
    public function get_private_key( $encrypt = false )
    {
        $key = $this->getConfig('carriers/econtdelivery/key');
        
        return $encrypt ? base64_encode( $key ) : $key;
    }

    /**
     * The tracking url
     * 
     * @return string
     */
    public function get_tracking_url( $code )
    {
        return Delivery_With_Econt_Options::get_track_url() . $code;
    }

    /**
     * check stored configuration
     *
     * Check stored shop_id, private_key and demo_service options with Econt via curl request
     *
     * @param array $new_settings The settings entered by the user
     * @return array 
     **/
    public function check_econt_configuration( $new_settings = array(), $order_number = '4812384' )
    {
        $endpoint = $this->get_service_url( array_key_exists( 'demo_service', $new_settings ) );
        $secret = $new_settings['private_key'];

        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $endpoint . "services/OrdersService.getTrace.json" );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt( $curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: " . $secret
        ] );
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( array(
            'orderNumber' => $order_number
        ) ) );
        curl_setopt( $curl, CURLOPT_TIMEOUT, 6 );
        $res = curl_exec( $curl );
        $response = json_decode( $res, true );

        curl_close( $curl );

        // if( is_array( $response ) && array_key_exists('type', $response) && $response['type'] == 'ExAccessDenied' ) {
        //     return $response;
  
        // } 

        return $response;
    }

    public function econt_calculate_cart_price( $cart )
    {
        $price = 0;
        foreach ($cart as $key => $item) {
            $price += $item['line_total'];
        }

        return $price;
    }
    
    public function getWaybillPopupUrl($order_number) {
        $conf = ['private_key' => $this->get_private_key()];
        $data = $this->check_econt_configuration($conf, $order_number);
        return $data['pdfURL'];

    }

    /**
     * Shipping tracking popup URL getter
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     * @return string
     */
    public function getTrackingPopupUrl($tracking_object)
    {
        $tracksCollection = $tracking_object->getTracksCollection();
        foreach ($tracksCollection->getItems() as $track) {
            $trackNumbers[] = $track->getTrackNumber();       
        }
        $tracking_number = $trackNumbers[0];
        return 'https://www.econt.com/services/track-shipment/' . $tracking_number;
    }

}
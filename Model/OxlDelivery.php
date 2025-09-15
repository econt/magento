<?php

namespace Oxl\Delivery\Model;

use \Oxl\Delivery\Api\Data\OxlDeliveryInterface;
use \Oxl\Delivery\Helper\DataFactory;
use \Magento\Checkout\Model\Session;

/**
 * Marketplace Product Model.
 *
 * @method \Webkul\Marketplace\Model\ResourceModel\Product _getResource()
 * @method \Webkul\Marketplace\Model\ResourceModel\Product getResource()
 */
class OxlDelivery  implements OxlDeliveryInterface
{
    const REAL_URL = 'https://delivery.econt.com/';
    const DEMO_URL = 'http://delivery.demo.econt.com/';

    protected $helper;

    protected $is_demo;

    protected $_checkoutSession;

    public $id;
    public $key;
    public $shop_id;
    public $customer_info_url;

    public function __construct ( DataFactory $data, Session $checkoutSession )
    {
        $this->helper = $data->create();

        // $this->is_demo = boolval( $this->helper->getConfig( 'carriers/econt_delivery/demo_service' ) );
        $this->_checkoutSession = $checkoutSession;
    }
    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->helper->getConfig( 'carriers/econtdelivery/name' );;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function setTitle($title)
    {
    }

    /**
     * Get desc.
     *
     * @return string|null
     */
    public function getPrivateKey()
    {
        return $this->helper->getConfig( 'carriers/econtdelivery/key' );
    }

    /**
     * Set Desc.
     *
     * @param string $desc
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function setPrivateKey( $key )
    {
        if ($key)
            $this->key = $this->helper->getConfig( 'carriers/econtdelivery/key' );
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @return string|null
     **/
    public function getEcontCustomerInfoUrl()
    {     
        if ( $this->customer_info_url === null )
            return $this->helper->is_demo() ? self::DEMO_URL : self::REAL_URL;

        return $this->customer_info_url;
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function setEcontCustomerInfoUrl( $key )
    {                
        if ( $key )
            $this->customer_info_url = ( $this->helper->is_demo() ? self::DEMO_URL : self::REAL_URL ) . 'customer_info.php?';
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @return string|null
     **/
    public function getEcontShopId()
    {
        return $this->shop_id;
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param bool $key
     * 
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function setEcontShopId( $key )
    {
        if ( $key )
            $this->shop_id = $this->helper->getConfig( 'carriers/econtdelivery/identifier' );
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param mixed $var Description
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function prepareModel( $var = null )
    {
        if( gettype( $var ) === 'string' )
            $this->setModel( $var );
        else if ( gettype( $var ) === 'array' ) {
            foreach( $var as $key => $value ) {
                $this->setModel( $value );
            }
        } else {
            throw new \Exception;
        }
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param string $var Description
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function setModel( $var )
    {
        switch ( $var ) {
            case "key":
                $this->setPrivateKey( true );
                break;
            case "shop_id":
                $this->setEcontShopId( true );
                break;
            case "customer_info_url":
                $this->setEcontCustomerInfoUrl( true );
                break;
        }
        return $this;
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @return \Magento\Checkout\Model\Session
     **/
    public function getCheckoutSession() 
    {
        return $this->_checkoutSession;
    }    
}
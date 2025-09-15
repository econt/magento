<?php

namespace Oxl\Delivery\Api;

interface OxlDeliveryManagementInterface
{
    /**
     * get test Api data.
     *
     * @api
     *
     * @param int $id
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function getApiData( $id );

    /**
     * get test Api data.
     *
     * @api     
     * 
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function getIframeData();

    /**
     * set payment data.
     *
     * @api     
     * 
     * @param string $econt_id
     * @param float $shipping_price
     * @param float $shipping_price_cod
     * 
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function setPaymentData( $econt_id, $shipping_price, $shipping_price_cod );
}
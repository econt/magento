<?php

namespace Oxl\Delivery\Api;

interface OxlDeliveryManagementInterface
{
    /**
     * Get test Api data.
     *
     * @api
     *
     * @param int $id
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function getApiData($id);

    /**
     * Get test Api data.
     *
     * @api
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function getIframeData();

    /**
     * Set payment data.
     *
     * @api
     *
     * @param string $econt_id
     * @param float $shipping_price
     * @param float $shipping_price_cod
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function setPaymentData($econt_id, $shipping_price, $shipping_price_cod);
}

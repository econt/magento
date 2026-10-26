<?php

namespace Oxl\Delivery\Model\Api;

use \Oxl\Delivery\Api\OxlDeliveryManagementInterface;
use \Oxl\Delivery\Model\OxlDeliveryFactory;
use \Oxl\Delivery\Helper\DataFactory;

class OxlDeliveryManagement implements OxlDeliveryManagementInterface
{
    public const SEVERE_ERROR = 0;
    public const SUCCESS = 1;
    public const LOCAL_ERROR = 2;

    /**
     * @var \Oxl\Delivery\Model\OxlDeliveryFactory
     */
    protected $testApiFactory;

    /**
     * @var \Oxl\Delivery\Helper\Data
     */
    protected $helper;
    
    /**
     * Constructor
     *
     * @param OxlDeliveryFactory $testApiFactory
     * @param DataFactory $data
     */
    public function __construct(
        OxlDeliveryFactory $testApiFactory,
        DataFactory $data
    ) {
        $this->testApiFactory = $testApiFactory;
        $this->helper = $data;
    }

    /**
     * Get iframe url.
     *
     * @api
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function getIframeData()
    {
        $model = $this->testApiFactory
                ->create();
        
        $model->prepareModel(['shop_id', 'customer_info_url']);

        return $model;
    }

    /**
     * Set payment data.
     *
     * @api
     *
     * @param string $econt_id
     * @param float $shipping_price
     * @param float $shipping_price_cod
     *
     * @return int status
     */
    public function setPaymentData($econt_id = null, $shipping_price = null, $shipping_price_cod = null)
    {
        $model = $this->testApiFactory->create();
        if (! $econt_id) {
            return self::SEVERE_ERROR;
        }

        // Only the customer info id of the confirmed Econt form is kept. The prices come from the Econt server
        // (Helper\Data::getQuoteShippingCost), so the prices in the request are not used. The form keeps its id when
        // the customer changes the address in it, so the prices for the id are asked again.
        $model->getCheckoutSession()->setEcontId($econt_id);
        $model->getCheckoutSession()->setEcontServerPrices(null);

        return self::SUCCESS;
    }
}

<?php

namespace Oxl\Delivery\Model\Api;

use \Oxl\Delivery\Api\OxlDeliveryManagementInterface;
use \Oxl\Delivery\Model\OxlDeliveryFactory;
use \Oxl\Delivery\Helper\DataFactory;
use \Oxl\Delivery\Helper\Order;

class OxlDeliveryManagement implements OxlDeliveryManagementInterface
{
    const SEVERE_ERROR = 0;
    const SUCCESS = 1;
    const LOCAL_ERROR = 2;

    protected $_testApiFactory;
    protected $helper;
    protected $order;
    
    public function __construct(
        OxlDeliveryFactory $testApiFactory,
        DataFactory $data,
        Order $order
    ) {
        // var_dump($data);die();
        $this->_testApiFactory = $testApiFactory;        
        $this->helper = $data;        
        $this->order = $order;
    }

    /**
     * get test Api data.
     *
     * @api
     *
     * @param int $id
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function getApiData($id)
    {
        try {
            $this->order->sync_order(1);

            // return $model;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $returnArray['error'] = $e->getMessage();
            $returnArray['status'] = 0;
            return $returnArray;
        } catch (\Exception $e) {
            dd($e);
            $returnArray['error'] = __('unable to process request');
            $returnArray['status'] = 2;
            return $returnArray;
        }
    }

    /**
     * get iframe url.
     *
     * @api
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function getIframeData()
    {
        $model = $this->_testApiFactory
                ->create();
        
        $model->prepareModel(['shop_id', 'customer_info_url']);

        return $model;
    }

    /**
     * set payment data.
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
        $model = $this->_testApiFactory->create();
        if ( ! $econt_id || ! $shipping_price || ! $shipping_price_cod) 
            return self::SEVERE_ERROR;

        $model->getCheckoutSession()->setEcontId( $econt_id );
        $model->getCheckoutSession()->setEcontShippingPrice( $shipping_price );
        $model->getCheckoutSession()->setEcontShippingPriceCod( $shipping_price_cod );

        return self::SUCCESS;
    }
}
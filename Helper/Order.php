<?php

namespace Oxl\Delivery\Helper;

use \Magento\Checkout\Model\Session;
use \Magento\Framework\App\Helper\Context;
use \Oxl\Delivery\Model\OxlDeliveryFactory;
use \Magento\Framework\Message\ManagerInterface;

class Order  extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_checkoutSession;

    protected $_oxlDeliveryFactory;

    protected $_messageManager;

    public function __construct ( 
        Context $context,
        Session $checkoutSession,
        OxlDeliveryFactory $oxldelivery,
        ManagerInterface $messageManager
    )
    {
        $this->_checkoutSession = $checkoutSession;
        $this->_oxlDeliveryFactory = $oxldelivery->create();
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }
    /**
     * If there is an order in Econt syste, it will be updated.
     * If not - will be created.
     * 
     * @param int $local_order If there is a order in our system, the order_id will be used.
     * @param array $items If array of item ids is passed to the function, will loop trought them.
     * Other way $order->get_items() will be used.
     * @param bool $get_new_price If this is set to true, will send another request to Econt service
     * in order to fetch the order price. This is used in admin dashboard to recalculate shipping
     * 
     * @return string - the new price
     * @return bool - false - to finish the execution
     */
    public function sync_order( $order = null, $get_new_price = false )
    {
        if ( $order === null ) return false;
        
        if (gettype($order) === 'integer' ) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $order = $objectManager->create('Magento\Sales\Api\Data\OrderInterface')->load(intval($order));
        }
        
        if( $order->getShippingMethod() != 'econtdelivery_econtdelivery' ) return false;

        $data = array(
            'id' => '', 
            'orderNumber' => $order->getId(),
            'status' => $order->getStatus(),
            'orderTime' => '',
            'cod' => $order->getPayment()->getMethod() === 'cashondelivery' ? true : '',
            'partialDelivery' => '',
            'currency' => $order->getOrderCurrencyCode(),
            'shipmentDescription' => '',
            'shipmentNumber' => '',
            'customerInfo' => array( 
                'id' => $this->_checkoutSession->getEcontId(),
                'name' => '',
                'face' => '',
                'phone' => '',
                'email' => '',
                'countryCode' => '',
                'cityName' => '',
                'postCode' => '',
                'officeCode' => '',
                'zipCode' => '',
                'address' => '',
                'priorityFrom' => '',
                'priorityTo' => ''
            ),        
            'items' => array(
                
            ),
            "packCount" => null,
            "receiverShareAmount" => null
        );

        $iteration = count($order->getAllVisibleItems());
        
        foreach ($order->getAllVisibleItems() as $item) {                   
            $price  = $item->getPrice();
            // $count  = $item->get_quantity();
            $weight = floatval($item->getWeight());
            $quantity = intval($item->getQtyOrdered());

            array_push($data['items'], array( 
                'name' => $item->getName(),
                'SKU' => $item->getSku(),
                'URL' => '',
                'count' => $quantity,
                'hideCount' => '',
                'totalPrice' => $price * $quantity,
                'totalWeight' => $weight * $quantity
            ));
            
            $data['shipmentDescription'] .= $item->getName() . ($iteration === 1 ? '' : ', ');
            $iteration -= 1;
        }
        
        mb_strimwidth($data['shipmentDescription'], 0, 160, "...");
        
        if( $order->getTotalItemCount() > 1 && $data['cod'] ) $data['partialDelivery'] = true;        

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->_oxlDeliveryFactory->getEcontCustomerInfoUrl() . 'services/OrdersService.updateOrder.json');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . $this->_oxlDeliveryFactory->getPrivateKey()
        ]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        // Изпращане на заявката
        $response = curl_exec($curl);
        
        $parsed_error = json_decode($response, true);
        
        // dump($parsed_error);
        if( array_key_exists('type', $parsed_error) ) {            
            $this->_messageManager->addErrorMessage($parsed_error['message']);
            return false;
        }

        $this->_checkoutSession->unsEcontShippingPriceCod();
        return;

        // if ( $get_new_price ) {
        //     curl_setopt($curl, CURLOPT_URL, $this->get_service_url() . 'services/OrdersService.getPrice.json');
        //     $price = curl_exec($curl);

        //     return json_decode($price, true)['receiverDueAmount'];
        // }
    }    
}
<?php

namespace Oxl\Delivery\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\OfflinePayments\Model\Cashondelivery;
use Oxl\Delivery\Model\OxlDeliveryFactory;

class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Oxl\Delivery\Model\OxlDelivery
     */
    protected $_oxlDeliveryFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\HTTP\ClientInterface
     */
    protected $_client;

    /**
     * Get the module config data
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Oxl\Delivery\Model\OxlDeliveryFactory $oxldelivery
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\HTTP\ClientInterface $client
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OxlDeliveryFactory $oxldelivery,
        ManagerInterface $messageManager,
        ClientInterface $client
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_oxlDeliveryFactory = $oxldelivery->create();
        $this->_messageManager = $messageManager;
        $this->_client = $client;
        parent::__construct($context);
    }
    /**
     * If there is an order in Econt syste, it will be updated. If not - will be created.
     *
     * @param int $order If there is a order in our system, the order_id will be used.
     * @param bool $get_new_price If this is set to true, will send another request to Econt service
     * in order to fetch the order price. This is used in admin dashboard to recalculate shipping
     *
     * @return string - the new price
     * @return bool - false - to finish the execution
     */
    public function syncOrder($order = null, $get_new_price = false)
    {
        if ($order === null) {
            return false;
        }
        
        if (is_int($order)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $order = $objectManager->create(\Magento\Sales\Api\Data\OrderInterface::class)->load((int)($order));
        }
        
        if ($order->getShippingMethod() != 'econtdelivery_econtdelivery') {
            return false;
        }

        $data = [
            'id' => '',
            'orderNumber' => $order->getId(),
            'status' => $order->getStatus(),
            'orderTime' => '',
            'cod' => $order->getPayment()->getMethod() === Cashondelivery::PAYMENT_METHOD_CODE ? true : '',
            'partialDelivery' => '',
            'currency' => $order->getOrderCurrencyCode(),
            'shipmentDescription' => '',
            'shipmentNumber' => '',
            'customerInfo' => [
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
            ],
            'items' => [
                
            ],
            "packCount" => null,
            "receiverShareAmount" => null
        ];

        $iteration = count($order->getAllVisibleItems());
        
        foreach ($order->getAllVisibleItems() as $item) {
            $price  = $item->getPrice();
            // $count  = $item->get_quantity();
            $weight = floatval($item->getWeight());
            $quantity = (int)($item->getQtyOrdered());

            array_push($data['items'], [
                'name' => $item->getName(),
                'SKU' => $item->getSku(),
                'URL' => '',
                'count' => $quantity,
                'hideCount' => '',
                'totalPrice' => $price * $quantity,
                'totalWeight' => $weight * $quantity
            ]);
            
            $data['shipmentDescription'] .= $item->getName() . ($iteration === 1 ? '' : ', ');
            --$iteration;
        }
        
        mb_strimwidth($data['shipmentDescription'], 0, 160, "...");
        
        if ($order->getTotalItemCount() > 1 && $data['cod']) {
            $data['partialDelivery'] = true;
        }

        $url = $this->_oxlDeliveryFactory->getEcontCustomerInfoUrl() . 'services/OrdersService.updateOrder.json';
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $this->_oxlDeliveryFactory->getPrivateKey()
        ];
        $this->client->setHeaders($headers);
        $this->client->setOptions([
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 6
        ]);
        $this->client->post($url, json_encode($data));

        $res = $this->client->getBody();
        $response = json_decode($res, true);

        $parsed_error = json_decode($response, true);

        if (array_key_exists('type', $parsed_error)) {
            $this->_messageManager->addErrorMessage($parsed_error['message']);
            return false;
        }

        $this->_checkoutSession->unsEcontShippingPriceCod();
    }
}

<?php

namespace Oxl\Delivery\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Oxl\Delivery\Model\OxlDelivery;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\ClientInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * Get the module config data
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\HTTP\ClientInterface $client
     */
    public function __construct(
        Context $context,
        ClientInterface $client
    ) {
        parent::__construct($context);
        $this->client = $client;
    }

    /**
     * Get the module config data
     *
     * @param string $config_path The config path
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
    public function isDemo()
    {
        $options = $this->getConfig('carriers/econtdelivery/demo_service');

        return boolval($options);
    }

    /**
     * Based on the demo setting returns the appropiate url
     *
     * @return string URL
     */
    public function getServiceUrl()
    {
        $url = '';
        
        if ($this->isDemo()) {
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
    public function getPrivateKey($encrypt = false)
    {
        $key = $this->getConfig('carriers/econtdelivery/key');

        return $encrypt ? base64_encode($key) : $key;
    }

    /**
     * URL of Econt's waybill form for the order. The form gets a short label session for this order
     * from Econt (OrdersService.createLabelSession), so the private key stays on the server.
     *
     * @param string $order_number
     * @return array ['url' => ...] or ['error' => ...]
     */
    public function getLabelUrl($order_number)
    {
        try {
            $this->client->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $this->getPrivateKey()
            ]);
            $this->client->setOptions([
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_TIMEOUT => 6
            ]);
            $this->client->post($this->getServiceUrl() . 'services/OrdersService.createLabelSession.json', json_encode([
                'orderNumber' => (string)$order_number
            ]));
            $result = json_decode((string)$this->client->getBody(), true);
        } catch (\Exception $ex) {
            $this->_logger->error('Econt: createLabelSession failed: ' . $ex->getMessage());
            return ['error' => $ex->getMessage()];
        }

        if (!is_string($result) || $result === '') {
            return ['error' => $result['message'] ?? 'Unknown error'];
        }

        return ['url' => $this->getServiceUrl() . 'create_label.php?' . http_build_query([
            'order_number' => $order_number,
            'token' => $result
        ])];
    }

    /**
     * The tracking url
     *
     * @param string $code The tracking code
     * @return string
     */
    public function getTrackingUrl($code)
    {
        return Delivery_With_Econt_Options::get_track_url() . $code;
    }

    /**
     * Check stored configuration
     *
     * Check stored shop_id, private_key and demo_service options with Econt via curl request
     *
     * @param array $new_settings The settings entered by the user
     * @param string $order_number The order number
     * @return array
     **/
    public function checkEcontConfiguration($new_settings = [], $order_number = '4812384')
    {
        try {
            $endpoint = $this->getServiceUrl(array_key_exists('demo_service', $new_settings));
            $secret = $new_settings['private_key'];

            $url = $endpoint . "services/OrdersService.getTrace.json";
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => $secret
            ];
            $payload = json_encode([
                'orderNumber' => $order_number
            ]);
            $this->client->setHeaders($headers);
            $this->client->setOptions([
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_TIMEOUT => 6
            ]);
            $this->client->post($url, $payload);
            $res = $this->client->getBody();
            $response = json_decode($res, true);

            return $response;
        } catch (\Exception $ex) {
            $this->_logger->error($ex->getMessage());
            return ['error' => $ex->getMessage()];
        }
    }

    /**
     * Econt shipping cost of a quote, with the prices from the Econt server (OrdersService.getPrice) for the customer
     * info id from the Econt form. The amounts are in the base currency, like the shipping rates.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\Quote $quote
     * @param float $total Base subtotal with discount of the shipping address
     * @param float $weight Weight of the shipping address
     * @param bool $fresh Ask Econt even if the prices are in the session
     * @return float|null null without a confirmed Econt form or if Econt gives no price
     */
    public function getQuoteShippingCost($checkoutSession, $quote, $total, $weight, $fresh = false)
    {
        $customerInfoId = trim((string)$checkoutSession->getEcontId());
        if ($customerInfoId === '') {
            return null;
        }

        $total = round((float)$total, 2);
        $weight = round((float)$weight, 3);
        $currency = (string)$quote->getBaseCurrencyCode();

        // One request to Econt per address and cart; the session is cleared when the Econt form is confirmed again
        $key = md5(implode('|', [$customerInfoId, $total, $weight, $currency]));
        $cached = $checkoutSession->getEcontServerPrices();
        $prices = (!$fresh && is_array($cached) && ($cached['key'] ?? '') === $key) ? $cached['prices'] : null;

        if (!$prices) {
            $price = $this->getEcontPrice($customerInfoId, $total, $weight, $currency, false);
            if ($price === null) {
                return null;
            }

            // Like the Econt form: an order without a total has no cash on delivery
            $priceCod = $total > 0 ? $this->getEcontPrice($customerInfoId, $total, $weight, $currency, true) : $price;
            if ($priceCod === null) {
                return null;
            }

            $prices = ['price' => $price, 'price_cod' => $priceCod];
            $checkoutSession->setEcontServerPrices(['key' => $key, 'prices' => $prices]);
        }

        // Cash on delivery unless another payment method is chosen, as the checkout of the module did so far
        $paymentMethod = $quote->getPayment() ? $quote->getPayment()->getMethod() : null;

        return ($paymentMethod && $paymentMethod !== 'cashondelivery') ? $prices['price'] : $prices['price_cod'];
    }

    /**
     * Shipping price (receiverDueAmount) from OrdersService.getPrice. Without an order number Econt calculates it on
     * a temporary order and saves nothing.
     *
     * @param string $customerInfoId
     * @param float $total
     * @param float $weight
     * @param string $currency
     * @param bool $cod
     * @return float|null null on a connection error or an error from Econt
     */
    private function getEcontPrice($customerInfoId, $total, $weight, $currency, $cod)
    {
        try {
            $this->client->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $this->getPrivateKey()
            ]);
            $this->client->setOptions([
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_TIMEOUT => 6
            ]);
            $this->client->post($this->getServiceUrl() . 'services/OrdersService.getPrice.json', json_encode([
                'cod' => (bool)$cod,
                'currency' => $currency,
                'customerInfo' => ['id' => $customerInfoId],
                'items' => [[
                    'name' => 'Order',
                    'count' => 1,
                    'totalPrice' => $total,
                    'totalWeight' => $weight
                ]]
            ]));
            $status = (int)$this->client->getStatus();
            $result = json_decode((string)$this->client->getBody(), true);
        } catch (\Exception $ex) {
            $this->_logger->error('Econt: getPrice failed: ' . $ex->getMessage());
            return null;
        }

        if ($status !== 200 || !isset($result['receiverDueAmount']) || !is_numeric($result['receiverDueAmount'])) {
            $this->_logger->error(sprintf('Econt: getPrice failed [%d] %s', $status, $result['message'] ?? ''));
            return null;
        }

        return round((float)$result['receiverDueAmount'], 2);
    }

    /**
     * Calculate cart price
     *
     * @param array $cart The cart items
     * @return float
     */
    public function econtCalculateCartPrice($cart)
    {
        $price = 0;
        foreach ($cart as $key => $item) {
            $price += $item['line_total'];
        }

        return $price;
    }
    
    /**
     * Waybill popup URL getter
     *
     * @param string $order_number
     * @return string
     */
    public function getWaybillPopupUrl($order_number)
    {
        $conf = ['private_key' => $this->getPrivateKey()];
        $data = $this->checkEcontConfiguration($conf, $order_number);

        if (!array_key_exists('pdfURL', $data)) {
            $this->_logger->warning(__('Econt: Waybill generation failed. No PDF URL returned.'));
            return null;
        }
        return $data['pdfURL'];
    }

    /**
     * Shipping tracking popup URL getter
     *
     * @param \Magento\Sales\Model\AbstractModel $tracking_object
     * @return string
     */
    public function getTrackingPopupUrl($tracking_object)
    {
        $tracksCollection = $tracking_object->getTracksCollection();
        foreach ($tracksCollection->getItems() as $track) {
            $trackNumbers[] = $track->getTrackNumber();
        }
        $tracking_number = isset($trackNumbers[0]) ? $trackNumbers[0] : '';
        return 'https://www.econt.com/services/track-shipment/' . $tracking_number;
    }
}

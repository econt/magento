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
class OxlDelivery implements OxlDeliveryInterface
{
    public const REAL_URL = 'https://delivery.econt.com/';
    
    public const DEMO_URL = 'http://delivery.demo.econt.com/';

    /**
     * @var \Oxl\Delivery\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $shop_id;

    /**
     * @var string
     */
    public $customer_info_url;

    /**
     * @var string
     */
    public $title;

    /**
     * Constructor
     *
     * @param DataFactory $data
     * @param Session $checkoutSession
     */
    public function __construct(DataFactory $data, Session $checkoutSession)
    {
        $this->helper = $data->create();

        $this->checkoutSession = $checkoutSession;
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
        return $this->helper->getConfig('carriers/econtdelivery/name');
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
        $this->title = $title;
        return $this;
    }

    /**
     * Get desc.
     *
     * @return string|null
     */
    public function getPrivateKey()
    {
        return $this->helper->getConfig('carriers/econtdelivery/key');
    }

    /**
     * Set Key.
     *
     * @param string $key
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function setPrivateKey($key)
    {
        if ($key) {
            $this->key = $this->helper->getConfig('carriers/econtdelivery/key');
        }
    }

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @return string|null
     **/
    public function getServiceUrl()
    {
        return $this->helper->isDemo() ? self::DEMO_URL : self::REAL_URL;
    }

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @return string|null
     **/
    public function getEcontCustomerInfoUrl()
    {
        if ($this->customer_info_url === null) {
            return $this->getServiceUrl();
        }

        return $this->customer_info_url;
    }

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @param bool $key
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function setEcontCustomerInfoUrl($key)
    {
        if ($key) {
            $this->customer_info_url = $this->getServiceUrl() . 'customer_info.php?';
        }
    }

    /**
     * Undocumented function summary
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
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @param bool $key
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function setEcontShopId($key)
    {
        if ($key) {
            $this->shop_id = $this->helper->getConfig('carriers/econtdelivery/identifier');
        }
    }

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @param mixed $var Description
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function prepareModel($var = null)
    {
        if (is_string($var)) {
            $this->setModel($var);
        } elseif (is_array($var)) {
            foreach ($var as $key => $value) {
                $this->setModel($value);
            }
        }
    }

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @param string $var Description
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function setModel($var)
    {
        switch ($var) {
            case "key":
                $this->setPrivateKey(true);
                break;
            case "shop_id":
                $this->setEcontShopId(true);
                break;
            case "customer_info_url":
                $this->setEcontCustomerInfoUrl(true);
                break;
        }
        return $this;
    }

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @return \Magento\Checkout\Model\Session
     **/
    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }
}

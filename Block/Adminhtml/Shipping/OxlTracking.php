<?php

namespace Oxl\Delivery\Block\Adminhtml\Shipping;

use Magento\Shipping\Block\Adminhtml\Create\Form;

class OxlTracking extends Form
{

    /**
     * Cache waybill popup URL
     *
     * @var string|null
     */
    private $waybillPopupUrl = null;

    /**
     * Retrieve invoice order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getShipment()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getSource()
    {
        return $this->getShipment();
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        return $this->_coreRegistry->registry('current_shipment');
    }

    /**
     * Prepare layout.
     */
    public function getText()
    {
        return "Override Text " . $this->getOrder()->getId();
    }

    /**
     * Prepare layout.
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }

    /**
     * Get order currency code
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->getOrder()->getOrderCurrencyCode();
    }

    /**
     * Check if order has tracking numbers
     *
     * @return bool
     */
    public function hasTracking()
    {
        return boolval($this->getOrder()->getTrackingNumbers());
    }

    /**
     * Get waybill popup URL
     *
     * @return string|null
     */
    public function getWaybillPopupUrl()
    {
        if ($this->waybillPopupUrl !== null) {
            return $this->waybillPopupUrl;
        }
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(\Oxl\Delivery\Helper\Data::class);
        $this->waybillPopupUrl = $helper->getWaybillPopupUrl($this->getOrder()->getId());
        return $this->waybillPopupUrl;
    }

    /**
     * Prepare HTML output
     *
     * @return string
     */
    protected function _toHtml()
    {
        // Do not display if shipping method is not Econt Delivery
        if ($this->getOrder()->getShippingMethod() != 'econtdelivery_econtdelivery') {
            return '';
        }

        // Get \Oxl\Delivery\Helper\Data::class instance
        $waybillPopupUrl = $this->getWaybillPopupUrl();
        
        if ($waybillPopupUrl === null) {
            return ''; // No waybill available
        }
        return parent::_toHtml();
    }
}

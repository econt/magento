<?php

namespace Oxl\Delivery\Block\Adminhtml\Shipping;

use Magento\Shipping\Block\Adminhtml\Create\Form;

class OxlTracking extends Form {   

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

    public function getText() 
    {         
        return "Override Text " . $this->getOrder()->getId(); 
    }

    public function getOrderId() 
    {         
        return $this->getOrder()->getId(); 
    }

    public function getCurrency()
    {
        return $this->getOrder()->getOrderCurrencyCode();
    }

    public function hasTracking()
    {
        return boolval($this->getOrder()->getTrackingNumbers());
    }

    public function getTracking()
    {

    }

}

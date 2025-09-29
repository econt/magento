<?php

namespace Oxl\Delivery\Block\Adminhtml\Shipping;

use Magento\Shipping\Block\Adminhtml\Create\Form;

class OxlShippingAbstract extends Form
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
     * Retrieve helper
     *
     * @return \Oxl\Delivery\Helper\Data
     */
    public function helper()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get(\Oxl\Delivery\Helper\Data::class);
    }

    /**
     * Get order ID
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
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
        $helper = $this->helper();
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

        return parent::_toHtml();
    }
}

<?php

namespace Oxl\Delivery\Block\Adminhtml\Shipping;

use Oxl\Delivery\Block\Adminhtml\Shipping\OxlShippingAbstract;

class OxlGenerateWaybill extends OxlShippingAbstract
{
    /**
     * Check if order has tracking numbers
     *
     * @return bool
     */
    public function hasTracking()
    {
        return boolval($this->getOrder()->getTrackingNumbers());
    }
}

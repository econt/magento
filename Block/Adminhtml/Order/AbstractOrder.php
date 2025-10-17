<?php

namespace Oxl\Delivery\Block\Adminhtml\Order;

use Magento\Sales\Block\Adminhtml\Order\AbstractOrder as OrderAbstract;

class AbstractOrder extends OrderAbstract
{
    /**
     * Retrieve helper
     *
     * @param string $class
     *
     * @return \Magento\Tax\Helper\Data
     */
    public function helper($class)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($class);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $order = $this->getOrder();

        if ($order->getIsVirtual()) {
            return '';
        }

        return parent::_toHtml();
    }
}

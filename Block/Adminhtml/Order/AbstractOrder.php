<?php

namespace Oxl\Delivery\Block\Adminhtml\Order;

use Magento\Sales\Block\Adminhtml\Order\AbstractOrder as OrderAbstract;

class AbstractOrder extends OrderAbstract
{
    /**
     * Retrieve helper
     *
     * @return \Magento\Tax\Helper\Data
     */
    public function helper()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Tax\Helper\Data::class);
    }
}

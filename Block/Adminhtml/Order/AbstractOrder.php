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
}

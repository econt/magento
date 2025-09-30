<?php

namespace Oxl\Delivery\Observer;

use Oxl\Delivery\Helper\Order;

class CreateOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Oxl\Delivery\Helper\Order
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param \Oxl\Delivery\Helper\Order $helper
     */
    public function __construct(Order $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Create or update order in Econt system
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        
        $this->helper->syncOrder($order);
    }
}

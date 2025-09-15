<?php

namespace Oxl\Delivery\Observer;

use \Oxl\Delivery\Helper\Order;

class CreateOrder implements \Magento\Framework\Event\ObserverInterface
{
	protected $helper;

	public function __construct(Order $helper)
	{
		$this->helper = $helper;
	}
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
        $order = $observer->getEvent()->getOrder();
        
        $this->helper->sync_order( $order );
	}
}
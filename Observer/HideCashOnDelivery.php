<?php

namespace Oxl\Delivery\Observer;

use Magento\Checkout\Model\Session;
use Oxl\Delivery\Helper\Data;

/**
 * Cash on delivery is not offered with Econt when Econt does not allow it for the quote (e.g. above the legal limit
 * for cash payments). Event payment_method_is_active.
 */
class HideCashOnDelivery implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Oxl\Delivery\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param \Oxl\Delivery\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(Data $helper, Session $checkoutSession)
    {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $method = $event->getMethodInstance();
        $quote = $event->getQuote();
        $result = $event->getResult();

        if (!$method || !$quote || !$result || $method->getCode() !== 'cashondelivery' || !$result->getData('is_available')) {
            return;
        }

        if ($this->helper->getQuoteCodError($this->checkoutSession, $quote) !== '') {
            $result->setData('is_available', false);
        }
    }
}

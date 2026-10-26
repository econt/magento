<?php

namespace Oxl\Delivery\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Oxl\Delivery\Helper\Data;

class CheckShippingPrice implements \Magento\Framework\Event\ObserverInterface
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
     * Constructor
     *
     * @param \Oxl\Delivery\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(Data $helper, Session $checkoutSession)
    {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Before the order is placed, asks Econt for the shipping price again without the cache: the Econt form keeps its
     * customer info id when the customer changes the address in it. The order is not placed without a price from
     * Econt or with another price; the new price is in the session for the next calculation of the shipping.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if (!$order || !$quote || strpos((string)$order->getShippingMethod(), 'econtdelivery_') !== 0) {
            return;
        }

        $address = $quote->getShippingAddress();
        $cost = $this->helper->getQuoteShippingCost(
            $this->checkoutSession,
            $quote,
            $address->getBaseSubtotalWithDiscount(),
            $address->getWeight(),
            true
        );

        if ($cost === null) {
            throw new LocalizedException(__("You can't submit order, if shipping price is not calculated properly!"));
        }

        if (abs($cost - (float)$order->getBaseShippingAmount()) >= 0.005) {
            $address->setCollectShippingRates(true);
            throw new LocalizedException(
                __('The Econt shipping price has changed. Please review the updated total and place your order again.')
            );
        }
    }
}

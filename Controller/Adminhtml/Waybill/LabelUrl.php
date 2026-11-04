<?php

namespace Oxl\Delivery\Controller\Adminhtml\Waybill;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Oxl\Delivery\Helper\Data;

/**
 * URL of Econt's waybill form with a label session for the order, the private key stays on the server
 */
class LabelUrl extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Magento_Sales::ship';

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Data $helper
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory, Data $helper)
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $orderId = (int)$this->getRequest()->getParam('order_id');
        if ($orderId <= 0) {
            return $result->setData(['error' => __('Invalid order.')]);
        }

        return $result->setData($this->helper->getLabelUrl($orderId));
    }
}

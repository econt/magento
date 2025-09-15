<?php
/**
 * Copyright © 2015 AionNext Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Oxl\Delivery\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;

/**
 * Contact index controller
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Aion\Test\Helper\Data
     */
    protected $helper;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Aion\Test\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory
        // \Aion\Test\Helper\Data $helper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        // $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        // if (!$this->helper->isEnabled()) {
        //     throw new NotFoundException(__('Page not found.'));
        // }
        return parent::dispatch($request);
    }

    /**
     * Aion Test Page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Aion Test Page'));
        if (!$resultPage) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        return $resultPage;
    }

    public function get_iframe(RequestInterface $request)
    {
        // var_dump($request); die;
    }
} 
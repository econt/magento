<?php
/**
 * Copyright © 2016 AionNext Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Oxl\Delivery\Block;

use Magento\Framework\View\Element\Template;

/**
 * Aion Test Page block
 */
class Modal extends Template
{
    /**
     * @var \Aion\Test\Model\Test
     */
    protected $test;

    /**
     * Test factory
     *
     * @var \Aion\Test\Model\TestFactory
     */
    protected $testFactory;

    /**
     * @var \Aion\Test\Model\ResourceModel\Test\CollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var \Aion\Test\Model\ResourceModel\Test\Collection
     */
    protected $items;

    /**
     * Test constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Aion\Test\Model\Test $test
     * @param \Aion\Test\Model\TestFactory $testFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        // \Aion\Test\Model\Test $test,
        // \Aion\Test\Model\TestFactory $testFactory,
        // \Aion\Test\Model\ResourceModel\Test\CollectionFactory $itemCollectionFactory,
        array $data = []
    ) {
        // $this->test = $test;
        // $this->testFactory = $testFactory;
        // $this->itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Test instance
     *
     * @return \Aion\Test\Model\Test
     */
    public function getTestModel()
    {
        if (!$this->hasData('test')) {
            // if ($this->getTestId()) {
            //     /** @var \Aion\Test\Model\Test $test */
            //     $test = $this->testFactory->create();
            //     $test->load($this->getTestId());
            // } else {
            //     $test = $this->test;
            // }
            // $this->setData('test', $test);
        }
        return $this->getData('test');
    }

    /**
     * Get items
     *
     * @return bool|\Aion\Test\Model\ResourceModel\Test\Collection
     */
    public function getItems()
    {
        // if (!$this->items) {
        //     $this->items = $this->itemCollectionFactory->create()->addFieldToSelect(
        //         '*'
        //     )->addFieldToFilter(
        //         'is_active',
        //         ['eq' => \Aion\Test\Model\Test::STATUS_ENABLED]
        //     )->setOrder(
        //         'creation_time',
        //         'desc'
        //     );
        // }
        // return $this->items;
    }

    /**
     * Get Test Id
     *
     * @return int
     */
    public function getTestId()
    {
        return 1;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getAjaxUrl()
    {
        return $this->getBaseUrl();
    }
} 
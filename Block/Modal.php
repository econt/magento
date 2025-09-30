<?php

namespace Oxl\Delivery\Block;

use Magento\Framework\View\Element\Template;

class Modal extends Template
{
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

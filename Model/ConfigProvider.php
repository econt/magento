<?php

namespace Oxl\Delivery\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface  */
    protected $layout;

    /** @var array  */
    protected $blocks;

    /**
     * @param LayoutInterface $layout
     * @param array $blockIds
     */
    public function __construct(LayoutInterface $layout, $blockIds)
    {
        $this->layout = $layout;
        $this->blocks = $this->buildBlocks($blockIds);
    }
    
    /**
     * Build blocks
     *
     * @param array $blockIds
     * @return array
     */
    public function buildBlocks($blockIds)
    {
        $blocks = [];
        foreach ($blockIds as $blockName => $blockId) {
            $blocks[$blockName] = $this->constructBlock($blockId);
        }
        return $blocks;
    }

    /**
     * Construct block by its ID
     *
     * @param string $blockId
     * @return string
     */
    public function constructBlock($blockId)
    {
        $block = $this->layout->createBlock(\Magento\Cms\Block\Block::class)
                 ->setBlockId($blockId)->toHtml();
        return $block;
    }

    /**
     * Retrieve associative array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->blocks;
    }
}

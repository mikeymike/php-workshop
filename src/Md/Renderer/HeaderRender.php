<?php

namespace PhpWorkshop\PhpWorkshop\Md\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Header;
use PhpWorkshop\PhpWorkshop\Md\CliRenderer;

/**
 * Class HeaderRender
 * @package PhpWorkshop\PhpWorkshop\Md\Renderer
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
class HeaderRender implements CliBlockRendererInterface
{

    /**
     * @param AbstractBlock $block
     * @param CliRenderer   $renderer
     *
     * @return string
     */
    public function render(AbstractBlock $block, CliRenderer $renderer)
    {
        if (!($block instanceof Header)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $level  = $block->getLevel();
        $text   = $renderer->renderInlines($block->children());

        return $renderer->style($text, ['bold', 'light_blue']);
    }
}
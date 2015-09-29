<?php

namespace PhpWorkshop\PhpWorkshop\Md\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Paragraph;
use PhpWorkshop\PhpWorkshop\Md\CliRenderer;

/**
 * Class ParagraphRender
 * @package PhpWorkshop\PhpWorkshop\Md\Renderer
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
class ParagraphRender implements CliBlockRendererInterface
{

    /**
     * @param AbstractBlock $block
     * @param CliRenderer   $renderer
     *
     * @return string
     */
    public function render(AbstractBlock $block, CliRenderer $renderer)
    {
        if (!($block instanceof Paragraph)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        return $renderer->renderInlines($block->children()) . "\n";
    }
}
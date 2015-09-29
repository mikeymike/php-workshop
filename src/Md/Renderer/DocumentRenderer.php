<?php


namespace PhpWorkshop\PhpWorkshop\Md\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Document;
use PhpWorkshop\PhpWorkshop\Md\CliRenderer;
use PhpWorkshop\PhpWorkshop\Md\Renderer\CliBlockRendererInterface;

/**
 * Class DocumentRenderer
 * @package League\CommonMark\Block\Renderer
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
class DocumentRenderer implements CliBlockRendererInterface
{

    /**
     * @param AbstractBlock $block
     * @param CliRenderer   $renderer
     *
     * @return string
     */
    public function render(AbstractBlock $block, CliRenderer $renderer)
    {
        if (!($block instanceof Document)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $wholeDoc = $renderer->renderBlocks($block->children());
        return $wholeDoc === '' ? '' : $wholeDoc . "\n";
    }
}

<?php

namespace PhpWorkshop\PhpWorkshop\Md\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use PhpWorkshop\PhpWorkshop\Md\CliRenderer;

/**
 * Class CliBlockRendererInterface
 * @package PhpWorkshop\PhpWorkshop\Md\Renderer
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
interface CliBlockRendererInterface
{
    /**
     * @param AbstractBlock $block
     * @param CliRenderer   $renderer
     *
     * @return string
     */
    public function render(AbstractBlock $block, CliRenderer $renderer);
}
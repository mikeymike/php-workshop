<?php

namespace PhpWorkshop\PhpWorkshop\Md\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\HorizontalRule;
use PhpWorkshop\PhpWorkshop\Md\CliRenderer;

/**
 * Class HorizontalRuleRender
 * @package PhpWorkshop\PhpWorkshop\Md\Renderer
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
class HorizontalRuleRender implements CliBlockRendererInterface
{

    /**
     * @param AbstractBlock $block
     * @param CliRenderer   $renderer
     *
     * @return string
     */
    public function render(AbstractBlock $block, CliRenderer $renderer)
    {
        if (!($block instanceof HorizontalRule)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        return $renderer->style(str_repeat('-', exec('tput cols')), 'yellow');
    }
}
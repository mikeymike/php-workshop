<?php

namespace PhpWorkshop\PhpWorkshop\Md\InlineRenderer;

use League\CommonMark\Inline\Element\AbstractInline;
use PhpWorkshop\PhpWorkshop\Md\CliRenderer;

/**
 * Interface CliInlineRendererInterface
 * @package PhpWorkshop\PhpWorkshop\Md\InlineRenderer
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
interface CliInlineRendererInterface
{

    /**
     * @param AbstractInline $inline
     * @param CliRenderer    $renderer
     *
     * @return string
     */
    public function render(AbstractInline $inline, CliRenderer $renderer);
}

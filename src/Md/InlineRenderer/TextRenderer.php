<?php

namespace PhpWorkshop\PhpWorkshop\Md\InlineRenderer;

use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Text;
use PhpWorkshop\PhpWorkshop\Md\CliRenderer;

/**
 * Class TextRenderer
 * @package League\CommonMark\Inline\Renderer
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
class TextRenderer implements CliInlineRendererInterface
{

    /**
     * @param AbstractInline $inline
     * @param CliRenderer    $renderer
     *
     * @return string
     */
    public function render(AbstractInline $inline, CliRenderer $renderer)
    {
        if (!($inline instanceof Text)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        return $inline->getContent();
    }
}

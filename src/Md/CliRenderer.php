<?php

namespace PhpWorkshop\PhpWorkshop\Md;

use Colors\Color;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Header;
use League\CommonMark\Block\Element\HorizontalRule;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Text;
use PhpWorkshop\PhpWorkshop\Md\InlineRenderer\TextRenderer;
use PhpWorkshop\PhpWorkshop\Md\Renderer\DocumentRenderer;
use PhpWorkshop\PhpWorkshop\Md\Renderer\HeaderRender;
use PhpWorkshop\PhpWorkshop\Md\Renderer\HorizontalRuleRender;
use PhpWorkshop\PhpWorkshop\Md\Renderer\ParagraphRender;

/**
 * Class CliRenderer
 * @package PhpWorkshop\PhpWorkshop\Md
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
class CliRenderer
{

    /**
     * @var CliBlockRendererInterface[]
     */
    private $renderers = [];

    /**
     * @var array
     */
    private $inlineRenderers = [];

    /**
     * @var Color
     */
    private $color;

    /**
     * @param Color       $color
     */
    public function __construct(Color $color)
    {
        $this->renderers[Document::class]               = new DocumentRenderer;
        $this->renderers[Header::class]                 = new HeaderRender;
        $this->renderers[HorizontalRule::class]         = new HorizontalRuleRender;
        $this->renderers[Paragraph::class]              = new ParagraphRender;
        $this->inlineRenderers[Text::class]             = new TextRenderer;

        $this->color = $color;
    }

    /**
     * @param string $string
     * @param array|string $colourOrStyle
     *
     * @return string
     *
     */
    public function style($string, $colourOrStyle)
    {
        if (is_array($colourOrStyle)) {
            $this->color->__invoke($string);

            while ($style = array_shift($colourOrStyle)) {
                $this->color->apply($style);
            }
            return $this->color->__toString();
        }

        return $this->color->__invoke($string)->apply($colourOrStyle, $string);
    }

    /**
     * @param AbstractInline[] $inlines
     *
     * @return string
     */
    public function renderInlines($inlines)
    {
        return implode(
            "",
            array_map(
                function (AbstractInline $inline) {
                    $renderer = $this->getInlineRendererForClass(get_class($inline));
                    if (!$renderer) {
                        return '';
                        throw new \RuntimeException('Unable to find corresponding renderer for inline type ' . get_class($inline));
                    }

                    return $renderer->render($inline, $this);
                },
                $inlines
            )
        );
    }

    /**
     * @param string $inlineBlockClass
     *
     * @return null|CliBlockRendererInterface
     */
    private function getInlineRendererForClass($inlineBlockClass)
    {
        if (!isset($this->inlineRenderers[$inlineBlockClass])) {
            return null;
        }

        return $this->inlineRenderers[$inlineBlockClass];
    }

    /**
     * @param string $blockClass
     *
     * @return null|CliBlockRendererInterface
     */
    private function getBlockRendererForClass($blockClass)
    {
        if (!isset($this->renderers[$blockClass])) {
            return null;
        }

        return $this->renderers[$blockClass];
    }

    /**
     * @param AbstractBlock $block
     * @param bool          $inTightList
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderBlock(AbstractBlock $block, $inTightList = false)
    {
        $renderer = $this->getBlockRendererForClass(get_class($block));
        if (!$renderer) {
            return '';
            throw new \RuntimeException('Unable to find corresponding renderer for block type ' . get_class($block));
        }

        return $renderer->render($block, $this, $inTightList);
    }

    /**
     * @param AbstractBlock[] $blocks
     * @param bool            $inTightList
     *
     * @return string
     */
    public function renderBlocks($blocks, $inTightList = false)
    {
        return implode(
            "\n",
            array_map(
                function (AbstractBlock $block) {
                    return $this->renderBlock($block);
                },
                $blocks
            )
        );
    }
}
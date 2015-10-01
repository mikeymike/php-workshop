<?php

namespace PhpWorkshop\PhpWorkshop;

/**
 * Interface ColourAdapterInterface
 * @package PhpWorkshop\PhpWorkshop
 */
interface ColourAdapterInterface
{
    /**
     * Color the string if possible,
     * if not - just return the string
     *
     * @param string $string
     * @param string $colour
     *
     * @return string
     */
    public function colour($string, $colour);
}
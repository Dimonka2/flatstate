<?php

namespace dimonka2\flatstate\Commands;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;

trait StyledTrait 
{
    protected function addStyle($styleName, $foreColor = 'default', $bgColor = 'default', $options = [])
    {
        $style = new OutputFormatterStyle($foreColor, $bgColor, $options);
        $this->output->getFormatter()->setStyle($styleName, $style);
    }

    protected static function format($text, $styleName)
    {
        return "<" . $styleName . ">" . $text . "</" . $styleName . ">";
    }
}
<?php

namespace Shopen\Blocks;

class Block
{
    protected static $view = null;

    public static function getView(): ?string
    {
        return self::$view;
    }
}
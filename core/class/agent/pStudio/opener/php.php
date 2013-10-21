<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2012 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

use Patchwork\Utf8 as u;

class agent_pStudio_opener_php extends agent_pStudio_opener
{
    protected $language;

    function compose($o)
    {
        isset($this->language) || $this->language = substr($this->extension, 0, -1);
        $o = parent::compose($o);
        $o->language = $this->language;
        return $o;
    }

    protected function composeReader($o)
    {
        if (is_file($this->realpath) && false !== $a = file_get_contents($this->realpath))
        {
            $a && false !== strpos($a, "\r") && $a = strtr(str_replace("\r\n", "\n", $a), "\r", "\n");
            u::isUtf8($a) || $a = u::utf8_encode($a);

            $o->code = pStudio_highlighter::highlight($a, $this->language, true);
        }

        return $o;
    }
}

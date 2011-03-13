<?php /***** vi: set encoding=utf-8 expandtab shiftwidth=4: ****************
 *
 *   Copyright : (C) 2011 Nicolas Grekas. All rights reserved.
 *   Email     : p@tchwork.org
 *   License   : http://www.gnu.org/licenses/agpl.txt GNU/AGPL
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU Affero General Public License as
 *   published by the Free Software Foundation, either version 3 of the
 *   License, or (at your option) any later version.
 *
 ***************************************************************************/


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
        $a = @file_get_contents($this->realpath);

        if (false !== $a)
        {
            $a && false !== strpos($a, "\r") && $a = strtr(str_replace("\r\n", "\n", $a), "\r", "\n");
            u::isUTF8($a) || $a = utf8_encode($a);

            $o->code = pStudio_highlighter::highlight($a, $this->language, true);
        }

        return $o;
    }
}

<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2012 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */


class pStudio_highlighter
{
    protected static $map = array(
        'conf'    => 'apache',
        'html'    => 'html4strict',
        'js'      => 'javascript',
        'sh'      => 'bash',
        'tex'     => 'latex',
        'ptl'     => 'html4strict',
        'ptl/js'  => 'javascript',
        'ptl/css' => 'css',
    );

    static function highlight($a, $language, $line_numbers)
    {
        isset(self::$map[$language]) && $language = self::$map[$language];

        $a = new geshi($a, $language);
        $a->set_encoding('UTF-8');
        $line_numbers && $a->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
        $a->set_header_type(GESHI_HEADER_DIV);
        $a->set_tab_width(4);

        return $a->parse_code();
    }
}

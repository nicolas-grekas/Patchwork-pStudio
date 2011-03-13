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

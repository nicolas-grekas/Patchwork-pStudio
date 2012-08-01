<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2012 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */


class pipe_pStudio_extension
{
    static function php($s)
    {
        if (preg_match('/(\.[^.\/]+)+$/', $s, $s))
        {
            $s = explode('.', $s[0]);
            $s = array_reverse($s);
            $s = implode('/', $s);
        }
        else $s = '';

        return strtolower($s);
    }

    static function js()
    {
        ?>/*<script>*/

function($s)
{
    if ($s = str($s).match(/(\.[^.\/]+)+$/))
    {
        $s = $s[0].split('.');
        var $a = [], $i = $s.length;
        while ($i-- > 0) $a.push($s[$i]);
        $s = $a.join('/');
    }
    else $s = '';

    return $s.toLowerCase();
}

<?php   }
}

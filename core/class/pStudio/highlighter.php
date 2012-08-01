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
    static function highlight($a, $language, $line_numbers)
    {
        $language = 'highlight_' . strtr($language, '/', '_');

        return method_exists(__CLASS__, $language)
            ? self::$language($a, $line_numbers)
            : self::highlight_txt($a, $line_numbers);
    }


    protected static function highlight_txt($a, $line_numbers)
    {
        $a = htmlspecialchars($a);
        $a = nl2br($a);

        return self::finalize($a, $line_numbers);
    }

    protected static function finalize($a, $line_numbers)
    {
        $a = str_replace("\t", '    ' , $a);
        $a = str_replace('  ' , ' &nbsp;', $a);

        if ($line_numbers)
        {
            $a = preg_replace("'^.*$'m", '<li><code>$0</code></li>', $a);
            $a = '<ol style="font-family:monospace;">' . $a . '</ol>';
        }
        else $a = '<code>' . $a . '</code>';

        return $a;
    }


    protected static $pool;

    protected static function highlight_php($a, $line_numbers)
    {
        $a = highlight_string($a, true);
        $a = substr($a, 6, -7);
        $a = str_replace('&nbsp;' , ' ', $a);

        if ($line_numbers)
        {
            $a = str_replace("\n", '', $a);
            $b = array();
            self::$pool = array();

            foreach (explode('<br>', $a) as $a)
            {
                $b[] = implode('', self::$pool)
                    . preg_replace_callback("'<(/?)span[^>]*>'", array(__CLASS__, 'pool_callback'), $a)
                    . str_repeat('</span>', count(self::$pool));
            }

            $a = implode("<br>\n", $b);
        }

        return self::finalize($a, $line_numbers);
    }

    protected static function pool_callback($m)
    {
        if ($m[1]) array_pop(self::$pool);
        else array_push(self::$pool, $m[0]);

        return $m[0];
    }
}

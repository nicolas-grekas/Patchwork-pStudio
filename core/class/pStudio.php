<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2012 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

use Patchwork as p;

class pStudio
{
    public static

    $appWhitelist = array(''),
    $appBlacklist = array(),

    $readWhitelist = array(
        '^class/',
        '^public/',
        '^example/',
        '^data/((utf8|unicode)/.*)?[^/]*$',
        '^[^/]+$',
    ),

    $readBlacklist = array(
        '(^|/)\.',
        '(^|/)zcache(/|$)',
        '(^|/)config\.patchwork\.php',
        '(^|/)error\.patchwork\.log',
        '~trashed$',
    ),

    $editWhitelist = array(),
    $editBlacklist = array();


    static function isAuthApp($path)
    {
        static $cache = array();

        isset($cache[$path]) || $cache[$path] = self::isAuth($path, self::$appWhitelist, self::$appBlacklist);

        return $cache[$path];
    }

    static function isAuthRead($path)
    {
        if ('' === $path) return true;

        static $cache = array();

        isset($cache[$path]) || $cache[$path] = self::isAuth($path, self::$readWhitelist, self::$readBlacklist);

        return $cache[$path];
    }

    static function isAuthEdit($path)
    {
        static $cache = array();

        isset($cache[$path]) || $cache[$path] = self::isAuth($path, self::$editWhitelist, self::$editBlacklist);

        return $cache[$path];
    }

    protected static function isAuth($path, $whitelist, $blacklist)
    {
        $auth = false;

        foreach ($whitelist as $rx)
        {
            if (preg_match("\"{$rx}\"uD", $path))
            {
                $auth = true;
                break;
            }
        }

        if (!$auth) return false;

        foreach ($blacklist as $rx)
        {
            if (preg_match("\"{$rx}\"uD", $path)) return false;
        }

        return true;
    }

    static function getAppname($depth)
    {
        static $appname;

        if (!isset($appname))
        {
            global $patchwork_path;

            $a = array();

            foreach ($patchwork_path as $p)
            {
                $p = explode(DIRECTORY_SEPARATOR, substr($p, 0, -1));
                $n = array_pop($p);

                while (isset($a[$n]))
                {
                    if (false !== $a[$n])
                    {
                        $a[array_pop($a[$n]) . DIRECTORY_SEPARATOR . $n] = $a[$n];
                        $a[$n] = false;
                    }

                    $n = array_pop($p) . DIRECTORY_SEPARATOR . $n;
                }

                $a[$n] = $p;
            }

            foreach ($a as $n => $p)
            {
                if (false !== $p)
                {
                    $p[] = $n . DIRECTORY_SEPARATOR;
                    $appname[implode(DIRECTORY_SEPARATOR, $p)] = $n;
                }
            }

            foreach ($patchwork_path as $n => $p)
            {
                $appname[PATCHWORK_PATH_LEVEL - $n] = $appname[$p];
                unset($appname[$p]);
            }
        }

        return isset($appname[$depth]) ? $appname[$depth] : false;
    }

    static function resetCache()
    {
        unlink(PATCHWORK_PROJECT_PATH . '.patchwork.php');
    }

    static function syncCache($file, $depth)
    {
        if (0 === strpos($file, 'public/'))
        {
            p::touch('public');
            p::updateAppId();
        }
        else
        {
            if (0 === strpos($file, 'class/Patchwork/'))
            {
                self::resetCache();
            }
            else if (0 === strpos($file, 'class/'))
            {
                $file = p\Superloader::file2cache(substr($file, 6), $depth);
                @unlink($file);
            }

            p\Debugger::purgeZcache();
        }
    }
}

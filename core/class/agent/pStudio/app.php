<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2012 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */


class agent_pStudio_app extends agent
{
    function control()
    {
    }

    function compose($o)
    {
        patchworkPath('zcache/', $o->zcacheDepth);

        $app = array();

        foreach ($GLOBALS['patchwork_path'] as $k => $v)
        {
            pStudio::isAuthApp($v) && $app[$k] = $v;
        }

        $o->apps = new loop_array($app, array($this, 'filterApp'));

        return $o;
    }

    function filterApp($o)
    {
        $depth = PATCHWORK_PATH_LEVEL - $o->KEY;

        $o = (object) array(
            'name' => pStudio::getAppname($depth),
            'depth' => $depth,
            'path' => $o->VALUE,
        );

        return $o;
    }
}

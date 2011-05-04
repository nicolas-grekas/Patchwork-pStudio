<?php /***************** vi: set fenc=utf-8 ts=4 sw=4 et: ******************
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

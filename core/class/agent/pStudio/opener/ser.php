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


class agent_pStudio_opener_ser extends agent_pStudio_opener
{
    protected function composeReader($o)
    {
        if (is_file($this->realpath) && false !== $a = file_get_contents($this->realpath))
        {
            $b = @unserialize($a);

            if (false !== $b || $a === serialize(false))
            {
                $o->text = '<?php serialize(' . var_export($b, true) . ')';
            }
        }

        return $o;
    }
}

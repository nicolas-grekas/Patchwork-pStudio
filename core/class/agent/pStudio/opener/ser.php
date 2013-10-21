<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2012 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */


class agent_pStudio_opener_ser extends agent_pStudio_opener
{
    protected function composeReader($o)
    {
        if (is_file($this->realpath) && false !== $a = file_get_contents($this->realpath))
        {
            $b = @unserialize($a);

            if (false !== $b || $a === serialize(false))
            {
                $a = '<?php serialize(' . var_export($b, true) . ')';
                u::isUtf8($a) || $a = u::utf8_encode($a);

                $o->text = $a;
            }
        }

        return $o;
    }
}

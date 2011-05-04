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


class agent_pStudio_opener_jpeg extends agent_pStudio_opener_gif
{
    protected $rawContentType = 'image/jpeg';

    protected function composeReader($o)
    {
        $o = parent::composeReader($o);

        if (extension_loaded('exif'))
        {
            $exif = exif_read_data($this->realpath);

            E($exif);
        }

        return $o;
    }
}

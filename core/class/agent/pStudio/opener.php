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


class agent_pStudio_opener extends agent_pStudio_explorer
{
    public $get = array(
        'path:c',
        'low:i' => false,
        'high:i' => PATCHWORK_PATH_LEVEL,
        'p\::c:serverside',
    );

    protected

    $extension = '',
    $editorTemplate = 'pStudio/opener';


    function control()
    {
        $a = get_class($this);

        if (0 === strpos($a, __CLASS__ . '_'))
        {
            $a = substr($a, strlen(__CLASS__) + 1);
            $a = strtr($a, '_', '/') . '/';
            $this->extension = $a;
        }

        $this->get->__0__ = $this->get->path;

        parent::control();
    }

    function compose($o)
    {
        $o->extension = $this->extension;
        $o->is_auth_edit = $this->is_auth_edit;

        $o = $o->is_auth_edit
            ? $this->composeEditor($o)
            : $this->composeReader($o);

        return $o;
    }

    protected function composeReader($o)
    {
        if (false !== $a = @file_get_contents($this->realpath))
        {
            if (preg_match('/[\x00-\x08\x0B\x0E-\x1A\x1C-\x1F]/', substr($a, 0, 512)))
            {
                $o->is_binary = true;
            }
            else
            {
                $a && false !== strpos($a, "\r") && $a = strtr(str_replace("\r\n", "\n", $a), "\r", "\n");
                u::isUTF8($a) || $a = utf8_encode($a);
                $o->text = $a;
            }
        }

        return $o;
    }

    protected function composeEditor($o)
    {
        $o = self::composeReader($o);

        if (!empty($o->is_binary))
        {
            unset($o->is_binary, $o->is_auth_edit);
            $o = $this->composeReader($o);
        }
        else
        {
            $this->editorTemplate && $this->template = $this->editorTemplate;

            $f = new pForm($o);
            $f->add('textarea', 'code', array('default' => $o->text));
            $send = $f->add('submit', 'save');
            $send->attach('code', '', '');

            if ($send->isOn())
            {
                $code = $send->getData();
                $code = $code['code'];
                if ('' !== $code && "\n" !== substr($code, -1)) $code .= "\n";

                file_put_contents($this->realpath, $code);

                pStudio::syncCache($this->path, $this->depth);
                patchwork::redirect();
            }

            unset($o->text);
        }

        return $o;
    }
}

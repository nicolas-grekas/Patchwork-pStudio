<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2012 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

use Patchwork\Utf8 as u;

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
        if (is_file($this->realpath) && false !== $a = file_get_contents($this->realpath))
        {
            if (preg_match('/[\x00-\x08\x0B\x0E-\x1A\x1C-\x1F]/', substr($a, 0, 512)))
            {
                $o->is_binary = true;
            }
            else
            {
                $a && false !== strpos($a, "\r") && $a = strtr(str_replace("\r\n", "\n", $a), "\r", "\n");
                u::isUtf8($a) || $a = utf8_encode($a);
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
                Patchwork::redirect();
            }

            unset($o->text);
        }

        return $o;
    }
}

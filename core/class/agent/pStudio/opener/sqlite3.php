<?php /***************** vi: set fenc=utf-8 ts=4 sw=4 et: ******************
 *
 *   Copyright : (C) 2012 Nicolas Grekas. All rights reserved.
 *   Email     : p@tchwork.org
 *   License   : http://www.gnu.org/licenses/agpl.txt GNU/AGPL
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU Affero General Public License as
 *   published by the Free Software Foundation, either version 3 of the
 *   License, or (at your option) any later version.
 *
 ***************************************************************************/

// TODO: factorize with agent_pStudio_opener_sqlite

class agent_pStudio_opener_sqlite extends agent_pStudio_opener
{
    public $get = array(
        'path:c',
        'low:i' => false,
        'high:i' => PATCHWORK_PATH_LEVEL,
        'sql:t',
        'start:i:0' => 0,
        'length:i:1' => 25,
    );


    function control()
    {
        $this->get->sql = trim($this->get->sql);
        parent::control();
    }

    protected function getDb($o)
    {
        static $db;

        if (!isset($db))
        {
            try
            {
                $db = new PDO('sqlite:' . $this->realpath);
            }
            catch (Exception $e)
            {
                $o->error_msg = $e->getMessage();
                return;
            }

            $db->sqliteCreateFunction('php', array(__CLASS__, 'php'));
            $db->sqliteCreateFunction('preg_match', 'preg_match', 2);
        }

        return $db;
    }

    protected function composeReader($o)
    {
        if (!$db = $this->getDb($o)) return $o;

        if ($sql = $this->get->sql)
        {
            $o->read_sql = pStudio_highlighter::highlight($sql, 'sql', false);

            if (self::isReadOnlyQuery($db, $sql, $o->error_msg))
            {
                try
                {
                    $sql = "{$sql}\n LIMIT {$this->get->start}, {$this->get->length}";
                    if ($rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC))
                    {
                        $o->fields = new loop_array(array_keys($rows[0]));
                        $o->rows = new loop_array($rows, array($this, 'filterRow'));
                        $o->start = $this->get->start;
                        $o->length = $this->get->length;
                    }
                }
                catch (Exception $e)
                {
                    $o->error_msg = $e->getMessage();
                }
            }
        }
        else
        {
            $sql = "SELECT name, type
                    FROM sqlite_master
                    WHERE type IN ('table', 'view')
                    ORDER BY name";
            $tables = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $o->tables = new loop_array($tables, 'filter_rawArray');

            if (!$o->is_auth_edit)
            {
                $f = new pForm($o, '', false);
                $f->setPrefix('');
                $f->add('hidden', 'low');
                $f->add('hidden', 'high');
                $f->add('textarea', 'sql');
            }
        }

        return $o;
    }

    protected function composeEditor($o)
    {
        if (!$this->get->sql)
        {
            $f = new pForm($o);
            $f->setPrefix('');
            $f->add('hidden', 'low');
            $f->add('hidden', 'high');
            $sql = $f->add('textarea', 'sql');

            if ($sql->isOn())
            {
                $sql = trim($sql->getValue());

                $sql || Patchwork::redirect();

                if (!$db = $this->getDb($o)) return $o;

                if (self::isReadOnlyQuery($db, $sql, $o->error_msg))
                {
                    $sql = urlencode($sql);

                    $uri = Patchwork::__URI__();
                    $uri = $uri !== strtr($uri, '?&', '--')
                        ? preg_replace("'([?&]sql=)[^&]*'", '$1' . $sql, $uri)
                        : $uri . '?sql=' . $sql;

                    Patchwork::redirect($uri);
                }

                try
                {
                    $db->exec($sql);
                }
                catch (Exception $e)
                {
                    $o->error_msg = $e->getMessage();
                }

                $o->write_sql = pStudio_highlighter::highlight($sql, 'sql', false);
            }
        }

        return $this->composeReader($o);
    }

    function filterRow($o)
    {
        $o = (object) array(
            'columns' => new loop_array($o->VALUE),
        );

        return $o;
    }


    protected static function isReadOnlyQuery($db, $sql, &$error_msg)
    {
        $sql = str_replace(';', ';EXPLAIN ', $sql);

        try
        {
            $db->exec("EXPLAIN {$sql}\n LIMIT 1");
            $db->exec("EXPLAIN SELECT 1 FROM ({$sql}\n LIMIT 1)");
            return true;
        }
        catch (Exception $e)
        {
            $error_msg = $e->getMessage();
            return false;
        }
    }

    static function php()
    {
        return '<!php() function is disabled>';
    }
}

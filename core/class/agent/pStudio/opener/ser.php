<?php /*********************************************************************
 *
 *   Copyright : (C) 2007 Nicolas Grekas. All rights reserved.
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
		$a = @file_get_contents($this->realpath);

		if (false !== $a)
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

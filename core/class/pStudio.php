<?php

class
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

	static function resetCache($file, $depth)
	{
		if (0 === strpos($file, 'public/'))
		{
			p::touch('public');
			p::updateAppId();
		}
		else
		{
			if (0 === strpos($file, 'class/patchwork/'))
			{
				unlink(PATCHWORK_PROJECT_PATH . '.patchwork.php');
			}
			else if (0 === strpos($file, 'class/'))
			{
				$file = patchwork_file2class(substr($file, 6));
				$file = patchwork_class2cache($file, $depth);
				@unlink($file);
			}

			patchwork_debugger::purgeZcache();
		}
	}
}

<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.                                   |
// | Copyright (c) 2003-2006 the Agavi Project.                                |
// | Based on the Mojavi3 MVC Framework, Copyright (c) 2003-2005 Sean Kerr.    |
// |                                                                           |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the    |
// | LICENSE file online at http://www.agavi.org/LICENSE.txt                   |
// |   vi: set noexpandtab:                                                    |
// |   Local Variables:                                                        |
// |   indent-tabs-mode: t                                                     |
// |   End:                                                                    |
// +---------------------------------------------------------------------------+

/**
 * AgaviToolkit provides basic utility methods.
 *
 * @package    agavi
 * @subpackage util
 *
 * @author     Sean Kerr <skerr@mojavi.org>
 * @copyright  (c) Authors
 * @since      0.9.0
 *
 * @version    $Id$
 */
final class AgaviToolkit
{
	/**
	 * Determine if a filesystem path is absolute.
	 *
	 * @param      path A filesystem path.
	 *
	 * @return     bool true, if the path is absolute, otherwise false.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public static function isPathAbsolute($path)
	{

		if($path[0] == '/' || $path[0] == '\\' ||
			(
				strlen($path) >= 3 && ctype_alpha($path[0]) &&
				$path[1] == ':' &&
				($path[2] == '\\' || $path[2] == '/')
			)
		) {
			return true;
		}

		return false;
	}

	/**
	 * Normalizes a path to contain only '/' as path delimiter.
	 *
	 * @param      string The path to normalize.
	 *
	 * @return     string The unified bool The mkdir return value.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function normalizePath($path)
	{
		return str_replace('\\', '/', $path);
	}

	/**
	 * Creates a directory without sucking at permissions.
	 * PHP mkdir() doesn't do what you tell it to, it takes umask into account.
	 *
	 * @param      string   The path name.
	 * @param      int      The mode. Really.
	 * @param      bool     Recursive or not.
	 * @param      resource A Context.
	 *
	 * @return     bool The mkdir return value.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function mkdir($path, $mode = 0777, $recursive = false, $context = null)
	{
		if($context !== null) {
			$retval = @mkdir($path, $mode, $recursive, $context);
		} else {
			$retval = @mkdir($path, $mode, $recursive);
		}
		if($retval) {
			chmod($path, $mode);
		}
		return $retval;
	}

	/**
	 * Returns the base for two strings (the part at the beginning of both which
	 * is equal)
	 *
	 * @param      string The base string.
	 * @param      string The string which should be compared to the base string.
	 * @param      int    The number of characters which are equal.
	 *
	 * @return     string The equal part at the beginning of both strings.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function stringBase($baseString, $compString, &$equalAmount = 0)
	{
		$equalAmount = 0;
		$base = '';
		for($i = 0; isset($baseString[$i]) && isset($compString[$i]) && $baseString[$i] == $compString[$i]; ++$i) {
			$base .= $baseString[$i];
			$equalAmount = $i;
		}
		return $base;
	}

	/**
	 * Deletes a specified path in the cache dir recursively. If a folder is given
	 * the contents of this folder and all sub-folders get erased, but not the
	 * folder itself.
	 *
	 * @param      string The path to remove
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function clearCache($path = '')
	{
		$ignores = array('.', '..', '.svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr');
		$path = str_replace('/', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $path));
		$path = realpath(AgaviConfig::get('core.cache_dir') . DIRECTORY_SEPARATOR . $path);
		if($path === false) {
			return false;
		}
		if(is_file($path)) {
			@unlink($path);
		} else {
			foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST) as $iterator) {
				// omg, thanks spl for always using forward slashes ... even on windows
				$pathname = str_replace('/', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $iterator->getPathname()));
				$continue = false;
				if(in_array($iterator->getFilename(), $ignores)) {
					$continue = true;
				} else {
					foreach($ignores as $ignore) {
						if(strpos($pathname, DIRECTORY_SEPARATOR . $ignore . DIRECTORY_SEPARATOR) !== false) {
							$continue = true;
							break;
						} elseif(strrpos($pathname, DIRECTORY_SEPARATOR . $ignore) == (strlen($pathname) - strlen(DIRECTORY_SEPARATOR . $ignore))) {
							// if we hit the directory itself it wont include a trailing /
							$continue = true;
							break;
						}
					}
				}
				if($continue) {
					continue;
				}
				if($iterator->isDir()) {
					@rmdir($pathname);
				} elseif($iterator->isFile()) {
					@unlink($pathname);
				}
			}
		}
	}

	/**
	 * Returns the method from the given definition list matching the given
	 * parameters.
	 *
	 * @param      array  The definitions of the functions.
	 * @param      array  The parameters which were passed to the function.
	 *
	 * @return     string The name of the function which matched.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function overloadHelper(array $definitions, array $parameters)
	{
		$countedDefs = array();
		foreach($definitions as $def) {
			$countedDefs[count($def['parameters'])][] = $def;
		}

		$paramCount = count($parameters);
		if(!isset($countedDefs[$paramCount])) {
			throw new AgaviException('overloadhelper couldn\'t find a matching method with the parameter count ' . $paramCount);
		}
		if(count($countedDefs[$paramCount]) > 1) {
			$matchCount = 0;
			$matchIndex = null;
			foreach($countedDefs[$paramCount] as $key => $paramDef) {
				$success = true;
				for($i = 0; $i < $paramCount; ++$i) {
					if(substr(gettype($parameters[$i]), 0, strlen($paramDef['parameters'][$i])) != $paramDef['parameters'][$i]) {
						$success = false;
						break;
					}
				}
				if($success) {
					++$matchCount;
					$matchIndex = $key;
				}
			}
			if($matchCount == 0) {
				throw new AgaviException('overloadhelper couldn\'t find a matching method');
			} elseif($matchCount > 1) {
				throw new AgaviException('overloadhelper found ' . $matchCount . ' matching methods');
			}
			return $countedDefs[$paramCount][$key]['name'];
		} else {
			return $countedDefs[$paramCount][0]['name'];
		}
	}

	/**
	 * This function takes the numerator and divides it thru the denominator while
	 * storing the remainder and returning the quotient.
	 *
	 * @param      float The numerator.
	 * @param      int   The denominator.
	 * @param      int   The remainder.
	 *
	 * @return     int   The floored quotient.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function floorDivide($numerator, $denominator, &$remainder)
	{
		if(intval($denominator) != $denominator) {
			throw new AgaviException('AgaviToolkit::floorDivive works only for int denominators');
		}
		$quotient = floor($numerator / $denominator);
		$remainder = (int) ($numerator - ($quotient * $denominator));

		return $quotient;
	}
}

?>
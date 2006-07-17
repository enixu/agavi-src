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
 * AgaviDatabaseConfigHandler allows you to setup database connections in a
 * configuration file that will be created for you automatically upon first
 * request.
 *
 * @package    agavi
 * @subpackage config
 *
 * @author     Sean Kerr <skerr@mojavi.org>
 * @author     Dominik del Bondio <ddb@bitxtender.com>
 * @copyright  (c) Authors
 * @since      0.9.0
 *
 * @version    $Id$
 */
class AgaviDatabaseConfigHandler extends AgaviConfigHandler
{

	/**
	 * Execute this configuration handler.
	 *
	 * @param      string An absolute filesystem path to a configuration file.
	 *
	 * @return     string Data to be written to a cache file.
	 *
	 * @throws     <b>AgaviUnreadableException</b> If a requested configuration file
	 *                                             does not exist or is not readable.
	 * @throws     <b>AgaviParseException</b> If a requested configuration file is
	 *                                        improperly formatted.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.9.0
	 */
	public function execute($config, $context = null)
	{
		// parse the config file
		$configurations = $this->orderConfigurations(AgaviConfigCache::parseConfig($config, false, $this->getValidationFile())->configurations, AgaviConfig::get('core.environment'), $context);

		$databases = array();
		$default = null;
		foreach($configurations as $cfg) {
			// make sure we have a default database exists
			if(!$cfg->databases->hasAttribute('default') && $default === null) {
				// missing default database
				$error = 'Configuration file "%s" must specify a default database configuration';
				$error = sprintf($error, $config);

				throw new AgaviParseException($error);
			}
			$default = $cfg->databases->getAttribute('default');

			// let's do our fancy work
			foreach($cfg->databases as $db) {
				$name = $db->getAttribute('name');

				if(!isset($databases[$name])) {
					$databases[$name] = array('params' => array(), 'file' => null);

					if(!$db->hasAttribute('class')) {
						$error = 'Configuration file "%s" specifies category "%s" with missing class key';
						$error = sprintf($error, $config, $category);

						throw new AgaviParseException($error);
					}
				}

				$databases[$name]['class'] = $db->hasAttribute('class') ? $db->getAttribute('class') : $databases[$name]['class'];
				$databases[$name]['file'] = $db->hasAttribute('file') ? $db->getAttribute('file') : $databases[$name]['file'];

				$databases[$name]['params'] = $this->getItemParameters($db, $databases[$name]['params']);
			}
		}

		$data = array();
		$includes = array();

		foreach($databases as $name => $db) {

			if($db['file'] !== null) {
				// we have a file to include
				$file =  $db['file'];
				$file =  $this->replaceConstants($file);
				$file =  $this->replacePath($file);
				$file =  realpath($file);

				if(!is_readable($file)) {
					// database file doesn't exist
					$error = 'Configuration file "%s" specifies class "%s" with nonexistent or unreadable file "%s"';
					$error = sprintf($error, $config, $db['class'], $file);

					throw new AgaviParseException($error);
				}

				$tmp        = "require_once(%s);";
				$includes[]  = sprintf($tmp, var_export($file, true));
			}


			// append new data
			$tmp = "\$database = new %s();\n" .
							"\$database->initialize(\$this, %s);\n" .
							"\$this->databases[%s] = \$database;";

			$data[] = sprintf($tmp, $db['class'], var_export($db['params'], true), var_export($name, true));

		}

		$data[] = sprintf("\$this->databases['default'] = \$this->databases[%s];", var_export($default, true));

		// compile data
		$retval = "<?php\n" .
				"// auto-generated by ".__CLASS__."\n" .
				"// date: %s GMT\n%s\n%s\n?>";

		$retval = sprintf($retval, gmdate('m/d/Y H:i:s'), implode("\n", $includes), implode("\n", $data));

		return $retval;
	}
}

?>
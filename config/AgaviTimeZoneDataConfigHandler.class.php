<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.                                   |
// | Copyright (c) 2003-2007 the Agavi Project.                                |
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
 * AgaviTimeZoneDataConfigHandler
 *
 * @package    agavi
 * @subpackage config
 *
 * @author     Dominik del Bondio <ddb@bitxtender.com>
 * @copyright  Authors
 * @copyright  The Agavi Project
 *
 * @since      0.11.0
 *
 * @version    $Id$
 */
class AgaviTimeZoneDataConfigHandler extends AgaviConfigHandler
{
	/**
	 * Execute this configuration handler.
	 *
	 * @param      string An absolute filesystem path to a configuration file.
	 * @param      AgaviContext An optional context.
	 *
	 * @return     string Data to be written to a cache file.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public function execute($config, $context = null)
	{
		if($context === null) {
			throw new AgaviException('this config handler needs a context specified!');
		}
		
		$parserClass = $this->parser;
		$parser = new $parserClass();
		$parser->initialize(AgaviContext::getInstance($context));

		//$tzData = AgaviConfigCache::parseConfig($config, false, $this->getValidationFile(), $this->parser);
		$tzData = $parser->parse($config);

		$data = array();
		$data[] = 'return ' . var_export($tzData, true) . ';';

		return $this->generate($data);
	}
}

?>
<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.                                   |
// | Copyright (c) 2003-2007 the Agavi Project.                                |
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
 * AgaviReturnArrayConfigHandler allows you to retrieve the contents of a config
 * file as an array
 *
 * @package    agavi
 * @subpackage config
 *
 * @author     David Zülke <dz@bitxtender.com>
 * @copyright  Authors
 * @copyright  The Agavi Project
 *
 * @since      0.10.0
 *
 * @version    $Id$
 */

class AgaviReturnArrayConfigHandler extends AgaviConfigHandler
{
	/**
	 * @see        AgaviIniConfigHandler::execute()
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      0.10.0
	 */
	public function execute($config, $context = null)
	{
		$configurations = $this->orderConfigurations(AgaviConfigCache::parseConfig($config, false, $this->getValidationFile(), $this->parser)->configurations, AgaviConfig::get('core.environment'), $context);
		$data = array();
		foreach($configurations as $cfg) {
			$data = array_merge($data, $this->convertToArray($cfg));
		}
		if(isset($data['environment'])) {
			unset($data['environment']);
		}
		if(isset($data['context'])) {
			unset($data['context']);
		}

		// compile data
		$code = "return " . var_export($data, true) . ";";

		return $this->generate($code);
	}

	/**
	 * Converts an AgaviConfigValueHolder into an array.
	 *
	 * @param      AgaviConfigValueHolder The config value to convert.
	 *
	 * @return     array The config values as an array.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	protected function convertToArray(AgaviConfigValueHolder $item)
	{
		$singularParentName = AgaviInflector::singularize($item->getName());

		$data = array();

		if(!$item->hasChildren()) {
			$data = $item->getValue();
		} else {
			foreach($item->getChildren() as $key => $child) {
				if((is_int($key) || $key == $singularParentName) && !$child->hasAttribute('name')) {
					$data[] = $this->convertToArray($child);
				} else {
					$name = $child->hasAttribute('name') ? $child->getAttribute('name') : $child->getName();
					$data[$name] = $this->convertToArray($child);
				}
			}
		}

		foreach($item->getAttributes() as $name => $value) {
			if(!isset($data[$name])) {
				$data[$name] = $this->literalize($value);
			}
		}
		return $data;
	}
}
?>
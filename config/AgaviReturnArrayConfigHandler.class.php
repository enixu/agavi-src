<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.                                   |
// | Copyright (c) 2003-2006 the Agavi Project.                                |
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
 * @author     David Zuelke <dz@bitxtender.com>
 * @copyright  (c) Authors
 * @since      0.10.0
 *
 * @version    $Id$
 */

class AgaviReturnArrayConfigHandler extends AgaviConfigHandler
{
	/**
	 * @see        AgaviIniConfigHandler::execute()
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.10.0
	 */
	public function execute($config, $context = null)
	{
		$configurations = $this->orderConfigurations(AgaviConfigCache::parseConfig($config, false, $this->getValidationFile())->configurations, AgaviConfig::get('core.environment'), $context);
		$data = array();
		foreach($configurations as $cfg) {
			$data = array_merge_recursive($data, $this->convertToArray($cfg));
		}
		if(isset($data['environment'])) {
			unset($data['environment']);
		}
		if(isset($data['context'])) {
			unset($data['context']);
		}

		$return = "<?php return " . var_export($data, true) . ";?>";
		return $return;

	}

	protected function convertToArray($item, $append = false)
	{
		$data = array();

		if(!$item->hasChildren()) {
			$data = $item->getValue();
		} else {
			foreach($item->getChildren() as $key => $child) {
				if(is_int($key) && !$child->hasAttribute('name')) {
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
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
 * AgaviRotatingFileAppender extends AgaviFileAppender by enabling per-day log files
 * and removing unwanted old log files.
 *
 * <b>Required parameters:</b>
 *
 * # <b>dir</b>    - [none]              - Log directory
 *
 * <b>Optional parameters:</b>
 *
 * # <b>cycle</b>  - [7]                 - Number of log files to keep.
 * # <b>prefix</b> - [core.webapp_name-] - Log filename prefix.
 * # <b>suffix</b> - [.log]              - Log filename suffix.
 *
 * @package    agavi
 * @subpackage logging
 *
 * @author     Veikko Makinen <mail@veikkomakinen.com>
 * @copyright  (c) Authors
 * @since      0.11.0
 *
 * @version    $Id$
 */
class AgaviRotatingFileAppender extends AgaviFileAppender
{

	public function initialize($params = array())
	{
		$cycle = 7;
		$prefix = str_replace(' ', '_', AgaviConfig::get('core.webapp_name')).'-';
		$suffix = '.log';

		if(!isset($params['dir'])) {
			throw new AgaviLoggingException('No directory defined for rotating logging.');
		}

		$dir = $params['dir'];

		if(isset($params['cycle'])) {
			$cycle = $params['cycle'];
		}

		if(isset($params['prefix'])) {
			$prefix = $params['prefix'];
		}

		if(isset($params['suffix'])) {
			$suffix = $params['suffix'];
		}

		$logfile = $dir . $prefix . date('Y-m-d') . $suffix;

		if (!file_exists($logfile)) {

			// todays log file didn't exist so we need to create it
			// and at the same time we'll remove all unwanted history files

			$files = array();
			foreach (glob($dir . $prefix . '*-*-*' . $suffix) as $filename) {
				$files[] = $filename;
			}

			while (count($files) > $cycle-1) { //-1 because todays file is not yet created
				unlink(array_shift($files));
			}

		}

		//it's all up to the parent after this
		$params['file'] = $logfile;
		parent::initialize($params);
	}

}

?>
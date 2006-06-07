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
 * AgaviStdoutAppender appends an AgaviMessage to stdout.
 *
 * @package    agavi
 * @subpackage logging
 *
 * @author     Bob Zoller <bob@agavi.org>
 * @copyright  (c) Authors
 * @since      0.10.0
 *
 * @version    $Id$
 */
class AgaviStdoutAppender extends AgaviFileAppender
{

	/**
	 * Initialize the object.
	 * 
	 * @param      array An array of parameters.
	 * 
	 * @author     Bob Zoller <bob@agavi.org>
	 * @since      0.10.0
	 */
	public function initialize($params = array())
	{
		$params['file'] = 'php://stdout';
		parent::initialize($params);
	}

}

?>
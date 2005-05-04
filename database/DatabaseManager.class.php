<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.                                   |
// | Copyright (c) 2003, 2004 Agavi Foundation.                                |
// |                                                                           |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the    |
// | LICENSE file online at http://www.agavi.org.                              |
// +---------------------------------------------------------------------------+

/**
 * DatabaseManager allows you to setup your database connectivity before the
 * request is handled. This eliminates the need for a filter to manage database
 * connections.
 *
 * @package    agavi
 * @subpackage database
 *
 * @author    Agavi Foundation (info@agavi.org)
 * @copyright (c) Agavi Foundation, {@link http://www.agavi.org}
 * @since     3.0.0
 * @version   $Id$
 */
class DatabaseManager extends AgaviObject
{

	// +-----------------------------------------------------------------------+
	// | PRIVATE DATA                                                          |
	// +-----------------------------------------------------------------------+

	private
		$databases = array();

	// +-----------------------------------------------------------------------+
	// | METHODS                                                               |
	// +-----------------------------------------------------------------------+

	/**
	 * Retrieve the database connection associated with this Database
	 * implementation.
	 *
	 * @param string A database name.
	 *
	 * @return mixed A Database instance.
	 *
	 * @throws <b>DatabaseException</b> If the requested database name does
	 *                                  not exist.
	 */
	public function getDatabase ($name = 'default')
	{

		if (isset($this->databases[$name]))
		{

			return $this->databases[$name];

		}

		// nonexistent database name
		$error = 'Database "%s" does not exist';
		$error = sprintf($error, $name);

		throw new DatabaseException($error);

	}

	// -------------------------------------------------------------------------

	/**
	 * Initialize this DatabaseManager.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *              false.
	 *
	 * @throws <b>InitializationException</b> If an error occurs while
	 *                                        initializing this DatabaseManager.
	 *
	 * @author Agavi Foundation (info@agavi.org)
	 * @since  3.0.0
	 */
	public function initialize ()
	{

		// load database configuration
		require_once(ConfigCache::checkConfig('config/databases.ini'));

	}

	// -------------------------------------------------------------------------

	/**
	 * Execute the shutdown procedure.
	 *
	 * @return void
	 *
	 * @throws <b>DatabaseException</b> If an error occurs while shutting down
	 *                                 this DatabaseManager.
	 *
	 * @author Agavi Foundation (info@agavi.org)
	 * @since  3.0.0
	 */
	public function shutdown ()
	{

		// loop through databases and shutdown connections
		foreach ($this->databases as $database)
		{

			$database->shutdown();

		}

	}

}

?>

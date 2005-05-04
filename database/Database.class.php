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
 * Database is a base abstraction class that allows you to setup any type of
 * database connection via a configuration file.
 *
 * @package    agavi
 * @subpackage database
 *
 * @author    Agavi Foundation (info@agavi.org)
 * @copyright (c) Agavi Foundation, {@link http://www.agavi.org}
 * @since     3.0.0
 * @version   $Id$
 */
abstract class Database extends ParameterHolder
{

	// +-----------------------------------------------------------------------+
	// | PROTECTED VARIABLES                                                   |
	// +-----------------------------------------------------------------------+

	protected
		$connection = null,
		$resource   = null;

	// +-----------------------------------------------------------------------+
	// | METHODS                                                               |
	// +-----------------------------------------------------------------------+

	/**
	 * Connect to the database.
	 *
	 * @throws <b>DatabaseException</b> If a connection could not be created.
	 *
	 * @author Agavi Foundation (info@agavi.org)
	 * @since  3.0.0
	 */
	abstract function connect ();

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the database connection associated with this Database
	 * implementation.
	 *
	 * When this is executed on a Database implementation that isn't an
	 * abstraction layer, a copy of the resource will be returned.
	 *
	 * @return mixed A database connection.
	 *
	 * @throws <b>DatabaseException</b> If a connection could not be retrieved.
	 */
	public function getConnection ()
	{

		if ($this->connection == null)
		{

			$this->connect();

		}

		return $this->connection;

	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve a raw database resource associated with this Database
	 * implementation.
	 *
	 * @return mixed A database resource.
	 *
	 * @throws <b>DatabaseException</b> If a resource could not be retrieved.
	 */
	public function getResource ()
	{

		if ($this->resource == null)
		{

			$this->connect();

		}

		return $this->resource;

	}

	// -------------------------------------------------------------------------

	/**
	 * Initialize this Database.
	 *
	 * @param array An associative array of initialization parameters.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *              false.
	 *
	 * @throws <b>InitializationException</b> If an error occurs while
	 *                                        initializing this Database.
	 *
	 * @author Agavi Foundation (info@agavi.org)
	 * @since  3.0.0
	 */
	public function initialize ($parameters = null)
	{

		if ($parameters != null)
		{

			$this->parameters = array_merge($this->parameters, $parameters);

		}

	}

	// -------------------------------------------------------------------------

	/**
	 * Execute the shutdown procedure.
	 *
	 * @return void
	 *
	 * @throws <b>DatabaseException</b> If an error occurs while shutting down
	 *                                 this database.
	 *
	 * @author Agavi Foundation (info@agavi.org)
	 * @since  3.0.0
	 */
	abstract function shutdown ();

}

?>

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
 * PDODatabase provides connectivity for the PDO database abstraction layer.
 *
 * @package    agavi
 * @subpackage database
 *
 * @author     Daniel Swarbrick (daniel@pressure.net.nz)
 * @author     Agavi Project <info@agavi.org>
 * @copyright  (c) Authors
 * @since      0.9.0
 *
 * @version    $Id$
 */
class PDODatabase extends Database
{

	/**
	 * Connect to the database.
	 *
	 * @throws     <b>DatabaseException</b> If a connection could not be created
	 *
	 * @author     Daniel Swarbrick (daniel@pressure.net.nz)
	 * @since      0.9.0
	 */
	public function connect ()
	{

		// determine how to get our parameters
		$method = $this->getParameter('method', 'dsn');

		// get parameters
		switch ($method) {

			case 'dsn' :

				$dsn = $this->getParameter('dsn');

				if ($dsn == null) {

					// missing required dsn parameter
					$error = 'Database configuration specifies method ' .
						 '"dsn", but is missing dsn parameter';

					throw new DatabaseException($error);

				}

				break;

		}

		try	{

			$pdo_username = $this->getParameter('username');
			$pdo_password = $this->getParameter('password');
			$this->connection = new PDO($dsn, $pdo_username, $pdo_password);

		} catch (PDOException $e)	{

			throw new DatabaseException($e->getMessage());

		}

		// lets generate exceptions instead of silent failures
		if (defined('PDO::ATTR_ERRMODE')) {
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} else {
			$this->connection->setAttribute(PDO_ATTR_ERRMODE, PDO_ERRMODE_EXCEPTION);
		}

	}

	/**
	 * Execute the shutdown procedure.
	 *
	 * @return     void
	 *
	 * @throws     <b>DatabaseException</b> If an error occurs while shutting
	 *                                      down this database.
	 *
	 * @author     Daniel Swarbrick (daniel@pressure.net.nz)
	 * @since      0.9.0
	 */
	public function shutdown ()
	{

		if ($this->connection !== null)	{

			@$this->connection = null;

		}

	}

}
?>
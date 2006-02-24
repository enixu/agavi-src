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
 * Context provides information about the current application context, such as
 * the module and action names and the module directory. 
 * It also serves as a gateway to the core pieces of the framework, allowing
 * objects with access to the context, to access other useful objects such as
 * the current controller, request, user, actionstack, databasemanager, storage,
 * and loggingmanager.
 *
 * @package    agavi
 * @subpackage core
 *
 * @author     Sean Kerr <skerr@mojavi.org>
 * @author     Mike Vincent <mike@agavi.org>
 * @copyright  (c) Authors
 * @since      0.9.0
 *
 * @version    $Id$
 */
class Context
{

	protected
		$actionStack      = null,
		$controller       = null,
		$databaseManager  = null,
		$loggerManager    = null,
		$request          = null,
		$securityFilter   = null,
		$storage          = null,
		$user             = null,
		$validatorManager = null;
	protected static
		$instances       = null,
		$profiles        = array();

	/*
	 * Clone method, overridden to prevent cloning, there can be only one. 
	 *
	 * @author     Mike Vincent <mike@agavi.org>	
	 * @since      0.9.0
	 */
	public function __clone()
	{
		trigger_error('Cloning the Context object is not allowed.', E_USER_ERROR);
	}	

	// -------------------------------------------------------------------------
	
	/*
	 * Constuctor method, intentionally made private so the context cannot be 
	 * created directly.
	 *
	 * @author     Mike Vincent <mike@agavi.org>	
	 * @since      0.9.0
	 */
	protected function __construct() 
	{
		// Singleton, use Context::getInstance($controller) to get the instance
	}

	// -------------------------------------------------------------------------
	
	/**
	 * Retrieve the action name for this context.
	 *
	 * @return     string The currently executing action name, if one is set,
	 *                    otherwise null.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getActionName ()
	{

		// get the last action stack entry
		$actionEntry = $this->actionStack->getLastEntry();

		return $actionEntry->getActionName();

	}

	// -------------------------------------------------------------------------
	
	/**
	 * Retrieve the ActionStack.
	 *
	 * @return     ActionStack the ActionStack instance
	 *
	 * @author     Mike Vincent <mike@agavi.org>
	 * @since      0.9.0
	 */
	public function getActionStack()
	{
		return $this->actionStack;
	}

	/**
	 * Retrieve the controller.
	 *
	 * @return     Controller The current Controller implementation instance.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getController ()
	{

		return $this->controller;

	}

	/**
	 * Retrieve a database connection from the database manager.
	 *
	 * This is a shortcut to manually getting a connection from an existing
	 * database implementation instance.
	 *
	 * If the AG_USE_DATABASE setting is off, this will return null.
	 *
	 * @param      name A database name.
	 *
	 * @return     mixed A Database instance.
	 *
	 * @throws     <b>DatabaseException</b> If the requested database name does
	 *                                      not exist.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getDatabaseConnection ($name = 'default')
	{

		if ($this->databaseManager != null)
		{

			return $this->databaseManager->getDatabase($name)->getConnection();

		}

		return null;

	}

	/**
	 * Retrieve the database manager.
	 *
	 * @return     DatabaseManager The current DatabaseManager instance.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getDatabaseManager ()
	{

		return $this->databaseManager;

	}

	/**
	 * Retrieve the Context instance.
	 *
	 * @param      string name corresponding to a section of the config
	 *
	 * @return     Context instance of the requested name
	 *
	 * @author     Mike Vincent <mike@agavi.org>
	 * @since      0.9.0
	 */
	public static function getInstance($profile = 'default')
	{
		$profile = strtolower($profile);
		if (!isset(self::$instances[$profile])) {
			$class = __CLASS__;
			self::$instances[$profile] = new $class;
			self::$instances[$profile]->initialize($profile);
		}
		return self::$instances[$profile];
	}
	
	/**
	 * Retrieve the LoggerManager
	 *
	 * @return     LoggerManager The current LoggerManager implementation instance
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function getLoggerManager()
	{
		return $this->loggerManager;
	}

	/**
	 * (re)Initialize the Context instance.
	 *
	 * @param      string name corresponding to a section of the config
	 * @param      array overrides, key => class
	 *
	 * @return     Context instance
	 *
	 * @author     Mike Vincent <mike@agavi.org>
	 * @since      0.10.0
	 */
	public function initialize($profile = 'default', $overrides = array())
	{
		static $profiles;
		$profile = strtolower($profile);
		
		if (!$profiles) {
			$profiles = array_change_key_case(include(ConfigCache::checkConfig('config/contexts.ini')), CASE_LOWER);
			$default = $profiles['contexts']['default'];
			if ($default && isset($profiles['default']) && $default != 'default') {
				$error = 'You have a specified "'.$default.'" should be the default Context, ' 
							 . 'but you also have a section named "default".';
				throw new ConfigurationException("Invalid or undefined Context name ($profile).");
			} else if ($default && !isset($profiles['default'])) {
				$profiles['default'] =& $profiles[$default];
			}
			
			// fix default references to Context instance
			if ($profile == 'default' && $profile != $default) {
				// we're working with the 'default' Context instance, 
				// and our default profile isnt named 'default', make a reference 
				self::$instances[$default] =& self::$instances[$profile];
			} else if ($profile != 'default' && $profile == $default) {
				// we asked for the default Context by it's name, make a reference
				self::$instances['default'] =& self::$instances[$profile];
			}
		}
		
		if (isset($profiles[$profile])) {
			$params = array_merge($profiles[$profile], array_change_key_case((array) $overrides, CASE_LOWER));
		} else {
			throw new ConfigurationException("Invalid or undefined Context name ($profile).");
		}
		
		$required = array();
		if (AG_USE_DATABASE) {
			$required[] = 'database_manager';
		}
		// they have to be in this order; this is also why we're using array_merge()
		$required = array_merge($required, array('action_stack', 'request', 'storage', 'controller', 'execution_filter', 'validator_manager'));
		if (AG_USE_SECURITY) {
			$required[] = 'user';
			$required[] = 'security_filter';
		}
		if (defined('AG_USE_LOGGING') && AG_USE_LOGGING) {
			$required[] = 'logger_manager';
		}


		if ($missing = array_diff($required, array_keys($params))) {
			throw new ConfigurationException("Missing required definition(s) (".implode(', ',$missing).") in [$profile] section of contexts.ini");
		}
	
		foreach ($required as $req) {	
			$args = $class = null;
			$class = $params[$req];
			$args = isset($params[$req .'.param']) ? $params[$req . '.param'] : null;
			switch ($req) {
				case 'action_stack':
					$this->actionStack = new $class(); 
					break;
				case 'database_manager':
					$this->databaseManager = new $class();
					$this->databaseManager->initialize($this);
					break;
				case 'request':
					$this->request = Request::newInstance($class);
					break;
				case 'storage':
					$this->storage = Storage::newInstance($class);
					$this->storage->initialize($this, $args);
					$this->storage->startup();
					break;
				case 'user':
					$this->user = User::newInstance($class);
					$this->user->initialize($this, $args);
					break;
				case 'security_filter':
					$this->securityFilter = SecurityFilter::newInstance($class);
					$this->securityFilter->initialize($this, $args);
					break;
				case 'logger_manager':
					$this->loggerManager = new $class();
					$this->loggerManager->initialize($this);
					break;
				case 'validator_manager':
					$this->validatorManager = new $class();
					$this->validatorManager->initialize($this);
					break;
			}
		}
		$this->controller = Controller::newInstance($params['controller']);
		$args = isset($params['controller.param']) ? $params['controller.param'] : null;
		$this->controller->initialize($this, $args);
		$this->controller->setExecutionFilterClassName($params['execution_filter']); 
		$args = isset($params['request.param']) ? $params['request.param'] : null;
		$this->request->initialize($this, $args);
		
		return $this;
	}
	
	// We could even add a method to switch contexts on the fly..
	

	/**
	 * Retrieve the module directory for this context.
	 *
	 * @return     string An absolute filesystem path to the directory of the
	 *                    currently executing module if set, otherwise null.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getModuleDirectory ()
	{

		// get the last action stack entry
		$actionEntry = $this->actionStack->getLastEntry();

		return AG_MODULE_DIR . '/' . $actionEntry->getModuleName();

	}

	/**
	 * Retrieve the module name for this context.
	 *
	 * @return     string The currently executing module name, if one is set,
	 *                    otherwise null.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getModuleName ()
	{

		// get the last action stack entry
		$actionEntry = $this->actionStack->getLastEntry();

		return $actionEntry->getModuleName();

	}

	/**
	 * Retrieve the request.
	 *
	 * @return     Request The current Request implementation instance.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getRequest ()
	{

		return $this->request;

	}

	/**
	 * Retrieve the securityFilter
	 *
	 * @return     SecurityFilter The current SecurityFilter implementation 
	 *                            instance.
	 *
	 * @author     Mike Vincent <mike@agavi.org>
	 * @since      0.9.0
	 */
	public function getSecurityFilter ()
	{

		return $this->securityFilter;

	}

	/**
	 * Retrieve the storage.
	 *
	 * @return     Storage The current Storage implementation instance.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getStorage ()
	{

		return $this->storage;

	}

	/**
	 * Retrieve the user.
	 *
	 * @return     User The current User implementation instance.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getUser ()
	{

		return $this->user;

	}
	
	/**
	 * Retrieve the ValidatorManager
	 *
	 * @return     ValidatorManager The current ValidatorManager implementation
	 *                              instance.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function getValidatorManager()
	{
		return $this->validatorManager;
	}


}

?>
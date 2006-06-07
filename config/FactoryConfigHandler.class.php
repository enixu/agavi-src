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
 * AgaviFactoryConfigHandler allows you to specify which factory implementation 
 * the system will use.
 *
 * @package    agavi
 * @subpackage config
 *
 * @author     David Zuelke <dz@bitxtender.com>
 * @author     Dominik del Bondio <ddb@bitxtender.com>
 * @copyright  (c) Authors
 * @since      0.9.0
 *
 * @version    $Id$
 */
class AgaviFactoryConfigHandler extends AgaviConfigHandler
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
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.9.0
	 */
	public function execute($config, $context = null)
	{
		if($context == null) {
			$context = '';
		}

		// parse the config file
		$configurations = $this->orderConfigurations(AgaviConfigCache::parseConfig($config, true, $this->getValidationFile())->configurations, AgaviConfig::get('core.environment'), $context);
		
		$data = array();
		foreach($configurations as $cfg) {
			// Class names for ActionStack, DispatchFilter, ExecutionFilter, FilterChain and SecurityFilter
			if(isset($cfg->action_stack)) {
				$data['action_stack'] = '$this->factories["action_stack"] = array("class" => "' . $cfg->action_stack->getAttribute('class') . '", "parameters" => ' . var_export($this->getItemParameters($cfg->action_stack), true) . ');';
			}
			if(isset($cfg->dispatch_filter)) {
				$data['dispatch_filter'] = '$this->factories["dispatch_filter"] = array("class" => "' . $cfg->dispatch_filter->getAttribute('class') . '", "parameters" => ' . var_export($this->getItemParameters($cfg->dispatch_filter), true) . ');';
			}
			if(isset($cfg->execution_filter)) {
				$data['execution_filter'] = '$this->factories["execution_filter"] = array("class" => "' . $cfg->execution_filter->getAttribute('class') . '", "parameters" => ' . var_export($this->getItemParameters($cfg->execution_filter), true) . ');';
			}
			if(isset($cfg->filter_chain)) {
				$data['filter_chain'] = '$this->factories["filter_chain"] = array("class" => "' . $cfg->filter_chain->getAttribute('class') . '", "parameters" => ' . var_export($this->getItemParameters($cfg->filter_chain), true) . ');';
			}
			if(isset($cfg->security_filter)) {
				$data['security_filter'] = '$this->factories["security_filter"] = array("class" => "' . $cfg->security_filter->getAttribute('class') . '", "parameters" => ' . var_export($this->getItemParameters($cfg->security_filter), true) . ');';
			}

			// Database
			if(AgaviConfig::get('core.use_database', false) && isset($cfg->database_manager)) {
				$data['database_manager'] = '$this->databaseManager = new ' . $cfg->database_manager->getAttribute('class') . '();' . "\n" .
																		'$this->databaseManager->initialize($this, ' . var_export($this->getItemParameters($cfg->database_manager), true) . ');';
			}

			// Request
			if(isset($cfg->request)) {
				$data['request'] = '$this->request = new ' . $cfg->request->getAttribute('class') . '();' . "\n" . 
														'$this->request->initialize($this, ' . var_export($this->getItemParameters($cfg->request), true) . ');';
			}

			// Storage
			if(isset($cfg->storage)) {
				$data['storage'] = '$this->storage = new ' . $cfg->storage->getAttribute('class') . '();' . "\n" .
														'$this->storage->initialize($this, ' . var_export($this->getItemParameters($cfg->storage), true) . ');' . "\n" .
														'$this->storage->startup();';
			}

			// ValidatorManager
			if(isset($cfg->validator_manager)) {
				$data['validator_manager'] = '$this->validatorManager = new ' . $cfg->validator_manager->getAttribute('class') . '();' . "\n" .
																			'$this->validatorManager->initialize($this, ' . var_export($this->getItemParameters($cfg->validator_manager), true) . ');';
			}

			// User
			if(AgaviConfig::get('core.use_security', true) && isset($cfg->user)) {
				$data['user'] = '$this->user = new ' . $cfg->user->getAttribute('class') . '();' . "\n" .
												'$this->user->initialize($this, ' . var_export($this->getItemParameters($cfg->user), true) . ');';
			}

			// LoggerManager
			if(AgaviConfig::get('core.use_logging', false) && isset($cfg->logger_manager)) {
				$data['logger_manager'] = '$this->loggerManager = new ' . $cfg->logger_manager->getAttribute('class') . '();' . "\n" .
																	'$this->loggerManager->initialize($this, ' . var_export($this->getItemParameters($cfg->logger_manager), true) . ');';

			}

			// Controller 
			if(isset($cfg->controller)) {
				$data['controller'] = '$this->controller = new ' . $cfg->controller->getAttribute('class') . '();' . "\n" .
															'$this->controller->initialize($this, ' . var_export($this->getItemParameters($cfg->controller), true) . ');';
			}
				
		
			if(isset($cfg->routing)) {
				// Routing
				$data['routing'] = '$this->routing = new ' . $cfg->routing->getAttribute('class') . '();' . "\n" .
														'$this->routing->initialize($this, ' . var_export($this->getItemParameters($cfg->routing), true) . ');' . "\n";
			}
		}

		// The order of this initialisiation code is fixed, to not change

		// name => required?
		$requiredItems = array('dispatch_filter' => true, 'execution_filter' => true, 'filter_chain' => true, 'security_filter' => false, 'database_manager' => false, 'action_stack' => true, 
					'storage' => true, 'validator_manager' => true, 'user' => false, 'logger_manager' => false, 'controller' => true, 'request' => true, 'routing' => false);

		$code = '';

		foreach($requiredItems as $item => $required) {
			if($required && !isset($data[$item])) {
				$error = 'Configuration file "%s" is missing an entry for %s in the current configuration';
				$error = sprintf($error, $config, $item);
				throw new AgaviParseException($error);
			}

			if(isset($data[$item])) {
				$code .= $data[$item] . "\n";
			}
		}

		// compile data
		$retval = "<?php\n" .
		"// auto-generated by FactoryConfigHandler\n" .
		"// date: %s\n%s\n?>";
		$retval = sprintf($retval, date('m/d/Y H:i:s'), $code);

		return $retval;

	}
}

?>
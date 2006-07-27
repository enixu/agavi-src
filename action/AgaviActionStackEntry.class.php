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
 * ActionStackEntry represents information relating to a single Action request
 * during a single HTTP request.
 *
 * @package    agavi
 * @subpackage action
 *
 * @author     Sean Kerr <skerr@mojavi.org>
 * @copyright  (c) Authors
 * @since      0.9.0
 *
 * @version    $Id$
 */
class AgaviActionStackEntry
{

	// +-----------------------------------------------------------------------+
	// | PRIVATE VARIABLES                                                     |
	// +-----------------------------------------------------------------------+
	
	private
		$actionInstance = null,
		$actionName     = null,
		$microtime      = null,
		$moduleName     = null,
		$parameters     = array(),
		$presentation   = null,
		$next           = null;
	
	// +-----------------------------------------------------------------------+
	// | METHODS                                                               |
	// +-----------------------------------------------------------------------+
	
	/**
	 * Class constructor.
	 *
	 * @param      string A module name.
	 * @param      string An action name.
	 * @param      AgaviAction An action implementation instance.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function __construct ($moduleName, $actionName, AgaviAction $actionInstance, AgaviParameterHolder $parameters)
	{
		
		$this->actionName     = $actionName;
		$this->actionInstance = $actionInstance;
		$this->microtime      = microtime();
		$this->moduleName     = $moduleName;
		$this->parameters     = $parameters;
		
	}
	
	/**
	 * Retrieve this entry's action name.
	 *
	 * @return     string An action name.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getActionName ()
	{
		
		return $this->actionName;
	
	}
	
	/**
	 * Retrieve this entry's action instance.
	 *
	 * @return     AgaviAction An action implementation instance.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getActionInstance ()
	{
		
		return $this->actionInstance;
	
	}
	
	/**
	 * Retrieve this entry's microtime.
	 *
	 * @return     string A string representing the microtime this entry was
	 *                    created.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getMicrotime ()
	{
		
		return $this->microtime;
	
	}
	
	/**
	 * Retrieve this entry's module name.
	 *
	 * @return     string A module name.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getModuleName ()
	{
		
		return $this->moduleName;
	
	}
	
	/**
	 * Retrieve the request parameters for this Action.
	 *
	 * @return     array An array of request parameters for this Action.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function getParameters()
	{
		return $this->parameters;
	}
	
	/**
	 * Set the request parameters for this Action.
	 *
	 * @param      array An array of request parameters for this Action.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function setParameters($parameters = array())
	{
		$this->parameters = $parameters;
	}
	
	/**
	 * Retrieve this entry's rendered view presentation.
	 *
	 * This will only exist if the view has processed and the render mode
	 * is set to AgaviView::RENDER_VAR.
	 *
	 * @return     string An action name.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function & getPresentation ()
	{
		
		return $this->presentation;
	
	}
	
	/**
	 * Set the rendered presentation for this action.
	 *
	 * @param      string A rendered presentation.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function setPresentation (&$presentation)
	{
		
		$this->presentation =& $presentation;
		
	}
	
	/**
	 * Set the next entry that will be run after this Action finished.
	 *
	 * @param      AgaviActionStackEntry The entry to execute next.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function setNext($moduleName, $actionName, $parameters = array())
	{
		$this->next = array('moduleName' => $moduleName, 'actionName' => $actionName, 'parameters' => $parameters);
	}
	
	/**
	 * Check if this Action or a View specified another Action to run next.
	 *
	 * @return     bool Whether or not a next Action has been set.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function hasNext()
	{
		return is_array($this->next);
	}
	
	/**
	 * Get the Action that should be run after this one finished execution.
	 *
	 * @return     array An associative array of information on the next Action.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function getNext()
	{
		return $this->next;
	}
}

?>
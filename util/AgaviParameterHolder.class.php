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
 * AgaviParameterHolder provides a base class for managing parameters.
 *
 * @package    agavi
 * @subpackage util
 *
 * @author     Sean Kerr <skerr@mojavi.org>
 * @copyright  (c) Authors
 * @since      0.9.0
 *
 * @version    $Id$
 */
abstract class AgaviParameterHolder
{

	// +-----------------------------------------------------------------------+
	// | PROTECTED DATA                                                        |
	// +-----------------------------------------------------------------------+

	protected
		$parameters = array();

	/**
	 * Clear all parameters associated with this request.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function clearParameters ()
	{

		$this->parameters = array();

	}

	/**
	 * Retrieve a parameter.
	 *
	 * @param      string A parameter name.
	 * @param      mixed  A default parameter value.
	 *
	 * @return     mixed A parameter value, if the parameter exists, otherwise
	 *               null.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function & getParameter ($name, $default = null)
	{

		$retval =& $default;

		if (isset($this->parameters[$name]))
		{

			$retval =& $this->parameters[$name];

		}

		return $retval;

	}

	/**
	 * Retrieve an array of parameter names.
	 *
	 * @return     array An indexed array of parameter names.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getParameterNames ()
	{

		return array_keys($this->parameters);

	}

	/**
	 * Retrieve an array of parameters.
	 *
	 * @return     array An associative array of parameters.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function getParameters ()
	{

		return $this->parameters;

	}

	/**
	 * Indicates whether or not a parameter exists.
	 *
	 * @param      string A parameter name.
	 *
	 * @return     bool true, if the parameter exists, otherwise false.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function hasParameter ($name)
	{

		return isset($this->parameters[$name]);

	}

	/**
	 * Remove a parameter.
	 *
	 * @param      string A parameter name.
	 *
	 * @return     string A parameter value, if the parameter was removed,
	 *                otherwise null.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function & removeParameter ($name)
	{

		$retval = null;

		if (isset($this->parameters[$name]))
		{

			$retval =& $this->parameters[$name];

			unset($this->parameters[$name]);

		}

		return $retval;

	}

	/**
	 * Set a parameter.
	 *
	 * If a parameter with the name already exists the value will be overridden.
	 *
	 * @param      string A parameter name.
	 * @param      mixed  A parameter value.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function setParameter ($name, $value)
	{

		$this->parameters[$name] = $value;

	}

	/**
	 * Append a parameter.
	 * 
	 * If this parameter is already set, convert it to an array and append the
	 * new value.  If not, set the new value like normal.
	 *
	 * @param      string A parameter name.
	 * @param      mixed  A parameter value.
	 *
	 * @author     Bob Zoller <bob@agavi.org>
	 * @since      0.10.0
	 */
	public function appendParameter($name, $value)
	{

		if (!isset($this->parameters[$name]) || !is_array($this->parameters[$name])) {
			settype($this->parameters[$name], 'array');
		}
		$this->parameters[$name][] = $value;

	}

	/**
	 * Set a parameter by reference.
	 *
	 * If a parameter with the name already exists the value will be
	 * overridden.
	 *
	 * @param      string A parameter name.
	 * @param      mixed  A reference to a parameter value.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function setParameterByRef ($name, &$value)
	{

		$this->parameters[$name] =& $value;

	}

	/**
	 * Append a parameter by reference.
	 * 
	 * If this parameter is already set, convert it to an array and append the
	 * reference to the new value.  If not, set the new value like normal.
	 *
	 * @param      string A parameter name.
	 * @param      mixed  A reference to a parameter value.
	 *
	 * @author     Bob Zoller <bob@agavi.org>
	 * @since      0.10.0
	 */
	public function appendParameterByRef($name, &$value)
	{

		if (!isset($this->parameters[$name]) || !is_array($this->parameters[$name])) {
			settype($this->parameters[$name], 'array');
		}
		$this->parameters[$name][] =& $value;

	}

	/**
	 * Set an array of parameters.
	 *
	 * If an existing parameter name matches any of the keys in the supplied
	 * array, the associated value will be overridden.
	 *
	 * @param      array An associative array of parameters and their associated
	 *              values.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function setParameters ($parameters)
	{

		$this->parameters = array_merge($this->parameters, $parameters);

	}

	/**
	 * Set an array of parameters by reference.
	 *
	 * If an existing parameter name matches any of the keys in the supplied
	 * array, the associated value will be overridden.
	 *
	 * @param      array An associative array of parameters and references to their
	 *              associated values.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function setParametersByRef (&$parameters)
	{

		foreach ($parameters as $key => &$value)
		{

			$this->parameters[$key] =& $value;

		}

	}

}

?>
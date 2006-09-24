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
 * AgaviRouting allows you to centralize your entry point urls in your web
 * application.
 *
 * @package    agavi
 * @subpackage routing
 *
 * @author     Dominik del Bondio <ddb@bitxtender.com>
 * @author     David Zuelke <dz@bitxtender.com>
 * @copyright  (c) Authors
 * @since      0.11.0
 *
 * @version    $Id$
 */
abstract class AgaviRouting
{
	const ANCHOR_NONE = 0;
	const ANCHOR_START = 1;
	const ANCHOR_END = 2;

	/**
	 * @var        array An array of route information
	 */
	protected $routes = array();

	/**
	 * @var        AgaviContext An AgaviContext instance.
	 */
	protected $context = null;

	/**
	 * @var        string Route input.
	 */
	protected $input = null;

	/**
	 * @var        array An array of AgaviRoutingArraySource.
	 */
	protected $sources = array();

	/**
	 * @var        string Route prefix to use with gen()
	 */
	protected $prefix = '';

	/**
	 * @var        array An array of default options for gen()
	 */
	protected $defaultGenOptions = array(
		'relative' => true
	);


	/**
	 * Initialize the routing instance.
	 *
	 * @param      AgaviContext A Context instance.
	 * @param      array        An array of initialization parameters.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public function initialize(AgaviContext $context, array $parameters = array())
	{
		$this->context = $context;
		if(isset($parameters['generator'])) {
			$this->defaultGenOptions = array_merge($this->defaultGenOptions, $parameters['generator']);
		}
		$cfg = AgaviConfig::get("core.config_dir") . "/routing.xml";
		// allow missing routing.xml when routing is not enabled
		if(AgaviConfig::get("core.use_routing", false) || is_readable($cfg)) {
			include(AgaviConfigCache::checkConfig($cfg, $context->getName()));
		}

		$this->sources['_ENV'] = new AgaviRoutingArraySource($_ENV);
		
		$this->sources['_SERVER'] = new AgaviRoutingArraySource($_SERVER);
		
		if(AgaviConfig::get('core.use_security')) {
			$this->sources['user'] = new AgaviRoutingUserSource($this->getContext()->getUser());
		}
	}

	/**
	 * Retrieve the current application context.
	 *
	 * @return     AgaviContext A Context instance.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public final function getContext()
	{
		return $this->context;
	}

	/**
	 * Retrieve the info about a named route for this routing instance.
	 *
	 * @return     mixed The route info or null if the route doesn't exist.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public final function getRoute($name)
	{
		if(!isset($this->routes[$name])) {
			return null;
		}
		return $this->routes[$name];
	}

	/**
	 * Retrieve the input for this routing instance.
	 *
	 * @return     string The input.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public final function getInput()
	{
		return $this->input;
	}

	/**
	 * Retrieve the prefix for this routing instance.
	 *
	 * @return     string The prefix.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public final function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * Adds a route to this routing instance.
	 *
	 * @param      string A string with embedded regexp.
	 * @param      array An array with options. The array can contain following
	 *                   items:
	 *                   <ul>
	 *                    <li>name</li>
	 *                    <li>stopping</li>
	 *                    <li>output_type</li>
	 *                    <li>module</li>
	 *                    <li>action</li>
	 *                    <li>parameters</li>
	 *                    <li>ignores</li>
	 *                    <li>defaults</li>
	 *                    <li>childs</li>
	 *                    <li>callback</li>
	 *                    <li>imply</li>
	 *                    <li>cut</li>
	 *                    <li>source</li>
	 *                   </ul>
	 * @param      string The name of the parent route (if any).
	 *
	 * @return     string The name of the route.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public function addRoute($route, array $options = array(), $parent = null)
	{
		// catch the old options from the route which has to be overwritten
		if(isset($options['name']) && isset($this->routes[$options['name']])) {
			$defaultOpts = $this->routes[$options['name']]['opt'];

			// when the parent is set and differs from the parent of the route to be overwritten bail out
			if($parent !== null && $defaultOpts['parent'] != $parent) {
				throw new AgaviException('You are trying to overwrite a route but are not staying in the same hierarchy');
			}

			if($parent === null) {
				$parent = $defaultOpts['parent'];
			} else {
				$defaultOpts['parent'] = $parent;
			}
		} else {
			$defaultOpts = array('name' => uniqid (rand()), 'stopping' => true, 'output_type' => null, 'module' => null, 'action' => null, 'parameters' => array(), 'ignores' => array(), 'defaults' => array(), 'childs' => array(), 'callback' => null, 'imply' => false, 'cut' => null, 'source' => null, 'method' => array(), 'locale' => null, 'parent' => $parent, 'reverseStr' => '', 'nostops' => array(), 'anchor' => self::ANCHOR_NONE);
		}

		if(isset($options['defaults'])) {
			foreach($options['defaults'] as $name => &$value) {
				$val = $pre = $post = '';
				if(preg_match('#(.*)\{(.*)\}(.*)#', $value, $match)) {
					$pre = $match[1];
					$val = $match[2];
					$post = $match[3];
				} else {
					$val = $value;
				}

				$value = array(
					'pre' => $pre,
					'val' => $val,
					'post' => $post,
				);
			}
		}

		// set the default options + user opts
		$options = array_merge($defaultOpts, $options);
		list($regexp, $options['reverseStr'], $routeParams, $options['anchor']) = $this->parseRouteString($route);

		$params = array();

		// transfer the parameters and fill available automatic defaults
		foreach($routeParams as $name => $param) {
			$params[] = $name;

			if(!isset($options['defaults'][$name]) && ($param['pre'] || $param['val'] || $param['post'])) {
				$options['defaults'][$name] = $param;
			}
		}

		// remove all ignore from the parameters in the route
		foreach($options['ignores'] as $ignore) {
			if(($key = array_search($ignore, $params)) !== false) {
				unset($params[$key]);
			}
		}

		$routeName = $options['name'];



		// check if 2 nodes with the same name in the same execution tree exist
		foreach($this->routes as $name => $route) {
			// if a route with this route as parent exist check if its really a child of our route
			if($route['opt']['parent'] == $routeName && !in_array($name, $options['childs'])) {
				throw new AgaviException('The route ' . $routeName . ' specifies a child route with the same name');
			}
		}

		// direct childs/parents with the same name arent caught by the above check
		if($routeName == $parent) {
			throw new AgaviException('The route ' . $routeName . ' specifies a child route with the same name');
		}

		// if we are a child route, we need add this route as a child to the parent
		if($parent !== null) {
			foreach($this->routes[$parent]['opt']['childs'] as $name) {
				$route = $this->routes[$name];
				if(!$route['opt']['stopping']) {
					$options['nostops'][] = $name;
				}
			}
			$this->routes[$parent]['opt']['childs'][] = $routeName;
		} else {
			foreach($this->routes as $name => $route) {
				if(!$route['opt']['stopping'] && !$route['opt']['parent']) {
					$options['nostops'][] = $name;
				}
			}
		}


		$route = array('rxp' => $regexp, 'par' => $params, 'opt' => $options);
		$this->routes[$routeName] = $route;

		return $routeName;
	}

	/**
	 * Retrieve the internal representation of the route info.
	 *
	 * @return     array The info about all routes.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public function exportRoutes()
	{
		return $this->routes;
	}

	/**
	 * Sets the internal representation of the route info.
	 *
	 * @param      array The info about all routes.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public function importRoutes(array $routes)
	{
		$this->routes = $routes;
	}

	/**
	 * Retrieves the routes which need to be taken into account when generating
	 * the reverse string of a routing to be generated.
	 *
	 * @param      string  The route name(s, delimited by +) to calculate.
	 *
	 * @return     array A list of names of affected routes.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public function getAffectedRoutes($route)
	{
		$routes = explode('+', $route);

		$route = $routes[0];
		unset($routes[0]);

		$myRoutes = array();
		foreach($routes as $r) {
			$myRoutes[$r] = true;
		}

		$affectedRoutes = array();

		if(isset($this->routes[$route])) {
			$parent = $route;
			do {
				$affectedRoutes[] = $parent;
				$r = $this->routes[$parent];

				foreach(array_reverse($r['opt']['nostops']) as $noStop) {
					$myR = $this->routes[$noStop];
					if(isset($myRoutes[$noStop])) {
						unset($myRoutes[$noStop]);
					} elseif(!$myR['opt']['imply']) {
						continue;
					}

					$affectedRoutes[] = $noStop;
				}

				$parent = $r['opt']['parent'];

			} while($parent);
		} else {
			// TODO: error handling - route with the given name does not exist
		}

		if(count($myRoutes)) {
			// TODO: error handling - we couldn't find some of the nonstopping rules
		}

		return $affectedRoutes;
	}


	/**
	 * Generate a formatted Agavi URL.
	 *
	 * @param      string A route name.
	 * @param      array  An associative array of parameters.
	 * @param      array  An array of options.
	 *
	 * @return     string
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public function gen($route, array $params = array(), array $options = array())
	{
		$routes = $route;
		if(is_string($route)) {
			$routes = $this->getAffectedRoutes($routes);
		}

		$url = '';
		$defaults = array();
		$availableParams = array();
		$firstRoute = true;
		foreach($routes as $route) {
			$r = $this->routes[$route];

			// if the route has a source we shouldn't put its stuff in the generated string
			if($r['opt']['source']) {
				continue;
			}

			$myDefaults = $r['opt']['defaults'];
			$availableParams += $r['par'] + $r['opt']['ignores'];

			if($firstRoute || $r['opt']['cut'] || (count($r['opt']['childs']) && $r['opt']['cut'] === null)) {
				if($r['opt']['anchor'] & self::ANCHOR_START || $r['opt']['anchor'] == self::ANCHOR_NONE) {
					$url = $r['opt']['reverseStr'] . $url;
				} else {
					$url = $url . $r['opt']['reverseStr'];
				}
			}

			if(isset($r['opt']['callback'])) {
				if(!isset($r['cb'])) {
					$cb = $r['opt']['callback'];
					$r['cb'] = new $cb();
					$r['cb']->initialize($this->getContext(), $r);
				}
				$myDefaults = $r['cb']->onGenerate($myDefaults, $params);
			}

			$defaults = array_merge($myDefaults, $defaults);
			$firstRoute = false;
		}

		$np = array();

		foreach($defaults as $name => $val) {
			if(isset($params[$name])) {
				$np[$name] = $val['pre'] . $params[$name] . $val['post'];
			} elseif($val['val']) {
				// more then just pre or postfix
				$np[$name] = $val['pre'] . $val['val'] . $val['post'];
			}
		}
		// get the remaining params too
		$params = array_merge($params, array_merge($np, array_filter($params, 'is_null')));

		// $params = array_merge($defaults, $params);

		$from = array();
		$to = array();

		// remove not specified available parameters
		foreach(array_unique($availableParams) as $name) {
			if(!isset($params[$name])) {
				$from[] = '(:' . $name . ':)';
				$to[] = '';
			}
		}

		foreach($params as $n => $p) {
			$from[] = '(:' . $n . ':)';
			$to[] = $p;
		}

		$url = str_replace($from, $to, $url);
		return $this->prefix . $url;
	}

	/**
	 * Matches the input against the routing info and sets the info as request
	 * parameter.
	 *
	 * @return     array All routes that matched.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public function execute()
	{
		$matchedRoutes = array();

		if(!AgaviConfig::get('core.use_routing', false) || count($this->routes) == 0) {
			// routing disabled, bail out
			return $matchedRoutes;
		}

		$req = $this->context->getRequest();

		$input = $this->input;

		$vars = array();
		$ot = null;
		$locale = null;
		$ma = $req->getModuleAccessor();
		$aa = $req->getActionAccessor();
		$requestMethod = $req->getMethod();

//		$routes = array_keys($this->routes);

		// get all top level routes
		foreach($this->routes as $name => $route) {
			if(!$route['opt']['parent']) {
				$routes[] = $name;
			}
		}

		// prepare the working stack with the root routes
		$routeStack = array($routes);

		do
		{
			$routes = array_pop($routeStack);
			foreach($routes as $key) {
				$route =& $this->routes[$key];
				$opts =& $route['opt'];
				if(count($opts['method']) == 0 || in_array($requestMethod, $opts['method'])) {
					if($opts['callback'] && !isset($route['cb'])) {
						$cb = $opts['callback'];
						$route['cb'] = new $cb();
						$route['cb']->initialize($this->context, $route);
					}

					$match = array();
					if($this->parseInput($route, $input, $match)) {
						$ign = array();
						if(count($opts['ignores']) > 0) {
							$ign = array_flip($opts['ignores']);
						}

						foreach($opts['defaults'] as $key => $value) {
							if(!isset($ign[$key]) && $value['val']) {
								$vars[$key] = $value['val'];
							}
						}

						foreach($route['par'] as $param) {
							if(isset($match[$param]) && $match[$param][1] != -1) {
								$vars[$param] = $match[$param][0];
							}
						}

						if($opts['callback']) {
							if(!$route['cb']->onMatched($vars)) {
								continue;
							}
						}

						$matchedRoutes[] = $opts['name'];

						foreach($match as $name => $m) {
							if(is_string($name) && !isset($opts['defaults'][$name])) {
								if(!isset($opts['defaults'][$name])) {
									$opts['defaults'][$name] = array('pre' => '', 'val' => '', 'post' => '');
								}
								$opts['defaults'][$name]['val'] = $m[0];
							}
						}

						if($opts['module']) {
							$vars[$ma] = $opts['module'];
						}

						if($opts['action']) {
							$vars[$aa] = $opts['action'];
						}


						if($opts['output_type']) {
							$ot = $opts['output_type'];
						}

						if($opts['locale']) {
							$locale = $opts['locale'];
						}

						if($opts['cut'] || (count($opts['childs']) && $opts['cut'] === null)) {
							if($route['opt']['source'] !== null) {
								$s =& $this->sources[$route['opt']['source']];
							} else {
								$s =& $input;
							}

							$ni = '';
							// if the route didn't match from the start of the input preserve the 'prefix'
							if($match[0][1] > 0) {
								$ni = substr($s, 0, $match[0][1]);
							}
							$ni .= substr($s, $match[0][1] + strlen($match[0][0]));
							$s = $ni;
						}

						if(count($opts['childs'])) {
							// our childs need to be processed next and stop processing 'afterwards'
							$routeStack[] = $opts['childs'];
							break;
						}

						if($opts['stopping']) {
							break;
						}

					} else {
						if($opts['callback']) {
							$route['cb']->onNotMatched();
						}
					}
				}
			}
		} while(count($routeStack) > 0);

		// set the output type if necessary
		if($ot !== null) {
			$this->context->getController()->setOutputType($ot);
		}

		// set the locale if necessary
		if($locale) {
			$req->setLocale($locale);
		}

		// put the vars into the request
		$req->setParameters($vars);

		if(!$req->hasParameter($ma) || !$req->hasParameter($aa)) {
			// no route which supplied the required parameters matched, use 404 action
			$req->setParameters(array(
				$ma => AgaviConfig::get('actions.error_404_module'),
				$aa => AgaviConfig::get('actions.error_404_action')
			));
		}
		// return a list of matched route names
		return $matchedRoutes;
	}

	/**
	 * Performs as match of the route against the input
	 *
	 * @param      array The route info array.
	 * @param      string The input.
	 * @param      array The array where the matches will be stored to.
	 *
	 * @return     bool Whether the regexp matched.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	protected function parseInput(array $route, $input, &$matches)
	{
		if($route['opt']['source'] !== null) {
			$parts = AgaviArrayPathDefinition::getPartsFromPath($route['opt']['source']);
			$partArray = $parts['parts'];
			$count = count($partArray);
			if($count > 0 && isset($this->sources[$partArray[0]])) {
				$input = $this->sources[$partArray[0]];
				if($count > 1) {
					array_shift($partArray);
					if(is_array($input)) {
						$input = AgaviArrayPathDefinition::getValue($partArray, $input);
					} elseif($input instanceof AgaviIRoutingSource) {
						$input = $input->getSource($partArray);
					}
				}
			}
		}
		return preg_match($route['rxp'], $input, $matches, PREG_OFFSET_CAPTURE);
	}

	/**
	 * Parses a route pattern string.
	 *
	 * @param      string The route pattern.
	 *
	 * @return     array The info for this route pattern.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	protected function parseRouteString($str)
	{
		$vars = array();
		$rxStr = '';
		$reverseStr = '';

		$anchor = 0;
		$anchor |= (substr($str, 0, 1) == '^') ? self::ANCHOR_START : 0;
		$anchor |= (substr($str, -1) == '$') ? self::ANCHOR_END : 0;

		$str = substr($str, (int)$anchor & self::ANCHOR_START, $anchor & self::ANCHOR_END ? -1 : strlen($str));

		$rxChars = implode('', array('.', '\\', '+', '*', '?', '[', '^', ']', '$', '(', ')', '{', '}', '=', '!', '<', '>', '|', ':'));

		$len = strlen($str);
		$state = 'start';
		$tmpStr = '';
		$inEscape = false;

		$rxName = '';
		$rxInner = '';
		$rxPrefix = '';
		$rxPostfix = '';
		$parenthesisCount = 0;
		$bracketCount = 0;
		$hasBrackets = false;
		// whether the regular expression is clean of any regular expression (\o/)
		$cleanRx = true;

		for($i = 0; $i < $len; ++$i) {
			$atEnd = $i + 1 == $len;

			$c = $str[$i];

			if(!$atEnd && !$inEscape && $c == '\\') {
				$cNext = $str[$i + 1];

				if(
					($cNext == '\\') ||
					($state == 'start' && $cNext == '(') ||
					($state == 'rxStart' && in_array($cNext, array('(',')','{','}')))
				) {
					$inEscape = true;
					continue;
				}
				if($state == 'afterRx' && $cNext == '?') {
					$inEscape = false;
					$state = 'start';
					continue;
				}
			} elseif($inEscape) {
				$tmpStr .= $c;
				$inEscape = false;
				continue;
			}


			if($state == 'start') {
				// start of regular expression block
				if($c == '(') {
					$rxStr .= preg_quote($tmpStr);
					$reverseStr .= $tmpStr;

					$tmpStr = '';
					$state = 'rxStart';
					$rxName = $rxInner = $rxPrefix = $rxPostfix = '';
					$parenthesisCount = 1;
					$bracketCount = 0;
					$hasBrackets = false;
				} else {
					$tmpStr .= $c;
				}

				if($atEnd) {
					$rxStr .= preg_quote($tmpStr);
					$reverseStr .= $tmpStr;
				}
			} elseif($state == 'rxStart') {
				if($c == '{') {
					++$bracketCount;
					if($bracketCount == 1) {
						$hasBrackets = true;
						$rxPrefix = $tmpStr;
						$tmpStr = '';
					} else {
						$tmpStr .= $c;
					}
				} elseif($c == '}') {
					--$bracketCount;
					if($bracketCount == 0) {
						list($rxName, $rxInner) = $this->parseParameterDefinition($tmpStr);
						$tmpStr = '';
					} else {
						$tmpStr .= $c;
					}
				} elseif($c == '(') {
					++$parenthesisCount;
					$tmpStr .= $c;
				} elseif($c == ')') {
					--$parenthesisCount;
					if($parenthesisCount > 0) {
						$tmpStr .= $c;
					} else {
						if($parenthesisCount < 0) {
							throw new AgaviException('The pattern ' . $str . ' contains an unbalanced set of parentheses!');
						}

						if(!$hasBrackets) {
							list($rxName, $rxInner) = $this->parseParameterDefinition($tmpStr);
						} else {
							if($bracketCount != 0) {
								throw new AgaviException('The pattern ' . $str . ' contains an unbalanced set of brackets!');
							}
							$rxPostfix = $tmpStr;
						}

						if(!$rxName) {
							$myRx = $rxPrefix . $rxInner . $rxPostfix;
							// if the entire regular expression doesn't contain any regular expression character we can savely append it to the reverseStr
							//if(strlen($myRx) == strcspn($myRx, $rxChars)) {
							if(strpbrk($myRx, $rxChars) === false) {
								$reverseStr .= $myRx;
							}
							$rxStr .= sprintf('(%s)', $myRx);
						} else {
							$rxStr .= sprintf('(%s(?P<%s>%s)%s)', $rxPrefix, $rxName, $rxInner, $rxPostfix);
							$reverseStr .= sprintf('(:%s:)', $rxName);

							if(!isset($vars[$rxName])) {
								if(strpbrk($rxPrefix, $rxChars) !== false) {
									$rxPrefix = '';
								}
								if(strpbrk($rxInner, $rxChars) !== false) {
									$rxInner = '';
								}
								if(strpbrk($rxPostfix, $rxChars) !== false) {
									$rxPostfix = '';
								}

								$vars[$rxName] = array('pre' => $rxPrefix, 'val' => $rxInner, 'post' => $rxPostfix);
							}
						}

						$tmpStr = '';
						$state = 'afterRx';
					}
				} else {
					$tmpStr .= $c;
				}

				if($atEnd && $parenthesisCount != 0) {
					throw new AgaviException('The pattern ' . $str . ' contains an unbalanced set of parentheses!');
				}
			} elseif($state == 'afterRx') {
				if($c == '?') {
					$rxStr .= $c;
				} else {
					// let the start state parse the char
					--$i;
				}

				$state = 'start';
			}
		}

		$rxStr = sprintf('#%s%s%s#', $anchor & self::ANCHOR_START ? '^' : '', $rxStr, $anchor & self::ANCHOR_END ? '$' : '');
		return array($rxStr, $reverseStr, $vars, $anchor);
	}

	/**
	 * Parses an embedded regular expression in the route pattern string.
	 *
	 * @param      string The definition.
	 *
	 * @return     array The name and the regexp.
	 *
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	protected function parseParameterDefinition($def)
	{
		$name = '';
		$rx = '';

		preg_match('#([a-z0-9_-]+:)?(.*)#i', $def, $match);
		return array(substr($match[1], 0, -1), $match[2]);
	}
}

?>
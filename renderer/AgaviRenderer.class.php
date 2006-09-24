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
 * A renderer produces the output as defined by a View
 *
 * @package    agavi
 * @subpackage renderer
 *
 * @author     David Zuelke <dz@bitxtender.com>
 * @author     Agavi Project <info@agavi.org>
 * @copyright  (c) Authors
 * @since      0.11.0
 *
 * @version    $Id$
 */
abstract class AgaviRenderer implements AgaviIRenderingFilter
{
	/**
	 * @var        AgaviContext An AgaviContext instance.
	 */
	protected $context = null;
	
	/**
	 @var          AgaviResponse A Response instance.
	 */
	protected $response = null;
	
	/**
	 * @var        string A string with the default template file extension,
	 *                    including the dot.
	 */
	protected $extension = '';
	
	/**
	 * @var        array An associative array containing the output of slots and
	 *                   the output of the content view.
	 */
	protected $output = array();
	
	/**
	 * @var        AgaviView The View instance that belongs to this Renderer.
	 */
	protected $view = null;
	
	/**
	 * @var        string The name of the array that contains the template vars.
	 */
	protected $varName = 'template';
	
	/**
	 * @var        string The name of the array that contains the slot output.
	 *                    Defaults to null, which means it'll be the identical to
	 *                    the varName setting.
	 *
	 * @see        AgaviRenderer::$varName
	 */
	protected $slotsVarName = null;
	
	/**
	 * @var        bool Whether or not the template vars should be extracted.
	 */
	protected $extractVars = false;
	
	/**
	 * @var        bool Whether or not the slot output vars should be extracted.
	 *                  Defaults to null, which means it behaves according to the
	 *                  extractVars setting.
	 *
	 * @see        AgaviRenderer::$extractVars
	 */
	protected $extractSlots = null;
	
	/**
	 * @var        array An array of objects to be exported for use in templates.
	 */
	protected $assigns = array();
	
	/**
	 * Initialize this Renderer.
	 *
	 * @param      AgaviContext The current application context.
	 * @param      array        An associative array of initialization parameters.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function initialize(AgaviContext $context, array $parameters = array())
	{
		$this->context = $context;
		if(isset($parameters['var_name'])) {
			$this->varName = $parameters['var_name'];
		}
		if(isset($parameters['slots_var_name'])) {
			$this->slotsVarName = $parameters['slots_var_name'];
		}
		if(isset($parameters['extract_vars'])) {
			$this->extractVars = $parameters['extract_vars'];
		}
		if(isset($parameters['extract_slots'])) {
			$this->extractSlots = $parameters['extract_slots'];
		}
		if($this->slotsVarName === null) {
			$this->slotsVarName = $this->varName;
		}
		if(isset($parameters['assigns'])) {
			foreach($parameters['assigns'] as $factory => $var) {
				$getter = 'get' . $factory;
				$this->assigns[$var] = $this->context->$getter();
			}
		}
	}

	/**
	 * Retrieve the current application context.
	 *
	 * @return     AgaviContext The current AgaviContext instance.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public final function getContext()
	{
		return $this->context;
	}
	
	/**
	 * Get the template file extension
	 *
	 * @return     string The extension, including a leading dot.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function getExtension()
	{
		return $this->extension;
	}
	
	/**
	 * Set the template file extension
	 *
	 * @param      string The extension, including a leading dot.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function setExtension($extension)
	{
		$this->extension = $extension;
	}

	/**
	 * Set the View instance that belongs to this Renderer instance.
	 *
	 * @param      AgaviView An AgaviView instance
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function setView($view)
	{
		$this->view = $view;
	}
	
	/**
	 * Retrieve the View instance that belongs to this Renderer instance.
	 *
	 * @return     AgaviView An AgaviView instance
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function getView()
	{
		return $this->view;
	}
	
	/**
	 * Build a template name based on "literal" flag in the template info.
	 * Depending on whether or not the "literal" flag is set, the file extension
	 * for this Renderer instance will be appended ("literal" false) or not (true)
	 *
	 * @param      array The (decorator) template info given by the View.
	 *
	 * @return     string A template file name.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function buildTemplateName($templateData)
	{
		list($file, $literal) = $templateData;
		if($literal) {
			return $file;
		} else {
			return $file . $this->getExtension();
		}
	}
	
	/**
	 * Loop through all template slots and fill them in with the results of
	 * presentation data.
	 *
	 * @param      string A chunk of decorator content.
	 *
	 * @return     string A decorated template.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function decorate($content)
	{
		$view = $this->getView();
		
		// alias controller
		$controller = $view->getContext()->getController();
		
		// get original render mode
		$renderMode = $controller->getRenderMode();
		
		// set render mode to var
		$controller->setRenderMode(AgaviView::RENDER_VAR);
		
		// grab the action stack
		$actionStack = $controller->getActionStack();
		
		// loop through our slots, and replace them one-by-one in the
		// decorator template
		$slots = $view->getSlots();
		
		foreach($slots as $name => $slot) {
			// grab this next forward's action stack index
			$index = $actionStack->getSize();
			
			// forward to the first slot action
			$controller->forward($slot['module_name'], $slot['action_name'], $slot['additional_params']);
			
			$response = $actionStack->getEntry($index)->getPresentation();
			
			if($response) {
				// set the presentation data as a template attribute
				$this->output[$name] = $response->getContent();
			
				$this->response->merge($response->exportInfo());
			} else {
				$this->output[$name] = null;
			}
		}
		
		// put render mode back
		$controller->setRenderMode($renderMode);
		
		// set the decorator content as an attribute
		$this->output['content'] = $content;
		
		// return a null value to satisfy the requirement
		$retval = null;
		
		return $retval;
	}
	
	/**
	 * Retrieve the template engine associated with this view.
	 *
	 * Note: This will return null for PHPView instances.
	 *
	 * @return     mixed A template engine instance.
	 */
	abstract function getEngine();

	/**
	 * Execute a basic pre-render check to verify all required variables exist
	 * and that the template is readable.
	 *
	 * @throws     <b>AgaviRenderException</b> If the pre-render check fails.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function preRenderCheck()
	{
		$view = $this->getView();
		$oti = $this->context->getController()->getOutputTypeInfo();
		
		if($view->getTemplate() === null) {
			// a template has not been set
			return;
		}
		
		$template = $view->getDirectory() . '/' . $this->buildTemplateName($view->getTemplate());
		if(!is_readable($template)) {
			// the template isn't readable
			$error = 'The template "%s" does not exist or is unreadable';
			$error = sprintf($error, $template);
			throw new AgaviRenderException($error);
		}

		// check to see if this is a decorator template
		if($view->isDecorator() && !(isset($oti['ignore_decorators']) && $oti['ignore_decorators'])) {
			$template = $view->getDecoratorDirectory() . '/' . $this->buildTemplateName($view->getDecoratorTemplate());
			if(!is_readable($template)) {
				// the decorator template isn't readable
				$error = 'The decorator template "%s" does not exist or is unreadable';
				$error = sprintf($error, $template);
				throw new AgaviRenderException($error);
			}
		}

		if(isset($oti['ignore_decorators']) && $oti['ignore_decorators']) {
			$view->clearDecorator();
		}
		if(isset($oti['ignore_slots']) && $oti['ignore_slots']) {
			$view->clearSlots();
		}
	}

	/**
	 * Render the presentation to the Response.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	abstract function render();
	
	/**
	 * Get the Response instance for this Renderer
	 *
	 * @return     AgaviResponse A Response instance.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * Execute the Renderer.
	 *
	 * This method is called by the rendering FilterChain.
	 * It puts the returned data into the View (if appropriate)
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function executeOnce(AgaviFilterChain $filterChain, AgaviResponse $response)
	{
		$this->execute($filterChain, $response);
	}

	/**
	 * Execute the Renderer.
	 *
	 * This method is called by the rendering FilterChain.
	 * It puts the returned data into the View (if appropriate)
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function execute(AgaviFilterChain $filterChain, AgaviResponse $response)
	{
		$this->response = $response;
		$this->render();
	}
}

?>
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
 * AgaviFormPopulationFilter automatically populates a form that is re-posted,
 * which usually happens when a View::INPUT is returned again after a POST 
 * request because an error occured during validation.
 * That means that developers don't have to fill in request parameters into
 * form elements in their templates anymore. Text inputs, selects, radios, they
 * all get set to the value the user selected before submitting the form.
 * If you would like to set default values, you still have to do that in your
 * template. The filter will recognize this situation and automatically remove
 * the default value you assigned after receiving a POST request.
 * This filter only works with POST requests, and compares the form's URL and
 * the requested URL to decide if it's appropriate to fill in a specific form
 * it encounters while processing the output document sent back to the browser.
 * Since this form is executed very late in the process, it works independently
 * of any template language.
 *
 * <b>Optional parameters:</b>
 *

 * # <b>cdata_fix</b> - [true] - Fix generated CDATA delimiters in script and 
 *                               style blocks.
 * # <b>error_class</b> - "error" - The class name that is assigned to form 
 *                                  elements which didn't pass validation and 
 *                                  their labels.
 * # <b>force_output_mode</b> - [false] - If false, the output mode (XHTML or 
 *                                        HTML) will be auto-detected using the 
 *                                        document's DOCTYPE declaration. Set 
 *                                        this to "html" or "xhtml" to force 
 *                                        one of these output modes explicitly.
 * # <b>include_hidden_inputs</b> - [false] - If hidden input fields should be 
 *                                            re-populated.
 * # <b>include_password_inputs</b> - [false] - If password input fields should 
 *                                              be re-populated.
 * # <b>remove_xml_prolog</b> - [true] - If the XML prolog generated by DOM 
 *                                       should be removed (existing ones will 
 *                                       remain untouched).
 *
 * @package    agavi
 * @subpackage filter
 *
 * @author     David Zuelke <dz@bitxtender.com>
 * @copyright  (c) Authors
 * @since      0.11.0
 *
 * @version    $Id$
 */
class AgaviFormPopulationFilter extends AgaviFilter implements AgaviIGlobalFilter, AgaviIActionFilter
{
	/**
	 * Execute this filter.
	 *
	 * @param      AgaviFilterChain The filter chain.
	 *
	 * @throws     <b>AgaviFilterException</b> If an error occurs during execution.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function executeOnce(AgaviFilterChain $filterChain, AgaviResponse $response)
	{
		$filterChain->execute($filterChain, $response);
		
		$req = $this->getContext()->getRequest();
		
		$cfg = array_merge(array('populate' => null, 'skip' => null), $this->getParameters(), $req->getAttributes('org.agavi.filter.FormPopulationFilter'));
		
		if(is_array($cfg['output_types']) && !in_array($this->getContext()->getController()->getOutputType(), $cfg['output_types'])) {
			return;
		}
		
		if(is_array($cfg['populate']) || $cfg['populate'] instanceof AgaviParameterHolder) {
			$populate = $cfg['populate'];
		} elseif(in_array($req->getMethod(), $cfg['methods']) && $cfg['populate'] !== false) {
			$populate = $req;
		} else {
			return;
		}
		
		// if(is_array($cfg['skip'])) {
		// 	$cfg['skip'] = new AgaviParameterHolder($cfg['skip']);
		// } elseif(!($cfg['skip'] instanceof AgaviParameterHolder)) {
		// 	$cfg['skip'] = new AgaviParameterHolder();
		// }
		// 
		$output = $response->getContent();
		
		$doc = new DOMDocument();
		
		$doc->substituteEntities = $cfg['dom_substitute_entities'];
		$doc->resolveExternals   = $cfg['dom_resolve_externals'];
		$doc->validateOnParse    = $cfg['dom_validate_on_parse'];
		$doc->preserveWhiteSpace = $cfg['dom_preserve_white_space'];
		$doc->formatOutput       = $cfg['dom_format_output'];
		
		$xhtml = (preg_match('/<!DOCTYPE[^>]+XHTML[^>]+/', $output) > 0 && strtolower($cfg['force_output_mode']) != 'html') || strtolower($cfg['force_output_mode']) == 'xhtml';
		if($xhtml && $cfg['parse_xhtml_as_xml']) {
			$doc->loadXML($output);
			$xpath = new DomXPath($doc);
			if($doc->documentElement->namespaceURI) {
				$xpath->registerNamespace('html', $doc->documentElement->namespaceURI);
				$ns = 'html:';
			} else {
				$ns = '';
			}
		} else {
			$doc->loadHTML($output);
			$xpath = new DomXPath($doc);
			$ns = '';
		}
		$properXhtml = false;
		foreach($xpath->query('//' . $ns . 'head/' . $ns . 'meta') as $meta) {
			if(strtolower($meta->getAttribute('http-equiv')) == 'content-type' && strpos($meta->getAttribute('content'), 'application/xhtml+xml') !== false) {
				$properXhtml = true;
				break;
			}
		}
		
		$encoding = strtolower($doc->encoding);
		$utf8 = $encoding == 'utf-8';
		if(!$utf8 && $encoding != 'iso-8859-1' && !function_exists('iconv')) {
			throw new AgaviException('No iconv module available, input encoding "' . $encoding . '" cannot be handled.');
		}
		
		$hasXmlProlog = false;
		if(preg_match('/<\?xml[^\?]*\?>/iU' . ($utf8 ? 'u' : ''), $output)) {
			$hasXmlProlog = true;
		}
		$baseHref = '';
		foreach($xpath->query('//' . $ns . 'head/' . $ns . 'base[@href]') as $base) {
			$baseHref = parse_url($base->getAttribute('href'));
			$baseHref = $baseHref['path'];
			break;
		}
		if(is_array($populate)) {
			foreach(array_keys($populate) as $id) {
				$query[] = '@id="' . $id . '"';
			}
			$query = '//' . $ns . 'form[' . implode(' or ', $query) . ']';
		} else {
			$query = '//' . $ns . 'form[@action]';
		}
		foreach($xpath->query($query) as $form) {
			if($populate instanceof AgaviParameterHolder) {
				$action = $form->getAttribute('action');
				if(!($baseHref . $action == $_SERVER['REQUEST_URI'] || $baseHref . '/' . $action == $_SERVER['REQUEST_URI'] || (strpos($action, '/') === 0 && $action == $_SERVER['REQUEST_URI']) || (strlen($_SERVER['REQUEST_URI']) == strrpos($_SERVER['REQUEST_URI'], $action) + strlen($action)))) {
					continue;
				}
				$p = $populate;
			} else {
				$p = $populate[$form->getAttribute('id')];
			}
			
			// build the XPath query
			$query = 'descendant::' . $ns . 'textarea[@name] | descendant::' . $ns . 'select[@name] | descendant::' . $ns . 'input[@name and (not(@type) or @type="text" or @type="checkbox" or @type="radio" or @type="password"';
			if($cfg['include_hidden_inputs']) {
				$query .= ' or @type="hidden"';
			}
			$query .= ')]';
			foreach($xpath->query($query, $form) as $element) {
				
				$name = $element->getAttribute('name');
				if(!$utf8) {
					if($encoding == 'iso-8859-1') {
						$name = utf8_decode($name);
					} else {
						$name = iconv('UTF-8', $encoding, $name);
					}
				}
				
				// there's an error with the element's name in the request? good. let's give the baby a class!
				if($req->hasError($name)) {
					$element->setAttribute('class', preg_replace('/\s*$/', ' ' . $cfg['error_class'], $element->getAttribute('class')));
					// assign the class to all implicit labels
					foreach($xpath->query('ancestor::' . $ns . 'label[not(@for)]', $element) as $label) {
						$label->setAttribute('class', preg_replace('/\s*$/', ' ' . $cfg['error_class'], $label->getAttribute('class')));
					}
					if(($id = $element->getAttribute('id')) != '') {
						// assign the class to all explicit labels
						foreach($xpath->query('descendant::' . $ns . 'label[@for="' . $id . '"]', $form) as $label) {
							$label->setAttribute('class', preg_replace('/\s*$/', ' ' . $cfg['error_class'], $label->getAttribute('class')));
						}
					}
				}
				
				if($braces = strpos($name, '[]') !== false && ($braces != strlen($name) -3 && $element->nodeName != 'select')) {
					// auto-generated index, we can't populate that
					continue;
				}
				
				$value = $p->getParameter($name);
				
				if(is_array($value) && $element->nodeName != 'select') {
					// name didn't match exactly. skip.
					continue;
				}
				
				if(!$utf8) {
					if($encoding == 'iso-8859-1') {
						if(is_array($value)) {
							$value = array_map('utf8_encode', $value);
						} else {
							$value = utf8_encode($value);
						}
					} else {
						if(is_array($value)) {
							foreach($value as &$val) {
								$val = iconv($encoding, 'UTF-8', $val);
							}
						} else {
							$value = iconv($encoding, 'UTF-8', $value);
						}
					}
				}
				
				if($element->nodeName == 'input') {
					
					if(!$element->hasAttribute('type') || $element->getAttribute('type') == 'text' || $element->getAttribute('type') == 'hidden') {
						
						// text inputs
						$element->removeAttribute('value');
						if($p->hasParameter($name)) {
							$element->setAttribute('value', $value);
						}
						
					} elseif($element->getAttribute('type') == 'checkbox' || $element->getAttribute('type') == 'radio') {
						
						// checkboxes and radios
						$element->removeAttribute('checked');
						if($p->hasParameter($name) && (($element->hasAttribute('value') && $element->getAttribute('value') == $value) || (!$element->hasAttribute('value') && $p->getParameter($name)))) {
							$element->setAttribute('checked', 'checked');
						}
						
					} elseif($element->getAttribute('type') == 'password') {
						
						// passwords
						$element->removeAttribute('value');
						if($cfg['include_password_inputs'] && $p->hasParameter($name)) {
							$element->setAttribute('value', $value);
						}
					}
					
				} elseif($element->nodeName == 'select') {
					$multiple = $element->hasAttribute('multiple');
					// select elements
					// yes, we still use XPath because there could be OPTGROUPs
					foreach($xpath->query('descendant::' . $ns . 'option', $element) as $option) {
						$option->removeAttribute('selected');
						if($p->hasParameter($name) && ($option->getAttribute('value') == $value || ($multiple && is_array($value) && in_array($option->getAttribute('value'), $value)))) {
							$option->setAttribute('selected', 'selected');
						}
					}
					
				} elseif($element->nodeName == 'textarea') {
					
					// textareas
					foreach($element->childNodes as $cn) {
						// remove all child nodes (= text nodes)
						$element->removeChild($cn);
					}
					// append a new text node
					if($xhtml && $properXhtml) {
						$element->appendChild($doc->createCDATASection($value));
					} else {
						$element->appendChild($doc->createTextNode($value));
					}
				}
				
			}
		}
		if($xhtml) {
			if(!$cfg['parse_xhtml_as_xml']) {
				// workaround for a bug in dom or something that results in two xmlns attributes being generated for the <html> element
				foreach($xpath->query('//html') as $html) {
					$html->removeAttribute('xmlns');
				}
			}
			$out = $doc->saveXML();
			if((!$cfg['parse_xhtml_as_xml'] || !$properXhtml) && $cfg['cdata_fix']) {
				// these are ugly fixes so inline style and script blocks still work. better don't use them with XHTML to avoid trouble
				$out = preg_replace('/<style([^>]*)>\s*<!\[CDATA\[/iU' . ($utf8 ? 'u' : ''), '<style$1><!--/*--><![CDATA[/*><!--*/', $out);
				$out = preg_replace('/\]\]><\/style>/iU' . ($utf8 ? 'u' : ''), '/*]]>*/--></style>', $out);
				$out = preg_replace('/<script([^>]*)>\s*<!\[CDATA\[/iU' . ($utf8 ? 'u' : ''), '<script$1><!--//--><![CDATA[//><!--', $out);
				$out = preg_replace('/\]\]><\/script>/iU' . ($utf8 ? 'u' : ''), '//--><!]]></script>', $out);
			}
			if($cfg['remove_auto_xml_prolog'] && !$hasXmlProlog) {
				// there was no xml prolog in the document before, so we remove the one generated by DOM now
				$out = preg_replace('/<\?xml.*?\?>\s+/iU' . ($utf8 ? 'u' : ''), '', $out);
			} elseif(!$cfg['parse_xhtml_as_xml']) {
				// yes, DOM sucks and inserts another XML prolog _after_ the DOCTYPE... and it has two question marks at the end, not one, don't ask me why
				$out = preg_replace('/<\?xml.*?\?\?>\s+/iU' . ($utf8 ? 'u' : ''), '', $out);
			}
			$response->setContent($out);
		} else {
			$response->setContent($doc->saveHTML());
		}
		unset($xpath);
		unset($doc);
	}

	/**
	 * Initialize this filter.
	 *
	 * @param      AgaviContext The current application context.
	 * @param      array        An associative array of initialization parameters.
	 *
	 * @throws     <b>AgaviFilterException</b> If an error occurs during initialization
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public function initialize(AgaviContext $context, $parameters = array())
	{
		// set defaults
		$this->setParameter('cdata_fix', true);
		$this->setParameter('error_class', 'error');
		$this->setParameter('force_output_mode', false);
		$this->setParameter('parse_xhtml_as_xml', true);
		$this->setParameter('include_password_inputs', false);
		$this->setParameter('include_hidden_inputs', true);
		$this->setParameter('remove_auto_xml_prolog', true);
		$this->setParameter('methods', array());
		$this->setParameter('output_types', null);
		$this->setParameter('dom_substitute_entities', false);
		$this->setParameter('dom_resolve_externals', false);
		$this->setParameter('dom_validate_on_parse', false);
		$this->setParameter('dom_preserve_white_space', true);
		$this->setParameter('dom_format_output', false);
		
		// initialize parent
		parent::initialize($context, $parameters);
		
		$this->setParameter('methods', (array) $this->getParameter('methods'));
		if($ot = $this->getParameter('output_types')) {
			$this->setParameter('output_types', (array) $ot);
		}
	}
}

?>
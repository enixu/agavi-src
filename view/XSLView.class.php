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
 * @package    agavi
 * @subpackage view
 *
 * @author     Wes Hays <weshays@gbdev.com>
 * @author     Agavi Project <info@agavi.org>
 * @copyright  (c) Authors
 * @since      0.10.0
 *
 * @version    $Id$
 */
abstract class AgaviXSLView extends AgaviView
{
	/**
	 * Append an attribute in the form of a DOMNode.
	 *
	 * @param      string The name of the attribute.
	 * @param      mixed  The value of the attribute in the form of a string or 
	 *                    DOMNode.
	 *
	 * @return     True on success, otherwise false.
	 *
	 * @author     Wes Hays <weshays@gbdev.com>
	 * @since      0.10.0
	 */
	public function appendAttribute($name, $value)
	{
		return $this->appendAttributeByRef($name, $value);
	}

	/**
	 * Set an attribute by reference in the form of a DOMNode.
	 *
	 * @param      string The name of the attribute.
	 * @param      mixed The value of the attribute in the form of a string or 
	 *                   DOMNode.
	 *
	 * @return     True on success, otherwise false.
	 *
	 * @author     Wes Hays <weshays@gbdev.com>
	 * @since      0.10.0
	 */
	public function appendAttributeByRef($name, &$value)
	{
		if(($this->rootNode != null) && is_string($name)) {
			if(is_string($value)) {
			$this->rootNode->appendChild(new DOMElement($name, $value));
			return true;
			}
			else if($value instanceof DOMNode) {
			$this->rootNode->appendChild($value);
			return true;
			}
		}

		return false;
	}

	/**
	 * Clear all attributes associated with this view.
	 *
	 * @author     Wes Hays <weshays@gbdev.com>
	 * @since      0.10.0
	 */
	public function clearAttributes()
	{
		$this->domDoc   = $this->domDocRS->cloneNode(true);
		$this->rootNode = $this->domDoc->firstChild;
	}

	/**
	 * Indicates whether or not an attribute exists in the rootnode.
	 *
	 * @param      string The attribute name to check if it exists.
	 *
	 * @return     bool true, if the attribute exists, otherwise false.
	 *
	 * @author     Wes Hays <weshays@gbdev.com>
	 * @since      0.10.0
	 */
	public function hasAttribute($name)
	{
		for($i=0; $i<$this->rootNode->childNodes->length; $i++) {
			if($this->rootNode->childNodes->item($i)->nodeName == $name) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieve an attribute.
	 *
	 * @param      string An attribute name.
	 *
	 * @return     DOMNodeList An attribute value.
	 *
	 * @author     Wes Hays <weshays@gbdev.com>
	 * @since      0.10.0
	 */
	public function &getAttribute($name)
	{
		$retVal = $this->domDoc->getElementsByTagName($name);
	return $retVal;
	}

	/**
	 * Retrieve an array of attribute names.
	 *
	 * @return     Array An indexed array of attribute names.
	 *
	 * @author     Wes Hays <weshays@gbdev.com>
	 * @since      0.10.0
	 */
	public function getAttributeNames()
	{
		$retVal = array();

		for($i=0; $i<$this->rootNode->childNodes->length; $i++) {
			$retVal[] = $this->rootNode->childNodes->item($i)->nodeName;
		}

		return $retVal;
	}

	/**
	 * Remove an attribute.  If there are multiple attributes with the same name
	 * then they are all removed.
	 *
	 * @param      string An attribute name.
	 *
	 * @return     array An array of DOMNodes, if the attribute was removed, 
	 *                   otherwise null.
	 *
	 * @author     Wes Hays <weshays@gbdev.com>
	 * @since      0.10.0
	 */
	public function &removeAttribute($name)
	{
		$retVal = array();
		for($i=$this->rootNode->childNodes->length-1; $i>=0; $i--) {
			if($this->rootNode->childNodes->item($i)->nodeName == $name) {
				$retVal[] = $this->rootNode->removeChild($this->rootNode->childNodes->item($i));
			}
		}
		$retVal = array_reverse($retVal);
		return $retVal;
	}

	/**
	 * Set an attribute.  If there are multiple attributes with the
	 * same name then they all will be set to this value.
	 *
	 * @param      string name The name of the attribute to set.
	 * @param      mixed value The value in the form of a string or DOMNode.
	 *
	 * @return     True on success, otherwise false.
	 *
	 * @author     Wes Hays <weshays@gbdev.com>
	 * @since      0.10.0
	 */
	public function setAttribute($name, $value)
	{
	return $this->setAttributeByRef($name, $value);
	}

	/**
	 * Set an attribute by reference.  If there are multiple attributes
	 * with the same name then they all be set to this value.
	 *
	 * @param      name  The name of the attribute to set.
	 * @param      mixed The value in the form of a string or DOMNode.
	 *
	 * @return     True on success, otherwise false.
	 *
	 * @author     Wes Hays <weshays@gbdev.com>
	 * @since      0.10.0
	 */
	public function setAttributeByRef($name, &$value)
	{
		if(!is_string($value) && !($value instanceof DOMNode)) return false;
		for($i=0; $i<$this->rootNode->childNodes->length; $i++) {
			if($this->rootNode->childNodes->item($i)->nodeName == $name) {
			$this->rootNode->childNodes->item($i)->nodeValue = $value;
			}
		}
		return true;
	}

	/**
	 * Set an array of attributes. The array must be in the form:
	 * $array['att1'] = 'value1';
	 * $array['att2'] = DOMNode Object;
	 * Any thing else will fail.
	 *
	 * @param      array An associative array of attributes and their associated
	 *                   values.
	 *
	 * @return     bool True if all set successfully, otherwise false.  If false
	 *                  is returned then no attributes were set.
	 *
	 * @author     Wes Hays <weshays@gbdev.com>
	 * @since      0.10.0
	 */
	public function setAttributes($attributes)
	{
		return $this->setAttributesByRef($attributes);
	}

	/**
	 * Set an array of attributes by reference.
	 *
	 * $array['att1'] = 'value1';
	 * $array['att2'] = DOMNode Object;
	 * Any thing else will fail.
	 *
	 * @param      array An associative array of attributes and their associated
	 *                   values.
	 *
	 * @return     bool True if all set successfully, otherwise false.  If false
	 *                  is returned then no attributes were set.
	 *
	 * @author     Wes Hays <weshays@gbdev.com>
	 * @since      0.10.0
	 */
	public function setAttributesByRef(&$attributes)
	{
		foreach ($attributes as $value) {
			if(!is_string($value) && !($value instanceof DOMNode)) return false;
		}
		foreach ($attributes as $name => $value) {
			$this->setAttributeByRef($name, $value);
		}
		return true;
	}

}

?>
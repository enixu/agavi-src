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
 * AgaviExecutionTimeFilter tracks the length of time it takes for an entire
 * request to be served starting with the dispatch and ending when the last 
 * action request has been served.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>comment</b> - [Yes] - Should we add an HTML comment to the end of each
 *                            output with the execution time?
 * # <b>replace</b> - [No] - If this exists, every occurance of the value in the
 *                           client response will be replaced by the execution
 *                           time.
 *
 * @package    agavi
 * @subpackage filter
 *
 * @author     Sean Kerr <skerr@mojavi.org>
 * @copyright  (c) Authors
 * @since      0.9.0
 *
 * @version    $Id$
 */
class AgaviExecutionTimeFilter extends AgaviFilter implements AgaviIActionFilter
{
	/**
	 * Execute this filter.
	 *
	 * @param      AgaviFilterChain The filter chain.
	 * @param      AgaviResponse A Response instance.
	 *
	 * @throws     <b>AgaviFilterException</b> If an error occurs during execution.
	 *
	 * @author     David Zuelke <dz@bitxtender.com>
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function execute(AgaviFilterChain $filterChain, AgaviResponse $response)
	{
		$context = $this->getContext();
		
		if($context->getController()->getActionStack()->getSize() == 1) {
			
			$comment = $this->getParameter('comment');
			$replace = $this->getParameter('replace', false);
			
			$start = microtime(true);
			$filterChain->execute();
			$time = (microtime(true) - $start);
			
			
			if($replace) {
				$output = $response->getContent();
				$output = str_replace($replace, $time, $output);
				$response->setContent($output);
			}
			
			if($comment) {
				$response->appendContent("\n\n<!-- This page took " . $time . " seconds to process -->");
			}
			
		} else {
			$filterChain->execute();
		}
	}

	/**
	 * Initialize this filter.
	 *
	 * @param      AgaviContext The current application context.
	 * @param      array        An associative array of initialization parameters.
	 *
	 * @throws     <b>AgaviFilterException</b> If an error occurs during 
	 *                                         initialization.
	 *
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function initialize(AgaviContext $context, $parameters = array())
	{
		// set defaults
		$this->setParameter('comment', true);
		$this->setParameter('replace', null);

		// initialize parent
		parent::initialize($context, $parameters);
	}
}

?>
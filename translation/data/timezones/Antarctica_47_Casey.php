<?php

/**
 * Data file for timezone "Antarctica/Casey".
 * Compiled from olson file "antarctica", version 8.7.
 *
 * @package    agavi
 * @subpackage translation
 *
 * @copyright  Authors
 * @copyright  The Agavi Project
 *
 * @since      0.11.0
 *
 * @version    $Id$
 */

return array (
  'types' => 
  array (
    0 => 
    array (
      'rawOffset' => 28800,
      'dstOffset' => 0,
      'name' => 'WST',
    ),
    1 => 
    array (
      'rawOffset' => 39600,
      'dstOffset' => 0,
      'name' => 'CAST',
    ),
  ),
  'rules' => 
  array (
    0 => 
    array (
      'time' => -31536000,
      'type' => 0,
    ),
    1 => 
    array (
      'time' => 1255802400,
      'type' => 1,
    ),
  ),
  'finalRule' => 
  array (
    'type' => 'static',
    'name' => 'CAST',
    'offset' => 39600,
    'startYear' => 2010,
  ),
  'source' => 'antarctica',
  'version' => '8.7',
  'name' => 'Antarctica/Casey',
);

?>
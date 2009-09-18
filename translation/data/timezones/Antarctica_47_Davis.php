<?php

/**
 * Data file for Antarctica/Davis timezone, compiled from the olson data.
 *
 * Auto-generated by the phing olson task on 08/17/2009 13:26:25
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
      'rawOffset' => 25200,
      'dstOffset' => 0,
      'name' => 'DAVT',
    ),
    1 => 
    array (
      'rawOffset' => 0,
      'dstOffset' => 0,
      'name' => 'zzz',
    ),
  ),
  'rules' => 
  array (
    0 => 
    array (
      'time' => -409190400,
      'type' => 0,
    ),
    1 => 
    array (
      'time' => -163062000,
      'type' => 1,
    ),
    2 => 
    array (
      'time' => -28857600,
      'type' => 0,
    ),
  ),
  'finalRule' => 
  array (
    'type' => 'static',
    'name' => 'DAVT',
    'offset' => 25200,
    'startYear' => 1970,
  ),
  'name' => 'Antarctica/Davis',
);

?>
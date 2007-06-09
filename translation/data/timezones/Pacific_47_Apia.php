<?php

/**
 * Data file for Pacific/Apia timezone, compiled from the olson data.
 *
 * Auto-generated by the phing olson task on 06/03/2007 18:13:14
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
      'rawOffset' => -41216,
      'dstOffset' => 0,
      'name' => 'LMT',
    ),
    1 => 
    array (
      'rawOffset' => -41400,
      'dstOffset' => 0,
      'name' => 'SAMT',
    ),
    2 => 
    array (
      'rawOffset' => -39600,
      'dstOffset' => 0,
      'name' => 'WST',
    ),
  ),
  'rules' => 
  array (
    0 => 
    array (
      'time' => -2855737984,
      'type' => 0,
    ),
    1 => 
    array (
      'time' => -1861878784,
      'type' => 1,
    ),
    2 => 
    array (
      'time' => -631110600,
      'type' => 2,
    ),
  ),
  'finalRule' => 
  array (
    'type' => 'static',
    'name' => 'WST',
    'offset' => -39600,
    'startYear' => 1951,
  ),
  'name' => 'Pacific/Apia',
);

?>
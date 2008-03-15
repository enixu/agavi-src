<?php

/**
 * Data file for Asia/Katmandu timezone, compiled from the olson data.
 *
 * Auto-generated by the phing olson task on 03/15/2008 17:07:52
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
      'rawOffset' => 19800,
      'dstOffset' => 0,
      'name' => 'IST',
    ),
    1 => 
    array (
      'rawOffset' => 20700,
      'dstOffset' => 0,
      'name' => 'NPT',
    ),
  ),
  'rules' => 
  array (
    0 => 
    array (
      'time' => -1577943676,
      'type' => 0,
    ),
    1 => 
    array (
      'time' => 504901800,
      'type' => 1,
    ),
  ),
  'finalRule' => 
  array (
    'type' => 'static',
    'name' => 'NPT',
    'offset' => 20700,
    'startYear' => 1986,
  ),
  'name' => 'Asia/Katmandu',
);

?>
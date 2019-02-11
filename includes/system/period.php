<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$period = array(
	'year'	=> array(
		'year'	=> 1,
		'month'	=> 12,
		'week'	=> 52.177457,
		'day'		=> 365.242199,
		'hour'	=> 8765.81277,
		'minute'=> 525948.766
	),
	'month'	=> array(
		'year'	=> 0.0833333333,
		'month'	=> 1,
		'week'	=> 4.34812141,
		'day'		=> 30.4368499,
		'hour'	=> 730.484398,
		'minute'=> 43829.0639
	),
	'week'	=> array(
		'year'	=> 0.0191653649,
		'month'	=> 0.229984378,
		'week'	=> 1,
		'day'		=> 7,
		'hour'	=> 168,
		'minute'=> 10080
	),
	'day'		=> array(
		'year'	=> 0.00273790926,
		'month'	=> 0.0328549112,
		'week'	=> 0.142857143,
		'day'		=> 1,
		'hour'	=> 24,
		'minute'=> 1440
	),
	'hour'	=> array(
		'year'	=> 0.000114079553,
		'month'	=> 0.00136895463,
		'week'	=> 0.00595238095,
		'day'		=> 0.0416666667,
		'hour'	=> 1,
		'minute'=> 60
	),
	'minute'=> array(
		'year'	=> 0.00000190132588,
		'month'	=> 0.0000228159105,
		'week'	=> 0.0000992063492,
		'day'		=> 0.000694444444,
		'hour'	=> 0.0166666667,
		'minute'=> 1
	)
);

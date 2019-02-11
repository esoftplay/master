<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!$sys->menu_real)
{
	$sys->nav_change(lang('Testimonial'), 'testimonial');
	$sys->nav_add(lang('Testimonial Sent'));
}

echo msg(@$_SESSION['testimonial']);
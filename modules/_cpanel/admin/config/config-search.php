<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

ob_start();
@include _ROOT.'modules/search/admin/config.php';
$output = ob_get_contents();
ob_end_clean();

<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

define('smiley_dir', str_replace('\\', '/', dirname(__FILE__)).'/');
define('smiley_url', str_replace(_ROOT, _URL, smiley_dir));
include_once smiley_dir.'config_array.php';
$r_smiley = $imagetosmilies;
$r_smiley_icon = $icontosmilies;

<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_change(lang('Links'));
$q = "SELECT * FROM links WHERE publish=1 ORDER BY orderby";
$r = $db->getAll($q);
include tpl('list.html.php');
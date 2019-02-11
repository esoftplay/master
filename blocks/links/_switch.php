<?php if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Semua data dari module "links" bisa anda tampilkan melalui block ini
$q = "SELECT * FROM links WHERE publish=1 ORDER BY orderby LIMIT ".intval($config['limit']);
$r = $db->getAll($q);
include tpl(@$config['template'].'.html.php', 'links.html.php');
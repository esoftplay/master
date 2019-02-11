<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$dt = $sys->menu_fetch('link', 'index.php?mod=_cpanel', 'like');
$sys->nav_change($dt['title'], $dt['link']);

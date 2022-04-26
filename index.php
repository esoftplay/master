<?php
$Bbc         = new stdClass();
$Bbc->no_log = 1;
define( '_VALID_BBC', 1 );
define( '_ADMIN', '' );
include_once 'config.php';
define( 'bbcAuth', 'bbcAuthUser' );
include_once _ROOT.'includes/includes.php';

<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

user_logout($_GET['id']);
redirect();

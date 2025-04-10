<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

lock_start('testing_lock', function($a){
	die('sedang proses: '.$a);
	return false;
});

for ($i=1; $i <= 5; $i++)
{
	sleep(1);
	file_put_contents(_ROOT.'images/modules_lock_main.txt', $i."\n", FILE_APPEND);
}

echo 'berhasil.. <br />';

lock_end();

die();
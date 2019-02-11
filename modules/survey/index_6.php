<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// CHECK SESSION...
if(!isset($sess['message']) || empty($sess['message'])) redirect($Bbc->mod['circuit']);
echo msg($sess['message'], '');

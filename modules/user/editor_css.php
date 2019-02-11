<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

ob_start('ob_gzhandler');
header('content-type: text/css; charset: UTF-8');
header('cache-control: must-revalidate');
$offset = 60 * 60 * 24 * 365;
$expire = 'expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
header($expire);
echo '@import url("../templates/'.config('template').'/css/style.css");'."\n";
?>
body {
	text-align: left;
	background: none;
	background: #fff;
}
.cke_editable {
	font-size: 13px;
	line-height: 1.6em;
}
.cke_contents_ltr blockquote {
	padding-left: 20px;
	padding-right: 8px;
	border-left-width: 5px;
}
.cke_contents_rtl blockquote {
	padding-left: 8px;
	padding-right: 20px;
	border-right-width: 5px;
}
<?php
die();
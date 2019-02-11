<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

ob_start();
echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0">
<?php	echo implode('', $content);	?>
</urlset>
<?php
$output = ob_get_contents();
ob_end_clean();
header('Content-type: text/xml');
header('Content-length: ' . strlen($output));
echo $output;
die;

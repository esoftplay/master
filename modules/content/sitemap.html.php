<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

ob_start();
echo '<?xml version="1.0" encoding="UTF-8"?>';/*<?xml-stylesheet type="text/xsl" href="http://fisip.net/images/sitemap.xsl"?>';#*/?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php	echo implode('', $content);	?>
</urlset>
<?php
$output = ob_get_contents();
ob_end_clean();
header('Content-type: text/xml');
header('Content-length: ' . strlen($output));
echo $output;

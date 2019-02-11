<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if($db->debug)
{
	echo '<div align="left">'.implode('<hr /><hr />',(array)$Bbc->debug).'</div>';
}

if(preg_match('~^demo\.~is', @$_SERVER['HTTP_HOST']))
{	?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-24449352-2']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script><?php
}
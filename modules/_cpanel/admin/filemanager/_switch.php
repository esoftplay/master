<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

?>
<html>
	<head></head>
	<body style="padding: 0; margin: 0;">
		<iframe src="<?php echo _URL.'user/files/?filemanagerPath=';?>" frameborder="0" width="100%" height="100%" scrolling="auto" style="padding: 0; margin: 0;" allowfullscreen="" mozallowfullscreen="mozallowfullscreen" msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen"></iframe>
	</body>
</html>
<?php
die();
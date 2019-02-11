<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$q = "SELECT name, code FROM contact_messenger WHERE id='".@intval($_GET['id'])."'";
$data = $db->getRow($q);
$sys->stop();
?>
<html>
	<head>
		<title><?php echo lang('Chat With');?> <?php echo $data['name'];?></title>
	</head>
	<body style="margin:0px;padding:0px;">
		<div style="width:240px;height:385px;overflow:hidden;padding:0px;margin:0px;">
			<?php echo $data['code'];?>
		</div>
	</body>
</html>
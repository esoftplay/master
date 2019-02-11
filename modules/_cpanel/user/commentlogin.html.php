<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$Bbc->exitpage = false;
if (!empty($_SESSION[_class('comment')->sesname]))
{
	_class('comment')->session();
	if (empty($user->website))
	{
		$user->website = '';
	}
	?>
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="utf-8" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="viewport" content="width=device-width, initial-scale=1" />
			<title>Fetching Data</title>
			<script type="text/javascript">
				function waitForBridge() {
					if (window.postMessage.length !== 1) {
						setTimeout(waitForBridge, 300);
					} else {
						var data = JSON.stringify({
						ok: 1,
						name: '<?php echo @$user->name; ?>',
						email: '<?php echo @$user->email; ?>',
						website: '<?php echo @$user->website; ?>',
						image: '<?php echo @$user->image; ?>'});
						postMessage(data);
					}
				};
				waitForBridge();
				if (typeof FisipAndroid!="undefined") {
					FisipAndroid.setUser("<?php echo $user->name; ?>","<?php echo $user->email; ?>","<?php echo $user->website; ?>","<?php echo $user->image; ?>");
				}
			</script>
		</head>
		<body>
			<h1 class="text-center">Fetching Data...</h1>
		</body>
	</html>
	<?php
	unset($_SESSION['commentUser']);
	die();
}
$id  = @$_GET['id'];
$uri = 'user/commentlogin';
$url = _URL.$uri;

if (empty($id) || $id == 'commentlogin')
{
	$tpl = $sys->template_dir.'blank.php';
	if (!file_exists($tpl))
	{
		$tpl = _ROOT.'templates/admin/blank.php';
	}
	$sys->set_layout($tpl);
	?>
	<ul class="list-group">
  	<li class="list-group-item"><a href="<?php echo $url; ?>/facebook"><?php echo icon('fa-facebook-square'); ?> Facebook</a></li>
  	<li class="list-group-item"><a href="<?php echo $url; ?>/google"><?php echo icon('fa-google-plus-square'); ?> Google+</a></li>
  	<li class="list-group-item"><a href="<?php echo $url; ?>/twitter"><?php echo icon('fa-twitter-square'); ?> Twitter</a></li>
  	<li class="list-group-item"><a href="<?php echo $url; ?>/linkedin"><?php echo icon('fa-linkedin-square'); ?> LinkedIn</a></li>
  	<li class="list-group-item"><a href="<?php echo $url; ?>/instagram"><?php echo icon('fa-instagram'); ?> Instagram</a></li>
	</ul>
	<?php
}else{
	$_SERVER['REQUEST_URI'] = _URI.$uri.'/'.$id;
	$data = $sys->login($id, $url.'/'.$id);
	if (!empty($data['email']))
	{
		if (!isset($data['website']))
		{
			$data['website'] = '';
			$r_alternate_web = array('url', 'link', 'profileUrl');
			foreach ($r_alternate_web as $j)
			{
				if (isset($data[$j]))
				{
					$data['website'] = $data[$j];
				}
			}
		}
		_class('comment')->session($data);
		redirect($url);
	}
}
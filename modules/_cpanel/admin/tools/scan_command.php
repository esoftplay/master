<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Scan Error Command');
$output = '';

_func('path');
_func('file');
/*================================================
 * GET CHMOD COMMAND
 *==============================================*/
if(!isset($_POST['Submit']))
{
	?>
	<form action="" method="POST" enctype="multipart/form-data" target="output">
		<table width="100%" height="100%" border=0 cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Scan Error Command</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label>User</label>
								<input type="text" name="user" class="form-control" />
							</div>
							<div class="form-group">
								<label>Group</label>
								<input type="text" name="group" class="form-control" />
							</div>
							<?php echo $sys->button(@$_GET['return']); ?>
							<button type=submit name="Submit" value="get command" class="btn btn-default">
								<?php echo icon('fa-terminal'); ?>
								Get Command
							</button>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<iframe src="" name="output" width="100%" height="100%" frameborder=0></iframe>
				</td>
			</tr>
		</table>
	</form>
	<?php
} else {
	$path	= _ROOT.'templates/';
	$text = 'cd '._ROOT.'
find . \( -path "./images/*" -o -path "./cgi-bin/*" \) -prune -o -type d -exec chmod 755 {} \;
find . \( -path "./images/*" -o -path "./cgi-bin/*" \) -prune -o -type f -exec chmod 644 {} \;
rm -rf images/cache
chmod -R 777 images
find templates/. -name style.css -exec chmod 777 {} \;';
	if(empty($_POST['user']) && preg_match('~^/home[0-9]?/~', $path))
	{
		preg_match('~/home[0-9]?/(.*?)/~', $path, $m);
		if(isset($m[1])) $_POST['user'] = $m[1];
	}

	if(isset($_POST['user']))
	{
		$user = $_POST['user'];
		$group= !empty($_POST['group']) ? $_POST['group'] : $user;
		$text .= "\nchown -R $user:$group *";
	}
	echo '<textarea style="width:100%;height: 90%;border: 0px;background: transparent;" onclick="this.select();">'.$text.'</textarea>';
	die();
}


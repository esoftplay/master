<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Backup & Restore');
require_once 'menuQRY.php';
if (!empty($_GET['type']))
{
	switch ($_GET['type'])
	{
		case 'download':
			$array  = menu_builder();
			$output = '{}';
			if (!empty($array))
			{
				if (is_object($array))
				{
					$array = (array)$array;
				}
				if (!is_array($array))
				{
					$output = $array;
				}else{
					if (defined('JSON_PRETTY_PRINT'))
					{
						$output = json_encode($array, JSON_PRETTY_PRINT);
					}else{
						$output = json_encode($array);
					}
				}
			}
			$filename = 'menu_'.date('YmdHis').'.json';
			file_write(_CACHE.$filename, $output);
			_func('download', 'file', $filename, _CACHE.$filename);
			break;
	}
}
if (!empty($_POST['submit']))
{
	$approval = @intval($_POST['approval']);
	switch ($_POST['submit'])
	{
		case 'backup':
			if (empty($approval))
			{
				echo msg('Please check the available checkbox to approve', 'danger');
			}else{
				echo msg('your backup menu will be downloaded shortly', 'success');
				?>
				<script type="text/javascript">
					setTimeout(function(){
						document.location.href="index.php?mod=_cpanel.menu&act=backup&type=download";
					}, 500);
				</script>
				<?php
			}
			break;
		case 'restore':
			if (empty($approval))
			{
				echo msg('Please check the available checkbox to approve', 'danger');
			}else{
				if (@is_uploaded_file($_FILES['menufile']['tmp_name']))
				{
					if (preg_match('~\.json$~is', $_FILES['menufile']['name']))
					{
						$filename = 'menu_'.date('YmdHis');
						if (move_uploaded_file($_FILES['menufile']['tmp_name'], _CACHE.$filename.'.json'))
						{
							echo msg('the page will redirect to the page for more configuration', 'success');
							?>
							<script type="text/javascript">
								setTimeout(function(){
									document.location.href="index.php?mod=_cpanel.menu&act=restore&file=<?php echo $filename; ?>&return=<?php echo urlencode(seo_uri()); ?>";
								}, 1000);
							</script>
							<?php
						}else{
							echo msg('Failed to upload the file', 'danger');
						}
					}else{
						echo msg('You can only upload json file', 'danger');
					}
				}else{
					echo msg('Please upload the file', 'danger');
				}
			}
			break;
	}
}
?>
<form action="" method="POST" enctype="multipart/form-data" class="formIsRequire">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Backup Menu</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label>Downloading the menu at your own risk. You must keep the file save from any changes</label>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="approval" value="1" />
						Yes, I'm aware of that
					</label>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<?php echo $sys->button($_GET['return']); ?>
			<button type="submit" name="submit" value="backup" class="btn btn-primary"><?php echo icon('floppy-save'); ?> Download</button>
		</div>
	</div>
</form>

<form action="" method="POST" enctype="multipart/form-data" class="formIsRequire">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Restore Menu</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label>Upload recovery menu</label>
				<input type="file" name="menufile" class="form-control" placeholder="configuration file" req="any true" />
				<div class="help-block">
					Upload your recovery file from the last time you've downloaded.
				</div>
			</div>
			<div class="form-group">
				<label>Restoring menu from file will remove the current menu and replace them with the new menu from your file</label>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="approval" value="1" />
						Yes, I'm aware of that
					</label>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<?php echo $sys->button($_GET['return']); ?>
			<button type="submit" name="submit" value="restore" class="btn btn-primary"><?php echo icon('floppy-open'); ?> Restore</button>
		</div>
	</div>
</form>
<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Chmod Directories');

if(!isset($_POST['Submit']))
{
	?>
	<form action="" method="POST" enctype="multipart/form-data" target="output">
		<table width="100%" height="100%" border=0 cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<div class="form-group">
						<label>File</label>
						<input type="text" name="file" value="644" maxlength="3" class="form-control" />
					</div>
					<div class="form-group">
						<label>Directory</label>
						<input type="text" name="directory" value="755" maxlength="3" class="form-control" />
					</div>
					<div class="form-group">
						<label>Path</label>
						<input type="text" name="path" value="<?php echo _ROOT; ?>" class="form-control" />
					</div>
					<?php echo $sys->button(@$_GET['return']); ?>
					<button type=submit name="Submit" value="Execute" class="btn btn-default">
						<?php echo icon('fa-lock'); ?>
						Execute
					</button>
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
	$file= intval(0 . intval( $_POST['file'], 10),8);
	$dir = intval(0 . intval( $_POST['directory'], 10),8);
	$path= file_exists(_ROOT.$_POST['path']) ? _ROOT.$_POST['path'] : _ROOT;
	if ( ! function_exists('path_chmod'))
	{
		function path_chmod($source_dir, $c_dir, $c_file, $top_level_only = FALSE)
		{
			if(file_exists($source_dir))
			{
				$filedata = array();
				if ($fp = @opendir($source_dir))
				{
					$source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
					while (FALSE !== ($file = readdir($fp)))
					{
						if (strncmp($file, '.', 1) == 0)
						{
							continue;
						}
						$mode = @is_dir($source_dir.$file) ? $c_dir : $c_file;
						if ($top_level_only == FALSE && @is_dir($source_dir.$file))
						{
							$temp_array = array();
							$temp_array = path_chmod($source_dir.$file.DIRECTORY_SEPARATOR, $c_dir, $c_file);
							ob_start();
								chmod($source_dir.$file, $mode);
	#							echo "chmod($source_dir.$file, $mode)";
								$o = ob_get_contents();
							ob_end_clean();
							if(!empty($o))
								$filedata[] = $o;
							$filedata   = array_merge($filedata, $temp_array);
						}
						else
						{
							ob_start();
								chmod($source_dir.$file, $mode);
	#							echo "chmod($source_dir.$file, $mode)";
								$o = ob_get_contents();
							ob_end_clean();
							if(!empty($o))
								$filedata[] = $o;
						}
					}
					closedir($fp);
				}else{
					ob_start();
						chmod($source_dir, $c_file);
#							echo "chmod($source_dir, $c_file);";
						$o = ob_get_contents();
					ob_end_clean();
					if(!empty($o))
						$filedata[] = $o;
				}
				return $filedata;
			}
		}
	}
	$r = path_chmod($path, $dir, $file);
	if(empty($r)) $r = 'Success Chmod :'.str_replace(_ROOT, '', $path)."\n".'All files :'.$_POST['file']."\n".'All directories :'.$_POST['directory'];
	echo '<textarea class="form-control" style="width: 100%;height: 100%;border: 0;">'.print_r($r, 1).'</textarea>';
	die();
}

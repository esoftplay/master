<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->set_layout('blank');
if (empty($_SESSION['bbcAuthAdmin']['id']))
{
	echo msg('You must login as administrator to access this feature!', 'danger');
}else
if (!defined('_IMAGE_STORAGE'))
{
	echo msg('you don\'t have enough configurations to access this feature!', 'danger');
}else{
	if (empty($_POST['submit']))
	{
		echo msg('Once you\'ve uploaded the file you must copy the path before leaving the page!', 'warning');
	}else
	if (is_uploaded_file($_FILES["fileToUpload"]["tmp_name"]))
	{
		$path  = 'images/uploads/';
		$name  = $_FILES['fileToUpload']['name'];
		$img   = _class('images');
		$ext   = $img->getExt($name);
		$allow = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'pdf'];
		if (in_array($ext, $allow))
		{
			$img->setPath(_ROOT.$path);
			$name = $img->upload($_FILES['fileToUpload'], $name);
			$src  = _URL.$path.$name;
			echo msg('Please copy the file path before closing the page!', 'success');
			?>
			<div class="panel panel-default">
				<div class="panel-body">
					<center>
						<img src="<?php echo $src; ?>" alt="<?php echo $src; ?>" class="img-thumbnail">
						<form action="" class="form-inline" role="form">
							<div class="form-group">
								<input type="text" class="form-control" id="click_text" value="<?php echo $src; ?>" placeholder="Input field">
							</div>
							<button type="button" class="btn btn-default" id="click_copy">
								<span class="glyphicon glyphicon-duplicate"></span>
								Copy
							</button>
						</form>
					</center>
				</div>
			</div>
			<script type="text/javascript">
				_Bbc(function($){
					$("#click_copy").on("click", function(a){
						$("#click_text").select();
						document.execCommand("copy");
						alert("Copied path: " + $("#click_text").val());
					})
				})
			</script>
			<?php
		}else{
			echo msg('Sorry, the file extension is not allowed!', 'danger');
		}
	}else{
		echo msg('Sorry, the file cannot be saved in the system', 'danger');
	}
	?>
	<div class="panel panel-default">
		<div class="panel-body">
			<form action="" method="POST" role="form" enctype="multipart/form-data">
				<legend>Upload file</legend>

				<div class="form-group">
					<input type="file" class="form-control" name="fileToUpload" id="" placeholder="Input field">
				</div>
				<button type="submit" name="submit" value="1" class="btn btn-primary">Upload</button>
			</form>
		</div>
	</div>
	<?php
}

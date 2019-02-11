<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @$_GET['id'];
$i  = @$_GET['i']; // input ID on HTML to change
if (empty($i))
{
	$i = 'userimage';
}
if (!empty($id))
{
	$sys->set_layout('blank');
	$data = $sys->login($id);
	if (!empty($data['image']))
	{
		?>
		<script type="text/javascript">
			var a = window.opener;
			var b = true;
			if (a.document) {
				var c = a.document.getElementById("<?php echo $i; ?>");
				if (c) {
					c.value = "<?php echo $data['image']; ?>";
					b = false;
					window.close();
				}
			}
			if (b) {
				alert("<?php echo lang('HTML Element with ID %s is not found', $i) ?>");
			}
		</script>
		<?php
	}else{
		echo msg(lang('Sorry, your profile image is not found on your social media account'), 'danger');
	}
}

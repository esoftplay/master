<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$_setting = array(
	'image' => array(
		'text' => 'Image URL',
		'type' => 'text',
		'attr' => 'id="txtUrl"',
		'tips' => '<span class="btn btn-default" onclick="return browse(this);">Browse</span> or leave it blank to sycronize image logo with site main logo'
		),
	'size'	=> array(
		'text' => 'Size Display',
		'type' => 'text',
		'add'  => 'pixel',
		'tips' => 'Declare sizes for width and height in pixel, if you leave it blank system will measure the image automatically. Automatically measure won\'t work in flash file.<br />eg: 200x300 ("pixel" is not included)'
		),
	'is_link'	=> array(
		'text'    => 'Link active',
		'type'    => 'radio',
		'option'  => array('1' => 'Yes', '0' => 'No'),
		'default' => 1,
		'tips'    => 'Does image use any links ?'
		),
	'link'	=> array(
		'text' => 'URL',
		'type' => 'text',
		'tips' => 'Insert url where this image links to, or leave it blank to use main URL as the link'
		),
	'title'	=> array(
		'text' => 'Image alt text',
		'type' => 'text',
		'tips' => 'Leave it blank to sycronize this to site title'
		),
	'attribute'	=> array(
		'text' => 'Image Attribute',
		'type' => 'text',
		'tips' => 'Additional HTML attribute for this image logo if Link Active is "Yes" then this HTML Attribute will be placed inside a-href instead of the image'
		)
	);
?>
<script type="text/JavaScript">
function browse()
{
	var theURL = "<?php echo _URL;?>user/files/?editor=form&filemanagerPath=images/uploads/";
	wt = this.open(theURL, 'filemanager',	"width=820, height=510, align=top, scrollbars=auto, status=no, resizable=yes");
	wt.window.focus();
	return false;
}
</script>
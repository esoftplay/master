<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$ext        = $_CONFIG['site']['logo'] ? strtolower(substr($_CONFIG['site']['logo'], -4)) : '';
$style_size = '';
$src_size   = '';
if($config['caption'])
{
	list($width, $height) = image_size($config['caption'], true);
	if($width > 0)
	{
		$style_size	= 'width:'.$width.'px;height:'.$height.'px;';
		$src_size		= ' width="'.$width.'px" height="'.$height.'px"';
	}
}
if(is_file(_ROOT.'images/'.$_CONFIG['site']['logo']))
{
	switch($ext)
	{
		case '.swf':
			?>
			<div style="<?php echo $style_size;?>padding:0px;background-color:#0d1d55;overflow:hidden;">
				<object type="application/x-shockwave-flash" data="<?php echo _URL;?>images/<?php echo $_CONFIG['site']['logo'];?>"<?php echo $src_size;?>>
				<param name="movie" value="<?php echo _URL;?>images/<?php echo $_CONFIG['site']['logo'];?>" />
				<param name="menu" value="false" />
				</object>
			</div>
			<?php
			break;
		case '.jpg':
		case '.gif':
		case '.bmp':
		case '.png':
			?>
			<a href="<?php echo _URL;?>" class="sitename"><img src="<?php echo _URL;?>images/<?php echo $_CONFIG['site']['logo'];?>" alt="<?php echo $_CONFIG['site']['title'];?>" <?php echo $src_size;?> /></a>
			<?php
			break;
	}
}
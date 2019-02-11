<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');?>

<h1><?php echo lang('Contact Form');?></h1>
<p>
	<?php echo get_config('contact', 'form', 'address');?>
</p>

<?php
echo $contact_form;
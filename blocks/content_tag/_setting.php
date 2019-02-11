<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if (get_config('content', 'manage', 'webtype') != '1')
{
	echo msg(lang('Content Tags is only available for News Article Website'), 'danger');
	die();
}
$_setting = array(
	'tag_type'	=> array(
		'text'   => 'Tag Type',
		'type'   => 'select',
		'attr'   => 'onchange="tag_set(this);"',
		'option' => array(1 => 'Popular Tags', 2 => 'Insert Multiple Tag IDs', 3 => 'Latest Tags')
		),
	'duration'	=> array(
		'text' => 'Limit Duration for Popular Tag',
		'type' => 'text',
		'attr' => 'rel="duration"',
		'add'  => 'Day(s)',
		'help' => 'If you leave this field empty, the current block will display popular tag for unlimited time. To limit duration number of days Eg. 3 or 7 or 30'
		),
	'tag_ids'	=> array(
		'text' => 'Tag IDs',
		'type' => 'text',
		'attr' => 'rel="tag_ids"',
		'help' => 'Insert multiple tag IDs separate by comma'
		),
	'limit'=> array(
		'text'    => 'Number of tags to display',
		'type'    => 'text',
		'attr'    => 'rel="limit"',
		'default' => '5',
		'help'    => 'Limit how many tags to display in this blocks'
		)
	);
?>
<script type="text/javascript">
	function tag_set(a) {
		var $ = BS3;
		var b = $(a).closest("form");
		var c = $('input[rel="duration"]', b).closest('.form-group');
		var d = $('input[rel="tag_ids"]', b).closest('.form-group');
		var e = $('input[rel="limit"]', b).closest('.form-group');
		switch($(a).val()) {
			case '1':
				$(c).fadeIn();
				$(d).hide();
				$(e).fadeIn();
				break;
			case '2':
				$(c).hide();
				$(d).fadeIn();
				$(e).hide();
				break;
			case '3':
				$(c).hide();
				$(d).hide();
				$(e).fadeIn();
				break;
		}
	};
	_Bbc(function($){
		tag_set($('#config\\[tag_type\\]'));
	});
</script>
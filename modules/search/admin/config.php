<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _class('bbcconfig');
$table = $db->GetAssoc("SELECT id, name FROM bbc_module WHERE search_func <> '' AND active=1 ORDER BY name");

$_setting = array(
	'from' => array(
		'text'    => 'Options',
		'type'    => 'radio',
		'option'  => array('1' => 'All', '0' => 'Custom', '2'=>'Google Search'),
		'default' => '1',
		'tips'    => '<b>All :</b> The keyword will be searched in all supported modules. <br /><b>Custom :</b> Only in selected modules the keyword will be looked into. <br /><b>Google Search :</b> Google\'s search engine will be used (This site must already indexed by Google)'
	),
	'module' => array(
		'text'   => 'Modules',
		'type'   => 'checkbox',
		'option' => $table,
		'delim'  => '<br />',
		'tips'   => 'If you select "Custom" in "Options" then you must select in which modules this search form will look into.'
	),
	'per_page' => array(
		'text'    => 'Total Per Page',
		'type'    => 'text',
		'default' => '10',
		'add'     => 'item(s)',
		'tips'    => 'Items to show per page'
	)
);
$output = array(
	'config' => $_setting,
	'name'   => 'search',
	'title'  => 'Search Configuration',
	'id'     => $sys->get_module_id('search')
);
$form->set($output);
echo $form->show();
?>
<script type="text/javascript">
_Bbc(function($) {
	$('#searchfrom0, #searchfrom1, #searchfrom2').change(function(){
		if ($(this).is(':checked')) {
			var a = $(this).parents('.form-group').next('.form-group');
			if ($(this).val()=='0') {
				$(a).show();
			}else{
				$(a).hide();
			}
		};
	}).trigger('change');
});
</script>
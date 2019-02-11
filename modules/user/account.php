<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$account_id = get_account_id();
if(!$account_id)
{
	$sys->denied();
}
$form   = _class('params');
$params = array(
	'title'       => 'My Profile',
	'table'       => 'bbc_account',
	'config_pre'  => array(),
	'config'      => user_field($user->id),
	'config_post' => array(),
	'pre_func'    => '_is_email_unique',
	'post_func'   => '_user_change',
	'name'        => 'params',
	'id'          => $account_id
	);

$params['config_pre'] = array(
	'image' => array(
		'text' => 'Image Profile',
		'type' => 'text',
		'attr' => 'readonly',
		'add'  => '
	<button type="button" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="imgProfile">
     <span class="caret"></span>
  </button>
	<ul class="dropdown-menu pull-right" id="imgLogin">
    <li><a href="#facebook">'.icon('fa-facebook-square').' Facebook</a></li>
    <li><a href="#google">'.icon('fa-google-plus-square').' Google</a></li>
    <li><a href="#twitter">'.icon('fa-twitter-square').' Twitter</a></li>
    <li><a href="#linkedin">'.icon('fa-linkedin-square').' LinkedIn</a></li>
    <li><a href="#instagram">'.icon('fa-instagram').' Instagram</a></li>
  </ul>',
		'tips' => 'Click input field above to fill image URL',
	),
	'username' => array(
		'text' => 'Username',
		'type' => 'plain'
		),
	'name' => array(
		'text'      => 'Name',
		'type'      => 'text',
		'mandatory' => '1'
		)
	);
$params['config_post'] = array(
	'email' => array(
		'text' => 'Email',
		'type' => 'plain'
		),
	'vcode' => array(
		'text' => 'Validation Code',
		'type' => 'captcha'
		)
	);
$form->set($params);
$form->set_encode(false);
// echo '<h1>'.lang('My Profile').'</h1>';
echo $form->show();

function _is_email_unique($form)
{
	if($form->is_updated)
	{

	}
}
function _user_change($form)
{
	global $user;
	user_call_func('user_change', $user->id);
}
?>
<script type="text/javascript">
	_Bbc(function($){
		var a = $("#imgLogin");
		var b = a.closest(".input-group");
		var c = $(".form-control", b);
		c.prop("id", "userimage")
		$("a", a).on("click", function(e){
			e.preventDefault();
			var d = $(this).attr("href").substr(1);
			window.open(_URL+"user/account_image/"+d+"?i=userimage", "userimage", "width=640,height=480,menubar=no,location=no,resizable=no,scrollbars=no,status=no");
		});
	});
</script>
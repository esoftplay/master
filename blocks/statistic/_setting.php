<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$_setting = array(
	'total_visit' => array(
		'text'   => 'Total Visitor',
		'type'   => 'radio',
		'option' => array('1' => 'show', '0' => 'hide'),
		'tips'   => 'show total this website visits'
		),
	'total_member' => array(
		'text'   => 'Total Member',
		'type'   => 'radio',
		'option' => array('1' => 'show', '0' => 'hide'),
		'tips'   => 'show total member in database'
		),
	'member_online' => array(
		'text'   => 'Member Online',
		'type'   => 'radio',
		'option' => array('1' => 'show', '0' => 'hide'),
		'tips'   => 'show total member online'
		),
	'user_online' => array(
		'text'   => 'User Online',
		'type'   => 'radio',
		'option' => array('1' => 'show', '0' => 'hide'),
		'tips'   => 'show total online visitor, member is included'
		),
	'active_days' => array(
		'text'   => 'Active Days',
		'type'   => 'radio',
		'option' => array('1' => 'show', '0' => 'hide'),
		'tips'   => 'show total days since this website is published'
		),
	'start_days' => array(
		'text' => 'Start Days',
		'type' => 'text',
		'attr' => 'id="datepicker"',
		'tips' => 'insert date this website start published'
		),
	'interval_time' => array(
		'text'    => 'Interval (second)',
		'type'    => 'text',
		'default' => '900'
		)
	);
?>
<script type="text/javascript">
_Bbc(function($) {
	var _path =  _URL+'templates/admin/bootstrap/';
	var _parm = {"format": "yyyy-mm-dd", autoclose: true, todayHighlight: true, todayBtn: true};
	if(typeof $.fn.datepicker!='function') {
		$('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', _path+'css/datepicker.css') );
		$.ajax({
		  url: _path+'js/datepicker.js',
		  dataType: "script",
		  success: function(){
		  	$('#datepicker').each(function(){
		  		var a = _parm;
		  		if ($(this).attr('data-date-format')) {
		  			a.format=$(this).attr('data-date-format')
		  		};
		  		$(this).datepicker(a);
		  	});
		  }
		});
	}else{
		$('#datepicker').each(function(){
			var a = _parm;
			if ($(this).attr('data-date-format')) {
				a.format=$(this).attr('data-date-format')
			};
			$(this).datepicker(a);
		});
	}
});</script>

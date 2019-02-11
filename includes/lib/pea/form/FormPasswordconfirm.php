<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE :
$form->edit->addInput('password', 'passwordConfirm');
*/
class FormPasswordConfirm extends Form
{
	var $typeEncription;
	var $subTitle="Password";
	var $subTitleConfirm="Confirm Password";

	function __construct()
	{
		$this->type = 'passwordConfirm';
		_func('password');
		$this->setEncryption('encode');
		$this->setAlert();
	}
	function setEncryption($type)
	{
		$this->typeEncription=$type;
	}
	function setAlert($alert='the password you have inserted are not match!')
	{
		$this->alert = $alert;
	}
	function setSubTitle($sub_password,$sub_confirm)
	{
		$this->subTitle=$sub_password;
		$this->subTitleConfirm=$sub_confirm;
	}
	function getAddSQL()
	{
		$name = $this->name;
		$out  = array();
		if(!empty($_POST[$name]['real']) && !empty($_POST[$name]['confirm']))
		{
			if($_POST[$name]['real'] == $_POST[$name]['confirm'])
			{
				$password = $_POST[$name]['real'];
				if(!empty($this->typeEncription))
				{
					$password = call_user_func($this->typeEncription,$password);
				}
				$out['into']	= $this->fieldName .", ";
				$out['value']	= "'".$password ."', ";
			}else{
				echo '<Script type="text/javascript">window.alert("'.$this->alert.'");</Script>';
			}
		}
		return $out;
	}
	function getRollUpdateSQL( $i = '' )
	{
		if ( $i == '' && !is_int($i) )
		{
			$password = $_POST[$this->name]['real'];
			$confirm  = $_POST[$this->name]['confirm'];
		}else{
			$password = $_POST[$this->name][$i]['real'];
			$confirm  = $_POST[$this->name][$i]['confirm'];
		}
		if (!empty($password) && $password != '******')
		{
			if ($password==$confirm)
			{
				if (!empty($this->typeEncription))
				{
					$password = call_user_func($this->typeEncription,$password);
				}
				return $query = "`". $this->fieldName ."` = '".$password."', ";
			}
		}
		return '';
	}
	function getReportOutput($str_value = '')
	{
		return '******';
	}
	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		$str_value = '';
		$GLOBALS['sys']->link_js(_PEA_URL.'includes/FormPasswordconfirm.js');
		$name	 = ( $str_name == '' ) ? $this->name : $str_name;
		$extra = $this->extra .' '. $str_extra;
		$nonul = $this->actionType=='edit' ? 'false' : 'true';
		$out   = '<input name="'. $name .'[real]" type="password" rel="password_real" req="any '.$nonul.'" value="'. $str_value .'" placeholder="'.$this->subTitle.'"'.$extra.' />';
		$out  .= '<input name="'. $name .'[confirm]" type="password" value="'. $str_value .'" req="any '.$nonul.'" placeholder="'.$this->subTitleConfirm.'"'.$extra.' />';
		return $out;
	}
}

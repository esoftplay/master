<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
Example:
$form->roll->addInput('fieldName','sqllinks');
#form->roll->input->fieldName->setTitle('fieldName');
$form->roll->input->fieldName->setLinks($Bbc->mod['circuit'].'.task_edit'); # ngeset links tujuan
#form->roll->input->fieldName->setGetName('id');                            # ngeset name dari GET jika tidak digunakan defaultnya 'id'
#form->roll->input->fieldName->setFieldName('anyfield AS fieldName');       # ngeset nama field yang akan dijadikan value dari get (default nya objectName itu sendiri jika tdk dipakai)
#form->roll->input->fieldName->setModal();																	# membuat link menjadi popup modal
*/
include_once _PEA_ROOT.'form/FormSqlplaintext.php';
class FormSqllinks extends FormSqlplaintext
{
	var $links;
	var $caption;
	var $getName;
	var $popWidth = 0;
	var $isModal;

	function __construct()
	{
		$this->type = 'sqllinks';
		$this->setTitle( 'Edit' );
		$this->setIsIncludedInUpdateQuery( false );
		$this->setIsIncludedInSearch( false );
		$this->setIsIncludedInReport( true );
		$this->setGetName();
		$this->links = '';
		$this->extra = '';
		$this->isModal = false;
	}

	function setLinks( $str_links = '' )
	{
		$this->links = $str_links;
	}

	function setGetName( $str_get_name = 'id' )
	{
		$this->getName = $str_get_name;
	}
	function setModal($boolean = true)
	{
		$this->isModal = $boolean ? true : false;
	}
	function setExtra( $str_extra )
	{
		$this->extra = $str_extra;
	}

	function setSizePop( $widht=500,$height=500)
	{
		$this->popWidth		= $widht;
		$this->popHeight	= $height;
	}

	function setUrlPop( $file,$title="View")
	{
		$this->urlPop   = $file;
		$this->titlePop = $title;
		$this->setModal(false);
		if (empty($this->popWidth))
		{
			$this->setSizePop();
		}
	}

	function getOutput( $arr_value = array(), $str_name = '', $str_extra = '' )
	{
		if($this->isMultiLanguage)
		{
			$arr_value[0] = @current($arr_value[0]);
		}else{
			$arr_value[0] = $arr_value[0];
		}
		if(empty($arr_value[0]))
		{
			$arr_value[0] = icon('edit');
		}else{
			$arr_value[0] = $this->getReportOutput($arr_value[0]);
		}
		$out = '';
		if ( !empty( $this->popWidth ) )
		{
			global $Bbc;
			if (empty($Bbc->FormSqllinks_is_load))
			{
				$out .= '<script type="text/javascript">
<!--
	function showPopUp(a,b, c, d) {
		e = this.open(a, b,
		"width="+c+", height="+d+", align=top, scrollbars=yes, status=yes, resizable=yes");
		e.window.focus();
	}
-->
</script>';
				$Bbc->FormSqllinks_is_load = 1;
			}
			if(empty($this->urlPop))
			{
				$this->urlPop = $this->links;
			}
			if(empty($this->titlePop))
			{
				$this->titlePop = "view";
			}
			$out		.='<a href="'.$this->urlPop .'&'. $this->getName.'='.$arr_value[1].'" onClick="showPopUp(this.href,\''.$this->titlePop.'\', \''.$this->popWidth.'\', \''.$this->popHeight.'\');return false;">'.$arr_value[0].'</a>';
		}else{
			$link  = $this->links;
			$link .= ( !preg_match( '~\?~s', $link) ) ? '?' : '&';
			$link .= $this->getName.'='.$arr_value[1];
			$link .= '&return='.urlencode(seo_uri());
			$extra = $this->extra .' '. $str_extra;
			if ($this->isModal)
			{
				if (!empty($extra))
				{
					$extra = trim($extra).' ';
				}
				$extra .= ' rel="editlinksmodal"';
				link_js(_LIB.'pea/includes/formLinkModal.js', false);
				icon('fa-ok');
			}
			$out   = '<a href="'.$link.'" '.$extra.'>'. $arr_value[0] .'</a>';
		}
		return $out;
	}
}

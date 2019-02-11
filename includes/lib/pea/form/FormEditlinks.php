<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
## EXAMPLE
$form->roll->addInput( 'test', 'editlinks' );
$form->roll->input->test->setTitle( 'Judul_Kolom' );
$form->roll->input->test->setCaption( 'Label_Link' );			# caption/text yang di link
#$form->roll->input->test->setModal();										# membuat semua link menjadi modal
#$form->roll->input->test->setGetName( 'id' );						# ngeset name dari GET id
$form->roll->input->test->setFieldName( 'id AS test' );		# ngeset nama field yang akan dijadikan value dari get
$form->roll->input->test->setLinks('link1', 'label1');		# ngeset links tujuan
#form->roll->input->test->setLinks('link2', 'label2');		# menambahkan links tujuan lain jika ada
#form->roll->input->test->setLinks(array(									# jika link tujuan ada banyak bisa dimasukkan ke array
	'link1' => 'label1',
	'link2' => 'label2',
	'link3' => 'label3'
	));
*/
class FormEditlinks extends Form
{
	var $links;
	var $caption;
	var $getName;
	var $isModal;

	function __construct()
	{
		$this->type = 'editlinks';
		$this->setTitle( 'Edit' );
		$this->setCaption( 'Edit' );
		$this->setIsIncludedInUpdateQuery( false );
		$this->setIsIncludedInReport( false );
		$this->setIsIncludedInSearch( false );
		$this->setIcon();
		$this->setGetName();
		$this->setFieldName('id AS edit');
		$this->links   = array();
		$this->isModal = false;
	}

	function setLinks($str_links = '', $str_title = '' )
	{
		if (empty($str_links))
		{
			return false;
		}
		if (is_array($str_links))
		{
			$this->links = array_merge($this->links, $str_links);
		}else{
			if (empty($str_title))
			{
				if (!empty($this->caption))
				{
					$str_title = $this->caption;
				}
				if (empty($str_title) && !empty($this->title))
				{
					$str_title = $this->title;
				}
			}
			$this->links[$str_links] = $str_title;
		}
	}
	function setGetName( $str_get_name = 'id' )
	{
		$this->getName = $str_get_name;
	}
	function setModal($boolean = true)
	{
		$this->isModal = $boolean ? true : false;
	}
	function setAtribute( $atribute= 'target=_blank' )
	{
		$this->atribute = $atribute;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( empty( $this->links ) )
		{
			die( 'Edit Links harus diset dg: $this->setLinks(); ' );
		}
		$output    = array();
		$add_link  = $this->getName.'='.$str_value.'&return='.urlencode(seo_uri());
		$add_extra = trim($this->extra);
		if (!empty($str_extra))
		{
			if (!empty($add_extra))
			{
				$add_extra .= ' ';
			}
			$add_extra .= $str_extra;
			$add_extra = trim($add_extra);
		}
		if ($this->isModal)
		{
			$add_extra .= ' rel="editlinksmodal"';
			link_js(_LIB.'pea/includes/formLinkModal.js', false);
			icon('fa-ok');
		}
		if (!empty($add_extra))
		{
			$add_extra = ' '.$add_extra;
		}
		foreach ($this->links as $link => $title)
		{
			$link .= ( !preg_match( '~\?~s', $link) ) ? '?' : '&';
			$link .= $add_link;
			$output[] = '<a href="'.$link.'" '.$add_extra.'>'. $title .'</a>';
		}
		if (count($output) > 1)
		{
			global $Bbc;
			if (empty($Bbc->FormEditlinks))
			{
				$Bbc->FormEditlinks = 0;
			}else{
				$Bbc->FormEditlinks++;
			}
			ob_start();
			?>
      <span class="dropdown">
			  <button id="editlinks<?php echo $Bbc->FormEditlinks; ?>" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-default btn-xs">
					<?php echo $this->caption;?>
			    <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu" aria-labelledby="editlinks<?php echo $Bbc->FormEditlinks; ?>">
			  	<li><?php echo implode('</li><li>', $output); ?></li>
			  </ul>
			</span>
			<?php
			$out = ob_get_contents();
			ob_end_clean();
		}else{
			$out = implode('', $output);
		}
		return $out;
	}
}
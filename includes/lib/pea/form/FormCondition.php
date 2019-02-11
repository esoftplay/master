<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// untuk membuat kolom yang berkondisi
// hanya bisa digunakan untuk roll

// modified


// addCondition( $operator, $comparator, $output = '' )
// 		untuk menambah kondisi
//		$operator	isinya {  '=', '==', '!=', '<>', '>', '<', '>=', '<=', 'default'  );
//		c: $this->addCondition( "=", 'makan', '<a href=index.php?makan>makan</a>' );
//		saat operatornya default, maka jika tidak ada kondisi yang emmenuhi, otomatis, outputnya adalah $output di default tersebut
//		c: $this->addCondition( 'default', '', '<a href=index.php?default>ini default</a>' );
//
//		$output bisa diisi dengan #_id_# yang nantinya akan di outputkan sebagai id dari row bersangkutan
//		c: $this->addCondition( '<>', 'makan', '<a href=index.php?id="#_id_#">ini default#_value_#</a>' );
class FormCondition extends Form
{
	var $operator;
	var $comparator;
	var $output;
	var $defaultCondition;

	function __construct()
	{
		$this->type = 'condition';
		$this->setIsIncludedInSearch( false );
		$this->setIsNeedDbObject( true );
		$this->setPlaintext(true);
		$this->addDefault();
	}

	function setParent($obj)
	{
		$this->parent = $obj;
	}

	function addDefault( $output='#_value_#' )
	{
		$this->defaultCondition	= $output;
	}

	function addCondition( $operator, $comparator, $output = '' )
	{
		$operator	= strtolower( $operator );
		if ( $operator != 'default' )
		{
			$this->operator[]	= $operator;
			$this->comparator[]	= $comparator;
			$this->output[]		= $output;
		} else {
			$this->defaultCondition	= $output;
		}
	}

	function getReportOutput( $str_value = '')
	{
		$this->getOutput( $str_value );
	}

	function getOutput( $arr_value = '', $str_name = '', $str_extra = '' )
	{
		// $arr_value
		// berbeda dengan yang lainnya, karena disini arr_value berisi array( $str_value, $field_id );

		// output dari plaintext sama dengan getOutput
		// if ( $this->isPlaintext ) return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		$out       = '';
		$name      = ($str_name == '') ? $this->name : $str_name;
		$extra     = $this->extra.' '.$str_extra;
		$str_value = is_array($arr_value) ? @$arr_value[0] : $arr_value;

		foreach( $this->operator as $i=>$operator )
		{
			switch( $operator )
			{
				case '=' :
				case '==' :
					if ( $str_value == $this->comparator[$i] )
						$out	= $this->output[$i];
				break;

				case '!=':
				case '<>':
					if ( $str_value != $this->comparator[$i] )
						$out	= $this->output[$i];
				break;

				case '>':
					if ( is_numeric( $str_value ) )
					{
						if ( $str_value > $this->comparator[$i] )
							$out	= $this->output[$i];
					}
				break;

				case '<':
					if ( is_numeric( $str_value ) )
					{
						if ( $str_value < $this->comparator[$i] )
							$out	= $this->output[$i];
					}
				break;

				case '>=':
					if ( is_numeric( $str_value ) )
					{
						if ( $str_value >= $this->comparator[$i] )
							$out	= $this->output[$i];
					}
				break;

				case '<=':
					if ( is_numeric( $str_value ) )
					{
						if ( $str_value <= $this->comparator[$i] )
							$out	= $this->output[$i];
					}
				break;
			} // switch
		} // foreach $operator

		// untuk menangani operator default
		if ( !isset($out) & !empty($this->defaultCondition) )
		{
			$out	= $this->defaultCondition;
		}
		$out	= preg_replace( '~#_value_#~is', $str_value, $out );
		if(preg_match_all('~(?:#_(\w+)_#)~s', $out, $match))
		{
			$arr = array_unique($match[1]);
			foreach ($arr as $key)
			{
				$replacer = !empty($this->parent->arrResult[$key]) ? $this->parent->arrResult[$key] : $key;
				$out	= preg_replace( '~#_'.$key.'_#~s', $replacer, $out );
			}
		}
		if (is_numeric($out))
		{
			$out = money($out);
		}
		return $this->getReturn($out);
	}
}
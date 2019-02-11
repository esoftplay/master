<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

class FormTextincrement extends Form
{
	function __construct()
	{
		$this->type = 'textincrement';
		$this->setIsNeedDbObject( true );
		$this->isNeedDbObject= true;
	}
	function setSequence($sequence)
	{
		$this->sequence=$sequence;
	}

	function getAddSQL()
	{
		$name			= $this->name;
		if($_POST[$name])
		{
			$old_val=$this->lastSequence();

			if($old_val==$_POST[$name])
			{
				$new_val=$old_val+1;
				$out['into']	= $this->fieldName .', ';
				$out['value']	= "'".$_POST[$name]."', ";
			}
			else
			{
				$out['into']	= $this->fieldName .", ";
				$out['value']	= "'".$_POST[$name] ."', ";
			}
		}
			return $out;
	}
	function lastSequence()
	{

			$q = "SELECT count(*) as totalrow FROM ".$this->tableName."";
			$r=$this->db->Execute($q);

			$data = $r->FetchRow( $q );

			$totalrow=$data['totalrow'];
			$limit=$totalrow-1;

			$sql = "SELECT ".$this->fieldName." as max FROM ".$this->tableName." LIMIT ".$limit.",-1";
			$r=$this->db->Execute($sql);
			$data = $r->FetchRow();

			return $data['max'];
	}

	function getSequenceVars ( $value )
	{

		if ( preg_match ( "/n{1,}/", $value ))
		{

			 $old_val=$this->lastSequence();
			//echo $old_val;
			//$new_val=settype($old_val, "integer");

			$old_val= preg_replace("/[^0-9]/","",$old_val) ;

			//echo "<br>".$old_val;

			$new_val=$old_val+1;

			if(strlen ($new_val) > strlen($value) ) die("Nilai sudah melebihi batas yang di ijinkan");

			if ( $new_val !== false )
			{

				$ret = sprintf ( "%0".strlen ($value)."d", $new_val);

				//echo $ret;
			}
			else
			{

				$new_val = 1;
				$ret = sprintf ( "%0".strlen ($value)."d", $new_val );
			}

		}
		return $ret;
	}

	function getSequence ()
	{

		if( is_array($this->sequence) )
		{

			foreach  ( $this->sequence as $key => $val )
			{

				switch ( substr ( $key, 0, 1 ) )
				{
					case "f":
						if ( $val != "" || $val != NULL )
						{
							preg_match("/((\w+|)(\((\[(.*)\]|(.*))\))|)(\[(.*)\]|.*)/", trim ($val), $match );
							// INI ADALAH VARIABEL
							if (	$match[0] != "" &&
									$match[1] == "" &&
									$match[2] == "" &&
									$match[3] == "" &&
									$match[4] == "" &&
									$match[5] == "" &&
									$match[6] == "" &&
									$match[7] != "" &&
									$match[8] != "") // key ini yang di resolve
							{
								//echo $match[8];
								$arrSeq[] = $this->getSequenceVars ( $match[8] );
							}
							// INI ADALAH FUNGSI DENGAN PARAMATER BUKAN VARIABEL
							elseif (	$match[0] != "" &&
										$match[1] != "" &&
										$match[2] != "" && // key ini adalah nama fungsi
										$match[3] != "" &&
										$match[4] != "" && // key ini parameter fungsi
										$match[5] == "" &&
										$match[6] != "" &&
										$match[7] == "")
							{
								$arg = date ( $match[4] );
								if ( function_exists ( $match[2] ) )
								{
									$arrSeq[] = $match[2] ( $arg );
								}
								else // retrun nilai aslinya aja, mbangane die (), harusnya ada sistem error reporting :p
								{
									$arrSeq[] = $arg;
								}
							}
							// INI ADALAH FUNGSI DENGAN PARAMATER VARIABEL
							elseif (	$match[0] != "" &&
										$match[1] != "" &&
										$match[2] != "" && // key ini adalah nama fungsi
										$match[3] != "" &&
										$match[4] != "" &&
										$match[5] != "" && // key ini yang di resolve dan kemudian dijadikan parameter dari fungsi
										$match[6] == "" &&
										$match[7] == "")
							{
								$resolve = $this->getSequenceVars ( $match[5]);
								if ( function_exists ( $match[2] ) )
								{
									$arrSeq[] = $match[2] ( $resolve );
								}
								else // retrn variable nya aja, mbangane die (), harusnya ada sistem error reporting :p
								{
									$arrSeq[] = $resolve;
								}
							}
							// INI ADALAH STRING BIASA
							elseif (	$match[0] != "" &&
										$match[1] == "" &&
										$match[2] == "" &&
										$match[3] == "" &&
										$match[4] == "" &&
										$match[5] == "" &&
										$match[6] == "" &&
										$match[7] != "")  // key ini adalah nilai string tersebut
							{
								$eval = date ( $match[7] );

								//echo $eval;

								$arrSeq[] = $eval;
							}
						}
						else
						{
							$arrSeq[] = "";
						}
						break;

					case "s":
						$arrSeq[] = $val;
						break;
				}
			}
			$a= implode ("", $arrSeq);

			return $a;
		}else
		{
			die("Sequence harus dalam bentuk array ");
		}
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );

		$str_value=$this->getSequence();

		$name	= ( $str_name == '' ) ? $this->name : $str_name;
		$extra	= $this->extra .' '. $str_extra;
		$out	= '<input name="'. $name .'" type="text" size="'.$this->size.'" value="'. $str_value .'" '.$extra.'>';
		return $out;
	}

}
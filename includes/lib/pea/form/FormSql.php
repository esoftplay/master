<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
membuat input field yang berupa berupa text yang output nya ditentukan berdasarkan dari query yang dimasukkan ke dalam {addSqlQuery}
EXAMPLE:
$form->roll->addInput('fieldName','sql');
$form->roll->input->fieldName->setTitle('Judul Field');
$form->roll->input->fieldName->addSqlQuery('SELECT `fieldName2` FROM `other_table` WHERE `current_table_id`={fieldName}');
$form->roll->input->fieldName->setDelimiter(', ')
*/
class FormSql extends Form
{
	var $operator;
	var $comparator;
	var $output;
	var $defaultCondition;

	function __construct()
	{
		$this->type = 'sql';
		$this->setIsIncludedInSearch( false );
		$this->setIsNeedDbObject( true );
		$this->setIsIncludedInUpdateQuery( false );
	}

	function setDelimiter( $str_delimiter	= '&nbsp;' )
	{
		$this->delimiter  = $str_delimiter;
	}

	function addSqlQuery( $sql )
	{
		$this->sql = $sql;
	}
	/*
	mendapatakan field yang di gunakan untuk kondisi sql,
	jika di temukan nanti di cek dulu di table utama ,apakah ada tidak field tersebut
	jika tidak ada langsung di die
	*/

	function parsingSql()
	{
		if ( preg_match("/{(.*?)}/",$this->sql,$match) )
		{
			$this->valueToReplace = $match[0]; // {blabla}
			$this->fieldToSelect  = $match[1]; // nama field yang akan di cek
		}else{
			die("SQL query yg anda masukan harus mengandung {}");
			return ;
		}
	}

	// fungsi untuk mengecek apakah field tersebut ada di table utama atau tidak,
	// memanfaatkan nilai id yang ada di table utama,
	// sekalian mereturn kan nilainya yang di gunakan untuk quer
	function getFieldValue($value_id)
	{
		$q = "SELECT ".$this->fieldToSelect." FROM `".$this->tableName."` WHERE ".$this->tableId."='".$value_id."'";
		$this->db->Execute($q);

		if($this->db->Affected_rows() > 0)
		{
			$field_value = $this->db->GetOne($q);
			return $field_value;
		}else{
			die('<strong>Nama Field yang ada di dalam tanda {} tidak ada di table '.$this->tableName.',<br> sql : '.$this->sql.'</strong>');
		}
		return '';
	}

	function getDataQuery($value)
	{
		$this->parsingSql();
		$field_value=$this->getFieldValue($value);
		$sql	= preg_replace( '~'.preg_quote($this->valueToReplace, '~').'~is', $field_value, $this->sql);
		$data = $this->db->getCol($sql);
		return $data;
	}
	function getReportOutput( $str_value = '' )
	{
		$out = $this->getOutput($str_value);
		return $out;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		$data  = $this->getDataQuery($str_value);
		$out   = '';
		$extra = $this->extra .' '. $str_extra;
		if(count($data) > 0)
		{
			$out = implode($this->delimiter, $data);
		}
		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $out, $str_name, $str_extra );
		return $out;
	}
}
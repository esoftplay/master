<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
untuk buat multi checkbox
# hubungan table many to many
$form->edit->addInput('NAMABEBAS','multicheckbox');
$form->edit->input->NAMABEBAS->setTitle('Reference Checkbox');
$form->edit->input->NAMABEBAS->setReferenceTable('bbc_content_cat_text');			# menentukan table reference
$form->edit->input->NAMABEBAS->setReferenceField('title','cat_id');						# menentukan field yang digunakan untuk table reference
$form->edit->input->NAMABEBAS->setReferenceCondition('lang_id='.lang_id());		# [optional] jika ada tambahan dalam pencarian di table reference (bisa banyak)
$form->edit->input->NAMABEBAS->setReferenceNested('par_id');									# [optional] jika reference table menggunakan par_id
#form->edit->input->NAMABEBAS->setColumn(2);																	# [optional] menentukan jumlah colom
$form->edit->input->NAMABEBAS->setRelationTable('bbc_content_category');			# [optional] jika menggunakan table sebagai relasi many to many (jika kosong maka akan menggunakan fieldName sebagai relasi dengan comma delimiter)
$form->edit->input->NAMABEBAS->setRelationTableId('category_id');							# [optional] jika table relasi menggunakan field primary yang auto_increment
$form->edit->input->NAMABEBAS->setRelationField('content_id','cat_id');				# menentukan field ang digunakan jika menggunakan table relasi (bukan lagi optional jika menggunakan table relasi)
$form->edit->input->NAMABEBAS->setRelationCondition('pruned=0');							# [optional] jika ada tambahan field dalam pencarian di table relasi (bisa banyak)
$form->edit->input->NAMABEBAS->setRelationCondition('active=1');							# [optional] jika ada tambahan field dalam pencarian di table relasi (bisa banyak)



terutama yang untuk hubungan many to many
jadi disini ada 3 tabel yang terlibat
	1. tabel main      = bbc_content
	2. tabel reference = bbc_content_cat_text
	3. tabel relation  = bbc_content_category (penghubung ke duanya)


contoh table:

	TABLE MAIN            TABLE RELATION     TABLE REFERENCE

 	id <----------------> content_id         title
 	title            			cat_id <---------> id
 	description
*/

include_once( _PEA_ROOT.'form/FormCheckbox.php' );

class FormMulticheckbox extends FormCheckbox
{

	var $referenceTable;
	var $referenceField;
	var $isLoaded;
	var $parent;
	var $column                = 1;
	var $referenceNested       = false;
	var $referenceNestedField  = '';
	var $relationTable         = '';
	var $relationTableId       = '';
	var $sqlReferenceCondition = array();
	var $sqlRelationCondition  = array();
	var $referenceData = array(
		'label' => array(),
		'value' => array()
		);

	function __construct()
	{
		$this->type 		= 'multicheckbox';
		$this->caption	= array();
		if(empty($this->isLoaded))
		{
			$this->isLoaded = new stdClass();
		}
		$this->isLoaded->getDataFromReferenceTable	= false;

		$this->setValue();
		$this->setCheckAll( false );
		$this->setDelimiter();
		$this->setIsNeedDbObject( true );
		$this->setIsIncludedInSelectQuery( true );
		$this->setIsIncludedInDeleteQuery(true); // agar getDeleteSQL($ids) di eksekusi sebelum dataRow dihapus
		$this->setIsIncludedInSearch( false );
		$this->setRelationTable( false );
	}
	function setParent($obj)
	{
		$this->parent = $obj;
	}

	// setReferenceTable( $str_reference_table )
	// untuk ngeset table yang mau di select sebagai reference
	// c: $this->setReferenceTable( "ref_nama_sekolah" );
	function setReferenceTable( $str_reference_table )
	{
		$this->referenceTable	= $str_reference_table;
	}

	// setReferenceField( $str_reference_label_field, $str_reference_value_field )
	// untuk ngeset nama field yang mau jadi value dan jadi label pada checkbox
	// hasil : <input type=checkbox value="$str_reference_value_field"><label>$str_reference_label_field</label>
	// c: $this->setReferenceTable( "nama_sekolah", "id_sekolah" );
	function setReferenceField( $str_reference_label_field, $str_reference_value_field )
	{
		$this->referenceField['value']	= $str_reference_value_field;
		$this->referenceField['label']	= $str_reference_label_field;
	}

	// setReferenceCondition( $sqlCondition )
	// untuk menambahkan sqlCondition di refrenceTable
	// c: $this->setReferenceCondition( 'city_id=2' );
	// c: $this->setReferenceCondition( array('country_id=1','city_id=2') );
	function setReferenceCondition($value)
	{
		if (is_array($value))
		{
			foreach ($value as $val)
			{
				$this->setReferenceCondition($val);
			}
		}else{
			if (!empty($value))
			{
				$this->sqlReferenceCondition[] = $value;
			}
		}
	}
	function setReferenceNested($referenceNestedField = 'par_id')
	{
		if (is_bool($referenceNestedField))
		{
			$this->referenceNested      = $referenceNestedField;
			$this->referenceNestedField = 'par_id';
		}else{
			$this->referenceNested      = true;
			$this->referenceNestedField = $referenceNestedField;
		}
	}

	// setRelationTable( $str_relation_table )
	// untuk ngeset table yang mau di select sebagai relasi penghubung antara table main dan table reference
	// c: $this->setRelationTable( "relasi_main_sekolah" );
	function setRelationTable( $str_relation_table )
	{
		if(!empty($str_relation_table))
		{
			$this->relationTable	= $str_relation_table;
			$this->setIsIncludedInSelectQuery( false );
		}
	}

	// setRelationField( $str_main_id_field, $str_reference_id_field )
	// untuk ngeset nama field di table relasi, yang mana yang foreign key dari table main,
	// dan yang mana yang jadi foreign key dari table reference
	// c: $this->setRelationField( "main_id", "reference_id" );
	function setRelationField( $str_main_id_field, $str_reference_id_field )
	{
		if (empty($this->relationTable))
		{
			die( __METHOD__.':: tentukan setRelationTable( $str_relation_table ) terlebih dahulu baru menentukan field yang digunakan di tabel relasi' );
		}
		$this->relationField['main']      = $str_main_id_field;				// Eg. content_id (jika table utama adalah bbc_content)
		$this->relationField['reference'] = $str_reference_id_field;	// Eg. cat_id
	}

	// setRelationCondition( $sqlCondition )
	// untuk menambahkan sqlCondition di relationTable
	// c: $this->setRelationCondition( 'city_id=2' );
	// c: $this->setRelationCondition( array('country_id=1','city_id=2') );
	function setRelationCondition($value)
	{
		if (is_array($value))
		{
			foreach ($value as $val)
			{
				$this->setRelationCondition($val);
			}
		}else{
			if (!empty($value))
			{
				$this->sqlRelationCondition[] = $value;
			}
		}
	}

	// setRelationTableId( $sqlCondition )
	// untuk menentukan fieldID dari relation table untuk meringankan filter delete,insert dan update SQL
	// c: $this->setRelationTableId( 'id' );
	function setRelationTableId( $str_relation_tableId='id' )
	{
		if (empty($this->relationTable))
		{
			die( __METHOD__.':: tentukan setRelationTable( $str_relation_table ) terlebih dahulu baru menentukan tableId dari tabel relasi' );
		}
		$this->relationTableId = $str_relation_tableId;
	}

	// untuk ngeset delimiter antar element saat di output kan
	function setDelimiter( $str_delimiter	= "<br/>\n" )
	{
		$this->delimiter	= $str_delimiter;
	}
	function setColumn($column=1)
	{
		if (!in_array($column, array(1,2,3,4,6)))
		{
			die(__METHOD__.':: Maaf, Jumlah kolom yang diperbolehkan adalah 2 atau 3 atau 4 atau 6');
		}
		$this->column = $column;
	}

	function addOption( $label, $value = null )
	{
		if (is_array($label))
		{
			$this->addOptionArray($label,$value);
		}else{
			$value = isset($value) ? $value : $label;
			$this->referenceData['value'][]	= $value;
			$this->referenceData['label'][]	= $label;
		}
	}

	function addOptionArray( $arrOption, $arrValue = array() )
	{
		$i = 0;
		foreach( $arrOption as $key => $title)
		{
			if (empty($arrValue) && is_array($title))
			{
				list($value, $label) = array_values($title);
			}else{
				if (!isset($key_is_value))
				{
					$key_is_value = ($key!=$i && empty($arrValue)) ? true : false;
				}
				$value = $key_is_value ? $key : ( isset($arrValue[$key]) ? $arrValue[$key] : (isset($arrValue[$i]) ? $arrValue[$i] : $title) );
				$label = is_array($title) ? reset($title) : $title;
			}
			$this->addOption( $label, $value );
			$i++;
		}
	}

	function getDataFromReferenceTable()
	{
		if ( $this->isLoaded->getDataFromReferenceTable == true ) return;

		$this->isLoaded->getDataFromReferenceTable = true;
		if (empty($this->referenceTable) || empty($this->referenceField['value']) || empty($this->referenceField['label']))
		{
			die( __METHOD__.':: setReferenceTable($str_reference_table) dan setReferenceField($str_reference_label_field, $str_reference_value_field) harus diset untuk menentukan table dan field yang digunakan.' );
		}
		// meng query data dari table reference
		$result = array();
		if (!empty($this->referenceNested))
		{
			$sql = "SELECT "
					. $this->referenceField['value'] ." AS `id`, "
					. $this->referenceNestedField ." AS `par_id`, "
					. $this->referenceField['label'] ." AS `title`"
					. "	FROM ". $this->getTableCondition($this->referenceTable, $this->sqlReferenceCondition);
			if (!preg_match('~\s+order\s+by\s+~is', $sql))
			{
				$sql .= ' ORDER BY `par_id`, `title` ASC';
			}
			$ar  = $this->db->getAll($sql);
			$arr = _func('array', 'path', $ar);
			if (!empty($this->isSingleSelect)) // ini untuk FormMultiselect
			{
				$this->referenceArray = $ar;
			}
			foreach ($arr as $key => $value)
			{
				$result[] = array(
					'label' => $value,
					'value' => $key
					);
			}
		}else{
			$sql = "SELECT ". $this->referenceField['value'] ." AS `value`, "
					. $this->referenceField['label'] ." AS `label`"
					. "	FROM ". $this->getTableCondition($this->referenceTable, $this->sqlReferenceCondition);
			if (!preg_match('~\s+order\s+by\s+~is', $sql))
			{
				$sql .= ' ORDER BY `label` ASC';
			}
			$result	= $this->db->getAll( $sql );
		}
		foreach ( $result AS $a )
		{
			$this->addOption( $a['label'], $a['value']);
		}
	}
	function getDataFromRelationTable( $str_value )
	{
		$output = array();
		if ($this->actionType=='add')
		{
			return (array)$this->defaultValue;
		}
		if($this->relationTable)
		{
			if (empty($this->relationField['reference']) || empty($this->relationField['main']))
			{
				die( __METHOD__.':: setRelationField($str_main_id_field, $str_reference_id_field) harus diset untuk menentukan table dan field yang digunakan.' );
			}
			if (!empty($str_value))
			{
				$q = 'SELECT '. $this->relationField['reference'].' FROM '. $this->relationTable.' WHERE '.$this->relationField['main'].'='.intval($str_value);
				$q = $this->getTableCondition($q, $this->sqlRelationCondition);
				$output	= $this->db->getCol( $q );
			}
		}else{
			$output = repairExplode($str_value);
		}
		return $output;
	}
	function getTableCondition($table, $condition, $method='WHERE')
	{
		$output = trim($table);
		if (!empty($condition))
		{
			$delimiter = $method == 'WHERE' ? ' AND ' : ', ';
			$method    = $method == 'WHERE' ? ' WHERE ' : '';
			$condition = implode($delimiter, $condition);
			if (preg_match('~\s+where\s+~is', $output))
			{
				$output = preg_replace('~\s+(where)\s+~is', $method.'%s'.$delimiter, $output);
			}else
			if (preg_match('~\s+order\s+by\s+~is', $output))
			{
				$output = preg_replace('~\s+(order\s+by)\s~is', $method.'%s ORDER BY ', $output);
			}else{
				if (!empty($condition) && empty($method))
				{
					$method = ', ';
				}
				$output .=  $method.'%s';
			}
			$output = sprintf($output, $condition);
		}
		return $output;
	}

	function getRollUpdateSQL( $i='' )
	{
		$name    = $this->name;
		$output  = '';
		$this->getDataFromReferenceTable();
		if ( $i == '' && !is_numeric($i) )
		{
			/*==============================================
			UPDATE SQL DARI EDIT FORM
			==============================================*/
			$query       = 'SELECT '.$this->tableId.' FROM '.$this->tableName .' '. $this->sqlCondition;
			$post        = isset($_POST[$name]) && is_array($_POST[$name]) ? $_POST[$name] : array();
			$mainTableId = $this->actionType == 'add' ? 0 : (!empty($this->parent->arrResult[$this->tableId]) ? intval($this->parent->arrResult[$this->tableId]) : intval($this->db->getOne($query)));
		}else{
			/*==============================================
			UPDATE SQL DARI ROLL FORM
      ==============================================*/
			$post        = isset( $_POST[$name][$i] ) ? $_POST[$name][$i] : array();
			$mainTableId = @intval($_POST[$this->formName.'_'.$this->tableId][$i]);
		}
		if($this->relationTable)
		{
			$add_sql = !empty($this->sqlRelationCondition) ? ', '.implode(', ', $this->sqlRelationCondition) : '';
			if (empty($this->relationTableId))
			{
				/* JIKA TABLE_ID DARI RELATION TABLE TIDAK ADA, MAKA LANGSUNG HAPUS SEMUA LALU DIMASUKKAN LAGI */
				$sql = 'DELETE FROM '. $this->relationTable.' WHERE '.$this->relationField['main'].'='.$mainTableId;
				$sql = $this->getTableCondition($sql,$this->sqlRelationCondition);
				$this->db->Execute( $sql );
				foreach( $this->referenceData['value'] as $j => $value )
				{
					if( in_array( $value, $post) )
					{
						$q = 'INSERT INTO '.$this->relationTable.' SET '.$this->relationField['main'].'='.$mainTableId
							.', '.$this->relationField['reference'].'=\''. $value.'\''.$add_sql;
						$this->db->Execute( $q );
					}
				}
			}else{
				/* JIKA MENGGUNAKAN TABLE_ID UNTUK RELATION TABLE, MAKA HAPUS DAN INSERT SATU-SATU AGAR LEBIH RINGAN */
				$q = 'SELECT '.$this->relationTableId.', '.$this->relationField['reference'].' FROM '.$this->relationTable;
				$c = array_merge($this->sqlRelationCondition, array($this->relationField['main'].'='.$mainTableId));
				$r = $this->db->getAssoc($this->getTableCondition($q,$c));
				foreach ($r as $tableId => $ref_id)
				{
					if (!in_array($ref_id, $post))
					{
						if (!is_numeric($tableId)) {
							$tableId = "'{$tableId}'";
						}
						$this->db->Execute("DELETE FROM {$this->relationTable} WHERE {$this->relationTableId}={$tableId}");
					}
				}
				foreach( $this->referenceData['value'] as $j => $value )
				{
					if( in_array($value, $post) && !in_array($value, $r))
					{
						$q = 'INSERT INTO '.$this->relationTable.' SET '.$this->relationField['main'].'='.$mainTableId
							.', '.$this->relationField['reference'].'=\''.$value.'\''.$add_sql;
						$this->db->Execute( $q );
					}
				}
			}
		}else{
			$output = "`".$this->fieldName."` = '".repairImplode($post, ',')."', ";
		}
		return $output;
	}

	function getAddSQL()
	{
		$name         = $this->name;
		$pendingQuery = array();
		if($this->relationTable)
		{
			$this->getDataFromReferenceTable();
			$add_sql = !empty($this->sqlRelationCondition) ? ', '.implode(', ', $this->sqlRelationCondition) : '';
			if ( !empty($this->referenceData['label']) )
			{
				if (empty($_POST[$name])) {
					$_POST[$name] = array();
				}
				$flagPendingQuery	= "_PENDING_QUERY";
				$pendingQuery[]		= $flagPendingQuery;
				foreach( $_POST[$name] as $i => $value )
				{
					if(in_array($value, $this->referenceData['value']))
					{
						$pendingQuery[] = 'INSERT INTO '.$this->relationTable.' SET '.$this->relationField['main'].'=\'_INSERT_ID\''
							.', '.$this->relationField['reference'].'=\''.$this->cleanSQL($value).'\''.$add_sql;
					}
				}
			}
		}else{
			$pendingQuery['into']	= '`'.$this->fieldName .'`, ';
			$pendingQuery['value']	= "'". $this->cleanSQL(repairImplode(@$_POST[$name])) ."', ";
		}
		return $pendingQuery;
	}
	function getDeleteSQL($ids)
	{
		$sql = '';
		if ($this->relationTable && !empty($ids))
		{
			if (is_array($ids))
			{
				ids($ids);
			}
			$sql = "DELETE FROM ". $this->relationTable." WHERE ".$this->relationField['main']." IN ({$ids})";
			$sql = $this->getTableCondition($sql, $this->sqlRelationCondition);
		}
		return $sql;
	}
	function getColumn($i, $devide = 5)
	{
		$col = 1;
		if ($i > $devide)
		{
			$col = floor($i/$devide);
			if ($col < 2)
			{
				$col = 2;
			}else
			if ($col > 6)
			{
				$devide += 5;
				$col = $this->getColumn($i, $devide);
			}
		}
		if ($col==5)
		{
			$col = 6;
		}
		return $col;
	}

	function getReportOutput( $str_value = '' )
	{
		$out = array();
		$relationData	= $this->getDataFromRelationTable($str_value);
		if ( !$this->isLoaded->getDataFromReferenceTable )
		{
			$this->getDataFromReferenceTable();
		}
		foreach( $this->referenceData['label'] as $i=>$label )
		{
			if (in_array($this->referenceData['value'][$i], $relationData))
			{
				$out[] = $label;
			}
		}
		return implode($this->delimiter, $out);
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext )
		{
			return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		}
		$extra	= $this->extra.' '. $str_extra;
		$output = array();
		$this->getDataFromReferenceTable();
		if (preg_match('~(\s?req=".*?")~', $extra, $match))
		{
			$extra = $match[1];
		}else{
			$extra = "";
		}
		if ( !empty($this->referenceData['label']) )
		{
			$relationData	= $this->getDataFromRelationTable($str_value);
			foreach( $this->referenceData['label'] as $i => $label )
			{
				$name		= ( $str_name == '' ) ? $this->name : $str_name;
				$name		= $name."[$i]";
				$checked= ( in_array( $this->referenceData['value'][$i], $relationData)  ) ? ' checked' : '';
				if($this->isPlaintext)
				{
					if($checked==' checked') $output[]	= $label;
				}else{
					$output[]	= '<label>'
										. '<input name="'.$name.'" type="checkbox" value="'.$this->referenceData['value'][$i].'"'.$checked.$extra.'> '
										. $label.'</label>';
				}
			}
		}
		$count = count($output);
		if ($this->column == 1)
		{
			$this->column = $this->getColumn($count);
		}
		if ($this->column > 1)
		{
			$c = ceil($count/$this->column);
			$i = $j = 0;
			foreach ($output as $data)
			{
				$r[$i][] = $data;
				$j++;
				if ($j == $c)
				{
					$j=0;
					$i++;
				}
			}
			$out = '';
			$x   = floor(12/$this->column);
			foreach ($r as $col)
			{
				$out .= '<div class="col-md-'.$x.' col-sm-'.$x.'">'.implode($this->delimiter, $col).'</div>';
			}
			$out .= '<div class="clearfix"></div>';
		}else{
			$out = implode($this->delimiter, $output);
		}
		return '<div class="checkbox">'.$out.'</div>';
	}
}
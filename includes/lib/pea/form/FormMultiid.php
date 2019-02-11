<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
EXAMPLE: (yang diawali # adalah optional)
$form->edit->addInput('related','multiid');
#form->edit->input->related->setTitle('Related Content');
$form->edit->input->related->setReferenceTable('bbc_content_text');				# menentukan table reference
$form->edit->input->related->setReferenceField('title','content_id');			# menentukan field yang digunakan untuk table reference
#form->edit->input->related->setReferenceCondition('lang_id='.lang_id());	# jika ada tambahan dalam pencarian di table reference (bisa banyak)
#form->edit->input->related->setDB('db1');																# diisi string untuk database lain berdasarkan urutan di config.php

// JIKA MENGGUNAKAN RELATION TABLE SEPERTI DI BAWAH MAKA 'related' AKAN MENJADI FIELD DENGAN NAMA BEBAS
// ATAU TIDAK MENJADI FIELD DALAM TABLE DATABASE LAGI SEHINGGA TIDAK DI QUERY DALAM `SELECT`
#form->edit->input->related->setRelationTable('bbc_content_category');
#form->edit->input->related->setRelationField('content_id', 'category_id');
// jika table relasi menggunakan field primary yang auto_increment maka gunakan di bawah
// jika tidak menggunakan opsi di bawah, maka secara otomatis akan diambil dari field pertama di DB dari relationTable
#form->edit->input->related->setRelationTableId('id');

// HARUS ADA FIELD `related` (->related->) PADA TABLE DATABASE JIKA TIDAK MENGGUNAKAN RELATION TABLE
// SEDANGKAN TYPE FIELD == `related` varchar(255) DEFAULT NULL
*/
include_once _PEA_ROOT.'form/FormMulticheckbox.php';
class FormMultiid extends FormMulticheckbox
{
	var $links;
	var $isOrderby;
	function __construct()
	{
		$this->type = 'multiid';
		$this->setDelimiter(',');
		$this->setIsNeedDbObject( true );
		$this->setIsIncludedInSelectQuery( true );
		$this->setIsIncludedInDeleteQuery(true); // agar getDeleteSQL($ids) di eksekusi sebelum dataRow dihapus
		$this->setIsIncludedInSearch( false );
	}
	function setLinks( $str_links = '' )
	{
		$this->links = $str_links;
	}
	function setRelationTable( $str_relation_table )
	{
		if(!empty($str_relation_table))
		{
			$this->relationTable	= $str_relation_table;
			$r = $this->db->getCol("SHOW FIELDS FROM ".$this->relationTable);
			if (in_array('orderby', $r))
			{
				$this->isOrderby = true;
			}
			$this->relationTableId = $r[0];
			$this->setIsIncludedInSelectQuery( false );
		}
	}
	function getDataFromRelationTable($str_value)
	{
		$output = array();
		if ($this->actionType=='add')
		{
			return $this->defaultValue;
		}
		if(!empty($this->relationTable))
		{
			if (empty($this->relationField['reference']) || empty($this->relationField['main']))
			{
				die( __METHOD__.':: setRelationField($str_main_id_field, $str_reference_id_field) harus diset untuk menentukan table dan field yang digunakan.' );
			}
			if (!empty($str_value))
			{
				$q = 'SELECT '. $this->relationField['reference'].' FROM '. $this->relationTable.' WHERE '.$this->relationField['main'].'='.intval($str_value);
				$q = $this->getTableCondition($q, $this->sqlRelationCondition);
				if ($this->isOrderby && !preg_match('~ order by ~is', $q))
				{
					$q .= ' ORDER BY orderby ASC';
				}
				$output	= implode($this->delimiter, $this->db->getCol($q));
			}
		}
		if(empty($output))
		{
			$output = $this->getValue($str_value);
		}
		return $output;
	}
	function getIDsFromReferenceTable($str_value)
	{
		if (empty($this->referenceTable) || empty($this->referenceField['value']) || empty($this->referenceField['label']))
		{
			die( 'FormMulticheckbox:: setReferenceTable($str_reference_table) dan setReferenceField($str_reference_label_field, $str_reference_value_field) harus diset untuk menentukan table dan field yang digunakan.' );
		}
		$out = array();
		$str = $this->getValue($str_value);
		if (!empty($str))
		{
			$sql = "SELECT ". $this->referenceField['value'] ." AS `value`, "
					. $this->referenceField['label'] ." AS `label`"
					. "	FROM ". $this->getTableCondition($this->referenceTable, $this->sqlReferenceCondition);
			$sql = $this->getTableCondition($sql, array($this->referenceField['value'].' IN ('.$str.')'));
			if (!preg_match('~\s+order\s+by\s+~is', $sql))
			{
				$sql .= ' ORDER BY `label` ASC';
			}
			$result	= $this->db->getAssoc( $sql );
			$ids = explode($this->delimiter, $str);
			foreach ($ids as $id)
			{
				if (isset($result[$id]))
				{
					$out[$id] = $result[$id];
				}
			}
		}
		return $out;
	}
	function getValue($value)
	{
		if (is_array($value))
		{
			$value = implode($this->delimiter, $value);
		}
		$str = preg_replace('~[^0-9'.$this->delimiter.']+~s', $this->delimiter, $value);
		$r   = array_unique(explode($this->delimiter, $str));
		return trim(implode($this->delimiter, $r), $this->delimiter);
	}
	function getToken()
	{
		$output = array(
			'reference' => array(
				'table' => $this->referenceTable,
				'value' => $this->referenceField['value'],
				'label' => $this->referenceField['label'],
				'sql'   => $this->sqlReferenceCondition
				),
			'expire' => strtotime('+2 HOURS'),
			);
		if (!empty($this->links))
		{
			$output['links'] = $this->links;
		}
		if (!empty($this->db_str))
		{
			$output['reference']['db'] = $this->db_str;
		}
		return encode(json_encode($output));
	}
	function getRollUpdateSQL( $i='' )
	{
		$name    = $this->name;
		$output  = '';
		if ( $i == '' && !is_numeric($i) )
		{
			/*==============================================
			UPDATE SQL DARI EDIT FORM
			==============================================*/
			$query       = 'SELECT '.$this->tableId.' FROM '.$this->tableName .' '. $this->sqlCondition;
			$post        = isset($_POST[$name]) ? $_POST[$name] : '';
			$mainTableId = $this->actionType == 'add' ? 0 : (!empty($this->parent->arrResult[$this->tableId]) ? intval($this->parent->arrResult[$this->tableId]) : intval($this->db->getOne($query)));
		}else{
			/*==============================================
			UPDATE SQL DARI ROLL FORM
      ==============================================*/
			$post        = isset( $_POST[$name][$i] ) ? $_POST[$name][$i] : '';
			$mainTableId = @intval($_POST[$this->formName.'_'.$this->tableId][$i]);
		}
		$resultData  = $this->getIDsFromReferenceTable($post);
		$ref_ids     = array_keys($resultData);
		if($this->relationTable)
		{
			$add_sql = !empty($this->sqlRelationCondition) ? ', '.implode(', ', $this->sqlRelationCondition) : '';
			$orderby = 1;
			if (empty($this->relationTableId))
			{
				/* JIKA TABLE_ID DARI RELATION TABLE TIDAK ADA, MAKA LANGSUNG HAPUS SEMUA LALU DIMASUKKAN LAGI */
				$sql = 'DELETE FROM '. $this->relationTable.' WHERE '.$this->relationField['main'].'='.$mainTableId;
				$sql = $this->getTableCondition($sql,$this->sqlRelationCondition);
				$this->db->Execute( $sql );

				foreach( $resultData as $value => $label )
				{
					$q = 'INSERT INTO '.$this->relationTable.' SET '.$this->relationField['main'].'='.$mainTableId
							.', '.$this->relationField['reference'].'=\''. $value.'\''.$add_sql;
					if ($this->isOrderby)
					{
						$q .= ', orderby='.$orderby;
						$orderby++;
					}
					$this->db->Execute( $q );
				}
			}else{
				/* JIKA MENGGUNAKAN TABLE_ID UNTUK RELATION TABLE, MAKA HAPUS DAN INSERT SATU-SATU AGAR LEBIH RINGAN */
				$q = 'SELECT '.$this->relationTableId.', '.$this->relationField['reference'].' FROM '.$this->relationTable;
				$c = array_merge($this->sqlRelationCondition, array($this->relationField['main'].'='.$mainTableId));
				$Q = $this->getTableCondition($q,$c);
				$r = $this->db->getAssoc($Q);
				// HAPUS ID YANG ADA DI TABLE RELATION TAPI TIDAK ADA DI POSTINGAN
				foreach ($r as $tableId => $ref_id)
				{
					if (!in_array($ref_id, $ref_ids))
					{
						if (!is_numeric($tableId)) {
							$tableId = "'{$tableId}'";
						}
						$this->db->Execute("DELETE FROM {$this->relationTable} WHERE {$this->relationTableId}={$tableId}");
					}
				}
				// INSERT ID YANG TIDAK ADA DI TABLE RELATION TAPI ADA DI POSTINGAN
				foreach( $resultData as $value => $label )
				{
					if(!in_array($value, $r))
					{
						$q = 'INSERT INTO '.$this->relationTable.' SET '.$this->relationField['main'].'='.$mainTableId
							.', '.$this->relationField['reference'].'=\''.$value.'\''.$add_sql;
						$this->db->Execute($q);
					}
				}
				// URUTKAN TABLE RELATION JIKA MEMANG ADA PENGURUTAN
				if ($this->isOrderby)
				{
					$q = 'SELECT '.$this->relationField['reference'].', '.$this->relationTableId.' FROM '.$this->relationTable;
					$c = array_merge($this->sqlRelationCondition, array($this->relationField['main'].'='.$mainTableId));
					$Q = $this->getTableCondition($q,$c);
					$r = $this->db->getAssoc($Q);
					foreach( $resultData as $value => $label )
					{
						$q = "UPDATE {$this->relationTable} SET orderby={$orderby} WHERE {$this->relationTableId}=".$r[$value];
						$this->db->Execute($q);
						$orderby++;
					}
				}
			}
		}else{
			$output = "`".$this->fieldName."` = '".$this->getValue(implode($this->delimiter, $ref_ids))."', ";
		}
		return $output;
	}
	function getAddSQL()
	{
		$add_sql      = !empty($this->sqlRelationCondition) ? ', '.implode(', ', $this->sqlRelationCondition) : '';
		$pendingQuery = array();
		$postData     = $this->getIDsFromReferenceTable(@$_POST[$this->name]);
		$orderby      = 1;
		if($this->relationTable)
		{
			$pendingQuery[]		= '_PENDING_QUERY';
			foreach ($postData as $value => $label)
			{
				$q = $add_sql;
				if ($this->isOrderby)
				{
					$q .= ', orderby='.$orderby;
				}
				$pendingQuery[] = 'INSERT INTO '.$this->relationTable
				.' SET '.$this->relationField['main'].'=\'_INSERT_ID\''
				.", `".$this->relationField['reference']."`='{$value}'"
				.$q;
				$orderby++;
			}
		}else{
			$pendingQuery['into']  = $this->fieldName .", ";
			$pendingQuery['value'] = "'".implode($this->delimiter, array_keys($postData))."', ";
		}
		return $pendingQuery;
	}
	function getDeleteSQL($ids)
	{
		$sql = '';
		ids($ids);
		if (!empty($ids))
		{
			if ($this->relationTable)
			{
				$sql = "DELETE FROM ". $this->relationTable." WHERE ".$this->relationField['main']." IN ({$ids})";
				$sql = $this->getTableCondition($sql, $this->sqlRelationCondition);
			}
		}
		return $sql;
	}
	function getReportOutput( $str_value = '' )
	{
		$out = $this->getDataFromRelationTable($str_value);
		$out = $this->getIDsFromReferenceTable($out);
		return implode($this->delimiter.' ', $out);
	}
	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ($this->isPlaintext)
		{
			return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		}
		$name  = ( $str_name == '' ) ? $this->name : $str_name;
		$extra = $this->extra.' '. $str_extra;
		$str   = $this->getDataFromRelationTable($str_value);
		$out   = '<div class="input-group FormMultiid">'
						.'<input name="'. $name .'" type="text" value="'. $str .'" data-token="'.$this->getToken().'" '.$extra.' />'
						.'<span class="input-group-addon">'.icon('play').'</span>'
						.'</div><ul class="list-group"></ul>';
		link_js(_PEA_URL.'includes/FormMultiid.js');
		return $out;
	}
}
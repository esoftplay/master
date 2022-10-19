<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

class Form
{
	var $db;
	var $db_str;
	var $title;
	var $caption;
	var $name;
	var $fieldName;
	var $size;
	var $align = 'left';
	var $extra;
	var $type;
	var $actionType;
	var $displayFunction         = null;
	var $reportFunction          = false;
	var $exportFunction          = false;
	var $formName                = '';
	var $isHeader                = false;// apakah suatu element itu merupakan header atau bukan, krn kl header, maka tr nya make background header, default false
	var $isHidden                = false;// apakah suatu element itu sama sekali tidak ditampilkan
	var $isInsideRow             = true; // apakah suatu element itu didalam row tr/td atau tidak, default true
	var $isInsideCell            = true; // apakah suatu element itu didalam row td atau tidak, default true
	var $isIncludedInSelectQuery = true; // apakah suatu element itu akan ikut didalam select query
	var $isIncludedInUpdateQuery = true; // apakah suatu element itu akan ikut didalam update query atau insert query
	var $isIncludedInDeleteQuery = false;// apakah getDeleteSQL($ids) akan di eksekusi sebelum baris dihapus (Roll & Edit)
	var $isIncludedInReport      = true; // apakah suatu element itu akan ikut didalam reporting
	var $isIncludedInSearch      = true; // apakah suatu element itu akan ikut didalam search
	var $isInsideMultiInput      = false;// untuk menandai suatu input, apakah suatu multi input ato bukan, karena kalo multi akan mendapatkan perlakuan khusus
	var $isMultiLanguage         = false;// apakah field ini merupakan field multi language
	var $isPlaintext             = false;// apakah component ini bukan merupakan output form, tapi merupakan plaintext, hanya untuk view detail
	var $isRequire               = false;// apakah component ini harus diisi oleh user ataukah tidak
	var $isDisplayColumn         = null; // apakah kolom ingin ditampilkan (hanya berlaku untuk phpRollAdmin saja)
	var $like                    = false;// jika untuk form search maka akan menggunakan LIKE '%$key%' atau = (sama dengan)
	var $isLoaded;
	var $objectName;
	var $noColumn       = 1;		// Jika form menggunakan multi kolom maka field ini akan menentukan posisi input berada di kolom mana
	var $isNeedDbObject = false;// apakah perlu object db, supaya bisa execute mysql
	var $status         = ''; 	// untuk persiapan status
	var $textHelp       = ''; 	// Text tips yang akan ditampilkan pada icon help di judul input
	var $textTip        = ''; 	// Text tips yang akan ditampilkan setelah input field
	var $tableName;							// table dari class phpAdd/Edit/RollAdmin, biasanya untuk type file
	var $tableId;								// table id dari class phpRollAdmin, biasanya untuk type file
	var $sqlCondition;					// sqlCondition dari class phpEditAdmin
	var $defaultValue;					// defaultValue jika actionType=='add'

	function __construct()
	{
		if(empty($this->isLoaded))
		{
			$this->isLoaded	= new stdClass();
		}
	}

	/**
	* set adodb instant
	* its used when a component need to query something from database
	* you dont need to call it, its automatically call by phpEasyAdmin
	* when $isNeedDbObject propery is true
	*
	* ex: $this->setDbObject( $db );
	*
	* @param string title
	* @access private
	*/
	function setDbObject( $db )
	{
		$this->db	= $db;
	}
	function setDB($string_db)
	{
		$str = 'db';
		if (empty($string_db))
		{
			global $db;
		}else
		if (is_string($string_db))
		{
			$str = $string_db;
			$db  = $GLOBALS[$string_db];
		}else{
			die(__CLASS__.'::setDB($parameter) -> $parameter nya harus berupa string dari variable database, semisal jika anda PUNYA $db1 maka masukkan paramater nya "db1" sebagai string');
		}
		$this->db     = $db;
		$this->db_str = $str;
	}

	/**
	* set Title of this form component
	* ex: $this->setTitle( "Company Name" );
	*
	* @param string title
	*/
	function setTitle( $str_title )
	{
		$this->title		= $str_title;
	}

	/**
	* set Label of this form component
	* ex: $this->setCaption( "Company Name" );
	*
	* @param string title
	*/
	function setCaption( $str_caption='' )
	{
		$this->caption	= $str_caption;
	}
	function setIcon( $str_caption = 'edit', $str_alt = '' )
	{
		$str_alt = $str_alt ? $str_alt : $str_caption;
		$this->setCaption(icon($str_caption,$str_alt));
	}

	/**
	* set Align of form component
	* ex: $this->setAlign( "center" );
	*
	* @param string align, allowed arguments: center | right | left
	*/
	function setAlign( $str_align	= "center" )
	{
		$this->align		= $str_align;
	}

	/**
	* set database field name
	* automatically call setName method and set the objectName properties
	* ex: $this->setFieldName( 'user_id' );
	*
	* @param string database fieldname
	*/
	function setFieldName( $str_field_name )
	{
		$this->fieldName = $str_field_name;
		$name            = $this->fieldName;
		if(preg_match ('~ as ~is',$this->fieldName))
		{
			if (preg_match('~(.*)\s+as\s+(.*)~is', $this->fieldName, $match ))
			{
				$name=$match[2];
			}
		}
		$this->setName( $name );
		$this->objectName	= $name;
	}

	/**
	* set the Name of this component form
	*
    * @param string the name of this components
    */
	function setName( $str_name )
	{
		$this->name	= $this->formName .'_'. $str_name;
	}

	/**
	* set the Name of html form
	* its usually used when we using two instance of phpEasyAdmin in one page
	* so this name is like a id for a form
	* this method automatically called by phpEasyAdmin, you dont to care about this
	*
    * @param string the name id of this form
	* @access private
    */
	function setFormName( $str_form_name )
	{
		$this->formName	= $str_form_name;
	}

	/**
	* set the element to use multiple language
	* this method only works in type : sqllinks, sqlplaintext, text, textarea,
	*
	* @access public
	*/
	function setLanguage($isMultiLanguage=true)
	{
		$allow = array('sqllinks', 'sqlplaintext', 'text', 'textarea');
		if($isMultiLanguage)
		{
			if(in_array($this->type, $allow))
			{
				$this->isMultiLanguage				= true;
				$this->isIncludedInSelectQuery= false;
				$this->isIncludedInUpdateQuery= false;
				if (is_string($isMultiLanguage))
				{
					$this->multiExtraField = $isMultiLanguage;
				}
			}else{
				die('Type : '.$this->type.' doesn\'t support multi languages');
			}
		}
	}

	/**
	* set the element to show / hide from the table
	* this method only works in phpRollAdmin only
	*
	* @access public
	*/
	function setDisplayColumn($boolean=false)
	{
		if ($this->actionType == 'roll')
		{
			$this->isDisplayColumn = $boolean;
		}else{
			die( 'Method <a href="http://dev.esoftplay.com/Form'.ucwords(trim(strtolower($this->type))).'::setDisplayColumn" target="_blank">setDisplayColumn()</a> hanya bisa digunakan dalam <a href="http://dev.esoftplay.com/phpRollAdmin" target="_blank">phpRollAdmin</a>');
		}
	}
	/**
	* set what function to call before displaying
	* this method only works if isPLaintext is TRUE
	*
	* @access public
	*/
	function setDisplayFunction($obj_func, $use_in_report=true)
	{
		$this->displayFunction = $obj_func;
		$this->reportFunction = $use_in_report ? true : false;
	}
	/**
	* set what function to call before printing in report
	* this method only works for single field form
	*
	* @access public
	*/
	function setExportFunction($obj_func)
	{
		$this->exportFunction = $obj_func;
	}
	/**
	* set the element to fill the column number of form
	* this method only works in in multiple column
	*
	* @access public
	*/
	function setNoColumn($noColumn=1)
	{
		$this->noColumn = $noColumn;
	}

	/**
	* set the Size of its html form component
	* for example when we create an input with text type
	* so its will output like this: <input type=text ..... size='10' >
	*                                                             |
	* this medthod used to set that input size <------------------|
	* the size is set to default if you not call it
	*
    * @param int the html size of component form
    */
	function setSize( $int_size )
	{
		$this->size		= $int_size;
	}

	/**
	* set the defaultValue
	* this method is used to set the defaultValue for Add / Search form ( phpAddAdmin class & phpSearchAdmin class )
	*
    * @param string the default value of component, in an Add form ( phpAddAdmin class )
    */
	function setDefaultValue( $str_default_value )
	{
		$this->defaultValue		= $str_default_value;
	}

	/**
	* set the extra text added in the end of component
	* this method is experimental
	* ex:
	* $this->setExtra( "onclick='gotoformone()'" );
	*                           |_______________
	*                                          V
	* <input type=text ..... size='10' onclick='gotoformone()' >
	*
    * @param string the default value of component, in an Add form ( phpAddAdmin class )
    */
	function setExtra( $str_extra )
	{
		$this->extra		= $str_extra;
	}

	function setPlaintext( $bool_is_plaintext = false )
	{
		$this->setIsIncludedInUpdateQuery( !$bool_is_plaintext );
		$this->isPlaintext		= $bool_is_plaintext;
	}

	// Untuk memvalidasi form (hanya untuk edit dan add)
	// setRequire( any | email | url | phone | money | number)
	function setRequire( $is_input_require = 'any', $is_mandatory = 1 )
	{
		$is_mandatory    = $is_mandatory ? 'true' : 'false';
		$this->isRequire = $is_input_require.' '.$is_mandatory;
	}

	// apakah suatu element itu merupakan header atau bukan
	function setIsHeader( $bool_header )
	{
		$this->isHeader= $bool_header;
	}

	function setIsMultiInput( $bool_multi )
	{
		$this->isInsideMultiInput= $bool_multi;
	}

	// untuk ngasih tau object ini dia dipanggil oleh class apa
	// isinya bisa edit/add/search/roll
	function setActionType( $str_action_type )
	{
		$this->actionType= $str_action_type;
	}

	// apakah suatu element ditampilkan atau tidak
	function setHidden( $bool_hidden	= true )
	{
		$this->isHidden= $bool_hidden;
	}

	// apakah suatu element itu didalam row tr/td atau tidak, default true
	function setIsInsideRow( $bool_inside )
	{
		$this->isInsideRow= $bool_inside;
	}

	// apakah suatu element itu didalam row td atau tidak, default true
	function setIsInsideCell( $bool_inside )
	{
		$this->isInsideCell= $bool_inside;
	}

	function setIsNeedDbObject( $bool_need = true )
	{
		$this->isNeedDbObject	= $bool_need;
	}

	function setTableName( $str_table_name )
	{
		$this->tableName	= $str_table_name;
	}

	function setTableId( $str_table_id )
	{
		$this->tableId	= $str_table_id;
	}

	function setSqlCondition( $sql_condition )
	{
		$this->sqlCondition	= $sql_condition;
	}

	// secara default parentObject tidak perlu di load untuk menjaga performa
	function setParent($obj)
	{
		return;
	}

	// apakah suatu element itu akan ikut didalam SELECT query
	function setIsIncludedInSelectQuery( $bool_included )
	{
		$this->isIncludedInSelectQuery	= $bool_included;
	}

	// apakah suatu element itu akan ikut didalam UPDATE query
	function setIsIncludedInUpdateQuery( $bool_included )
	{
		$this->isIncludedInUpdateQuery	= $bool_included;
	}
	// apakah suatu element itu akan dieksekusi didalam DELETE query (sebelum row dihapus)
	function setIsIncludedInDeleteQuery( $bool_included )
	{
		$this->isIncludedInDeleteQuery = $bool_included;
	}

	// apakah suatu element itu akan ikut didalam Reporting
	function setIsIncludedInReport( $bool_included )
	{
		$this->isIncludedInReport	= $bool_included;
	}

	// apakah suatu element itu akan ikut didalam Reporting
	function setIsIncludedInSearch( $bool_included )
	{
		$this->isIncludedInSearch	= $bool_included;
	}

	function getRollUpdateQuery( $i = '' )
	{
		// kalau element bersangkutan di includekan di dalam query, baru direturn kan sql nya
		if( $this->isIncludedInUpdateQuery )
		{
			return $this->getRollUpdateSQL( $i );
		}
		else
			return '';
	}

	function getRollUpdateSQL( $i = '' )
	{
		if ( $i == '' && !is_int($i) )
			$val	= $_POST[$this->name];
		else
			$val	= $_POST[$this->name][$i];
		return $query = $this->getFieldName()."='". $this->cleanSQL($val) ."', ";
	}

	function getAddQuery()
	{
		// kalau element bersangkutan di includekan di dalam query, baru direturn kan sql nya
		if( $this->isIncludedInUpdateQuery )
		{
			return $this->getAddSQL();
		}
		else
			return '';
	}

	// replace method di bawah ketika di formAdd dan akan dieksekusi setelah database dimasukkan jika $this->isIncludedInUpdateQuery==true
	function getAddAction($db, $Insert_ID)
	{
		return '';
	}

	function getDeleteQuery($ids)
	{
		// kalau element bersangkutan di includekan di dalam query, baru direturn kan sql nya
		if( $this->isIncludedInDeleteQuery )
		{
			return $this->getDeleteSQL($ids);
		}
		else
			return '';
	}

	function setSearchQueryLike($bool_like)
	{
		$this->like=$bool_like;
	}

	function getSearchQuery()
	{
		$searchCondition = '';
		// di includekan dalam condisi search jika _POST bersangkutan ada
		if (isset($this->defaultValue) && $this->defaultValue !== '') // pakai !== supaya 0 (nol) masih bisa di lewatkan
		{
			$val	= $this->defaultValue;
			if($this->like)
			{
				$searchCondition	= $this->getFieldName().' LIKE \'%'. $val .'%\'';
			}else{
				if (!is_numeric($val))
				{
					$val = "'{$val}'";
				}
				$searchCondition	= $this->getFieldName().'='.$val;
			}
		}// eof if ( isset( $_POST[$this->searchButton->name] ) )
		return $searchCondition;
	}

	// default method untuk mengambil value dan into dalam insert query di phpAddAdmin()
	function getAddSQL()
	{
		$name			= $this->name;
		$out['into']	= $this->getFieldName().', ';
		$out['value']	= "'".$this->cleanSQL($_POST[$name])."', ";
		return $out;
	}

	// default method untuk mengambil value dan into dalam insert query di phpAddAdmin()
	function getDeleteSQL($ids)
	{
		return '';
	}

	// clean SQL string
	function cleanSQL($q)
	{
		$o = preg_replace('~\\{2,}(\'|")~s', '\\$1', $q);
		$o = preg_replace("~([^\\\])('|\")~s", '$1\\\$2', $o);
		return $o;
	}
	function getFieldName()
	{
		if (!preg_match ('~ as ~is',$this->fieldName) && !preg_match('~`~', $this->fieldName))
		{
			return '`'.$this->fieldName .'`';
		}else{
			return $this->fieldName;
		}
	}

	// ini fungsi untuk memanggil getPlaintextOutput
	// ngecek dulu apakah plaintext atau bukan
	function checkIsPlaintext( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext )
		return $this->getReturn($this->getPlaintexOutput( $str_value, $str_name, $str_extra ));
	}

	function getPlaintexOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		return $this->getReturn($this->getReportOutput( $str_value ));
	}

	function getReportOutput( $str_value = '' )
	{
		if (is_array($str_value))
		{
			$str_value = current($str_value);
		}
		return $str_value;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		return 'defaultFormOutput';
	}

	function getReturn($value)
	{
		if (is_callable($this->displayFunction))
		{
			$value = call_user_func_array($this->displayFunction, array($value));
		}
		if (!empty($value) || $value == '0')
		{
			if ($this->actionType=='roll' || $this->type=='hidden')
			{
				return $value;
			}
			$c = '';
			// $c = preg_match('~(?:radio|checkbox)~is', $this->type) ? ' checkbox' : '';
			return '<div class="form-control-static form-control-plaintext'.$c.'">'.$value.'</div>';
		}else{
			return '';
		}
	}
	// fungsi untuk mengeset nama form dan nama field yang akan di fokus, dan kemudian masukkan ke $GLOBALS, kemudian di THEME harus meload manual
	function setFocus()
	{
		if (empty($this->formName))
		{
			$this->setFormName('EasyForm');
		}
		$GLOBALS['initFocus']['formName']  = $this->formName;
		$GLOBALS['initFocus']['fieldName'] = $this->name;
	}
	function setHelp( $value = '' )
	{
		$this->textHelp = $value;
	}
	function setTip( $value = '' )
	{
		$this->textTip = $value;
	}
	function addHelp( $value = '' )
	{
		$this->textHelp .= $value;
	}
	function addTip( $value = '' )
	{
		$this->textTip .= $value;
	}
}
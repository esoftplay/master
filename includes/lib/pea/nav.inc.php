<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!defined('_PEA_ROOT'))
{
	define ('_PEA_ROOT', dirname(__FILE__) .'/');
}

if (!defined('_BASEDIR'))
	define('_BASEDIR', _PEA_ROOT);

if (!defined('_BASIC_FILE'))
	define('_BASIC_FILE', _BASEDIR . 'basic.inc.php');

if (!defined('_SQLPARSER_BASEDIR'))
	define('_SQLPARSER_BASEDIR', _BASEDIR . 'sqlParser/');

include_once(_BASIC_FILE);
include_once(_SQLPARSER_BASEDIR . 'sqlParser.php');


//class oNav{
class oNav extends oDebug
{
	var $int_max_rows;			// jml maksimum roll
	var $query;					// query string, berbentuk array, setelah di parsing
	var $completeQuery;

	var $db;					// Instant dari class adodb

	var $string_cur_uri;
	var $cur_sql_pos;
	var $int_tot_rows;
	var $int_tot_page;
	var $int_num_rows_this_page;	// jumlah baris dari halaman yang sedang diakses

	var $layout;					// berisi array untuk nyimpen konfigurasi2 tampilan

	var $arr_search_field;			// field yang akan digunakan untuk search

	var $num_nav;				//jumlah navigasi kesamping, misale nilainya 3 maka navigasinya <<prev 1 2 3 next>>
	var $string_name;			// nama dari _GET variable miasalle page => http://localhost/ogi.php?page=9 => default page
	var $arr_result;
	var $int_pointer;
	var $is_loaded;				// untuk menandai apakah suatu action telah di jalankan
	var $a;

	function __construct($string_query, $string_table_id='id', $int_max_rows=10, $int_num_nav='10', $string_name='page', $db='')
	{
		$this->completeQuery          = $string_query;
		$this->int_max_rows           = $int_max_rows;
		$this->num_nav                = $int_num_nav;
		$this->string_name            = $string_name;
		$this->arr_result             = array();
		$this->int_pointer            = 0;
		$this->cur_sql_pos            = 0;
		$this->is_loaded['get_data']  = false;
		$this->is_loaded['get_array'] = false;
		$this->layout['prev_word']    = '&laquo;';
		$this->layout['next_word']    = '&raquo;';
		$this->layout['result']       = 'Result';			// @$_SESSION['lang'] == 'en' ? 'Result':"Data ke";
		$this->layout['to']           = 'to';					// @$_SESSION['lang'] == 'en' ? 'to':"s/d ke ";
		$this->layout['of']           = 'from total';	// @$_SESSION['lang'] == 'en' ? "from total":"dari total";
		$this->layout['link_class']   = '';						// contoh :  linktitle
		$this->layout['bgcolor'][0]   = '#FFFFFF';		// contoh :  #787687
		$this->layout['bgcolor'][1]   = '';						// contoh :  #787687
		$this->setDB($db);
	}


	// fungsi setDB untuk nge set instant database adodb yang mau di pake
	// secara default menggunakan $db
	function setDB($db = '')
	{
		if (empty($db))
		{
			global $db;
		}else
		if (is_string($db))
		{
			$db = $GLOBALS[$db];
		}
		$this->db = $db;
	}

	function setNumMaxRows($int_max_rows = '10')
	{
		$this->int_max_rows = $int_max_rows;
	}

	// masukkan array yang berisi field yang akan di include kan dalam search
	function setSearchField($arr_field)
	{
		$this->arr_search_field = $arr_field;
	}

	// untuk parsing query
	// dicari yang mana table, yang mana nama2 field nya
	function parseQuery()
	{
		// query di pecah menjadi array


		$result = parseSQL ($this->completeQuery);

		$result['field']	= getFieldsFromSQL ($this->completeQuery);

		// sqlparser emang tolol, ga mendukung where 1
		$result['all_after_from']	= $result['tableClause'] . ' '. $result['conditionClause'];

		$compileResult['all_field'] = $result['field'];
		$compileResult['all_field'] = $result['fieldClause'];
		$compileResult['all_table'] = $result['tableClause'];
		$compileResult['all_where']	= (!empty($result['whereClause'])) ? " WHERE ". $result['whereClause'] : '';
		$compileResult['all_order'] = (!empty($result['orderbyClause'])) ? " ORDER BY ". $result['orderbyClause'] : '';
		$compileResult['all_limit'] = (!empty($result['limitClause'])) ? " LIMIT ". $result['limitClause'] : '';
		$compileResult['distinct'] 	= (!empty($result['selectOption'])) ? true : false;
		$compileResult['after_from_no_limit']	= preg_replace('~\s+limit\s+~is', ' ', $result['all_after_from']);
		$compileResult['all_before_from'] = $result['fieldClause'];
		$this->query	= array_merge($result, $compileResult);
	}

	// Untuk menghitung jumlah baris dari return query tersebut
	// SELECT count(1)
	function getNumAllRows()
	{
		// ini method intern
		// untuk mendapatkan jumlah keseluruhan baris yang di query

		// mencari query clause setelah FROM

		if (!$this->query['distinct'])
		{
			$sql 	= "SELECT count(1) FROM ". $this->query['after_from_no_limit'];
		}else{
			if ($this->query['field'][0] != '*')
			{
				// menghilangkan as pada field pertama,
				// karena as menyebabkan error saat count
				$pos = strpos($this->query['field'][0], " as ");
				if (is_integer($pos))
				{
					 $first_field	= substr($this->query['field'][0], 0, $pos);
				}else{
					$first_field	= $this->query['field'][0];
				}

				$sql 	= "SELECT count(DISTINCT ". $first_field .") FROM ". $this->query['after_from_no_limit'];
			} else
			if (count($this->query['field']) > 1)
			{
				// menghilangkan as pada field pertama,
				// karena as menyebabkan error saat count
				$pos = strpos($this->query['field'][1], " as ");
				if (is_integer($pos)) {
					 $first_field	= substr($this->query['field'][1], 0, $pos);
				}else{
					$first_field	= $this->query['field'][1];
				}

				$sql 	= "SELECT count(DISTINCT ". $first_field .") FROM ". $this->query['after_from_no_limit'];
			}else{
				die('Query menggunakan DISTINCT field yang diselect harus bukan *');
			}
		}
		$sql = preg_replace('~\s+order\s+by\s+.*?$~is', '', $sql);
		$result = $this->db->GetOne($sql);
		$num_rows_count = $this->db->Affected_rows();

		// ini untuk menanggulangi kalau menggunakan group by
		if($num_rows_count == 1)
		{
			$this->int_tot_rows = $result;
		}else{
			$this->int_tot_rows = $num_rows_count;
		}

		return $this->int_tot_rows;
	}

	function getNumPage()
	{
		return $this->int_tot_page = ceil($this->int_tot_rows / $this->int_max_rows);
	}

	function parseUrl()
	{
		$this->string_cur_uri = preg_replace("#\?.*#", '', $_SERVER['REQUEST_URI']).'?';
		if (isset($_GET)) {
			foreach($_GET as $name=>$val)
			{
				if ($name != $this->string_name)
					$this->string_cur_uri .= $name . '=' . urlencode($val) .'&';
			}
		}
	}

	function getData()
	{
		// jika viewAll, maka setNumRows(500);
		if (isset($_GET[$this->string_name . '_viewAll']))
		{
			if ($_GET[$this->string_name . '_viewAll'] == '1')
			{
				$this->setNumMaxRows('500');
				// $_GET[$this->string_name]	= '1';
			}
		}

		if (isset($_GET[$this->string_name]))
		{
			if ($_GET[$this->string_name] != '')
			{
				$this->int_cur_page = $_GET[$this->string_name];
			}
		}
		else
			$this->int_cur_page=1;

		$this->cur_sql_pos = ($this->int_cur_page-1)*$this->int_max_rows;

		// pertama query di parsing dulu
		$this->parseQuery();

		// di hitung jumlah baris keseluruhan
		$this->getNumAllRows();

		// dihitung jumlah halamannya
		$this->getNumPage();

		// parsing url
		$this->parseUrl();

		$this->is_loaded['get_data'] = true;
	}

	//////////////////////////////////////////////////////////
	// set the navigation output, bout class, bgcolor, etc  //
	//////////////////////////////////////////////////////////
	function setPrevNextWord($string_prev_word, $string_next_word)
	{
		$this->layout['prev_word'] = $string_prev_word;
		$this->layout['next_word'] = $string_next_word;
	}

	function setLinkClass($string_class)
	{
		$this->layout['link_class'] = $string_class;
	}

	function setBgColorA($string_bgcolor)
	{
		$this->layout['bgcolor'][0]	= $string_bgcolor;
	}

	function setBgColorB($string_bgcolor)
	{
		$this->layout['bgcolor'][1]	= $string_bgcolor;
	}

	function setStatusWord($string_result, $string_to, $string_of)
	{
		$this->layout['result'] = $string_result;
		$this->layout['to'] 	= $string_to;
		$this->layout['of']	 	= $string_of;
	}


	//////////////////////////////////////////////////////////
	// get the navigation and/or its children               //
	//////////////////////////////////////////////////////////
	function getPrev()
	{
		$string_prev = '';
		if (!$this->is_loaded['get_data'])
		{
			$this->getData();
		}
		if ($this->int_cur_page != 1)
		{
			$int_prev_page = $this->int_cur_page-1;
			$a = !empty($this->layout['link_class']) ? ' class="'. $this->layout['link_class'] .'"' : '';
			$string_prev = "\n\t".'<li><a'.$a.' href="'.$this->string_cur_uri.$this->string_name.'='.$int_prev_page.'">'.$this->layout['prev_word'].'</a></li>'."\n\t";
		}
		return $string_prev;
	}

	function getNext()
	{
		if (!$this->is_loaded['get_data'])
		{
			$this->getData();
		}
		$string_next = '';
		if ($this->int_cur_page!=$this->int_tot_page && $this->int_tot_page!=0)
		{
			$int_next_page = $this->int_cur_page+1;
			$a = !empty($this->layout['link_class']) ? ' class="'. $this->layout['link_class'] .'"' : '';
			$string_next = "\n\t".'<li><a'.$a.' href="'.$this->string_cur_uri.$this->string_name.'='.$int_next_page.'">'.$this->layout['next_word'].'</a></li>'."\n\t";
		}
		return $string_next;
	}

	function getArrNav()
	{
		if (!$this->is_loaded['get_data']) $this->getData();
		$arr_nav = array();
		if ($this->int_tot_page < $this->num_nav)
		{
			$max = $this->int_tot_page;
		}else{
			$max = $this->int_cur_page + $this->num_nav -1;
		}

		if ($max > $this->int_tot_page)
		{
			$max = $this->int_tot_page;
		}

		if ($max > 2 * $this->num_nav-1)
		{
			$n = $max - 2 * $this->num_nav;
		}else{
			$n = 0;
		}
		$k = 0;
		$a = !empty($this->layout['link_class']) ? ' class="'. $this->layout['link_class'] .'"' : '';
		for ($i=$n; $i < $max; $i++)
		{
			$j = $i+1;
			$c = ($this->int_cur_page==$j) ? ' class="active"' : '';
			$arr_nav[$i] = '<li'.$c.'><a'.$a.' href="'.$this->string_cur_uri.$this->string_name.'='.$j.'">'.money($j).'</a></li>';
			$k++;
		}
		return $arr_nav;
	}

	function getNavStatus()
	{
		if (!$this->is_loaded['get_data']) $this->getData();
		$arr_status['begin'] = $this->cur_sql_pos + 1;
		$num_view_rows = $this->cur_sql_pos+$this->int_max_rows;
		$arr_status['end'] = ($num_view_rows < $this->int_tot_rows) ? $num_view_rows : $this->int_tot_rows;
		$arr_status['total'] =  $this->int_tot_rows;
		return $arr_status;
	}

	function cleanNav($string)
	{
		if (!empty($_GET['is_ajax']))
		{
			$string = preg_replace('~([\?&]+is_ajax=[^&]+)~', '', $string);
		}
		return $string;
	}

	function getNav()
	{
		if (!$this->is_loaded['get_data']) $this->getData();
		$print_nav = '';
		if ($this->int_tot_page > 1)
		{
			$print_nav = $this->getPrev().implode("\n\t", $this->getArrNav()).$this->getNext();
			if (!empty($print_nav)) {
				$print_nav = '<ul class="pagination pagination-sm" style="margin: 0;">'.$print_nav.'</ul>';
			}
		}
		return $this->cleanNav($print_nav);
	}

	function getStatus()
	{
		$arr_status = $this->getNavStatus();
		$status	= $this->layout['result'] .' '. money($arr_status['begin']);
		$status	.= ' '. $this->layout['to'] .' '. money($arr_status['end']);
		$status	.= ' '. $this->layout['of'] .' '. money($arr_status['total']);
		$status	.= ' ';

		if (isset($_POST[$this->string_name."_search"]))
		{
			$status = '';
		}
		return $status;
	}

	// untuk membuat form go to
	function getGoToForm($withFormTag=true)
	{
		$form	= '';
		if ($this->int_tot_page > 1 && empty($_GET['is_ajax']))
		{
			$action	= preg_replace("#\?.*#", '', $_SERVER['REQUEST_URI']);
			if ($withFormTag)
			{
				$form .= '<form method="GET" action="'.$action.'" name="'.$this->string_name.'_goto" role="form"><div class="input-group">';
			}
			foreach($_GET as $name=>$val)
			{
				if ($name != 'submit' && $name != $this->string_name && !empty($val))
				{
					$form .= '<input type=hidden name="'.$name.'" value="'.$val.'">';
				}
			}
			$total = money($this->int_tot_page);
			$form .= '
				<span class="input-group-addon">go to</span>
				<input type="text" name="'.$this->string_name.'" class="form-control" value="'.$this->int_cur_page.'">
				<span class="input-group-addon">of</span>
				<span class="input-group-addon">'.$total.'</span>
				<span class="input-group-btn">
					<button class="btn btn-default btn-secondary" type="submit">Go!</button>
				</span>';
			if ($withFormTag)
				$form	.= '</div></form>';
		}
		return $this->cleanNav($form);
	}

	// untuk membuat form go to
	function getViewAllLink($text_link_tampil = 'Show All',  $text_link_tidak_tampil	= 'Show part')
	{
		$out	= '';
		if ($this->int_tot_page > 1  || @$_GET[$this->string_name .'_viewAll'] == '1')
		{
			$string_cur_uri = preg_replace("#\?.*#", '', $_SERVER['REQUEST_URI']).'?';
			if (isset($_GET))
			{
				foreach($_GET as $name=>$val)
				{
					if ($name != $this->string_name .'_viewAll')
						$string_cur_uri .= $name . '=' . urlencode($val) .'&';
				}
			}
			if (!isset($_GET[$this->string_name .'_viewAll']))
			{
				$_GET[$this->string_name .'_viewAll']	= '0';
			}

			$a = !empty($this->layout['link_class']) ? ' class="'.$this->layout['link_class'].'"' : '';
			if ($_GET[$this->string_name .'_viewAll'] == '1')
			{
				$out .= '<a '.$a.' href="'.$string_cur_uri.$this->string_name.'_viewAll=0" >'.$text_link_tidak_tampil.'</a>';
			}else{
				$out .= '<a '.$a.' href="'.$string_cur_uri.$this->string_name.'_viewAll=1" >'.$text_link_tampil.'</a>';
			}
		}
		return $this->cleanNav($out);
	}

	// untuk membuat form search
	function getSearchForm()
	{
		if (count($this->arr_search_field) == 0)
			die('Fatal ERROR: Untuk menggunakan fasilitas search, harus diset dulu field mana yang mau disearch. gunakan method : setSearchField() pada kelas oNav ');

		$formName = 'search_form_'.$this->string_name;
		$textName = $this->string_name.'_search';
		$form     = '<form method="post" action="'. $this->string_cur_uri .'" name="'. $formName .'" class="form-inline" role="form">';
		$form    .= '<div class="form-group"><input type="text" size="5" name="'.$textName.'" value="search" onclick="'.$formName.'.'.$textName.'.value=\'\'" ></div>';
		$form    .= '<div class="form-group"><input type="submit" value="Go" class="btn btn-default btn-secondary"></div>';
		$form    .= '</form>';

		return $this->cleanNav($form);
	}

	// ini bisa dipanggil untuk mengembalikan navigasi secara lengkap
	function getCompleteNav()
	{
		$form = '<form method="POST" action="'.$this->string_cur_uri.'" role="form"><div class="input-group">';
		$form	.= '<span class="input-group-addon">'.$this->getNav().'</span>';
		$form	.= $this->getGoToForm(false);
		$form	.= '<span class="input-group-addon">'.$this->getStatus().'</span>';
		$form	.= '</div></form>';

		return $this->cleanNav($form);
	}

	//////////////////////////////////////////////////////////
	// Get the result query 		                        //
	//////////////////////////////////////////////////////////
	function getArrayResult()
	{
		if (!$this->is_loaded['get_data']) $this->getData();

		$sql 	= "SELECT ". $this->query['all_before_from'] . " FROM ". $this->query['all_after_from'];
		if (isset($_POST[$this->string_name."_search"]))
		{
			$sql = $this > getSearchSqlQuery();
		}
		$result	= $this->db->getAll($sql);
		$this->int_num_rows_this_page	= $this->db->Affected_rows();

		$this->arr_result				= $result;
		$this->is_loaded['get_array']	= true;
	}

	function fetch()
	{
		if (!$this->is_loaded['get_array']) $this->getArrayResult();
		if ($this->int_pointer < $this->int_num_rows_this_page)
		{
			$pointer	= $this->int_pointer;
			$this->int_pointer++;
			if ($this->layout['bgcolor'][1] == '')
				$c = $this->layout['bgcolor'][0];
			else {
				$c = $this->layout['bgcolor'][0];
				$this->layout['bgcolor'][0]	= $this->layout['bgcolor'][1];
				$this->layout['bgcolor'][1]	= $c;
			}
			$this->arr_result[$pointer]['bgcolor'] = $c;
			return $this->arr_result[$pointer];
		}else{
			return false;
		}
	}
}
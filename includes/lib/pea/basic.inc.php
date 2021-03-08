<?php
class indoDate{
	var $bulan;
	var $date;
	var $month;
	var $year;

	function __construct($source_date, $date_type='indo'){
		$this->bulan	= array('00' => '00', '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember');
		if ($date_type != 'indo') {
			$pattern='/(\d{4,4})-(\d{2,2})-(\d{2,2})/';
			preg_match($pattern, $source_date, $date_extracted);
			$this->year		= $date_extracted[1];
			$this->date		= $date_extracted[3];
		}
		else {
			$pattern='/(\d{2,2})-(\d{2,2})-(\d{4,4})/';
			preg_match($pattern, $source_date, $date_extracted);
			$this->year		= $date_extracted[3];
			$this->date		= $date_extracted[1];
		}

		$month_index	= $date_extracted[2];
		$this->month	= $this->bulan[$month_index];
	}
	function getDate(){
		return $this->date;
	}
	function getMonth(){
		return $this->month;
	}
	function getYear(){
		return $this->year;
	}
	function getIndoDate(){
		return $this->date.' '.$this->month.' '.$this->year;
	}
}



/*
///////////////////////////////////////////////////////////
	dropDate()
	example
		echo dropDate('tanggal', '01-12-2003');
///////////////////////////////////////////////////////////

	result :
<select name="[dd]">
<option value='1'  >1</option>
 0>< cut
<option value='16'  >16</option>
<option value='17' selected >17</option>
<option value='18'  >18</option>

 0>< cut

<option value='30'  >30</option>
<option value='31'  >31</option>
</select>
<select name="[mm]">
<option value='1'  >Januari</option>
<option value='2'  >Februari</option>

 0>< cut

</select>
<select name="[yyyy]">
<option value='1933'  >1933</option>
<option value='1934'  >1934</option>

 0>< cut

<option value='2001'  >2001</option>
<option value='2002'  >2002</option>
<option value='2003' selected >2003</option>
<option value='2004'  >2004</option>

 0>< cut

<option value='2072'  >2072</option>
<option value='2073'  >2073</option>
</select>
*/

function dropDate($option_name='', $default_date='') {
	if ($default_date == '' || substr($default_date, 0, 10) == '0000-00-00') $default_date = date('d-m-Y');
	if (preg_match('#([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})[0-9: ]{0,}#', $default_date , $match)){
		$dd 	= $match[1];
		$mm 	= $match[2];
		$yyyy	= $match[3];
	}else{
		preg_match('#([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})[0-9: ]{0,}#', $default_date , $match);
		$dd 	= $match[3];
		$mm 	= $match[2];
		$yyyy	= $match[1];
	}

	$bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

	$ret	= '<select name="'. $option_name .'[dd]">';
	for ($i=1; $i<=31; $i++) {
		$selected 	= ($dd == $i || $dd == '0'.$i) ? 'selected' :'';
		$ret		.= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
	}
	$ret	.= '</select>';
	$ret	.= '<select name="'. $option_name .'[mm]">';
	for ($i=1; $i<=12; $i++)
	{
		$selected 	= ($mm == $i || $mm == "0".$i) ? "selected" : "";
		$ret      .= '<option value="'.$i.'" '.$selected.'>'.$bulan[$i].'</option>';
	}
	$ret	.= '</select>';
	$ret	.= '<select name="'. $option_name .'[yyyy]">';
	for ($i = $yyyy-70; $i<=$yyyy+70; $i++)
	{
		$selected 	= ($yyyy == $i || $yyyy == '0'.$i) ? 'selected' : '';
		$ret      .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
	}
	$ret	.= '</select>';
	return($ret);
}


function dropDateTime($option_name='', $default_time='')
{
	if ($default_time == '' || substr($default_time, 0, 10) == '0000-00-00')
	{
		$default_time = date('d-m-Y H:i:s');
	}
	$default_date = substr($default_time, 0, 10);
	$default_time = substr($default_time, 11);
	if (preg_match('#([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#', $default_time , $match))
	{
		$hh = $match[1];
		$ii = $match[2];
		$ss = $match[3];
	}

	$ret = dropDate($option_name, $default_date);
	$ret.= '&nbsp; &nbsp; &nbsp;';
	$ret.= '<select name="'. $option_name .'[hh]">';

	for ($i=0; $i<=23; $i++)
	{
		$selected = ($hh == $i || $hh == '0'.$i) ? 'selected' : '';
		$ret     .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
	}
	$ret	.= '</select>:';
	$ret	.= '<select name="'. $option_name .'[ii]">';
	for ($i=0; $i<=59; $i++) {
		$selected 	= ($ii == $i || $ii == '0'.$i) ? 'selected' : '';
		$ret		.= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
	}
	$ret	.= '</select>:';
	$ret	.= '<select name="'. $option_name .'[ss]">';
	for ($i=0; $i<=59; $i++)
	{
		$selected 	= ($ss == $i || $ss == '0'.$i) ? 'selected' : '';
		$ret		.= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
	}
	$ret	.= '</select>';
	return($ret);
}


/////////////////////////////////////////////////////////////////////////////
//			class oDebug()
//
/////////////////////////////////////////////////////////////////////////////


class oDebug{
	var $bool_debug = false;

	function debug($bool_check, $string_debug = "", $string_ok_message="", $string_error_message = "")
	{
		if ($this->bool_debug)
		{
			echo '<ul>';
			if ($bool_check)
				echo '	<li>OK</li><li>'.$string_debug.'</li><li>'.$string_ok_message.'</li>';
			else
				echo '	<li>ERROR</li><li>'.$string_debug.'</li><li>'.$string_error_message.'</li>';
			echo '</ul>';
		}
	}
	function setDebug($bool_debug='on')
	{
		if ($bool_debug == 'on')
		{
			$this->bool_debug = true;
		}
		elseif ($bool_debug == 'off')
		{
			$this->bool_debug = false;
		}
		else
			$this->bool_debug = $bool_debug;
	}
}

/////////////////////////////////////////////
//	getConfig();
/////////////////////////////////////////////
function getConfig($table, $name){
	$q = "SELECT content FROM $table WHERE `name` = '$name'";
	$r = mysql_query($q) or die(mysql_error() . $q);
	$a = mysql_fetch_row($r);
	$value = $a[0];
	return $value;
}

function goodSubstr($str){
	$pos = strrpos($str, ' ');
	return substr($str, 0, $pos);
}

function getProporsionalImgSize($img_src, $max_width, $max_height){
	// dapatkan size img sesungguhnya
	$size	= getimagesize ($img_src);
	$width 	= $size[0];
	$height	= $size[1];
	$ok = $ratio = $width_jadi = $height_jadi = array();
	$ok[0] = $ok[1] = 1;
	$ratio[0] = $ratio[1] = 1;

	//coba diitung kedua2nya dikecilkan
	if ($height > $max_height){
		$ratio[0] = $max_height/$height;
		$height_jadi[0] = $max_height;
		$width_jadi[0]	= $ratio[0]*$width;
	}else{
		$height_jadi[0] = $height;
		$width_jadi[0]	= $width;
	}
	if ($width > $max_width){
		$ratio[1] = $max_width/$width;
		$height_jadi[1] = $ratio[1]*$height;
		$width_jadi[1]	= $max_width;
	}else{
		$height_jadi[1] = $height;
		$width_jadi[1]	= $width;
	}

	if ($width_jadi[0]>$max_width){
		$ok[0] = 0;
	}
	if ($height_jadi[1]>$max_height){
		$ok[1] = 0;
	}

	// keputusan akhir
	if ($ok[0] == 1 && $ok[1] == 1){
		return array('width' => round($width_jadi[0]), 'height' => round($height_jadi[0]) );
	}
	elseif ($ok[0] == 1){
		return array('width' => round($width_jadi[0]), 'height' => round($height_jadi[0]) );
	}else{
		return array('width' => round($width_jadi[1]), 'height' => round($height_jadi[1]) );
	}
}

class checkCookie{

	var $name;
	var $value;
	var $int_expire;

	function __construct( $name, $value, $int_expire ){
		$this->name 		= $name;
		$this->value 		= $value;
		$this->int_expire	= $int_expire;
	}

	function check(){
		$name	= $this->name;
		if ( !isset($_COOKIE[$name]) ) {
			return true;
		} else{
			return false;
		}
	}

	function set(){
		// ini untuk ngeset cookie yg ndukung all browser
		if (preg_match('~MSIE~s', getenv('HTTP_USER_AGENT'))) {
			$time = mktime() + $this->int_expire;
			$date = date('l, d-M-y H:i:s', ($time));
		}
		else {
			$date = time() + $this->int_expire;
		}
		$date = time() + $this->int_expire;
		setcookie( $this->name, $this->value, $date,'/' );
		$this->name;
	}
}


// fungsi untuk memasukkan setiap query mysql
// bisa dipaksa log, ato sebaliknya
// ato bisa dibiarkan supaya otomatis, jadi query Select ga masuk log
// isLog, 	1 jika force to Log
// 			0 jika force to unLog
//			leave it if let its function to decide
//				-> select => unLog
//				-> update, insert => Log
function logQuery($query, $isLog = '')
{
	$table = 'log';

	//echo $query;

	// log it first
	if ( $isLog == '' )
	{
		if ( preg_match( '#^SELECT #', $query ) )
		{
			$isLog = 0;
		}
	}
	if ( $isLog != 0 )
	{
		$who	= ( isset( $_SESSION['auth']['username'] ) ) ? $_SESSION['auth']['username'] : '-';
		$q = "INSERT INTO $table ( `who`, `what`, `when` )
					VALUES ( '". $who ."', '". addslashes( $query )."', NOW() )";
		mysql_query( $q ) or die ( mysql_error() );
	}

	// do the query
	$result = mysql_query( $query );

	return $result;
}


/**
 * diambil dari fungsi postNuke
 * clean user input
 * <br>
 * Gets a global variable, cleaning it up to try to ensure that
 * hack attacks don't work
 * @param var name of variable to get
 * @param ...
 * @returns string/array
 * @return prepared variable if only one variable passed
 * in, otherwise an array of prepared variables
 */
function varCleanFromInput()
{
		$resarray = array();
		foreach (func_get_args() as $var) {
				// Get var
				global $$var;
				if (empty($var)) {
						return;
				}
				$ourvar = $$var;

				if (!isset($ourvar)) {
						array_push($resarray, NULL);
						continue;
				}
				if (empty($ourvar)) {
						array_push($resarray, $ourvar);
						continue;
				}

				// Add to result array
				array_push($resarray, $ourvar);
		}

		// Return vars
		if (func_num_args() == 1) {
				return $resarray[0];
		} else {
				return $resarray;
		}
}

/**
 * diambil dari fungsi postNuke pnApi()
 * ready databse output
 * <br>
 * Gets a variable, cleaning it up such that the text is
 * stored in a database exactly as expected
 * @param var variable to prepare
 * @param ...
 * @returns string/array
 * @return prepared variable if only one variable passed
 * in, otherwise an array of prepared variables
 */
function varPrepForStore()
{
		$resarray = array();
		foreach (func_get_args() as $ourvar) {

				// Prepare var
				if (!get_magic_quotes_runtime()) {
#            $ourvar = addslashes($ourvar);
				}

				// Add to array
				array_push($resarray, $ourvar);
		}

		// Return vars
		if (func_num_args() == 1) {
				return $resarray[0];
		} else {
				return $resarray;
		}
}

/*
	Fungsi print_r yang indah dan readable
*/
function print_r_pre ( $arr )
{
	global $_CONFIG;
	if ( ini_get('display_errors') )
	{
		echo '<pre>';
		print_r ( $arr );
		echo '</pre>';
	}
}

?>

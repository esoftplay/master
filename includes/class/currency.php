<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

class currency
{
	var $main_tbl = 'store_currency';
	var $db;
	var $updated;
	var $baseCurrency;
	var $defCurrency;
	var $allCurrency= array();
	var $currencies = array();
	var $_round			= 2;
	
	function __construct($baseCurrency = 'IDR', $defCurrency = 'USD', $DB = 'db')
	{
		global $$DB;
		$this->_db = $$DB;
		$this->baseCurrency = $baseCurrency;
		$this->defCurrency 	= $defCurrency;
		$this->getAllCurrency();
	}
	function getAllCurrency()
	{
		$q = "SELECT * FROM `".$this->main_tbl."`";
		$arr = $this->_db->getAll($q);
		foreach($arr AS $dt){
			if($dt['publish']=='1') $this->currencies[$dt['code']] = round($dt['price'], $this->_round);
			$this->allCurrency[$dt['code']] = round($dt['price'], $this->_round);
		}
		$this->updated = $dt['updated'];
	}
	function price($value, $curr ='', $round = '')
	{
		$curr = $curr ? strtoupper(substr($curr, 0, 3)) : $this->defCurrency;
		$round= $round ? $round : $this->_round;
		if(intval($value) > 0){
			$out = round(($value/$this->allCurrency[$curr]), 2);
			return $out;
		}else{
			return false;
		}
	}
	function doUpdate($debug = false)
	{
		$field = array(
			'basecur'		=> $this->baseCurrency
		,	'historical'=> 'false'
		,	'month'			=> '6'
		,	'day'				=> '12'
		,	'year'			=> '2008'
		,	'sort_by'		=> 'name'	// option : name, code
		,	'image_x'		=> ''
		,	'image_y'		=> ''
		,	'image'			=> 'Submit'
		);
		$data = array(
			'referrer' 			=> 'http://www.xe.com/ict/'
		,	'action'				=> 'http://www.xe.com/ict/'
		,	'post'					=> 1
		,	'followLocation'=> 1
		,	'returntransfer'=> 1
		,	'postfield'			=> $field
		,	'cookieName'		=> '_currencies'
		);
		$this->_db->debug = $debug;
		$content = $this->curl_action($data, $debug);
		if (!empty($content)){
			$regex = "/<tr class=\"row\d{1,2}\" valign=\"top\"><td align=\"left\">(.*?)<\/td><td align=\"left\">(.*?)<\/td><td align=\"right\">(.*?)<\/td><td align =\"right\">(.*?)<\/td><\/tr>/is";
			preg_match_all($regex, $content, $match);
			$output = array(
				'code'	=> $match[1] 
			, 'title'	=> $match[2]
			, 'price'	=> $match[4]
			);
			$codes = array_unique($output['code']);
			$result = array();
			foreach($codes AS $dt)
			{
				$i = array_keys($output['code'], $dt);
				$q = "
				UPDATE `".$this->main_tbl."` 
				SET`title`	= '".trim($output['title'][$i[0]])."'
				,	`price`		= '".trim(str_replace(',', '', $output['price'][$i[0]]))."'
				,	`updated`	= NOW()
				WHERE `code`= '".trim($output['code'][$i[0]])."'
				";
				if(in_array(trim($output['code'][$i[0]]), $this->allCurrency))
					$this->_db->Execute($q);
				$result[] = array(
					'code'	=> trim($output['code'][$i[0]])
				,	'title'	=> trim($output['title'][$i[0]])
				,	'price'	=> trim(str_replace(',', '', $output['price'][$i[0]]))
				);
			}
		}else{
			$result = false;
		}
		return $result;
	}
	function doInsert($debug = false)
	{
		$arr = $this->doUpdate($debug);
		$affected = 0;
		foreach($arr AS $dt)
		{
			$q = "
			INSERT INTO `".$this->main_tbl."` 
			SET`code`		= '".trim($dt['code'])."'
			,	`title`		= '".trim($dt['title'])."'
			,	`price`		= '".trim(str_replace(',', '', $dt['price']))."'
			,	`updated`	= NOW()
			";
			if(in_array(trim($output['code'][$i[0]]), $this->allCurrency)){
				$this->_db->Execute($q);
				$affected += $this->_db->affected_rows();
			}
		}
		return $affected;
	}
	function joinRequestURL( $postfields )
	{
		foreach($postfields as $foo => $bar){
			 $bar = urlencode($bar); 
			 $postedfields[]  = "$foo=$bar"; 
		} 
		$urlstring = join("\n", $postedfields); 
		$urlstring = preg_replace("/\n/", "&", $urlstring); 
		return $urlstring;
	}
	function parseRequestURL($postfields)
	{
		$out = array();
		foreach(explode('&', $postfields) AS $str)
		{
			list($var, $value) = explode('=', $str);
			$out[$var] = urldecode($value);
		}
		return $out;
	}
	function curl_action($data, $debug = false)
	{
		if( $debug ){
			echo "<br><strong>POSTFIELDS</strong>:<br>";
			pr( $data['postfield'] );
		}
		$header	= array();
	  $init = curl_init( $data['action'] );
		if ($debug)	echo $this->joinRequestURL($data['postfield']);
	  curl_setopt($init, CURLOPT_REFERER, $data['referrer'] );
		if ( $data['post'] )
		curl_setopt($init, CURLOPT_POSTFIELDS, $this->joinRequestURL($data['postfield']));
	  curl_setopt($init, CURLOPT_HEADER, 0);
	  curl_setopt($init, CURLOPT_POST, $data['post']);
	  curl_setopt($init, CURLOPT_FOLLOWLOCATION, $data['followLocation'] );
	  curl_setopt($init, CURLOPT_RETURNTRANSFER, $data['returntransfer']);
	# curl_setopt($init, CURLOPT_COOKIE, 1);
	  curl_setopt($init, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($init, CURLOPT_SSL_VERIFYHOST , 0);
	# curl_setopt($init, CURLOPT_HTTPHEADER, $header );
	  curl_setopt($init, CURLOPT_COOKIEJAR, $data['cookieName']);
	  curl_setopt($init, CURLOPT_COOKIEFILE, $data['cookieName']);
	  $out = curl_exec($init);
	  if ( $debug )
	  {
		  echo curl_error( $init );
		  echo curl_errno( $init );
		  echo $out;
		}
		return $out;
	}
}
#$cur = new currency();
#echo $cur->price(10250, 'eur');
#$cur->doUpdate(true);
#pr($cur);
?>
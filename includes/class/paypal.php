<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*=========================================
$data = array(
	'order_id' => '26',
	'name'     => 'caterine zetta zone',
	'email'    => 'caterine@fisip.net',
	'total'    => '2.05'
	);
$params = array(
	'FORM_ACTION'     => 'https://www.paypal.com/cgi-bin/webscr',
	'INSTID'          => 'bbc_danang@fisip.net',
	'DESCRIPTION'     => 'Pay for Azaliamoda.Com',
	'ITEM_NAME'       => 'Azaliamoda.Com Order',
	'MIN_TRANSACTION' => 10,
	'COST'            => '5',
	'CURRENCY'        => 'USD',
	'COST_TYPE'       => 'percent',
	'NOTIFY_URL'      => 'index.php?mod=store.paypal-success'
	);
 *========================================*/
class Paypal
{
	var $order_id;
	var $hidden;
	var $total;
	var $cur;
	var $img_icon;
	var $autoSubmit = 0; # '1' OR '0'
	var $debug = 0; # '1' OR '0'
	function __construct($data, $params)
	{
		global $db;
		$this->data			= $data;
		$this->db				= $db;
		$this->order_id = $data['order_id'];
		$this->getAccountData($data);
		$this->getAccountPaypal($params);
		$this->set_total($this->admins['CURRENCY']);
		$this->set_icon('http://www.paypal.com/en_US/i/btn/x-click-but06.gif');
	}
	function set_icon($img_icon)
	{
		$this->img_icon = $img_icon;
	}
	function getAccountData($data)
	{
		$out = new stdClass;
		$out->first_name= preg_match('/ /', $data['name']) ? trim(substr($data['name'], 0, strpos($data['name'], ' '))) : trim($data['name']);
		$out->last_name	= trim(strrchr($data['name'], ' '));
		$out->address1	= '';	//	$data['order_address'];
		$out->city			= '';	//	$data[''];
		$out->state			= '';	//	$data[''];
		$out->zip				= '';	//	$data['order_zip'];
		$out->email			= '';	//	$data['order_email'];
		$this->total		= $data['total'];
		$this->clients	= $out;
	}
	function getAccountPaypal($params)
	{
		$this->admins = $params;
		$this->admins['CURRENCY'] = strtoupper(substr($this->admins['CURRENCY'], 0, 3));
		$this->admins['NOTIFY_URL'] = site_url($this->admins['NOTIFY_URL'].'&id='.$this->order_id);
	}
	function set_total($curValue, $round = '2')
	{
		if($this->admins['COST_TYPE']=='percent'){
			$this->admins['COST'] = ($this->admins['COST'] > 0) ? intval($this->admins['COST']) : 1;
			$this->total += $this->total * $this->admins['COST'] / 100;
		}elseif($this->admins['COST_TYPE']=='nominal'){
			$this->total += intval($this->admins['COST']);
		}
	}
	function show($months = 0)
	{
		if($this->total < $this->admins['MIN_TRANSACTION']) return false;
		$this->hidden = (!$this->debug) ? 'hidden' : 'text';
		if($months > 0){
			if(($months % 12) == 0){
				$period = (int)($months / 12);
				$time		= 'Y';
			}else{
				$period = $months;
				$time		= 'M';
			}
			$this->prefix = 'recurring';
			$out = $this->showButtonRecurring($period, $time);
		}else{
			$this->prefix = 'regular';
			$out = $this->showButtonRegular();
		}
		$out .= $this->showButtonClient();
		$out .= "\n</form>";
		if($this->autoSubmit){
			$out .= "\n<script>"
			.	"\n\t var tForm = document.getElementById('".$this->prefix."_xclick');"
			.	"\n\t tForm.target = '_self';"
			.	"\n\t tForm.submit();"
			.	"\n</script>";
		}
		return $out;
	}
	function showButtonRegular()
	{
		ob_start();
		?>
		<form name="_xclick" id="<?php echo $this->prefix;?>_xclick" action="<?php echo $this->admins['FORM_ACTION'];?>" method="post" target="_blank">
			<input type="<?php echo $this->hidden;?>" name="cmd" value="_xclick">
			<input type="<?php echo $this->hidden;?>" name="invoice" value="<?php echo $this->order_id;?>">
			<input type="<?php echo $this->hidden;?>" name="business" value="<?php echo $this->admins['INSTID'];?>">
			<input type="<?php echo $this->hidden;?>" name="item_name" value="<?php echo $this->admins['ITEM_NAME'];?>">
			<input type="<?php echo $this->hidden;?>" name="amount" value="<?php echo $this->total;?>">
			<input type="<?php echo $this->hidden;?>" name="currency_code" value="<?php echo $this->admins['CURRENCY'];?>">
			<!--input type="<?php echo $this->hidden;?>" name="country" value="<?php echo $this->admins['COUNTRY'];?>">
			<input type="<?php echo $this->hidden;?>" name="test_ipn" value="1"-->
			<input type="<?php echo $this->hidden;?>" name="notify_url" value="<?php echo $this->admins['NOTIFY_URL'];?>">
			<input type="image" src="<?php echo $this->img_icon;?>" border="0" width="62" height="31" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" style="border:0px" />
		<?php
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	function showButtonRecurring($period, $time)
	{
		/*=====================================================
		If you dynamically generate portions of your site, you can create
		Subscribe buttons dynamically and save time by updating the variables
		with information from your database. To use the button above for a
		different subscription, you would need to edit the values
		for three variables:
			* a3 - amount to billed each recurrence
			* p3 - number of time periods between each recurrence
			* t3 - time period (D=days, W=weeks, M=months, Y=years)
		https://www.paypal.com/us/cgi-bin/webscr?cmd=_pdn_subscr_techview_outside
		========================================================*/
		ob_start();
		?>
		<form name="_xclick" id="<?php echo $this->prefix;?>_xclick" action="<?php echo $this->admins['FORM_ACTION'];?>" method="post" target="_blank">
			<input type="<?php echo $this->hidden;?>" name="cmd" value="_xclick-subscriptions">
			<input type="<?php echo $this->hidden;?>" name="business" value="<?php echo $this->admins['INSTID'];?>">
			<input type="<?php echo $this->hidden;?>" name="currency_code" value="<?php echo $this->admins['CURRENCY'];?>">
			<input type="<?php echo $this->hidden;?>" name="item_name" value="[ticket#<?php echo $this->order_id;?>] <?php echo $this->admins['ITEM_NAME'];?>">
			<input type="<?php echo $this->hidden;?>" name="notify_url" value="<?php echo $this->admins['NOTIFY_URL'];?>&order_id=<?php echo $this->order_id;?>">
			<input type="<?php echo $this->hidden;?>" name="no_shipping" value="1">
			<input type="image" src="<?php echo $this->img_icon;?>" border="0" name="submit" width="62" height="31" alt="Make payments with PayPal - it's fast, free and secure!" style="border:0px" />
			<input type="<?php echo $this->hidden;?>" name="a3" value="<?php echo $this->total;?>">
			<input type="<?php echo $this->hidden;?>" name="p3" value="<?php echo $period;?>">
			<input type="<?php echo $this->hidden;?>" name="t3" value="<?php echo $time;?>">
			<input type="<?php echo $this->hidden;?>" name="src" value="1">
			<input type="<?php echo $this->hidden;?>" name="sra" value="1">
		<?php
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	function showButtonClient()
	{
		$out = '';
		foreach($this->clients AS $id => $data){
			if(!empty($data))
				$out .= "\n\t<input type='$this->hidden' name='$id' value=\"$data\">";
		}
		return $out;
	}
}
?>
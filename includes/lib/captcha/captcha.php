<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/**
* Google Captcha
* https://www.google.com/recaptcha/admin#site/319368291
*/
class captcha
{
	private $site_key   = '6LdjLAkTAAAAAJi4m2cz_8akfTE0rDT0jd5pKO2n';
	private $secret_key = '6LdjLAkTAAAAALGAcYigmv0ap8Jvg0h0BW2_KkeY';
	private $error_msg  = '';
	function __construct(){}
	public function CaseInsensitive() {}
	public function UseColour() {}
	public function Create()
	{
		return '<div class="g-recaptcha" data-sitekey="'.$this->site_key.'"></div><script src="https://www.google.com/recaptcha/api.js"></script>';
	}
	public function Validate($value='')
	{
		$output = array(
			'success'     => false,
			'error-codes' => lang('make sure that you are not a robot')
			);
		if (empty($value))
		{
			$value = @$_POST['g-recaptcha-response'];
		}
		if (!empty($value))
		{
			global $sys;
			$json = $sys->curl(
				'https://www.google.com/recaptcha/api/siteverify',
				array(
					'secret'   => $this->secret_key,
					'response' => $value
					)
				);
			$json = @gzinflate(substr($json, 10));
			$out  = @json_decode($json, 1);
			if (isset($out['success']))
			{
				$output = $out;
			}
		}
		if (!$output['success'])
		{
			$this->error_msg = $output['error-codes'];
		}
		return $output['success'];
	}
	public function msg()
	{
		return $this->error_msg;
	}
}
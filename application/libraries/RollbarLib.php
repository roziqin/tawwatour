<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class RollbarLib
{
	
	public function __construct()
	{
		require_once APPPATH . 'third_party/rollbar/Rollbar.php';
	}
}
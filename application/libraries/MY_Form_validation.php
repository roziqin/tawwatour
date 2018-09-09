<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class MY_Form_validation extends CI_Form_validation
{
	function run($module = '', $group = '') {
		(is_object($module)) AND $this->CI =& $module;
		return parent::run($group);
	}
	function check_captcha($str){
		$CI =& get_instance();
        $word = $CI->session->userdata('captchaWord');
        if(strcmp(strtoupper($str),strtoupper($word)) == 0){
          return true;
        }
        else{
          //$CI->form_validation->set_message('check_captcha', 'Please enter correct words!');
          return false;
        }
    }
}
/* End of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */
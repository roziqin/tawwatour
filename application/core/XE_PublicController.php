<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class XE_PublicController extends MX_Controller {
	protected $mname; //modul name
	protected $cname; //controller name
	protected $method; //method name
	protected $userdata = array();
	protected $branch_code = 'default';
	protected $path;
	private $userdata_name;
	protected $permission;
	private $theme = 'default';
	protected $layout = 'index';
	protected $view = 'index';
	protected $store = 0;
    protected $user = null;
	private $flashdata = null;

	function __construct($need_permission = false)
	{
		parent::__construct();
		$rollbar_token = $this->config->item('rollbar_token');
		if(!empty($rollbar_token) && (in_array(ENVIRONMENT, array('production','testing')))) {
        	Rollbar::init(array('access_token' => $rollbar_token));
		}
		$this->userdata_name = $this->config->item('xe_userdata');
		$default_title = $this->config->item('default_title');
		$this->userdata = $this->session->userdata($this->userdata_name);
		$this->twiggy->set(array('userdata'=>$this->userdata,'title'=>$default_title));
		if(!empty($this->userdata['username'])){
			$this->user = MDUser::find_by_username_and_deleted($this->userdata['username'],0);
		}
		if($need_permission){
			$this->cekLogin();
			if($this->user){
				$priv = $this->user->access;
				$this->permission = explode(',',$priv->privileges);
			}
			if(!in_array($need_permission, $this->permission)&&!in_array('bc14007405f01c334dbfac4c7b60d7f6', $this->permission)){
				die('You dont have permission to this feature.');
			}
		}
		$this->store = (!empty($this->userdata['store']))?$this->userdata['store']:0;
		$this->mname = explode('/', $this->router->directory)[2];
		$this->cname = $this->router->class;
		$this->method = $this->router->method;
		$this->theme = $this->config->item('xe_default_public_theme');
		$this->twiggy->publicTheme($this->theme);
		$this->view = $this->mname.'/index';
		$this->twiggy->template($this->view);
		$data['GLOBAL'] = array(
			'userAccess'=>$this->permission,
			'mname'=>$this->mname,
			'cname'=>$this->cname,
			'method'=>$this->method,
			'theme'=>$this->theme,
			'view'=>$this->view,
			'user'=>$this->user
			);
        $this->addFunction('truncateString');
		$this->twiggy->set($data);
		if(isset($_SESSION['xe_flash_data'])) {
			$this->flashdata = $_SESSION['xe_flash_data'];
			unset($_SESSION['xe_flash_data']);
		}
	}
	
	protected function setFlashData($data){
		$_SESSION['xe_flash_data'] = $data;
	}

	protected function getFlashData(){
		return $this->flashdata;
	}

	protected function isLogin()
	{
		if(empty($this->userData())) return false;
		else return true;
	}
	protected function cekLogin($redirect='')
	{
		$redirect = (empty($redirect))?base_url($this->router->default_controller):$redirect;
		if(empty($this->userData())) redirect($redirect);
	}
	protected function cekAccess($access)
	{
		if(!$this->haveAccess($access)) redirect($this->router->default_controller);
	}
	protected function haveAccess($access)
	{
		return (in_array($access, $this->permission)||in_array('bc14007405f01c334dbfac4c7b60d7f6', $this->permission));
	}
	protected function setBranchCode($branch_code)
	{
		$this->branch_code = $branch_code;
	}
	protected function useDb($branch_code)
	{
		$this->load->database($branch_code, TRUE);
	}
	protected function userData($name='')
	{
		if(empty($name)) {
			return (!empty($this->session->userdata($this->userdata_name)))?$this->session->userdata($this->userdata_name):array();
		} else {
			return (!empty($this->session->userdata($name)))?$this->session->userdata($name):array();
		}
	}
	protected function setUserData($data='',$name='')
	{
		if(empty($name)) {
			$this->session->set_userdata($this->userdata_name,$data);
			$this->userdata = $data;
		} else {
			$this->session->set_userdata($name,$data);
		}
	}
	protected function clearUserData($name='')
	{
		if(empty($name)) {
			$this->session->unset_userdata($this->userdata_name);
		} else {
			$this->session->unset_userdata($name);
		}
	}
	protected function viewTheme($set_theme='')
	{
		if(!empty($set_theme)) 
		{
			$this->theme = $set_theme;
			$this->twiggy->publicTheme($set_theme);
		}
		return $this->theme;
	}
	protected function viewLayout($set_layout='')
	{
		if(!empty($set_layout)) 
		{
			$this->layout = $set_layout;
			$this->twiggy->layout($set_layout);
		}
		return $this->layout;
	}
	protected function view($set_view='')
	{
		if(!empty($set_view)) 
		{
			$this->view = $this->mname.'/'.$set_view;
			$this->twiggy->template($this->view);
		}
		return $this;
	}
	protected function viewData($data)
	{
    	$this->twiggy->set($data);
    	return $this;
	}
	protected function renderView($view_name='')
	{
		if(!empty($view_name))$this->view($view_name);
		return $this->twiggy->render();
	}
	protected function displayView($view_name='')
	{
		if(!empty($view_name))$this->view($view_name);
		return $this->twiggy->display();
	}
	protected function addFunction($function_name)
	{
		$this->twiggy->register_function($function_name);
		return $this;
	}
	protected function getThemePath()
	{
		return $this->twiggy->get_theme();
	}
	protected function getThemesDir()
	{
		return $this->twiggy->get_themes_dir();
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends XE_Controller {

    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $this->login();
    }

    public function login()
    {
        // $this->sdb->update('admin',array('password'=>enc_pass('admin')));
        if(cek_auth_admin())//cek masih ada data login atau tidak
        {
            redirect(generate_url('admin_dashboard')); 
        }
        $post = $this->input->post();
        if(!empty($post))
        {
            $this->do_login();
        }

        $this->displayView('login');
    }  

    private function do_login()
    {
        $this->load->helper('cookie');
        $this->load->helper('security');
        
        $post = $this->input->post();
            // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
        if ($this->form_validation->run() == TRUE)
        {
            //Cek data login
            $user = MDUser::find_by_username($post['username']);
            if($user)
            {
                $userPermissions = $user->access;
                $prevs = explode(',',$userPermissions->privileges);
                if(password_verify($post['password'],$user->password) && in_array('admin',$prevs))
                {
                    unset($user->password);
                    $user->last_signin = date('Y-m-d H:i:s');
                    $user->save();
                    $userdata = array(
                            'userId' => $user->id,
                            'username' => $user->username,
                            'name' => $user->name,
                            // 'storeId' => $user->store,
                            'lastSignIn' => date('Y-m-d H:i:s')
                        );
                    $this->setUserData($userdata);
                    redirect(generate_url('admin_dashboard'));
                }
                else
                {
                    $data['message'] =  err_msg('Wrong Username or Password!');
                    // flash_err('Username atau Password salah!');
                }
            }
            else
            {
                $data['message'] =  err_msg('Wrong Username or Password!');
                // flash_err('Username atau Password salah!');
            }
        }
        else {
            $data['message'] =  err_msg(validation_errors());
        }
        unset($post['password']);
        $data['data'] = $post;
        $this->viewData($data);
        // redirect(base_url($this->cname));
    }

    public function logout()
    {
        $this->clearUserData();
        $this->clearUserData('pos_register');
        redirect(base_url($this->cname));
    } 
}

/* End of file auth.php */
/* Location: ./application/modules/auth/controllers/auth.php */
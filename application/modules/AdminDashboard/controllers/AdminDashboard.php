<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminDashboard extends XE_Controller {

    function __construct()
    {
        parent::__construct('admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $accountingPeriod = getAccountingPeriod();
        $startPeriod = $accountingPeriod['start'];
        $endPeriod = $accountingPeriod['end'];
        $this->viewData($data);
        $this->displayView('dashboard_index');
    }

    public function changepass()
    {
        $post = $this->input->post();
        $data['message'] = err_msg('Failed to change your password');
        if(!empty($post['oldpass'])){
            $this->load->library('form_validation');
            $this->load->helper('security');
            $this->form_validation->set_rules('oldpass', 'Old Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('newpass', 'New Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('newpassconfirm', 'New Password Confirmation', 'trim|required|xss_clean|matches[newpass]');
            if ($this->form_validation->run() == TRUE)
            {
                $oldpass = $post['oldpass'];
                $newpass = $post['newpass'];
                if(password_verify($oldpass,$this->user->password))
                {
                    $this->user->password = enc_pass($newpass);
                    $this->user->save();
                    $data['message'] = succ_msg('Your password has been changed');
                }
            } else {
                $data['message'] = err_msg(validation_errors());
            }
        }
        $this->viewData($data);
        $this->displayView('changepass');
    }
}

/* End of file dashboard.php */
/* Location: ./application/modules/dashboard/controllers/dashboard.php */
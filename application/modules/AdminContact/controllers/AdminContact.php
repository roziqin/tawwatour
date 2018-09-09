<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminContact extends XE_Controller {

    function __construct()
    {
        parent::__construct('contact-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['contacts'] = MDContact::all(array('conditions'=>'deleted = 0','order'=>'created_at'));
        $this->viewData($data);
        $this->displayView('contact_index');
    }

    public function form()
    {
        $message = '';
        $post = $this->input->post();
        $form_submitted = false;
        $companyContactLogo = MDSettingValue::find_by_key('company_logo');
        $companyContactBackground = MDSettingValue::find_by_key('company_contact_background');
        $companyContactIntro = MDSettingValue::find_by_key('company_contact_intro');
        $companyContactAddress = MDSettingValue::find_by_key('company_address');
        $companyContactPhone = MDSettingValue::find_by_key('company_phone');
        $companyContactEmail = MDSettingValue::find_by_key('company_email');
        if(!empty($post)) {            
            if(!empty($post['company_contact_intro'])) {                
                $data_logo = [
                    'key'=>'company_logo'
                ];                
                $res = $companyContactLogo->update_attributes($data_logo);
                if($res){
                    // UPLOAD FILE
                    $company_logo = '';
                    if(!empty($_FILES['company_logo'])&&$_FILES['company_logo']['error']!=4){
                        $path = 'assets/img/';
                        $input_name = 'company_logo';
                        $max_size = 5000000;
                        $valid_formats = array('png','jpg','jpeg','gif');
                        @$company_logo = upload_file($path, $input_name, $max_size, $valid_formats);
                        if($company_logo) {
                            # REMOVE OLD IMAGE
                            $old_file = $path.$companyContactLogo->value;
                            if(!empty($companyContactLogo->value) && file_exists($old_file)){
                                unlink($old_file);
                            }
                            # END OF REMOVE OLD IMAGE
                            $companyContactLogo->value = $company_logo;
                            $companyContactLogo->save();
                        }
                    }
                    //END UPLOAD FILE

                } else {
                    $res = MDSettingValue::create($data_logo);
                    // UPLOAD FILE
                    $company_logo = '';
                    if(!empty($_FILES['company_logo'])&&$_FILES['company_logo']['error']!=4){
                        $path = 'assets/img/';
                        $input_name = 'company_logo';
                        $max_size = 5000000;
                        $valid_formats = array('png','jpg','jpeg','gif');
                        @$company_logo = upload_file($path, $input_name, $max_size, $valid_formats);
                        if($company_logo) {
                            $res->img = $company_logo;
                            $res->save();
                        }
                    }
                    //END UPLOAD FILE   
                }
                $data_background = [
                    'key'=>'company_contact_background'
                ];                
                $result = $companyContactBackground->update_attributes($data_background);
                if($result){
                    // UPLOAD FILE
                    $company_contact_background = '';
                    if(!empty($_FILES['company_contact_background'])&&$_FILES['company_contact_background']['error']!=4){
                        $path = 'assets/img/contact/';
                        $input_name = 'company_contact_background';
                        $max_size = 5000000;
                        $valid_formats = array('png','jpg','jpeg','gif');
                        @$company_contact_background = upload_file($path, $input_name, $max_size, $valid_formats);
                        if($company_contact_background) {
                            # REMOVE OLD IMAGE
                            $old_file = $path.$companyContactBackground->value;
                            if(!empty($companyContactBackground->value) && file_exists($old_file)){
                                unlink($old_file);
                            }
                            # END OF REMOVE OLD IMAGE
                            $companyContactBackground->value = $company_contact_background;
                            $companyContactBackground->save();
                        }
                    }
                    //END UPLOAD FILE

                } else {
                    $res = MDSettingValue::create($data_background);
                    // UPLOAD FILE
                    $company_contact_background = '';
                    if(!empty($_FILES['company_contact_background'])&&$_FILES['company_contact_background']['error']!=4){
                        $path = 'assets/img/contact/';
                        $input_name = 'company_contact_background';
                        $max_size = 5000000;
                        $valid_formats = array('png','jpg','jpeg','gif');
                        @$company_contact_background = upload_file($path, $input_name, $max_size, $valid_formats);
                        if($company_contact_background) {
                            $res->img = $company_contact_background;
                            $res->save();
                        }
                    }
                    //END UPLOAD FILE   
                }
                if($companyContactIntro){
                    $companyContactIntro->value = $post['company_contact_intro'];
                    $companyContactIntro->save();
                } else {
                    $companyContactIntro = MDSettingValue::create(['key'=>'company_contact_intro','value'=>$post['company_contact_intro']]);
                }
                if($companyContactAddress){
                    $companyContactAddress->value = $post['company_address'];
                    $companyContactAddress->save();
                } else {
                    $companyContactAddress = MDSettingValue::create(['key'=>'company_address','value'=>$post['company_address']]);
                }
                if($companyContactPhone){
                    $companyContactPhone->value = $post['company_phone'];
                    $companyContactPhone->save();
                } else {
                    $companyContactPhone = MDSettingValue::create(['key'=>'company_phone','value'=>$post['company_phone']]);
                }
                if($companyContactEmail){
                    $companyContactEmail->value = $post['company_email'];
                    $companyContactEmail->save();
                } else {
                    $companyContactEmail = MDSettingValue::create(['key'=>'company_email','value'=>$post['company_email']]);
                }
            }
            $message = succ_msg('Data saved');
        }
        $data['company_contact_background'] = $companyContactBackground;
        $data['company_logo'] = $companyContactLogo;
        $data['company_contact_intro'] = $companyContactIntro;
        $data['company_address'] = $companyContactAddress;
        $data['company_phone'] = $companyContactPhone;
        $data['company_email'] = $companyContactEmail;
        $data['message'] = $message;
        // dump($data);
        $this->viewData($data);
        $this->displayView('admin_contact_info_form');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
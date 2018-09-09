<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminInformation extends XE_Controller {

    function __construct()
    {
        parent::__construct('information-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $message = '';
        $post = $this->input->post();
        $form_submitted = false;
        $companyName = MDSettingValue::find_by_key('company_name');
        $companyLogo = MDSettingValue::find_by_key('company_logo');
        $companyAddress = MDSettingValue::find_by_key('company_address');
        $companyPhone = MDSettingValue::find_by_key('company_phone');
        $companyEmail = MDSettingValue::find_by_key('company_email');
        $companyUrlFb = MDSettingValue::find_by_key('company_url_fb');
        $companyUrlIg = MDSettingValue::find_by_key('company_url_ig');
        $companyUrlTwitter = MDSettingValue::find_by_key('company_url_twitter');
        $companyFavicon = MDSettingValue::find_by_key('company_favicon');
        $companyMetaKeyword = MDSettingValue::find_by_key('company_meta_keyword');
        $companyMetaDescription = MDSettingValue::find_by_key('company_meta_description');
        if(!empty($post)) { 
            if(!empty($post['company_name'])) {             
                if($companyName){
                    $companyName->value = $post['company_name'];
                    $companyName->save();
                } else {
                    $companyName = MDSettingValue::create(['key'=>'company_name','value'=>$post['company_name']]);
                }
                $data_logo = [
                    'key'=>'company_logo'
                ];                
                $res = $companyLogo->update_attributes($data_logo);
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
                            $old_file = $path.$companyLogo->value;
                            if(!empty($companyLogo->value) && file_exists($old_file)){
                                unlink($old_file);
                            }
                            # END OF REMOVE OLD IMAGE
                            $companyLogo->value = $company_logo;
                            $companyLogo->save();
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
                if($companyAddress){
                    $companyAddress->value = $post['company_address'];
                    $companyAddress->save();
                } else {
                    $companyAddress = MDSettingValue::create(['key'=>'company_address','value'=>$post['company_address']]);
                }
                if($companyPhone){
                    $companyPhone->value = $post['company_phone'];
                    $companyPhone->save();
                } else {
                    $companyPhone = MDSettingValue::create(['key'=>'company_phone','value'=>$post['company_phone']]);
                }
                if($companyEmail){
                    $companyEmail->value = $post['company_email'];
                    $companyEmail->save();
                } else {
                    $companyEmail = MDSettingValue::create(['key'=>'company_email','value'=>$post['company_email']]);
                }
                if($companyUrlFb){
                    $companyUrlFb->value = $post['company_url_fb'];
                    $companyUrlFb->save();
                } else {
                    $companyUrlFb = MDSettingValue::create(['key'=>'company_url_fb','value'=>$post['company_url_fb']]);
                } 
                if($companyUrlIg){
                    $companyUrlIg->value = $post['company_url_ig'];
                    $companyUrlIg->save();
                } else {
                    $companyUrlIg = MDSettingValue::create(['key'=>'company_url_ig','value'=>$post['company_url_ig']]);
                } 
                if($companyUrlTwitter){
                    $companyUrlTwitter->value = $post['company_url_twitter'];
                    $companyUrlTwitter->save();
                } else {
                    $companyUrlTwitter = MDSettingValue::create(['key'=>'company_url_twitter','value'=>$post['company_url_twitter']]);
                } 
                if($companyFavicon){
                    $companyFavicon->value = $post['company_favicon'];
                    $companyFavicon->save();
                } else {
                    $companyFavicon = MDSettingValue::create(['key'=>'company_favicon','value'=>$post['company_favicon']]);
                } 
                if($companyMetaKeyword){
                    $companyMetaKeyword->value = $post['company_meta_keyword'];
                    $companyMetaKeyword->save();
                } else {
                    $companyMetaKeyword = MDSettingValue::create(['key'=>'company_meta_keyword','value'=>$post['company_meta_keyword']]);
                } 
                if($companyMetaDescription){
                    $companyMetaDescription->value = $post['company_meta_description'];
                    $companyMetaDescription->save();
                } else {
                    $companyMetaDescription = MDSettingValue::create(['key'=>'company_meta_description','value'=>$post['company_meta_description']]);
                } 
            }
            $message = succ_msg('Data saved');           
        }
        
        $data['company_name'] = $companyName;
        $data['company_logo'] = $companyLogo;
        $data['company_address'] = $companyAddress;
        $data['company_phone'] = $companyPhone;
        $data['company_email'] = $companyEmail;
        $data['company_url_fb'] = $companyUrlFb;
        $data['company_url_ig'] = $companyUrlIg;
        $data['company_url_twitter'] = $companyUrlTwitter;
        $data['company_favicon'] = $companyFavicon;
        $data['company_meta_keyword'] = $companyMetaKeyword;
        $data['company_meta_description'] = $companyMetaDescription;
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('information_index');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
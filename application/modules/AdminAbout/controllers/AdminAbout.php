<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminAbout extends XE_Controller {

    function __construct()
    {
        parent::__construct('about-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function history()
    {
        $message = '';
        $post = $this->input->post();
        $company_history = MDSettingValue::find_by_key('company_history');
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('about_title', 'Title', 'required');
            $this->form_validation->set_rules('about_description', 'Description', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $title = $post['about_title'];
                $description = $post['about_description'];
                $history = array(
                    array('title'=>$title, 'description'=>$description)
                );
                $data_history = [
                    'key' => 'company_history',
                    'value' => json_encode($history)              
                ];
                if($company_history){
                    $company_history->value = json_encode($history) ;
                    $company_history->save();
                } else {
                    $company_history = MDSettingValue::create($data_history);
                }           
                $message = succ_msg('History saved');
            }
        }
        $data['history'] = json_decode($company_history->value,true);
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('admin_about_history');
    }

    public function mission()
    {
        $message = '';
        $post = $this->input->post();
        $company_mission_title = MDSettingValue::find_by_key('company_mission_title');
        $company_mission_image = MDSettingValue::find_by_key('company_mission_image');
        $company_mission_item = MDSettingValue::find_by_key('company_mission_item');
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('mission_title', 'Mission Title', 'required');
            $this->form_validation->set_rules('icon[]', 'ICON', 'required');
            $this->form_validation->set_rules('title[]', 'Title', 'required');
            $this->form_validation->set_rules('description[]', 'Description', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $icon = $post['icon'];
                $title = $post['title'];
                $description = $post['description'];
                foreach($icon as $key=>$value){
                    $mission[$key] = array('icon'=>$icon[$key], 'title'=>$title[$key], 'description'=>$description[$key]);
                }
                $data_mission = [
                    'key' => 'company_mission_item',
                    'value' => json_encode($mission)              
                ];

                $data_mission_title = [
                    'key' => 'company_mission_title',
                    'value' => $post['mission_title']              
                ];
                
                if($company_mission_title){
                    $company_mission_title->value = $post['mission_title'] ;
                    $company_mission_title->save();
                    $message = succ_msg('Mission title saved');
                } else {
                    $company_mission_title = MDSettingValue::create($data_mission_title);
                    $message = succ_msg('Mission title saved');
                }

                if($company_mission_item){
                    $company_mission_item->value = json_encode($mission) ;
                    $company_mission_item->save();
                    $message = succ_msg('Mission saved');
                } else {
                    $company_mission_item = MDSettingValue::create($data_mission);
                    $message = succ_msg('Mission saved');
                }
                
                if($company_mission_image){
                    // UPLOAD FILE
                    $mission_image = '';
                    if(!empty($_FILES['mission_image'])&&$_FILES['mission_image']['error']!=4){
                        $path = 'assets/images/';
                        $input_name = 'mission_image';
                        $max_size = 5000000;
                        $valid_formats = array('png','jpg','jpeg','gif');
                        @$mission_image = upload_file($path, $input_name, $max_size, $valid_formats);
                        if($mission_image) {
                            # REMOVE OLD IMAGE
                            $old_file = $path.$company_mission_image->value;
                            if(!empty($company_mission_image->value) && file_exists($old_file)){
                                unlink($old_file);
                            }
                            # END OF REMOVE OLD IMAGE
                            $company_mission_image->value = $mission_image;
                            $company_mission_image->save();
                        }
                    }
                    //END UPLOAD FILE
                    $message = succ_msg('Images updated');
                }else{
                    $data_mission_image = [
                        'key' => 'company_mission_image'             
                    ];
                    $res = MDSettingValue::create($data_mission_image);            
                    if($res) {      
                        // UPLOAD FILE
                            $mission_image = '';
                            if(!empty($_FILES['mission_image'])&&$_FILES['mission_image']['error']!=4){
                                $path = 'assets/images/';
                                $input_name = 'mission_image';
                                $max_size = 5000000;
                                $valid_formats = array('png','jpg','jpeg','gif');
                                @$mission_image = upload_file($path, $input_name, $max_size, $valid_formats);
                                if($mission_image) {
                                    $res->value = $mission_image;
                                    $res->save();
                                }
                            }
                        //END UPLOAD FILE              
                        $message = succ_msg('Images saved');
                    } else {
                        $message = err_msg('Failed saving Images');
                    }
                }
                $message = succ_msg('Mission saved');
            }
        }
        $data['company_mission_title'] = $company_mission_title;
        $data['company_mission_item'] = json_decode($company_mission_item->value,true);
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('admin_about_mission');
    }

    public function counter()
    {
        $message = '';
        $post = $this->input->post();
        $company_counter = MDSettingValue::find_by_key('company_counter');
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('counter_title[]', 'Title', 'required');
            $this->form_validation->set_rules('counter_qty[]', 'QTY', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $title = $post['counter_title'];
                $qty = $post['counter_qty'];
                foreach($title as $key=>$value){
                    $counter[$key] = array('name'=>$title[$key], 'total'=>$qty[$key]);
                }
                $data_counter = [
                    'key' => 'company_counter',
                    'value' => json_encode($counter)              
                ];
                if($company_counter){
                    $company_counter->value = json_encode($counter) ;
                    $company_counter->save();
                } else {
                    $company_counter = MDSettingValue::create($data_counter);
                }           
                $message = succ_msg('Counter saved');
            }
        }
        $data['company_counter'] = json_decode($company_counter->value,true);
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('admin_about_counter');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
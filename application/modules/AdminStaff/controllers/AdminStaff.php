<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminStaff extends XE_Controller {

    function __construct()
    {
        parent::__construct('staff-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['staffs'] = MDStaff::all(array('conditions'=>'deleted = 0','order'=>'name'));
        foreach($data['staffs'] as $key=> $staffs)
        {
            $data['socmed'][$key] = json_decode($staffs->socmed,true);
        }
        foreach($data['staffs'] as $key=> $staffs)
        {
            $longcaracter[$key] = strlen($staffs->description);
            if($longcaracter[$key] <= 20){                
                $data['description'][$key] = $staffs->description;
            }elseif ($longcaracter[$key] > 20){
                $data['description'][$key] = substr($staffs->description, 0, 20).'...';
            }
        }
        $this->viewData($data);
        $this->displayView('staff_index');
    }

    public function detail($staff_id)
    {
        $data['message'] = '';
        $staffs = MDStaff::find_by_id_and_deleted($staff_id,0);
        if(!$staffs) {
            redirect(generate_url('admin_staff_index'));
        }
        $data['staffs'] = MDStaff::all(array('conditions'=>'id = '.$staff_id.' AND deleted = 0 ','order'=>'name'));
        foreach($data['staffs'] as $key=> $staffs)
        {
            $data['socmed'][$key] =json_decode($staffs->socmed,true);                   
        }
        $data['staffs'] = $staffs;
        $this->viewData($data);
        $this->displayView('staff_detail');
    }

    public function delete($staff_id)
    {
        $data['message'] = '';
        $staffs = MDStaff::find_by_id_and_deleted($staff_id,0);
        if(!$staffs) {
            $this->setFlashData(err_msg('Failed delete Staff'));
        } else {
            $staffs->deleted = 1;
            $staffs->save();
            $this->setFlashData(succ_msg('Staff has been deleted'));
        }
        redirect(generate_url('admin_staff_index'));
    }

    public function form()
    {
        $message = '';
        $staffs = MDStaff::all(array('conditions'=>'deleted = 0'));
        $post = $this->input->post();
        $get = $this->input->get();
        $mode = (!empty($get['mode'])) ? $get['mode'] : 'add';
        $staff_id = (!empty($get['id'])) ? $get['id'] : '';
        $form_submitted = false;
        $data['staffs'] = $staffs;
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('staff_name', 'Name', 'required');
            $this->form_validation->set_rules('staff_position', 'Position', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $socmed_ig = 'https://www.instagram.com/'.$post['staff_ig'];
                $socmed_fb = 'https://www.facebook.com/'.$post['staff_fb'];
                $socmed_tw = 'https://twitter.com/'.$post['staff_tw'];
                $socmed = array('ig'=>$socmed_ig, 'fb'=>$socmed_fb, 'tw'=>$socmed_tw);
                if(empty($post['top_staff'])){
                    $show_public = 0;
                }else{
                    $show_public = 1;
                }
                $data_staffs = [
                    'name' => $post['staff_name'],
                    'position' => $post['staff_position'],
                    'description' => $post['staff_description'],
                    'socmed' => json_encode($socmed),
                    'top'   => $show_public          
                ];
                $staff_temp = MDStaff::find_by_name_and_deleted($post['staff_name'],0);
                if(!$staff_temp||($staff_temp && $staff_temp->id == $staff_id)) {
                    $form_submitted = true;
                } else {
                    $message = err_msg('Staff name already used');
                }
            }
        }

         if($mode == 'edit') {
            if(empty($staff_id)) {
                $message = err_msg('No Staff selected');
            } else {
                $staffs = MDStaff::find_by_id_and_deleted($staff_id,0);
                $data['staffs'] = MDStaff::all(array('conditions'=>'id = '.$staff_id.' AND deleted = 0 ','order'=>'name'));
                $sosmed = array('https://www.instagram.com/', 'https://www.facebook.com/', 'https://twitter.com/');
                foreach($data['staffs'] as $key=> $staffs)
                {
                    $data['socmed'][$key] =  str_replace($sosmed, '', json_decode($staffs->socmed,true));                   
                }
                if(!$staffs) {
                    $message = err_msg('Staff not found');
                } else {
                    $data['staffs'] = $staffs;
                    if($form_submitted) {
                        $res = $staffs->update_attributes($data_staffs);
                        if($res) {
                        // UPLOAD FILE                        
                        $staffs_image = '';
                            if(!empty($_FILES['staff_image'])&&$_FILES['staff_image']['error']!=4){
                                $path = 'assets/images/staffs/';
                                $input_name = 'staff_image';
                                $max_size = 5000000;
                                $valid_formats = array('png','jpg','jpeg','gif');
                                @$staffs_image = upload_file($path, $input_name, $max_size, $valid_formats);
                                if($staffs_image) {
                                    # REMOVE OLD IMAGE
                                    $old_file = $path.$staffs->img;
                                    if(!empty($staffs->img) && file_exists($old_file)){
                                        unlink($old_file);
                                    }
                                    # END OF REMOVE OLD IMAGE
                                    $staffs->img = $staffs_image;
                                    $staffs->save();
                                }
                            }
                        //END UPLOAD FILE

                        $message = succ_msg('Staff updated');
                        } else {
                            $message = err_msg('Failed updating Staff');
                        }
                    }
                }
            }
        } else {
            if($form_submitted) {
                $res = MDStaff::create($data_staffs);            
                if($res) {      
                    // UPLOAD FILE
                        $staffs_image = '';
                        if(!empty($_FILES['staff_image'])&&$_FILES['staff_image']['error']!=4){
                            $path = 'assets/images/staffs/';
                            $input_name = 'staff_image';
                            $max_size = 5000000;
                            $valid_formats = array('png','jpg','jpeg','gif');
                            @$staffs_image = upload_file($path, $input_name, $max_size, $valid_formats);
                            if($staffs_image) {
                                $res->img = $staffs_image;
                                $res->save();
                            }
                        }
                    //END UPLOAD FILE              
                    $message = succ_msg('Staff saved');
                } else {
                    $message = err_msg('Failed saving Staff');
                }
            }
        }
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('staff_form');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
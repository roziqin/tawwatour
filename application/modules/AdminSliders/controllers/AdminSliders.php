<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminSliders extends XE_Controller {

    function __construct()
    {
        parent::__construct('sliders-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['sliders'] = MDSliders::all(array('conditions'=>'deleted = 0','order'=>'id'));
        foreach($data['sliders'] as $key=> $sliders)
        {
            $longcaracter[$key] = strlen($sliders->description);
            if($longcaracter[$key] <= 20){                
                $data['description'][$key] = $sliders->description;
            }elseif ($longcaracter[$key] > 20){
                $data['description'][$key] = substr($sliders->description, 0, 20).'...';
            }
        }
        $this->viewData($data);
        $this->displayView('data_sliders_index');
    }

    public function detail($sliders_id)
    {
        $data['message'] = '';
        $sliders = MDSliders::find_by_id_and_deleted($sliders_id,0);
        if(!$sliders) {
            redirect(generate_url('admin_sliders'));
        }
        $data['sliders'] = $sliders;
        $this->viewData($data);
        $this->displayView('data_sliders_detail');
    }

    public function delete($sliders_id)
    {
        $data['message'] = '';
        $sliders = MDSliders::find_by_id_and_deleted($sliders_id,0);
        if(!$sliders) {
            $this->setFlashData(err_msg('Failed delete Sliders'));
        } else {
            $sliders->deleted = 1;
            $sliders->save();
            $this->setFlashData(succ_msg('Sliders has been deleted'));
        }
        redirect(generate_url('admin_sliders'));
    }

    public function form()
    {
        $message = '';
        $post = $this->input->post();
        $get = $this->input->get();
        $mode = (!empty($get['mode'])) ? $get['mode'] : 'add';
        $sliders_id = (!empty($get['id'])) ? $get['id'] : '';
        $form_submitted = false;
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('sliders_title', 'Title', 'required');
            $this->form_validation->set_rules('sliders_subtitle', 'Subtitle', 'required');
            $this->form_validation->set_rules('sliders_description', 'Description', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $data_sliders = [
                    'title' => $post['sliders_title'],
                    'subtitle' => $post['sliders_subtitle'] ,
                    'description' => $post['sliders_description']               
                ];
                $sliders_temp = MDSliders::find_by_title_and_deleted($post['sliders_title'],0);
                if(!$sliders_temp||($sliders_temp && $sliders_temp->id == $sliders_id)) {
                    $form_submitted = true;
                } else {
                    $message = err_msg('Sliders caption already used');
                }
            }
        }

         if($mode == 'edit') {
            if(empty($sliders_id)) {
                $message = err_msg('No Sliders selected');
            } else {
                $sliders = MDSliders::find_by_id_and_deleted($sliders_id,0);
                if(!$sliders) {
                    $message = err_msg('Sliders not found');
                } else {
                    $data['sliders'] = $sliders;
                    if($form_submitted) {
                        $res = $sliders->update_attributes($data_sliders);
                        if($res) {
                        // UPLOAD FILE
                        $sliders_image = '';
                            if(!empty($_FILES['sliders_image'])&&$_FILES['sliders_image']['error']!=4){
                                $path = 'assets/img/slides/';
                                $input_name = 'sliders_image';
                                $max_size = 5000000;
                                $valid_formats = array('png','jpg','jpeg','gif');
                                @$sliders_image = upload_file($path, $input_name, $max_size, $valid_formats);
                                if($sliders_image) {
                                    # REMOVE OLD IMAGE
                                    $old_file = $path.$sliders->img;
                                    if(!empty($sliders->img) && file_exists($old_file)){
                                        unlink($old_file);
                                    }
                                    # END OF REMOVE OLD IMAGE
                                    $sliders->img = $sliders_image;
                                    $sliders->save();
                                }
                            }
                        //END UPLOAD FILE

                        $message = succ_msg('Sliders updated');
                        } else {
                            $message = err_msg('Failed updating Sliders');
                        }
                    }
                }
            }
        } else {
            if($form_submitted) {
                $res = MDSliders::create($data_sliders);            
                if($res) {      
                    // UPLOAD FILE
                        $sliders_image = '';
                        if(!empty($_FILES['sliders_image'])&&$_FILES['sliders_image']['error']!=4){
                            $path = 'assets/img/slides/';
                            $input_name = 'sliders_image';
                            $max_size = 5000000;
                            $valid_formats = array('png','jpg','jpeg','gif');
                            @$sliders_image = upload_file($path, $input_name, $max_size, $valid_formats);
                            if($sliders_image) {
                                $res->img = $sliders_image;
                                $res->save();
                            }
                        }
                    //END UPLOAD FILE              
                    $message = succ_msg('Sliders saved');
                } else {
                    $message = err_msg('Failed saving Sliders');
                }
            }
        }
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('data_sliders_form');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
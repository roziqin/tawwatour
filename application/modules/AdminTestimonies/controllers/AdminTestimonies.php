<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminTestimonies extends XE_Controller {

    function __construct()
    {
        parent::__construct('testimonies-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['testimonies'] = MDTestimony::all(array('conditions'=>'deleted = 0','order'=>'name'));
        foreach($data['testimonies'] as $key=> $testimonies)
        {
            $longcaracter[$key] = strlen($testimonies->description);
            if($longcaracter[$key] <= 20){                
                $data['description'][$key] = $testimonies->description;
            }elseif ($longcaracter[$key] > 20){
                $data['description'][$key] = substr($testimonies->description, 0, 20).'...';
            }
        }
        $this->viewData($data);
        $this->displayView('testimonies_index');
    }

    public function detail($testimonies_id)
    {
        $data['message'] = '';
        $testimonies = MDTestimony::find_by_id_and_deleted($testimonies_id,0);
        if(!$testimonies) {
            redirect(generate_url('admin_testimonies_index'));
        }
        $data['testimonies'] = $testimonies;
        $this->viewData($data);
        $this->displayView('testimonies_detail');
    }

    public function delete($testimonies_id)
    {
        $data['message'] = '';
        $testimonies = MDTestimony::find_by_id_and_deleted($testimonies_id,0);
        if(!$testimonies) {
            $this->setFlashData(err_msg('Failed delete Testimony'));
        } else {
            $testimonies->deleted = 1;
            $testimonies->save();
            $this->setFlashData(succ_msg('Testimony has been deleted'));
        }
        redirect(generate_url('admin_testimonies_index'));
    }

    public function form()
    {
        $message = '';
        $testimonies = MDTestimony::all(array('conditions'=>'deleted = 0'));
        $post = $this->input->post();
        $get = $this->input->get();
        $mode = (!empty($get['mode'])) ? $get['mode'] : 'add';
        $testimonies_id = (!empty($get['id'])) ? $get['id'] : '';
        $form_submitted = false;
        $data['testimonies'] = $testimonies;
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('testimonies_name', 'Name', 'required');
            $this->form_validation->set_rules('testimonies_job', 'Job', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $data_testimonies = [
                    'name' => $post['testimonies_name'],
                    'job' => $post['testimonies_job'],
                    'description' => $post['testimonies_description']                
                ];
                $testimonies_temp = MDTestimony::find_by_name_and_deleted($post['testimonies_name'],0);
                if(!$testimonies_temp||($testimonies_temp && $testimonies_temp->id == $testimonies_id)) {
                    $form_submitted = true;
                } else {
                    $message = err_msg('Testimony name already used');
                }
            }
        }

         if($mode == 'edit') {
            if(empty($testimonies_id)) {
                $message = err_msg('No Testimony selected');
            } else {
                $testimonies = MDTestimony::find_by_id_and_deleted($testimonies_id,0);
                if(!$testimonies) {
                    $message = err_msg('Testimony not found');
                } else {
                    $data['testimonies'] = $testimonies;
                    if($form_submitted) {
                        $res = $testimonies->update_attributes($data_testimonies);
                        if($res) {
                        // UPLOAD FILE
                        $testimonies_image = '';
                            if(!empty($_FILES['testimonies_image'])&&$_FILES['testimonies_image']['error']!=4){
                                $path = 'assets/img/testimonies/';
                                $input_name = 'testimonies_image';
                                $max_size = 5000000;
                                $valid_formats = array('png','jpg','jpeg','gif');
                                @$testimonies_image = upload_file($path, $input_name, $max_size, $valid_formats);
                                if($testimonies_image) {
                                    # REMOVE OLD IMAGE
                                    $old_file = $path.$testimonies->img;
                                    if(!empty($testimonies->img) && file_exists($old_file)){
                                        unlink($old_file);
                                    }
                                    # END OF REMOVE OLD IMAGE
                                    $testimonies->img = $testimonies_image;
                                    $testimonies->save();
                                }
                            }
                        //END UPLOAD FILE

                        $message = succ_msg('Testimony updated');
                        } else {
                            $message = err_msg('Failed updating Testimony');
                        }
                    }
                }
            }
        } else {
            if($form_submitted) {
                $res = MDTestimony::create($data_testimonies);            
                if($res) {      
                    // UPLOAD FILE
                        $testimonies_image = '';
                        if(!empty($_FILES['testimonies_image'])&&$_FILES['testimonies_image']['error']!=4){
                            $path = 'assets/img/testimonies/';
                            $input_name = 'testimonies_image';
                            $max_size = 5000000;
                            $valid_formats = array('png','jpg','jpeg','gif');
                            @$testimonies_image = upload_file($path, $input_name, $max_size, $valid_formats);
                            if($testimonies_image) {
                                $res->img = $testimonies_image;
                                $res->save();
                            }
                        }
                    //END UPLOAD FILE              
                    $message = succ_msg('Testimony saved');
                } else {
                    $message = err_msg('Failed saving Testimony');
                }
            }
        }
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('testimonies_form');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
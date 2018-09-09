<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminAfiliasi extends XE_Controller {

    function __construct()
    {
        parent::__construct('afiliasi-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['afiliasis'] = MDAfiliasi::all(array('conditions'=>'deleted = 0','order'=>'id'));
        foreach($data['afiliasis'] as $key=> $afiliasis)
        {
            $longcaracter[$key] = strlen($afiliasis->description);
            if($longcaracter[$key] <= 20){                
                $data['description'][$key] = $afiliasis->description;
            }elseif ($longcaracter[$key] > 20){
                $data['description'][$key] = substr($afiliasis->description, 0, 20).'...';
            }
        }
        $this->viewData($data);
        $this->displayView('data_afiliasi_index');
    }

    public function detail($afiliasi_id)
    {
        $data['message'] = '';
        $afiliasis = MDAfiliasi::find_by_id_and_deleted($afiliasi_id,0);
        if(!$afiliasis) {
            redirect(generate_url('admin_afiliasi'));
        }
        $data['afiliasis'] = $afiliasis;
        $this->viewData($data);
        $this->displayView('data_afiliasi_detail');
    }

    public function delete($afiliasi_id)
    {
        $data['message'] = '';
        $afiliasis = MDAfiliasi::find_by_id_and_deleted($afiliasi_id,0);
        if(!$afiliasis) {
            $this->setFlashData(err_msg('Failed delete afiliasi'));
        } else {
            $afiliasis->deleted = 1;
            $afiliasis->save();
            $this->setFlashData(succ_msg('afiliasi has been deleted'));
        }
        redirect(generate_url('admin_afiliasi'));
    }

    public function form()
    {
        $message = '';
        $post = $this->input->post();
        $get = $this->input->get();
        $mode = (!empty($get['mode'])) ? $get['mode'] : 'add';
        $afiliasi_id = (!empty($get['id'])) ? $get['id'] : '';
        $form_submitted = false;
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('afiliasi_name', 'Name', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $data_afiliasi = [
                    'name' => $post['afiliasi_name'],
                    'description' => $post['afiliasi_description']
                ];
                $afiliasi_temp = MDAfiliasi::find_by_name_and_deleted($post['afiliasi_name'],0);
                if(!$afiliasi_temp||($afiliasi_temp && $afiliasi_temp->id == $afiliasi_id)) {
                    $form_submitted = true;
                } else {
                    $message = err_msg('afiliasi name already used');
                }
            }
        }

         if($mode == 'edit') {
            if(empty($afiliasi_id)) {
                $message = err_msg('No afiliasi selected');
            } else {
                $afiliasis = MDAfiliasi::find_by_id_and_deleted($afiliasi_id,0);
                if(!$afiliasis) {
                    $message = err_msg('afiliasi not found');
                } else {
                    $data['afiliasis'] = $afiliasis;
                    if($form_submitted) {
                        $res = $afiliasis->update_attributes($data_afiliasi);
                        if($res) {
                        // UPLOAD FILE
                        $afiliasi_image = '';
                            if(!empty($_FILES['afiliasi_image'])&&$_FILES['afiliasi_image']['error']!=4){
                                $path = 'assets/images/afiliasi/';
                                $input_name = 'afiliasi_image';
                                $max_size = 5000000;
                                $valid_formats = array('png','jpg','jpeg','gif');
                                @$afiliasi_image = upload_file($path, $input_name, $max_size, $valid_formats);
                                if($afiliasi_image) {
                                    # REMOVE OLD IMAGE
                                    $old_file = $path.$afiliasis->img;
                                    if(!empty($afiliasis->img) && file_exists($old_file)){
                                        unlink($old_file);
                                    }
                                    # END OF REMOVE OLD IMAGE
                                    $afiliasis->img = $afiliasi_image;
                                    $afiliasis->save();
                                }
                            }
                        //END UPLOAD FILE

                        $message = succ_msg('afiliasi updated');
                        } else {
                            $message = err_msg('Failed updating afiliasi');
                        }
                    }
                }
            }
        } else {
            if($form_submitted) {
                $res = MDAfiliasi::create($data_afiliasi);            
                if($res) {      
                    // UPLOAD FILE
                        $afiliasi_image = '';
                        if(!empty($_FILES['afiliasi_image'])&&$_FILES['afiliasi_image']['error']!=4){
                            $path = 'assets/images/afiliasi/';
                            $input_name = 'afiliasi_image';
                            $max_size = 5000000;
                            $valid_formats = array('png','jpg','jpeg','gif');
                            @$afiliasi_image = upload_file($path, $input_name, $max_size, $valid_formats);
                            if($afiliasi_image) {
                                $res->img = $afiliasi_image;
                                $res->save();
                            }
                        }
                    //END UPLOAD FILE              
                    $message = succ_msg('afiliasi saved');
                } else {
                    $message = err_msg('Failed saving afiliasi');
                }
            }
        }
        $data['afiliasi'] = MDAfiliasi::all(array('conditions' => 'deleted = 0', 'order' => 'id'));
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('data_afiliasi_form');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
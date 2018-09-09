<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminGallery extends XE_Controller {

    function __construct()
    {
        parent::__construct('gallery-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['galleries'] = MDGallery::all(array('conditions'=>'deleted = 0','order'=>'id'));
        $this->viewData($data);
        $this->displayView('admin_gallery_index');
    }

    public function delete($gallery_id)
    {
        $data['message'] = '';
        $galleries = MDGallery::find_by_id_and_deleted($gallery_id,0);
        if(!$galleries) {
            $this->setFlashData(err_msg('Failed delete gallery'));
        } else {
            $galleries->deleted = 1;
            $galleries->save();
            $this->setFlashData(succ_msg('gallery has been deleted'));
        }
        redirect(generate_url('admin_gallery_index'));
    }

    public function form()
    {
        $message = '';
        $galleries = MDGallery::all(array('conditions'=>'deleted = 0'));
        $post = $this->input->post();
        $get = $this->input->get();
        $mode = (!empty($get['mode'])) ? $get['mode'] : 'add';
        $gallery_id = (!empty($get['id'])) ? $get['id'] : '';
        $form_submitted = false;
        $data['galleries'] = $galleries;
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('gallery_caption', 'Caption', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $data_gallery = [
                    'caption' => $post['gallery_caption'],
                    'id_gallery_category' => $post['gallery_category']             
                ];
                $gallery_temp = MDGallery::find_by_caption_and_deleted($post['gallery_caption'],0);
                if(!$gallery_temp||($gallery_temp && $gallery_temp->id == $gallery_id)) {
                    $form_submitted = true;
                } else {
                    $message = err_msg('gallery name already used');
                }
            }
        }

         if($mode == 'edit') {
            if(empty($gallery_id)) {
                $message = err_msg('No gallery selected');
            } else {
                $galleries = MDGallery::find_by_id_and_deleted($gallery_id,0);
                if(!$galleries) {
                    $message = err_msg('gallery not found');
                } else {
                    $data['galleries'] = $galleries;
                    if($form_submitted) {
                        $res = $galleries->update_attributes($data_gallery);
                        if($res) {
                        // UPLOAD FILE
                        $gallery_image = '';
                            if(!empty($_FILES['gallery_image'])&&$_FILES['gallery_image']['error']!=4){
                                $path = 'assets/img/gallery/';
                                $input_name = 'gallery_image';
                                $max_size = 5000000;
                                $valid_formats = array('png','jpg','jpeg','gif');
                                @$gallery_image = upload_file($path, $input_name, $max_size, $valid_formats);
                                if($gallery_image) {
                                    // # REMOVE OLD IMAGE
                                    // $img = explode(',',$galleries->img);
                                    // foreach ($img as $value) {
                                    //     $old_file = $path.$value;
                                    //     if(!empty($galleries->img) && file_exists($old_file)){
                                    //         unlink($old_file);
                                    //     }
                                    // }
                                    // # END OF REMOVE OLD IMAGE
                                    $current_img = $galleries->img;
                                    $new_img = implode(',',$gallery_image);
                                    $img = $current_img.','.$new_img;
                                    $galleries->img = $img;
                                    $galleries->save();
                                }
                            }
                        //END UPLOAD FILE

                        $message = succ_msg('gallery updated');
                        } else {
                            $message = err_msg('Failed updating gallery');
                        }
                    }
                }
            }
        } else {
            if($form_submitted) {
                $res = MDGallery::create($data_gallery);            
                if($res) {      
                    // UPLOAD FILE
                        $gallery_image = '';
                        if(!empty($_FILES['gallery_image'])&&$_FILES['gallery_image']['error']!=4){
                            $path = 'assets/img/gallery/';
                            $input_name = 'gallery_image';
                            $max_size = 5000000;
                            $valid_formats = array('png','jpg','jpeg','gif');
                            @$gallery_image = upload_file($path, $input_name, $max_size, $valid_formats);
                            if($gallery_image) {
                                $img = implode(',',$gallery_image);
                                $res->img = $img;
                                $res->save();
                            }
                        }
                    //END UPLOAD FILE              
                    $message = succ_msg('gallery saved');
                } else {
                    $message = err_msg('Failed saving gallery');
                }
            }
        }
        $data['gallery_categories'] = MDGalleryCategory::all(array('conditions'=>'deleted = 0','order'=>'id'));
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('admin_gallery_form');
    }

    public function delete_image($gallery_id,$img_name)
    {
        $data['message'] = '';
        $galleries = MDGallery::find_by_id_and_deleted($gallery_id,0);
        if(!$galleries) {
            $this->setFlashData(err_msg('Failed delete gallery'));
        } else {
            $path = 'assets/img/gallery/';
            # REMOVE OLD IMAGE
            $img = explode(',',$galleries->img);
            foreach ($img as $value) {
                $old_file = $path.$value;
                if(!empty($galleries->img) && file_exists($old_file) && ($value == $img_name)){
                    unlink($old_file);
                }else{
                    $new_img[] = $value; 
                }
            }
            $img = implode(',',$new_img);
            $galleries->img = $img;
            $galleries->save();
            # END OF REMOVE OLD IMAGE
            $data['galleries'] = $galleries;
            $message = err_msg('image has been deleted');
        }
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('admin_gallery_form');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
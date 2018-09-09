<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminBlog extends XE_Controller {

    function __construct()
    {
        parent::__construct('blog-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['blogs'] = MDBlog::all(array('conditions'=>'deleted = 0','order'=>'caption'));
        $this->viewData($data);
        $this->displayView('admin_blog_index');
    }

    public function delete($blog_id)
    {
        $data['message'] = '';
        $blogs = MDBlog::find_by_id_and_deleted($blog_id,0);
        if(!$blogs) {
            $this->setFlashData(err_msg('Failed delete blog'));
        } else {
            $blogs->deleted = 1;
            $blogs->save();
            $this->setFlashData(succ_msg('blog has been deleted'));
        }
        redirect(generate_url('admin_blog_index'));
    }

    public function form()
    {
        $message = '';
        $blogs = MDBlog::all(array('conditions'=>'deleted = 0'));
        $post = $this->input->post();
        $get = $this->input->get();
        $mode = (!empty($get['mode'])) ? $get['mode'] : 'add';
        $blog_id = (!empty($get['id'])) ? $get['id'] : '';
        $form_submitted = false;
        $data['blogs'] = $blogs;
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('blog_caption', 'Caption', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $data_blog = [
                    'caption' => $post['blog_caption'],
                    'id_blog_category' => $post['blog_category'],
                    'description' => $post['blog_description'],
                    'date' => $post['blog_date'],
                    'user' => $post['blog_user']
                ];
                $blog_temp = MDBlog::find_by_caption_and_deleted($post['blog_caption'],0);
                if(!$blog_temp||($blog_temp && $blog_temp->id == $blog_id)) {
                    $form_submitted = true;
                } else {
                    $message = err_msg('blog name already used');
                }
            }
        }

         if($mode == 'edit') {
            if(empty($blog_id)) {
                $message = err_msg('No blog selected');
            } else {
                $blogs = MDBlog::find_by_id_and_deleted($blog_id,0);
                if(!$blogs) {
                    $message = err_msg('blog not found');
                } else {
                    $data['blogs'] = $blogs;
                    if($form_submitted) {
                        $res = $blogs->update_attributes($data_blog);
                        if($res) {
                        // UPLOAD FILE
                        $blog_image = '';
                            if(!empty($_FILES['blog_image'])&&$_FILES['blog_image']['error']!=4){
                                $path = 'assets/img/blogs/';
                                $input_name = 'blog_image';
                                $max_size = 5000000;
                                $valid_formats = array('png','jpg','jpeg','gif');
                                @$blog_image = upload_file($path, $input_name, $max_size, $valid_formats);
                                if($blog_image) {
                                    // # REMOVE OLD IMAGE
                                    // $img = explode(',',$blogs->img);
                                    // foreach ($img as $value) {
                                    //     $old_file = $path.$value;
                                    //     if(!empty($blogs->img) && file_exists($old_file)){
                                    //         unlink($old_file);
                                    //     }
                                    // }
                                    // # END OF REMOVE OLD IMAGE
                                    
                                    //add current image
                                    $current_img = $blogs->img;

                                    //add new image
                                    $new_img = implode(',',$blog_image);

                                    //combine current & new image
                                    $img = $current_img.','.$new_img;

                                    //save them to field img in blogs table
                                    $blogs->img = $img;
                                    $blogs->save();
                                }
                            }
                        //END UPLOAD FILE

                        $message = succ_msg('blog updated');
                        } else {
                            $message = err_msg('Failed updating blog');
                        }
                    }
                }
            }
        } else {
            if($form_submitted) {
                $res = MDBlog::create($data_blog);            
                if($res) {      
                    // UPLOAD FILE
                        $blog_image = '';
                        if(!empty($_FILES['blog_image'])&&$_FILES['blog_image']['error']!=4){
                            $path = 'assets/img/blogs/';
                            $input_name = 'blog_image';
                            $max_size = 5000000;
                            $valid_formats = array('png','jpg','jpeg','gif');
                            @$blog_image = upload_file($path, $input_name, $max_size, $valid_formats);
                            if($blog_image) {
                                $img = implode(',',$blog_image);
                                $res->img = $img;
                                $res->save();
                            }
                        }
                    //END UPLOAD FILE              
                    $message = succ_msg('blog saved');
                } else {
                    $message = err_msg('Failed saving blog');
                }
            }
        }
        $data['categorys'] = MDBlogCategory::all(array('conditions'=>'deleted = 0','order'=>'id'));
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('admin_blog_form');
    }

    public function delete_image($blog_id,$img_name)
    {
        $data['message'] = '';
        $blogs = MDBlog::find_by_id_and_deleted($blog_id,0);
        if(!$blogs) {
            $this->setFlashData(err_msg('Failed delete gallery'));
        } else {
            $path = 'assets/img/blogs/';
            # REMOVE OLD IMAGE
            $img = explode(',',$blogs->img);
            foreach ($img as $value) {
                $old_file = $path.$value;
                if(!empty($blogs->img) && file_exists($old_file) && ($value == $img_name)){
                    unlink($old_file);
                }else{
                    $new_img[] = $value; 
                }
            }
            $img = implode(',',$new_img);
            $blogs->img = $img;
            $blogs->save();
            # END OF REMOVE OLD IMAGE
            $data['blogs'] = $blogs;
            $message = err_msg('image has been deleted');
        }
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('admin_blog_form');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
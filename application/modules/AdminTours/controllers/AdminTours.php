<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminTours extends XE_Controller {

    function __construct()
    {
        parent::__construct('tours-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['tourss'] = MDTour::all(array('conditions'=>'deleted = 0','order'=>'id'));
        foreach($data['tourss'] as $key=> $tourss)
        {
            $longcaracter[$key] = strlen($tourss->description);
            if($longcaracter[$key] <= 20){                
                $data['description'][$key] = $tourss->description;
            }elseif ($longcaracter[$key] > 20){
                $data['description'][$key] = substr($tourss->description, 0, 20).'...';
            }
            $idafiliasi[$key] = $tourss->id_afiliasi;
            if(!empty($idafiliasi[$key])){
                $explode_affiliate = explode(',',$idafiliasi[$key]);
                foreach($explode_affiliate as $index=>$row){
                    $affiliate = MDAfiliasi::find_by_id_and_deleted($row,0);
                    $data_afiliasi[$index] = $affiliate->name; 
                }
                $data['nameafiliasi'][$key] = implode(',',$data_afiliasi);
                $data_afiliasi = '';
            }else{
                $nameafiliasi[$key] = '-';
                $data['nameafiliasi'][$key] = $nameafiliasi[$key];
            }
        }
        $this->viewData($data);
        $this->displayView('data_tours_index');
    }

    public function detail($tours_id)
    {
        $data['message'] = '';
        $tourss = MDTour::find_by_id_and_deleted($tours_id,0);
        if(!$tourss) {
            redirect(generate_url('admin_tours'));
        }
        $data['tourss'] = $tourss;
        $this->viewData($data);
        $this->displayView('data_tours_detail');
    }

    public function delete($tours_id)
    {
        $data['message'] = '';
        $tourss = MDTour::find_by_id_and_deleted($tours_id,0);
        if(!$tourss) {
            $this->setFlashData(err_msg('Failed delete tours'));
        } else {
            $tourss->deleted = 1;
            $tourss->save();
            $this->setFlashData(succ_msg('tours has been deleted'));
        }
        redirect(generate_url('admin_tours'));
    }

    public function form()
    {
        $message = '';
        $post = $this->input->post();
        $get = $this->input->get();
        $mode = (!empty($get['mode'])) ? $get['mode'] : 'add';
        $tours_id = (!empty($get['id'])) ? $get['id'] : '';
        $form_submitted = false;
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('tours_name', 'Name', 'required');
            $this->form_validation->set_rules('tours_description', 'Description', 'required');
            $this->form_validation->set_rules('tours_itinerary', 'Itinerary', 'required');
            $this->form_validation->set_rules('tours_facility', 'Facility', 'required');
            $this->form_validation->set_rules('tours_description_summary', 'Description Sumary', 'required');
            $this->form_validation->set_rules('tours_category', 'Category', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                // $tours_afiliasi = $post['tours_afiliasi'];
                // $data_afiliasi = implode(',',$tours_afiliasi);
                // $md_count_featured = MDTour::find_by_sql("SELECT COUNT(id) as countFeatured FROM nanktour_tour WHERE featured = 1");
                // $count_featured = $md_count_featured[0]->countfeatured;
                // if(!empty($post['tours_featured']))
                // {
                //     if($count_featured >= 3)
                //     {
                //         $featured = 0;
                //     }else
                //     {
                //         $featured = $post['tours_featured'];
                //     }
                // }else
                // {
                //     $featured = 0;
                // }
                $data_tours = [
                    'name' => $post['tours_name'],
                    'description' => $post['tours_description'],
                    'itinerary' => $post['tours_itinerary'],
                    'facility' => $post['tours_facility'],
                    'description_summary' => $post['tours_description_summary'],
                    'id_tour_category' => $post['tours_category']
                    // 'id_afiliasi' => $data_afiliasi 
                ];
                $tours_temp = MDTour::find_by_name_and_deleted($post['tours_name'],0);
                if(!$tours_temp||($tours_temp && $tours_temp->id == $tours_id)) {
                    $form_submitted = true;
                } else {
                    $message = err_msg('tours name already used');
                }
            }
        }

         if($mode == 'edit') {
            if(empty($tours_id)) {
                $message = err_msg('No tours selected');
            } else {
                $tourss = MDTour::find_by_id_and_deleted($tours_id,0);
                if(!$tourss) {
                    $message = err_msg('tours not found');
                } else {
                    $data['tourss'] = $tourss;
                    if($form_submitted) {
                        $md_count_featured = MDTour::find_by_sql("SELECT COUNT(id) as countFeatured FROM nanktour_tour WHERE featured = 1 AND id != $tours_id");
                        $count_featured = $md_count_featured[0]->countfeatured;
                        if(!empty($post['tours_featured']))
                        {
                            if($count_featured >= 4)
                            {
                                $featured = 0;
                                $message = err_msg('featured tidak boleh lebih dari 4');
                            }else
                            {
                                $featured = $post['tours_featured'];
                                $message = succ_msg('tours updated');
                            }
                        }else
                        {
                            $featured = 0;
                            $message = succ_msg('tours updated');
                        }
                        $data_tours = [
                            'name' => $post['tours_name'],
                            'description' => $post['tours_description'],
                            'itinerary' => $post['tours_itinerary'],
                            'facility' => $post['tours_facility'],
                            'description_summary' => $post['tours_description_summary'],
                            'id_tour_category' => $post['tours_category'],
                            'featured' => $featured
                        ];


                        $res = $tourss->update_attributes($data_tours);
                        if($res) {
                        // UPLOAD FILE IMAGE
                        $tours_image = '';
                            if(!empty($_FILES['tours_image'])&&$_FILES['tours_image']['error']!=4){
                                $path = 'assets/img/tours/';
                                $input_name = 'tours_image';
                                $max_size = 5000000;
                                $valid_formats = array('png','jpg','jpeg','gif');
                                @$tours_image = upload_file($path, $input_name, $max_size, $valid_formats);
                                if($tours_image) {
                                    # REMOVE OLD IMAGE
                                    // $old_file = $path.$tourss->img;
                                    // if(!empty($tourss->img) && file_exists($old_file)){
                                    //     unlink($old_file);
                                    // }
                                    # END OF REMOVE OLD IMAGE
                                    $current_img = $tourss->img;
                                    $new_img = implode(',',$tours_image);
                                    if(isset($current_img) || $current_img != ''){
                                        $img = $current_img.','.$new_img;
                                    }else{
                                        $img = $new_img;
                                    }
                                    
                                    $tourss->img = $img;
                                    $tourss->save();
                                }
                            }
                        //END UPLOAD FILE IMAGE
                        // UPLOAD FILE THUMBNAIL
                        $tours_thumbnail = '';
                            if(!empty($_FILES['tours_thumbnail'])&&$_FILES['tours_thumbnail']['error']!=4){
                                $path = 'assets/img/tours/';
                                $input_name = 'tours_thumbnail';
                                $max_size = 5000000;
                                $valid_formats = array('png','jpg','jpeg','gif');
                                @$tours_thumbnail = upload_file($path, $input_name, $max_size, $valid_formats);
                                if($tours_thumbnail) {
                                    # REMOVE OLD IMAGE
                                    // $old_file = $path.$tourss->img;
                                    // if(!empty($tourss->img) && file_exists($old_file)){
                                    //     unlink($old_file);
                                    // }
                                    # END OF REMOVE OLD IMAGE
                                    $current_thumbnail = $tourss->thumbnail;
                                    $new_thumbnail = implode(',',$tours_thumbnail);
                                    if(isset($current_thumbnail) || $current_thumbnail != ''){
                                        $thumbnail = $current_thumbnail.','.$new_thumbnail;
                                    }else{
                                        $thumbnail = $new_thumbnail;
                                    }
                                    $tourss->thumbnail = $thumbnail;
                                    $tourss->save();
                                }
                            }
                        //END UPLOAD FILE THUMBNAIL
                        } else {
                            $message = err_msg('Failed updating tours image');
                        }

                    }
                }
            }
        } else {
            if($form_submitted) {
                $md_count_featured = MDTour::find_by_sql("SELECT COUNT(id) as countFeatured FROM nanktour_tour WHERE featured = 1");
                $count_featured = $md_count_featured[0]->countfeatured;
                if(!empty($post['tours_featured']))
                {
                    if($count_featured >= 4)
                    {
                        $featured = 0;
                        $message = err_msg('featured tidak boleh lebih dari 4');
                    }else
                    {
                        $featured = $post['tours_featured'];
                        $message = succ_msg('tours updated');
                    }
                }else
                {
                    $featured = 0;
                    $message = succ_msg('tours updated');
                }
                $data_tours = [
                    'name' => $post['tours_name'],
                    'description' => $post['tours_description'],
                    'itinerary' => $post['tours_itinerary'],
                    'facility' => $post['tours_facility'],
                    'description_summary' => $post['tours_description_summary'],
                    'id_tour_category' => $post['tours_category'],
                    'featured' => $featured
                ];

                $res = MDTour::create($data_tours);            
                if($res) {      
                    // UPLOAD FILE
                        $tours_image = '';
                        if(!empty($_FILES['tours_image'])&&$_FILES['tours_image']['error']!=4){
                            $path = 'assets/img/tours/';
                            $input_name = 'tours_image';
                            $max_size = 5000000;
                            $valid_formats = array('png','jpg','jpeg','gif');
                            @$tours_image = upload_file($path, $input_name, $max_size, $valid_formats);
                            if($tours_image) {
                                $img = implode(',',$tours_image);
                                $res->img = $tours_image;
                                $res->save();
                            }
                        }
                    //END UPLOAD FILE
                    // UPLOAD FILE
                        $tours_thumbnail = '';
                        if(!empty($_FILES['tours_thumbnail'])&&$_FILES['tours_thumbnail']['error']!=4){
                            $path = 'assets/img/tours/';
                            $input_name = 'tours_thumbnail';
                            $max_size = 5000000;
                            $valid_formats = array('png','jpg','jpeg','gif');
                            @$tours_thumbnail = upload_file($path, $input_name, $max_size, $valid_formats);
                            if($tours_thumbnail) {
                                $thumbnail = implode(',',$tours_thumbnail);
                                $res->thumbnail = $tours_thumbnail;
                                $res->save();
                            }
                        }
                    //END UPLOAD FILE
                } else {
                    $message = err_msg('Failed saving tours');
                }
            }
        }
        $data['categories'] = MDTourCategory::all(array('conditions' => 'deleted = 0', 'order' => 'id'));
        // $data['afiliasi'] = MDAfiliasi::all(array('conditions' => 'deleted = 0', 'order' => 'id'));
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('data_tours_form');
    }

    public function delete_image($tour_id,$img_name)
    {
        $data['message'] = '';
        $tour = MDTour::find_by_id_and_deleted($tour_id,0);
        if(!$tour) {
            $this->setFlashData(err_msg('Failed delete gallery'));
        } else {
            $path = 'assets/img/tours/';
            # REMOVE OLD IMAGE
            $img = explode(',',$tour->img);
            foreach ($img as $value) {
                $old_file = $path.$value;
                if(!empty($tour->img) && file_exists($old_file) && ($value == $img_name)){
                    unlink($old_file);
                }else{
                    $new_img[] = $value; 
                }
            }
            $img = implode(',',$new_img);
            $tour->img = $img;
            $tour->save();
            # END OF REMOVE OLD IMAGE
            $data['tourss'] = $tour;
            $message = err_msg('image has been deleted');
        }
        $data['message'] = $message;
        $data['categories'] = MDTourCategory::all(array('conditions' => 'deleted = 0', 'order' => 'id'));
        $this->viewData($data);
        $this->displayView('data_tours_form');
    }

    public function delete_thumbnail($tour_id,$img_name)
    {
        $data['message'] = '';
        $tour = MDTour::find_by_id_and_deleted($tour_id,0);
        if(!$tour) {
            $this->setFlashData(err_msg('Failed delete gallery'));
        } else {
            $path = 'assets/img/tours/';
            # REMOVE OLD IMAGE
            $img = explode(',',$tour->thumbnail);
            foreach ($img as $value) {
                $old_file = $path.$value;
                if(!empty($tour->thumbnail) && file_exists($old_file) && ($value == $img_name)){
                    unlink($old_file);
                }else{
                    $new_img[] = $value; 
                }
            }
            $img = implode(',',$new_img);
            $tour->thumbnail = $img;
            $tour->save();
            # END OF REMOVE OLD IMAGE
            $data['tourss'] = $tour;
            $message = err_msg('image has been deleted');
        }
        $data['message'] = $message;
        $data['categories'] = MDTourCategory::all(array('conditions' => 'deleted = 0', 'order' => 'id'));
        $this->viewData($data);
        $this->displayView('data_tours_form');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
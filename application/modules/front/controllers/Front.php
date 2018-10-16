<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Front extends XE_PublicController {

    function __construct()
    {
        parent::__construct();
        $company_name = getSettingValue('company_name');
        $this->company_name = $company_name ? $company_name->value : $this->config->item('default_title');
        $data['menucategories'] = MDTourCategory::all(array('conditions'=>"deleted = 0 AND status = 'child'",'order'=>'id'));
        $company_meta_description = getSettingValue('company_meta_description');
        $data['blogs'] = MDBlog::all(array('conditions'=>'deleted = 0','order'=>'caption','limit'=>'4'));
        foreach($data['blogs'] as $key=> $blogs)
        {
            $longcaracter[$key] = strlen($blogs->description);
            if($longcaracter[$key] <= 100){                
                $data['description'][$key] = $blogs->description;
            }elseif ($longcaracter[$key] > 100){
                $data['description'][$key] = substr($blogs->description, 0, 100).'...';
            }
        }
        $this->company_meta_description = $company_meta_description->value;
        $this->viewData($data);
    }

    public function index()
    {
        $data['title'] = $this->company_name;
        $data['meta_desc'] = $this->company_meta_description;
        $data['sliders'] = MDSliders::all(array('conditions'=>'deleted = 0','order'=>'id'));
        $data['quotes'] = MDQuote::all(array('conditions'=>'deleted = 0','order'=>'id'));
        $join = array('LEFT JOIN nanktour_tour_category c ON(nanktour_tour.id_tour_category = c.id)',
                    'LEFT JOIN nanktour_tour_category t ON(t.id = c.id_category)');
        $data['tours'] = MDTour::all(array(
                            'select'=> 'nanktour_tour.*, c.name as name_category, c.alias as alias_category, t.name as name_parent, t.alias as alias_parent',
                            'joins'=>$join,
                            'conditions' => array('nanktour_tour.featured = 1 AND nanktour_tour.deleted = 0'),
                            'order' => "nanktour_tour.name"
                        ));
        $data['blogs'] = MDBlog::all(array('conditions'=>'deleted = 0','order'=>'caption','limit'=>'4'));
        foreach($data['blogs'] as $key=> $blogs)
        {
            $longcaracter[$key] = strlen($blogs->description);
            if($longcaracter[$key] <= 100){                
                $data['description'][$key] = $blogs->description;
            }elseif ($longcaracter[$key] > 100){
                $data['description'][$key] = substr($blogs->description, 0, 100).'...';
            }
        }
        $data['testimoies'] = MDTestimony::all(array('conditions'=>'deleted = 0','order'=>'name'));
        $data['galleries'] = MDGallery::all(array('conditions'=>'deleted = 0','order'=>'id'));
        // $this->addFunction('htmlentities');
        $this->viewData($data);
        $this->displayView('index');
    }

    public function tour()
    {
        $data['title'] = $this->company_name . ' - Tour';
        $data['meta_desc'] = $this->company_meta_description;
        $data['categories'] = MDTourCategory::all(array('conditions'=>"deleted = 0",'order'=>'name'));
        $join = array('LEFT JOIN nanktour_tour_category c ON(nanktour_tour.id_tour_category = c.id)',
                    'LEFT JOIN nanktour_tour_category t ON(t.id = c.id_category)');
        $data['tours'] = MDTour::all(array(
                            'select'=> 'nanktour_tour.*, c.name as name_category, c.alias as alias_category, t.name as name_parent, t.alias as alias_parent',
                            'joins'=>$join,
                            'conditions' => array('nanktour_tour.deleted = 0'),
                            'order' => "nanktour_tour.name"
                        ));
        $this->viewData($data);
        $this->displayView('tour');
    }

    public function tours($alias)
    {
        $alias2 = $this->uri->segment(3);
        $alias3 = $this->uri->segment(4);
        $data['meta_desc'] = $this->company_meta_description;
    
        if(isset($alias) && !isset($alias2) && !isset($alias3)){
            $category = MDTourCategory::find_by_alias_and_deleted($alias,0);
            $id_category = $category->id;
            $data['category_name'] = $category->name;
            $data['title'] = $this->company_name . ' - Trip '.$category->name;
            $data['categories'] = MDTourCategory::all(array('conditions'=>"deleted = 0 AND id_category = $id_category",'order'=>'name'));
            $join = array('LEFT JOIN nanktour_tour_category c ON(nanktour_tour.id_tour_category = c.id)',
                    'LEFT JOIN nanktour_tour_category t ON(t.id = c.id_category)');
            $data['tours'] = MDTour::all(array(
                                'select'=> 'nanktour_tour.*, c.name as name_category, c.alias as alias_category, t.name as name_parent, t.alias as alias_parent',
                                'joins'=>$join,
                                'conditions' => array('t.id = ? AND nanktour_tour.deleted = 0',$id_category),
                                'order' => "nanktour_tour.name"
                            ));
            //dump($data['tours']);
            $view_data = 'tour';
        }else if(isset($alias) && isset($alias2) && !isset($alias3)){
            $parent = MDTourCategory::find_by_alias_and_deleted($alias,0);
            $id_parent = $parent->id;
            $child = MDTourCategory::find_by_alias_and_id_category_and_deleted($alias2,$id_parent,0);
            $id_child = $child->id;
            $data['parent_name'] = $parent->name;
            $data['parent_alias'] = $parent->alias;
            $data['child_name'] = $child->name;
            $data['child_alias'] = $child->alias;
            $data['title'] = $this->company_name . ' - '.$parent->name.' - '.$child->name;
            $join = array('LEFT JOIN nanktour_tour_category c ON(nanktour_tour.id_tour_category = c.id)',
                    'LEFT JOIN nanktour_tour_category t ON(t.id = c.id_category)');
            $data['tours'] = MDTour::all(array(
                                'select'=> 'nanktour_tour.*, c.name as name_category, c.alias as alias_category, t.name as name_parent',
                                'joins'=>$join,
                                'conditions' => array('t.id = ? AND c.id = ? AND nanktour_tour.deleted = 0',$id_parent,$id_child),
                                'order' => "nanktour_tour.name"
                            ));
            //echo MDTour::connection()->last_query;;exit;
            $data['tour_categories'] = MDTourCategory::all(array('conditions'=>'deleted = 0','order'=>'name'));
            $view_data='tour_category';
        }else if(isset($alias) && isset($alias2) && isset($alias3)){
            $parent = MDTourCategory::find_by_alias_and_deleted($alias,0);
            $id_parent = $parent->id;
            $child = MDTourCategory::find_by_alias_and_id_category_and_deleted($alias2,$id_parent,0);
            $id_child = $child->id;
            $data['parent_name'] = $parent->name;
            $data['parent_alias'] = $parent->alias;
            $data['child_name'] = $child->name;
            $data['child_alias'] = $child->alias;
            $data['message'] = '';
            $join = array('LEFT JOIN nanktour_tour_category c ON(nanktour_tour.id_tour_category = c.id)',
                    'LEFT JOIN nanktour_tour_category t ON(t.id = c.id_category)');
            $tour = MDTour::find(array(
                                'select'=> 'nanktour_tour.*, c.name as name_category, c.alias as alias_category, t.name as name_parent',
                                'joins'=>$join,
                                'conditions' => array('t.id = ? AND c.id = ? AND nanktour_tour.alias = ? AND nanktour_tour.deleted = 0',$id_parent,$id_child,$alias3),
                                'order' => "nanktour_tour.name"
                            ));
            $data['tour'] = $tour;
            $data['title'] = $this->company_name . ' - '.$tour->name;
            $view_data = 'tour_detail';
        }
        //print_r($view_data);exit;
        $this->viewData($data);
        $this->displayView($view_data);
        
    }
    
    public function about()
    {
        $data['title'] = $this->company_name . ' - About';
        $data['meta_desc'] = $this->company_meta_description;
        $history = MDSettingValue::find_by_key_and_deleted('company_history',0);
        $data['history'] = json_decode($history->value, true);
        $this->viewData($data);
        $this->displayView('about');
    }

    public function faq()
    {
        $data['title'] = $this->company_name . ' - FAQ';
        $data['meta_desc'] = $this->company_meta_description;
        $history = MDSettingValue::find_by_key_and_deleted('company_history',0);
        $data['faqs'] = MDFaq::all(array('conditions'=>'deleted = 0','order'=>'id'));
        $data['history'] = json_decode($history->value, true);
        $this->viewData($data);
        $this->displayView('faq');
    }


    public function blogs()
    {
        $data['title'] = $this->company_name . ' - Blog';
        $data['meta_desc'] = $this->company_meta_description;
        $data['blogs'] = MDBlog::all(array('conditions'=>'deleted = 0','order'=>'date'));
        $data['blog_categories'] = MDBlogCategory::all(array('conditions'=>'deleted = 0','order'=>'name'));
        foreach($data['blogs'] as $key=> $blogs)
        {
            $longcaracter[$key] = strlen($blogs->description);
            if($longcaracter[$key] <= 200){                
                $data['description'][$key] = $blogs->description;
            }elseif ($longcaracter[$key] > 200){
                $data['description'][$key] = substr($blogs->description, 0, 200).'...';
            }
        }
        $this->viewData($data);
        $this->displayView('blog');
    }

    public function blog($alias)
    {
        $data['title'] = $this->company_name . ' - Blog Detail';
        $data['meta_desc'] = $this->company_meta_description;
        $data['message'] = '';
        $blog = MDBlog::find_by_alias_and_deleted($alias,0);
        $img = explode(',',$blog->img);
        $blogsImg = count($img);   
        $divide = ceil($blogsImg / 4); 
        $data['array_img'] = array_chunk($img,4,$divide);
        if(!$blog) {
            redirect(generate_url('blogs'));
        }
        $data['blog_categories'] = MDBlogCategory::all(array('conditions'=>'deleted = 0','order'=>'name'));
        $data['blogs'] = $blog;
        $this->viewData($data);
        $this->displayView('blog_detail');
    }
    
    public function contact()
    {
        $data['title'] = $this->company_name . ' - Contact';
        $data['meta_desc'] = $this->company_meta_description;
        $message = '';
        $post = $this->input->post();
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('contact_name', 'Name', 'required');
            $this->form_validation->set_rules('contact_email', 'Email', 'required');
            $this->form_validation->set_rules('contact_phone', 'Phone', 'required');
            $this->form_validation->set_rules('contact_address', 'Address', 'required');
            $this->form_validation->set_rules('contact_message', 'Message', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $data_contact = [
                    'name' => $post['contact_name'],
                    'email' => $post['contact_email'] ,
                    'phone' => $post['contact_phone'],
                    'address' => $post['contact_address'],
                    'message' => $post['contact_message']              
                ];
                $res = MDContact::create($data_contact);            
                if($res) {            
                    $message = succ_msg('Data saved');
                } else {
                    $message = err_msg('Failed saving Data');
                }
            }
        }
        $data['message']=$message;
        $this->viewData($data);
        $this->displayView('contact');
    }  

    public function gallery()
    {
        $data['title'] = $this->company_name . ' - Gallery';
        $data['meta_desc'] = $this->company_meta_description;
        $data['galleries'] = MDGallery::all(array('conditions'=>'deleted = 0','order'=>'id'));
        $data['gallery_categories'] = MDGalleryCategory::all(array('conditions'=>'deleted = 0','order'=>'id'));
        $this->viewData($data);
        $this->displayView('gallery');
    }
    
    public function gallery_detail($gallery_id)
    {
        $gallery = MDGallery::find_by_id_and_deleted($gallery_id,0);
        $data['title'] = $this->company_name . ' - Gallery';
        $data['meta_desc'] = $this->company_meta_description;
        $data['galleries'] = $gallery;
        $this->viewData($data);
        $this->displayView('gallery_detail');
    }

    public function service($categories_id)
    {
        $data['title'] = $this->company_name . ' - Services';
        $data['meta_desc'] = $this->company_meta_description;
        $data['message'] = '';
        $categories = MDServiceCategory::find_by_id_and_deleted($categories_id,0);
        if(!$categories) {
            redirect(generate_url('index'));
        }
        $service = MDService::all(array('conditions'=>'id_service_category='.$categories_id.' AND deleted = 0','order'=>'id'));
        foreach($service as $key=> $servicess)
        {
            $idafiliasi[$key] = $servicess->id_afiliasi;
            if(!empty($idafiliasi[$key])){
                $explode_affiliate = explode(',',$idafiliasi[$key]);
                foreach($explode_affiliate as $index=>$row){
                    $affiliate = MDAfiliasi::find_by_id_and_deleted($row,0);
                    $data_name_afiliasi[$index] = $affiliate->name; 
                    $data_img_afiliasi[$index] = $affiliate->img; 
                }
                $data['nameafiliasi'][$key] = implode(',',$data_name_afiliasi);
                $data['imgafiliasi'][$key] = implode(',',$data_img_afiliasi);
                $data_name_afiliasi = '';
            }else{
                $data['nameafiliasi'][$key] = '';
                $data['imgafiliasi'][$key] = '';
            }
        }
        // dump($data['imgafiliasi']);
        if(!$service) {
            redirect(generate_url('index'));
        }
        $data['services'] = $service;
        $data['categories'] = $categories;
        $data['service_category'] = MDServiceCategory::all(array('conditions'=>'deleted = 0','order'=>'id'));
        $this->viewData($data);
        $this->displayView('service');
    } 

    public function service_detail($service_id)
    {
        $data['title'] = $this->company_name . ' - Services Detail';
        $data['meta_desc'] = $this->company_meta_description;
        $data['message'] = '';
        $service = MDService::find_by_id_and_deleted($service_id,0);
        if(!$service) {
            redirect(generate_url('index'));
        }
        $data['services'] = $service;
        $data['service_category'] = MDServiceCategory::all(array('conditions'=>'deleted = 0','order'=>'id'));
        $this->viewData($data);
        $this->displayView('service_detail');
    } 
    
    // public function ordernow()
    // {
    //     $data['title'] = $this->company_name . ' - Order Now';
    //     $message = '';
    //     $notification = '';
    //     $orders = MDFormOrder::all(array('conditions'=>'deleted = 0'));
    //     $post = $this->input->post();
    //     $get = $this->input->get();
    //     $data['prices'] = MDPrice::all();
    //     $mode = (!empty($get['mode'])) ? $get['mode'] : 'add';
    //     $orders_id = (!empty($get['id'])) ? $get['id'] : '';
    //     $form_submitted = false;
    //     $data['orders'] = $orders;
    //     if(!empty($post)) {
    //         $this->load->library('form_validation');
    //         $this->form_validation->set_rules('fullname', 'Fullname', 'required');
    //         $this->form_validation->set_rules('email', 'Email Address', 'required');
    //         $this->form_validation->set_rules('phone', 'Phone', 'required|numeric');
    //         $this->form_validation->set_rules('address', 'Address', 'required');
    //         $this->form_validation->set_rules('service', 'Service', 'required');
    //         $this->form_validation->set_rules('qty', 'Quantity', 'required|numeric');
    //         $this->form_validation->set_rules('note', 'Note', 'required');
    //         if ($this->form_validation->run() == FALSE) {
    //             $message = err_msg(validation_errors());
    //         } else {
    //             $prices = MDPrice::find_by_name_and_deleted($post['service'],0);
    //             $price = $prices->price;
                
    //             $data_orders = [
    //                 'fullname' => $post['fullname'],
    //                 'email' => $post['email'],
    //                 'phone' => $post['phone'],
    //                 'address' => $post['address'],
    //                 'service' => $post['service'],
    //                 'price' => $price,
    //                 'qty' => $post['qty'],       
    //                 'note' => $post['note']         
    //             ];
    //             $orders_temp = MDFormOrder::find_by_fullname_and_deleted($post['fullname'],0);
    //             if(!$orders_temp||($orders_temp && $orders_temp->id == $orders_id)) {
    //                 $form_submitted = true;
    //             } else {
    //                 $message = 'Orders name already used';
    //                 $notification = err_msg('Failed!');
    //             }
    //         }
    //     }
    //     if($form_submitted) {
    //         $res = MDFormOrder::create($data_orders);           
    //         if($res) {                               
    //             $message = 'Orders saved';
    //             $notification = succ_msg('Success!');
    //             if($message){
    //                 $this->load->helper('cookie');
    //                 $this->load->helper('security');    
    //                 $post = $this->input->post();        
    //                 // form validation
    //                 $this->load->library('form_validation');
    //                 $this->form_validation->set_rules('fullname', 'Fullname', 'trim|required|xss_clean');
    //                 $this->form_validation->set_rules('email', 'Email Address', 'trim|required|xss_clean|valid_email');
    //                 $this->form_validation->set_rules('phone', 'Phone', 'trim|xss_clean|numeric');
    //                 $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
    //                 $this->form_validation->set_rules('service', 'Service', 'trim|required|xss_clean');
    //                 $this->form_validation->set_rules('qty', 'Quantity', 'trim|xss_clean|numeric');
    //                 $this->form_validation->set_rules('note', 'Note', 'trim|required|xss_clean');        
    //                 $this->load->helper('string_helper');
    //                 if ($this->form_validation->run() == TRUE)
    //                 {        
    //                     $fullName = $post['fullname'];
    //                     $email = $post['email'];            
    //                     $phone = !empty($post['phone']) ? $post['phone'] : '';
    //                     $address = $post['address'];
    //                     $service = $post['service'];
    //                     $qty = !empty($post['qty']) ? $post['qty'] : '';
    //                     $note = $post['note'];
    //                     $companyEmail = MDSettingValue::find_by_key('company_email');                        
    //                     $userEmail = $companyEmail->value;
    //                     $prices = MDPrice::find_by_name_and_deleted($post['service'],0);
    //                     $price = $prices->price;
    //                     $data['message'] =  err_msg('Order Failed! Please try again later.');        
    //                     try {
    //                         $data['message'] =  succ_msg('Registration complete!');
    //                         $subject = 'New Order';
    //                         $message = "Fullname : <strong>$fullName</strong>
    //                                     <br>Email: <strong>$email</strong>
    //                                     <br>Phone: <strong>$phone</strong>
    //                                     <br>Address: <strong>$address</strong>
    //                                     <br>Service: <strong>$service</strong>
    //                                     <br>Service: <strong>$price</strong>
    //                                     <br>Quantity: <strong>$qty</strong>
    //                                     <br>Note: <strong>$note</strong>                              
    //                                     ";
            
    //                         $this->load->helper('xemail_helper');
    //                         sendEmail($userEmail,$subject,$message);
    //                     } catch (\Exception $e) {
    //                         echo $e->getMessage();
    //                     }
    //                     $message = 'Orders saved';
    //                     $notification = succ_msg('Success!');
    //                 }
    //                 else {        
    //                     $data['message'] =  err_msg(validation_errors());
    //                 }
    //             }
    //         } else {
    //             $message = 'Failed saving Orders';
    //             $notification = err_msg('Failed!');
    //         }
    //     }        

        
    //     // $data['data'] = $post;
    //     // $this->setFlashData($data);        
    //     $data['message_order'] = $message;
    //     $data['notification'] = $notification;
    //     $this->viewData($data);
    //     $this->displayView('index');
    // } 
}

/* End of file auth.php */
/* Location: ./application/modules/auth/controllers/auth.php */
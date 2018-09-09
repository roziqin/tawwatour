<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminPrice extends XE_Controller {

    function __construct()
    {
        parent::__construct('price-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['prices'] = MDPrice::all(array('conditions'=>'deleted = 0','order'=>'id'));
        foreach($data['prices'] as $key=> $prices)
        {
            $longcaracter[$key] = strlen($prices->description);
            if($longcaracter[$key] <= 20){                
                $data['description'][$key] = $prices->description;
            }elseif ($longcaracter[$key] > 20){
                $data['description'][$key] = substr($prices->description, 0, 20).'...';
            }
        }
        $this->viewData($data);
        $this->displayView('data_price_index');
    }

    public function detail($price_id)
    {
        $data['message'] = '';
        $prices = MDPrice::find_by_id_and_deleted($price_id,0);
        if(!$prices) {
            redirect(generate_url('admin_price'));
        }
        $data['prices'] = $prices;
        $this->viewData($data);
        $this->displayView('data_price_detail');
    }

    public function delete($price_id)
    {
        $data['message'] = '';
        $prices = MDPrice::find_by_id_and_deleted($price_id,0);
        if(!$prices) {
            $this->setFlashData(err_msg('Failed delete Price'));
        } else {
            $prices->deleted = 1;
            $prices->save();
            $this->setFlashData(succ_msg('Price has been deleted'));
        }
        redirect(generate_url('admin_price'));
    }

    public function form()
    {
        $message = '';
        $post = $this->input->post();
        $get = $this->input->get();
        $mode = (!empty($get['mode'])) ? $get['mode'] : 'add';
        $price_id = (!empty($get['id'])) ? $get['id'] : '';
        $form_submitted = false;
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('price_name', 'Name', 'required');
            $this->form_validation->set_rules('price_description', 'Description', 'required');
            $this->form_validation->set_rules('price_price', 'Price', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                if(empty($post['show_public'])){
                    $show_public = 1;
                }else{
                    $show_public = 0;
                }
                $data_price = [
                    'name' => $post['price_name'],
                    'description' => $post['price_description'] ,
                    'price' => $post['price_price'],
                    'show_public' => $show_public         
                ];
                $price_temp = MDPrice::find_by_name_and_deleted($post['price_name'],0);
                if(!$price_temp||($price_temp && $price_temp->id == $price_id)) {
                    $form_submitted = true;
                } else {
                    $message = err_msg('Price caption already used');
                }
            }
        }

         if($mode == 'edit') {
            if(empty($price_id)) {
                $message = err_msg('No Price selected');
            } else {
                $prices = MDPrice::find_by_id_and_deleted($price_id,0);
                if(!$prices) {
                    $message = err_msg('Price not found');
                } else {
                    $data['prices'] = $prices;
                    if($form_submitted) {
                        $res = $prices->update_attributes($data_price);
                        if($res) {
                        // UPLOAD FILE
                        $price_image = '';
                            if(!empty($_FILES['price_image'])&&$_FILES['price_image']['error']!=4){
                                $path = 'assets/images/price/';
                                $input_name = 'price_image';
                                $max_size = 5000000;
                                $valid_formats = array('png','jpg','jpeg','gif');
                                @$price_image = upload_file($path, $input_name, $max_size, $valid_formats);
                                if($price_image) {
                                    # REMOVE OLD IMAGE
                                    $old_file = $path.$prices->img;
                                    if(!empty($prices->img) && file_exists($old_file)){
                                        unlink($old_file);
                                    }
                                    # END OF REMOVE OLD IMAGE
                                    $prices->img = $price_image;
                                    $prices->save();
                                }
                            }
                        //END UPLOAD FILE

                        $message = succ_msg('Price updated');
                        } else {
                            $message = err_msg('Failed updating Price');
                        }
                    }
                }
            }
        } else {
            if($form_submitted) {
                $res = MDPrice::create($data_price);            
                if($res) {      
                    // UPLOAD FILE
                        $price_image = '';
                        if(!empty($_FILES['price_image'])&&$_FILES['price_image']['error']!=4){
                            $path = 'assets/images/price/';
                            $input_name = 'price_image';
                            $max_size = 5000000;
                            $valid_formats = array('png','jpg','jpeg','gif');
                            @$price_image = upload_file($path, $input_name, $max_size, $valid_formats);
                            if($price_image) {
                                $res->img = $price_image;
                                $res->save();
                            }
                        }
                    //END UPLOAD FILE              
                    $message = succ_msg('Price saved');
                } else {
                    $message = err_msg('Failed saving Price');
                }
            }
        }
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('data_price_form');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
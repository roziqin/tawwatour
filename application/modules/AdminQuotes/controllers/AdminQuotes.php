<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminQuotes extends XE_Controller {

    function __construct()
    {
        parent::__construct('quotes-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['quotes'] = MDQuote::all(array('conditions'=>'deleted = 0','order'=>'name'));
        foreach($data['quotes'] as $key=> $quotes)
        {
            $longcaracter[$key] = strlen($quotes->description);
            if($longcaracter[$key] <= 20){                
                $data['description'][$key] = $quotes->description;
            }elseif ($longcaracter[$key] > 20){
                $data['description'][$key] = substr($quotes->description, 0, 20).'...';
            }
        }
        $this->viewData($data);
        $this->displayView('quotes_index');
    }

    public function detail($quotes_id)
    {
        $data['message'] = '';
        $quotes = MDQuote::find_by_id_and_deleted($quotes_id,0);
        if(!$quotes) {
            redirect(generate_url('admin_quotes_index'));
        }
        $data['quotes'] = $quotes;
        $this->viewData($data);
        $this->displayView('quotes_detail');
    }

    public function delete($quotes_id)
    {
        $data['message'] = '';
        $quotes = MDQuote::find_by_id_and_deleted($quotes_id,0);
        if(!$quotes) {
            $this->setFlashData(err_msg('Failed delete Quote'));
        } else {
            $quotes->deleted = 1;
            $quotes->save();
            $this->setFlashData(succ_msg('Quote has been deleted'));
        }
        redirect(generate_url('admin_quotes_index'));
    }

    public function form()
    {
        $message = '';
        $quotes = MDQuote::all(array('conditions'=>'deleted = 0'));
        $post = $this->input->post();
        $get = $this->input->get();
        $mode = (!empty($get['mode'])) ? $get['mode'] : 'add';
        $quotes_id = (!empty($get['id'])) ? $get['id'] : '';
        $form_submitted = false;
        $data['quotes'] = $quotes;
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('quotes_name', 'Name', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $data_quotes = [
                    'name' => $post['quotes_name'],
                    'description' => $post['quotes_description']                
                ];
                $quotes_temp = MDQuote::find_by_name_and_deleted($post['quotes_name'],0);
                if(!$quotes_temp||($quotes_temp && $quotes_temp->id == $quotes_id)) {
                    $form_submitted = true;
                } else {
                    $message = err_msg('Quote name already used');
                }
            }
        }

         if($mode == 'edit') {
            if(empty($quotes_id)) {
                $message = err_msg('No Quote selected');
            } else {
                $quotes = MDQuote::find_by_id_and_deleted($quotes_id,0);
                if(!$quotes) {
                    $message = err_msg('Quote not found');
                } else {
                    $data['quotes'] = $quotes;
                    if($form_submitted) {
                        $res = $quotes->update_attributes($data_quotes);
                        if($res) {
                        // UPLOAD FILE
                        $quotes_image = '';
                            if(!empty($_FILES['quotes_image'])&&$_FILES['quotes_image']['error']!=4){
                                $path = 'assets/images/quotes/';
                                $input_name = 'quotes_image';
                                $max_size = 5000000;
                                $valid_formats = array('png','jpg','jpeg','gif');
                                @$quotes_image = upload_file($path, $input_name, $max_size, $valid_formats);
                                if($quotes_image) {
                                    # REMOVE OLD IMAGE
                                    $old_file = $path.$quotes->img;
                                    if(!empty($quotes->img) && file_exists($old_file)){
                                        unlink($old_file);
                                    }
                                    # END OF REMOVE OLD IMAGE
                                    $quotes->img = $quotes_image;
                                    $quotes->save();
                                }
                            }
                        //END UPLOAD FILE

                        $message = succ_msg('Quote updated');
                        } else {
                            $message = err_msg('Failed updating Quote');
                        }
                    }
                }
            }
        } else {
            if($form_submitted) {
                $res = MDQuote::create($data_quotes);            
                if($res) {      
                    // UPLOAD FILE
                        $quotes_image = '';
                        if(!empty($_FILES['quotes_image'])&&$_FILES['quotes_image']['error']!=4){
                            $path = 'assets/images/quotes/';
                            $input_name = 'quotes_image';
                            $max_size = 5000000;
                            $valid_formats = array('png','jpg','jpeg','gif');
                            @$quotes_image = upload_file($path, $input_name, $max_size, $valid_formats);
                            if($quotes_image) {
                                $res->img = $quotes_image;
                                $res->save();
                            }
                        }
                    //END UPLOAD FILE              
                    $message = succ_msg('Quote saved');
                } else {
                    $message = err_msg('Failed saving Quote');
                }
            }
        }
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('quotes_form');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
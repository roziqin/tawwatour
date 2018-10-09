<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminTourCategories extends XE_Controller {

    function __construct()
    {
        parent::__construct('tours-categories-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['categorys'] = MDTourCategory::all(array('conditions'=>'deleted = 0','order'=>'name'));
        $this->viewData($data);
        $this->displayView('tours_category_index');
    }

    public function detail($category_id)
    {
        $data['message'] = '';
        $category = MDTourCategory::find_by_id_and_deleted($category_id,0);
        if(!$category) {
            redirect(generate_url('admin_tours_categories'));
        }
        $data['category'] = $category;
        $this->viewData($data);
        $this->displayView('tours_category_detail');
    }

    public function delete($category_id)
    {
        $data['message'] = '';
        $category = MDTourCategory::find_by_id_and_deleted($category_id,0);
        if(!$category) {
            $this->setFlashData(err_msg('Failed delete category'));
        } else {
            $category->deleted = 1;
            $category->save();
            $this->setFlashData(succ_msg('Category has been deleted'));
        }
        redirect(generate_url('admin_tours_categories'));
    }

    public function form()
    {
        $message = '';
        $post = $this->input->post();
        $get = $this->input->get();
        $mode = (!empty($get['mode'])) ? $get['mode'] : 'add';
        $category_id = (!empty($get['id'])) ? $get['id'] : '';
        $form_submitted = false;
        if(!empty($post)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('tours_category_name', 'Name', 'required');
            if ($this->form_validation->run() == FALSE) {
                $message = err_msg(validation_errors());
            } else {
                $text = $post['tours_category_name'];
                $text = preg_replace('~[^\pL\d]+~u', '-', $text);
                $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
                $text = preg_replace('~[^-\w]+~', '', $text);
                $text = trim($text, '-');
                $text = preg_replace('~-+~', '-', $text);
                $text = strtolower($text);

                $data_category = [
                    'name' => $post['tours_category_name'],
                    'alias' => $text,
                    'status' => $post['tours_category_status']
                ];
                $category_temp = MDTourCategory::find_by_name_and_deleted($post['tours_category_name'],0);
                if(!$category_temp||($category_temp && $category_temp->id == $category_id)) {
                    $form_submitted = true;
                } else {
                    $message = err_msg('Category name already used');
                }
            }
        }

         if($mode == 'edit') {
            if(empty($category_id)) {
                $message = err_msg('No category selected');
            } else {
                $category = MDTourCategory::find_by_id_and_deleted($category_id,0);
                if(!$category) {
                    $message = err_msg('Category not found');
                } else {
                    $data['category'] = $category;
                    if($form_submitted) {
                        $res = $category->update_attributes($data_category);
                        if($res) {
                            $message = succ_msg('Category updated');
                        } else {
                            $message = err_msg('Failed updating category');
                        }
                    }
                }
            }
        } else {
            if($form_submitted) {
                $res = MDTourCategory::create($data_category);
                if($res) {
                    $message = succ_msg('Category saved');
                } else {
                    $message = err_msg('Failed saving category');
                }
            }
        }
        $data['message'] = $message;
        $this->viewData($data);
        $this->displayView('tours_category_form');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
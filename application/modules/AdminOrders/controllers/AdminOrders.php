<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminOrders extends XE_Controller {

    function __construct()
    {
        parent::__construct('orders-admin');
        $this->cekLogin(generate_url('login'));
    }

    public function index()
    {
        $data['message'] = $this->getFlashData();
        $data['orders'] = MDFormOrder::all(array('conditions'=>'deleted = 0','order'=>'fullname'));
        $this->viewData($data);
        $this->displayView('orders_index');
    }
}

/* End of file report.php */
/* Location: ./application/modules/report/controllers/report.php */
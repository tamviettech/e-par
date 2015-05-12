<?php

defined('DS') or die('no direct access');

class system_config_Controller extends Controller
{

    function __construct()
    {
        parent::__construct('cores', 'system_config');
        Session::init();
        //Kiem tra dang nhap
        session::check_login();
        Session::get('is_admin') or $this->access_denied();
    }

    function main()
    {
        $data['xml_data'] = $this->model->qry_all_system_config_item();
        
        $this->view->render('dsp_main', $data);
    }
    
    function update_options()
    {
        $this->model->update_options();
    }

}

?>

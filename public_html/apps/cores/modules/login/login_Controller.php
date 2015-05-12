<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class login_Controller extends Controller
{

    function __construct()
    {
        parent::__construct('cores', 'login');
        $this->model->goback_url = $this->view->get_controller_url();
    }

    public function main()
    {
        $this->dsp_login();
    }

    public function dsp_login()
    {
        session::init();
        if (empty($_SERVER['HTTPS']))
        {
            //header('location:' . 'https://demo.e-par.vn/cores/login/');
        }
        $this->view->render('dsp_login');
    }

    public function do_login()
    {
        session::destroy();
        session::init();
        session_regenerate_id(true);
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->do_login();
    }

    public function do_logout()
    {
        session::destroy();
        session::init();   
        session_regenerate_id(true);
        #header('location:' . SITE_ROOT);
        $this->dsp_login();
    }

    public function svc_check_session_token($token)
    {
        header('Content-type: application/json');
        echo json_encode(validate_session_token($token));
    }

}
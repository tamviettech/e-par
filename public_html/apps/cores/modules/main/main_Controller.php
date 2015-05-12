<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class main_Controller extends Controller {

    function __construct()
    {
        parent::__construct('cores', 'main');
        $this->view->template->show_left_side_bar =FALSE;

        //Kiem tra dang nhap
        session::check_login();
    }
    function main()
    {
        $VIEW_DATA['arr_my_application'] = $this->model->qry_all_application();
        $this->view->render('dsp_main_screen', $VIEW_DATA);
    }
}
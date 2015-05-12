<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class sequence_Controller extends Controller {
     function __construct() {
        parent::__construct('cores', 'sequence');
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;

        //Kiem tra dang nhap
        session::check_login();
    }

    function main()
    {

    }

    function next_val($seq_name)
    {
        echo $this->model->next_val($seq_name);
    }


}
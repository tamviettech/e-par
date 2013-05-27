<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');



class calendar_Controller extends Controller {
     function __construct() {
        parent::__construct('cores', 'calendar');
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;

        //Kiem tra dang nhap
        $this->check_login();
    }

    function main()
    {

        //Kiem tra quyen xem

        $v_year = isset($_POST['sel_year']) ? $_POST['sel_year'] : jwDate::dateNow('%Y');
        if (!( preg_match( '/^\d*$/', trim($v_year)) == 1 ))
        {
            $v_year =  jwDate::dateNow('%Y');
        }

        $VIEW_DATA['arr_all_date_off']       = $this->model->qry_all_date_off($v_year);
        $VIEW_DATA['arr_all_date_working']   = $this->model->qry_all_date_working($v_year);
        $VIEW_DATA['v_year_filter']            = $v_year;

        $this->view->render('dsp_calendar', $VIEW_DATA);
    }

    function update_calendar()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->update_calendar();
    }

    function date_which_diff_day($nDay)
    {
        echo $this->model->date_which_diff_day($nDay);
    }
}
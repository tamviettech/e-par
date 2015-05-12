<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
class event_Controller extends Controller {

    function __construct() {
        parent::__construct('schedule', 'event');
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->doc_direction = 'EVENT';
    }
    public function tv()
    {
        /*
        $v_today = Date('Y-m-d');

        $arr_all_am_event = $this->model->qry_all_date_event($v_today, 'am');
        $arr_all_pm_event = $this->model->qry_all_date_event($v_today, 'pm');

        $VIEW_DATA['today'] = $v_today;
        $VIEW_DATA['arr_all_am_event'] = $arr_all_am_event;
        $VIEW_DATA['arr_all_pm_event'] = $arr_all_pm_event;

        $this->view->render('dsp_today_event', $VIEW_DATA);
        */
        $this->view->render('dsp_today_event', array());

    }
    public function arp_today_event()
    {
        $v_today = Date('Y-m-d');

        $this->model->db->debug=0;
        $arr_all_am_event = $this->model->qry_all_date_event($v_today, 'am');
        $arr_all_pm_event = $this->model->qry_all_date_event($v_today, 'pm');

        $VIEW_DATA['today'] = $v_today;
        $VIEW_DATA['arr_all_am_event'] = $arr_all_am_event;
        $VIEW_DATA['arr_all_pm_event'] = $arr_all_pm_event;

        $this->view->render('arp_today_event', $VIEW_DATA);
        //$this->model->db->debug=DEBUG_MODE;
    }

    private function _check_login()
    {
        //Kiem tra session
        session::init();
        $login_name = session::get('login_name');
        if ($login_name == NULL)
        {
            session::destroy();
            header('location:' . SITE_ROOT . 'login.php');
            exit;
        }
    }

    public function main()
    {
        $this->_check_login();

        $this->view_week();
    }

    public function view_week()
    {
        $this->_check_login();

        $day = isset($_GET['day']) ? Model::replace_bad_char($_GET['day']) : jwDate::getDay();
        if (!( preg_match( '/^\d*$/', trim($day)) == 1 ))
        {
            $day = jwDate::getDay();
        }
        if ($day <=0 Or $day > 31 )
        {
            $day = jwDate::getDay();
        }

        $month = isset($_GET['month']) ? Model::replace_bad_char($_GET['month']) : jwDate::getMonth();
        if (!( preg_match( '/^\d*$/', trim($month)) == 1 ))
        {
            $month = jwDate::getMonth();
        }
        if ($month <=0 Or $month > 12 )
        {
            $month = jwDate::getMonth();
        }

        $year = isset($_GET['year']) ? Model::replace_bad_char($_GET['year']) : jwDate::getYear();
        if (!( preg_match( '/^\d*$/', trim($year)) == 1 ))
        {
            $year = jwDate::getYear();
        }
        if ($year < 1900 Or $year > 2100 )
        {
            $year = jwDate::getYear();
        }

        $v_begin_of_week = jwDate::beginOfWeek($day, $month, $year, '%Y-%m-%d');
        $v_end_of_week = jwDate::endOfWeek($day, $month, $year, '%Y-%m-%d');

        $VIEW_DATA['day'] = $day;
        $VIEW_DATA['month'] = $month;
        $VIEW_DATA['year'] = $year;
        $VIEW_DATA['arr_date_of_week'] = $this->model->qry_all_date_of_week($v_begin_of_week, $v_end_of_week);
		
        $this->view->render('dsp_view_week', $VIEW_DATA);
    }

    public function get_date_event_am($date_yyyymmdd)
    {
        $this->_check_login();

        $arr_all_event = $this->model->qry_all_date_event($date_yyyymmdd, 'am');

        echo json_encode($arr_all_event);
    }

    public function get_date_event_pm($date_yyyymmdd)
    {
        $this->_check_login();

        $arr_all_event = $this->model->qry_all_date_event($date_yyyymmdd, 'pm');
        echo json_encode($arr_all_event);
    }

    public function dsp_single_event($event_id)
    {
        $this->_check_login();

        if (!( preg_match( '/^\d*$/', trim($event_id)) == 1 ))
        {
            $event_id = 0;
        }

        $VIEW_DATA['arr_single_event'] = $this->model->qry_single_event($event_id);
        $VIEW_DATA['arr_all_event_owner'] = $this->model->qry_all_event_owner($event_id);
        $VIEW_DATA['arr_all_event_attender'] = $this->model->qry_all_event_attender($event_id);

        $VIEW_DATA['date'] = isset($_GET['date']) ? Model::replace_bad_char($_GET['date']) : '';
        $VIEW_DATA['noon'] = isset($_GET['noon']) ? Model::replace_bad_char($_GET['noon']) : '';
        $VIEW_DATA['event_id'] = $event_id;

        $this->view->render('dsp_single_event', $VIEW_DATA);
    }

    public function update_event()
    {
        $this->_check_login();

        $this->model->update_event();

        $v_begin_date = isset($_POST['txt_begin_date']) ? Model::replace_bad_char($_POST['txt_begin_date']) : date('d-m-Y');
        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
        $v_begin_hour  = isset($_POST['sel_begin_hour']) ? Model::replace_bad_char($_POST['sel_begin_hour']) : '0';
    }

    public function delete_event()
    {
        $this->_check_login();

        $this->model->delete_event();
    }

}
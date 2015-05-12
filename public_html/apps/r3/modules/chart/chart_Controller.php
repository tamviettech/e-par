<?php

class chart_Controller extends Controller
{

    /**
     *
     * @var \report_Model
     */
    public $model;

    /**
     *
     * @var View 
     */
    public $view;
    protected $_arr_user_role   = array();
    

    public function __construct()
    {
        parent::__construct('r3', 'chart');
        if (DEBUG_MODE < 10)
            $this->model->db->debug = 0;
    }

    public function main()
    {
        $this->dsp_pie_chart_progress();
    }
    
    public function dsp_pie_chart_progress()
    {
       //lay min max year
       $VIEW_DATA['arr_year'] = $this->model->get_year();
       
       $VIEW_DATA['arr_record_progress'] = $this->model->qry_record_progress();
       $this->view->render('dsp_pie_chart_progress',$VIEW_DATA);
    }
    
    /**
     * arp lay du lieu ho so dang xu ly
     */
    public function arp_get_record_progress($year = '')
    {
        $year = replace_bad_char($year);
        if(DEBUG_MODE > 10)
        {
            $this->model->db->debug = 1; 
        }
        $arr_record_progress = $this->model->qry_record_progress($year);
        echo json_encode($arr_record_progress);
    }
    
    public function arp_get_record_receive_respond($year = '')
    {
        $year = replace_bad_char($year);
        if(DEBUG_MODE > 10)
        {
            $this->model->db->debug = 1; 
        }
        $arr_record_receive_respond = $this->model->qry_record_receive_respond($year);
        echo json_encode($arr_record_receive_respond);
    }
}
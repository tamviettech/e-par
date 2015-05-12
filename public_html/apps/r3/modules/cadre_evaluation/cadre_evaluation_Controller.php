<?php
    if (!defined('SERVER_ROOT'))
        exit('No direct script access allowed');
    
    class cadre_evaluation_Controller extends Controller 
    {
        
        function __construct()
        {
            parent::__construct('r3', 'cadre_evaluation');
            $this->view->active_menu = 'cadre_evaluation';
        }
        
        function main()
        {
            $DATA_VIEW = array();
            $DATA_VIEW['arr_all_staff'] = $this->model->qry_all_staff();
            $this->view->render('dsp_all_staff',$DATA_VIEW);
        }
        public function dsp_check_vote()
        {
            $v_staff_id = isset($_REQUEST['id']) ? replace_bad_char($_REQUEST['id']) : '';
            $VIEW_DATA['CRITERIAL']               = $this->model->qry_all_criterial($v_staff_id);
            $VIEW_DATA['arr_single_staff']        = $this->model->qry_staff_get_id($v_staff_id);
            $this->view->render('dsp_main', $VIEW_DATA);
        }
        public function update_vote()
        {
            $this->model->update_vote();
        }
        
        public function dsp_all_report()
        {
            (Session::get('is_admin') == 1 OR  check_permission('TRA_CUU_DANH_GIA_CAN_BO','R3')) Or die($this->access_denied());
            $VIEW_DATA['arr_all_staff'] = $this->model->qry_all_staff();
            $VIEW_DATA['v_filter'] = get_post_var('txt_filter');
            $this->view->render('dsp_all_report', $VIEW_DATA);
        }
        
        public function dsp_single_report($user_id)
        {
            (Session::get('is_admin') == 1 OR check_permission('TRA_CUU_DANH_GIA_CAN_BO','R3')) Or die($this->access_denied());
            $VIEW_DATA['arr_result'] = $this->model->qry_single_result($user_id);
            $VIEW_DATA['arr_year'] = $this->model->qry_year();
            $VIEW_DATA['user_id'] = $user_id;
            $VIEW_DATA['c_day'] = get_post_var('day', '');
            $VIEW_DATA['c_month'] = get_post_var('month', '');
            $VIEW_DATA['c_year'] = get_post_var('year', '');
            $this->view->render('dsp_single_report', $VIEW_DATA);
        }
        /**
         * Lay danh sach ket qua danh gia can bo
         */
        public function dsp_cadre_report()
        {
            $v_begin_date = get_request_var('txt_begin_date','');
            $v_end_date   = get_request_var('txt_end_date','');
            $VIEW_DATA['arr_all_report']    = $this->model->qry_all_report($v_begin_date,$v_end_date);
            $VIEW_DATA['arr_all_criteria']  = $this->model->get_all_criteria();
            $VIEW_DATA['arr_all_staff']     = $this->model->qry_all_staff();
            $this->view->render('dsp_cadre_report',$VIEW_DATA);
        }
        
        public function dsp_print_report()
        {
            $v_begin_date = get_request_var('txt_begin_date','');
            $v_end_date   = get_request_var('txt_end_date','');
            $VIEW_DATA['arr_all_report']    = $this->model->qry_all_report($v_begin_date,$v_end_date);
            $VIEW_DATA['arr_all_criteria']  = $this->model->get_all_criteria();
            $VIEW_DATA['arr_all_staff']     = $this->model->qry_all_staff();
            $this->view->render('dsp_print_report',$VIEW_DATA);
        }
    }
    
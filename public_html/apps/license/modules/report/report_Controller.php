<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class report_Controller extends Controller
{

    function __construct()
    {
        deny_bad_http_referer();
        parent::__construct('license', 'report');
        $this->view->template->show_left_side_bar = FALSE;
        session::check_login();
    }

    function main()
    {
        $this->dsp_report_option();
    }

    function dsp_report_option()
    {
        $VIEW_DATA['arr_all_license_type'] = $this->model->qry_all_license_type();
        $VIEW_DATA['arr_report_type'] = array(
            1 => 'Báo cáo chi tiết các giấy phép đã cấp',
            2 => 'Báo cáo số lượng từng loại giấy phép cấp từng tháng',
            3 => 'Báo cáo số lượng từng loại giấy phép đã cấp trong năm'
        );
        $VIEW_DATA['arr_years'] = $this->model->qry_all_distinct_year();
        $this->view->render('dsp_report_option', $VIEW_DATA);
    }

    function print_report($options)
    {
        list($VIEW_DATA['report_type'], $VIEW_DATA['license_type_code'], $VIEW_DATA['year'], $VIEW_DATA['ext']) = explode('-', $options);
        $func = "print_report_" . $VIEW_DATA['report_type'];
        if (method_exists($this, $func))
        {
            $this->$func($VIEW_DATA['license_type_code'], $VIEW_DATA['year'], $VIEW_DATA['ext']);
        }
    }

    function print_report_1($license_type_code, $year, $ext)
    {
        $VIEW_DATA['license_type_code'] = $license_type_code;
        $VIEW_DATA['year'] = $year;
        $VIEW_DATA['ext'] = $ext;

        if ($VIEW_DATA['license_type_code'])
        {
            $VIEW_DATA['license_type_name'] = $this->model->qry_license_type_name_by_code($VIEW_DATA['license_type_code']);
        }
        $VIEW_DATA['arr_all_record'] = $this->model->qry_record($VIEW_DATA['license_type_code'], $VIEW_DATA['year']);
        $this->view->render('report_1/report_1.' . $VIEW_DATA['ext'], $VIEW_DATA);
    }

    function print_report_2($license_type_code, $year, $ext)
    {
        $VIEW_DATA['license_type_code'] = $license_type_code;
        $VIEW_DATA['year'] = $year;
        $VIEW_DATA['ext'] = $ext;
        
        $VIEW_DATA['arr_all_month'] = $this->model->qry_number_of_each_record_each_month_by_year($year);
        $this->view->render('report_2/report_2.' . $VIEW_DATA['ext'], $VIEW_DATA);
    }
    
    function print_report_3($license_type_code, $year, $ext){
        $VIEW_DATA['license_type_code'] = $license_type_code;
        $VIEW_DATA['year'] = $year;
        $VIEW_DATA['ext'] = $ext;
        
        $VIEW_DATA['arr_all_month'] = $this->model->qry_number_of_record_each_month_by_year($year);
        $this->view->render('report_3/report_3.' . $VIEW_DATA['ext'], $VIEW_DATA);
    }
}

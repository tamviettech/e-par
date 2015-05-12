<?php

class report_Controller extends Controller
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
    public $arr_all_report_type;
    protected $_arr_user_role   = array();
    

    public function __construct()
    {
        //in pdf khá lâu
        set_time_limit(60 * 5);
        parent::__construct('r3', 'report');
        $this->view->show_left_side_bar = $this->view->template->show_left_side_bar = TRUE;
        
        //Kiem tra dang nhap
        session::check_login();
        if (DEBUG_MODE < 10)
            $this->model->db->debug = 0;
        
        $this->view->template->active_role  ='';
        //Danh muc bao cao
        $this->arr_all_report_type            = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_BAO_CAO, CONST_USE_ADODB_CACHE_FOR_REPORT);
        
        $this->view->template->reportbook_url = $this->view->get_controller_url('reportbook', 'r3');
        
        //du lieu default
        $this->arr_year = $this->model->get_year();
        $this->arr_all_group = $this->model->qry_all_group();
    }

    function __destruct()
    {
        $this->model->db->debug = DEBUG_MODE;
    }

    public function main()
    {
        $arr_keys = array_keys($this->arr_all_report_type);
        $v_first_report = array_shift($arr_keys);
        $this->option($v_first_report);
    }

    public function type($type = '3')
    {
        $f = '_report' . $type;

        $this->view->template->current_report_type = $type;
        $this->$f();
    }

    public function option($type)
    {
        if ($type != 0)
        {
            $f = '_report' . $type;
            $this->$f();
        }
        else
        {
            $VIEW_DATA['arr_all_group']          = $this->arr_all_group;
            $VIEW_DATA['arr_all_report_type']    = $this->arr_all_report_type;
            $VIEW_DATA['report_type']            = strval($type);
            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
     * bao cao tong hop tinh hinh giai quyet ho so
     */
    private function _report1()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[1];
        if (get_request_var('print') == 1)
        {
            //Chu ky bao cao
            $v_report_type = get_request_var('type','spec');
            $report_group_name = '';
            $begin_date = get_request_var('begin_date','');
            $end_date   = get_request_var('end_date','');
            
            if($v_report_type == 'spec')
            {
                $report_group = '0';
            }
            else
            {
                $report_group      = 'C_SPEC_CODE';
                $report_group_name = 'C_SPEC_NAME';
            }
            
            $v_report_time = get_request_var('time','month');
            $v_unit        = get_request_var('unit','');
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]} <br>";
            if($v_report_time == 'month')
            {
                $report_subtitle .= "Tháng ".DATE('m')." Năm ".DATE('Y');
            }
            else if($v_report_time == 'quarter')
            {
                $v_quarter_roman = $this->integerToRoman(jwdate::quarterOfYear());
                $report_subtitle .= "Quý $v_quarter_roman Năm ".DATE('Y');
            }
            else if($v_report_time == 'year')
            {
                $report_subtitle .= "Năm " . DATE('Y');
            }
            else if($v_report_time == 'time')
            {
                if($begin_date != '')
                {
                    $report_subtitle .= "Từ ngày $begin_date";
                }
                
                if($end_date != '')
                {
                    $report_subtitle .= " Đến ngày $end_date";
                }
            }
                
            $VIEW_DATA['arr_all_report_data']   = $this->model->qry_all_report_data_1($v_unit, $report_group,$v_report_time,$begin_date,$end_date);
            
            $VIEW_DATA['report_group']      = $v_report_type;
            $VIEW_DATA['report_group_name'] = $report_group_name;
            $VIEW_DATA['report_title']      = 'BÁO CÁO TỔNG HỢP TÌNH HÌNH GIẢI QUYẾT THỦ TỤC HÀNH CHÍNH';
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            $VIEW_DATA['report_code']       = 'report_1';
            
            //tao fuction tinh toan ty le
            function formula_percent($arr_param)
            {
                $v_tra_som_han  = $arr_param[0];
                $v_tra_dung_han = $arr_param[1];
                $v_tra_qua_han  = $arr_param[2];
                //tong so
                $v_tong = $v_tra_som_han+$v_tra_dung_han+$v_tra_qua_han;
                if($v_tong <= 0)
                {
                    return ' - ';
                }
                else
                {
                    $v_percent = (float)(($v_tra_som_han + $v_tra_dung_han)*100)/$v_tong;
                    $v_percent = number_format($v_percent, 2);
                    return strval($v_percent) . '%';
                }
            }
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['report_type']         = 1;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
     * bao cao tong hop tinh hinh tiep nhan
     */
    private function _report2()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[2];
        if (get_request_var('print') == 1)
        {
            
            $v_unit       = get_request_var('unit','');
            $v_spec       = get_request_var('spec','');
            
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]}<br>";
            if($v_begin_date != '')
            {
                $report_subtitle .= "Từ ngày: $v_begin_date";
            }
            if($v_end_date != '')
            {
                $report_subtitle .= " đến ngày $v_end_date";
            }
            
            $v_begin_date = jwdate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date   = jwdate::ddmmyyyy_to_yyyymmdd($v_end_date);
            

            
            $VIEW_DATA['arr_all_report_data']   = $this->model->qry_all_report_data_2($v_unit, $v_spec,$v_begin_date,$v_end_date);
            
            $VIEW_DATA['report_group']      = 'C_SPEC_CODE';
            $VIEW_DATA['report_group_name'] = 'C_SPEC_NAME';
            
            $VIEW_DATA['report_title']      = 'BÁO CÁO TỔNG HỢP TIẾP NHẬN HỒ SƠ';
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            $VIEW_DATA['report_code']       = 'report_2';
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['report_type']         = 2;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    
    /**
    * Bao cao tong hop thu ly ho so
    */
    private function _report3()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[3];
        if (get_request_var('print') == 1)
        {
            
            $v_unit = get_request_var('unit','');
            $v_ou   = get_request_var('ou','');
            
            $v_period     = get_request_var('period','day');
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            $v_month      = get_request_var('month','');
            $v_year       = get_request_var('year','');
            $v_quarter    = get_request_var('quarter','');
            
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]}<br>";
            if($v_period == 'day')
            {
                if($v_begin_date != '')
                {
                    $report_subtitle .= "Từ ngày: $v_begin_date";
                }
                if($v_end_date != '')
                {
                    $report_subtitle .= " đến ngày $v_end_date";
                }
                 
            }
            else if($v_period == 'month')
            {
                $report_subtitle .= "Tháng $v_month Năm $v_year";
            }
            else if($v_period == 'quarter')
            {
                if($v_quarter != '')
                {
                    $v_quarter_roman = $this->integerToRoman($v_quarter);
                    $report_subtitle .= "Quý $v_quarter_roman Năm $v_year";
                }
            }
            else if($v_period == 'year')
            {
                $report_subtitle .= "Năm $v_year";
            }
            
            $v_begin_date = jwdate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date   = jwdate::ddmmyyyy_to_yyyymmdd($v_end_date);
            //tinh tong tung row
            function formula_total($arr_param)
            {
                return $arr_param[0] + $arr_param[1] + $arr_param[2];
            }
            //tinh ty le
            function formula_percent($arr_param)
            {
                $v_cur = $arr_param[0];
                
                $v_vuot_tien_do = $arr_param[1];
                $v_dung_tien_do = $arr_param[2];
                $v_cham_tien_do = $arr_param[3];
                //tong so
                $v_tong = $v_vuot_tien_do+$v_dung_tien_do+$v_cham_tien_do;
                if($v_tong <= 0)
                {
                    return ' - ';
                }
                else
                {
                    $v_percent = (float)($v_cur*100)/$v_tong;
                    $v_percent = number_format($v_percent, 2);
                    return strval($v_percent) . '%';
                }
            }
            
            $VIEW_DATA['arr_all_report_data']   = $this->model->qry_all_report_data_3($v_unit,$v_ou,$v_begin_date,$v_end_date,$v_month,$v_year,$v_quarter);
            
            $VIEW_DATA['report_group']      = 'FK_VILLAGE_ID';
            $VIEW_DATA['report_group_name'] = 'C_OU_NAME';
            
            $VIEW_DATA['report_title']      = 'BÁO CÁO TỔNG HỢP THỤ LÝ HỒ SƠ TẠI CÁC ĐƠN VỊ - PHÒNG BAN';
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            $VIEW_DATA['report_code']       = 'report_3';
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['arr_year']            = $this->arr_year;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['arr_all_group']       = $this->model->qry_all_group();
            
            $VIEW_DATA['report_type']         = 3;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
     * bao cao tong hop tra ho so
     */
    private function _report4()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[4];
        if (get_request_var('print') == 1)
        {
            
            $v_unit       = get_request_var('unit','');
            $v_spec       = get_request_var('spec','');
            
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]}<br>";
            if($v_begin_date != '')
            {
                $report_subtitle .= "Từ ngày: $v_begin_date";
            }
            if($v_end_date != '')
            {
                $report_subtitle .= " đến ngày $v_end_date";
            }
            
            $v_begin_date = jwdate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date   = jwdate::ddmmyyyy_to_yyyymmdd($v_end_date);
            

            
            $VIEW_DATA['arr_all_report_data']   = $this->model->qry_all_report_data_4($v_unit, $v_spec,$v_begin_date,$v_end_date);
            
            $VIEW_DATA['report_group']      = 'C_SPEC_CODE';
            $VIEW_DATA['report_group_name'] = 'C_SPEC_NAME';
            
            $VIEW_DATA['report_title']      = 'BÁO CÁO TỔNG HỢP TRẢ KẾT QUẢ';
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            $VIEW_DATA['report_code']       = 'report_4';
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['report_type']         = 4;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
     * bao cao chi tiet tiep nhan ho so
     */
    private function _report5()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[5];
        if (get_request_var('print') == 1)
        {
            
            $v_unit       = get_request_var('unit','');
            $v_spec       = get_request_var('spec','');
            
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]}<br>";
            if($v_begin_date != '')
            {
                $report_subtitle .= "Từ ngày: $v_begin_date";
            }
            if($v_end_date != '')
            {
                $report_subtitle .= " đến ngày $v_end_date";
            }
            
            $v_begin_date = jwdate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date   = jwdate::ddmmyyyy_to_yyyymmdd($v_end_date);
            

            
            $VIEW_DATA['arr_all_report_data']   = $this->model->qry_all_report_data_5($v_unit, $v_spec,$v_begin_date,$v_end_date);
            
            $VIEW_DATA['report_group']      = 'C_SPEC_CODE,FK_RECORD_TYPE';
            $VIEW_DATA['report_group_name'] = 'C_SPEC_NAME,C_RECORD_TYPE_NAME';
            
            $VIEW_DATA['report_title']      = 'BÁO CÁO CHI TIẾT TIẾP NHẬN HỒ SƠ';
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            $VIEW_DATA['report_code']       = 'report_5';
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['report_type']         = 5;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
    * Bao cao tong hop thu ly ho so
    */
    private function _report6()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[6];
        if (get_request_var('print') == 1)
        {
            $v_unit = get_request_var('unit','');
            $v_ou   = get_request_var('ou','');
            
            $v_period     = get_request_var('period','day');
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            $v_month      = get_request_var('month','');
            $v_year       = get_request_var('year','');
            $v_quarter    = get_request_var('quarter','');
            
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]}<br>";
            if($v_period == 'day')
            {
                if($v_begin_date != '')
                {
                    $report_subtitle .= "Từ ngày: $v_begin_date";
                }
                if($v_end_date != '')
                {
                    $report_subtitle .= " đến ngày $v_end_date";
                }
                 
            }
            else if($v_period == 'month')
            {
                $report_subtitle .= "Tháng $v_month Năm $v_year";
            }
            else if($v_period == 'quarter')
            {
                if($v_quarter != '')
                {
                    $v_quarter_roman = $this->integerToRoman($v_quarter);
                    $report_subtitle .= "Quý $v_quarter_roman Năm $v_year";
                }
            }
            else if($v_period == 'year')
            {
                $report_subtitle .= "Năm $v_year";
            }
            //tao function step_name
            function step_name($arr_param)
            {
                $task_code = $arr_param[0];
                $record_type = $arr_param[1];
                $v_workflow_file_path = SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS . $record_type . DS . $record_type . '_workflow' . '.xml';
                $dom_flow = @simplexml_load_file($v_workflow_file_path);
                if(!empty($dom_flow))
                {
                    return get_xml_value($dom_flow, "//step/task[@code='$task_code']/../@name");
                }
                return '';
            }
            
            $v_begin_date = jwdate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date   = jwdate::ddmmyyyy_to_yyyymmdd($v_end_date);
            
            $VIEW_DATA['arr_all_report_data']   = $this->model->qry_all_report_data_6($v_unit,$v_ou,$v_begin_date,$v_end_date,$v_month,$v_year,$v_quarter);
            
            $VIEW_DATA['report_group']      = 'C_CUR_VILLAGE_ID,C_GROUP_CODE';
            $VIEW_DATA['report_group_name'] = 'C_OU_NAME,C_GROUP_NAME';
            
            //tao report title
            $VIEW_DATA['report_title']      = 'BÁO CÁO CHI TIẾT THỤ LÝ HỒ SƠ TẠI ĐƠN VỊ - PHÒNG BAN';
            
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            
            $VIEW_DATA['report_code']       = 'report_6';
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['arr_year']            = $this->arr_year;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['arr_all_group']       = $this->model->qry_all_group();
            
            $VIEW_DATA['report_type']         = 6;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
     * bao cao chi tiet tra ket qua ho so
     */
    private function _report7()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[7];
        if (get_request_var('print') == 1)
        {
            
            $v_unit       = get_request_var('unit','');
            $v_spec       = get_request_var('spec','');
            
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]}<br>";
            if($v_begin_date != '')
            {
                $report_subtitle .= "Từ ngày: $v_begin_date";
            }
            if($v_end_date != '')
            {
                $report_subtitle .= " đến ngày $v_end_date";
            }
                 
            
            $v_begin_date = jwdate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date   = jwdate::ddmmyyyy_to_yyyymmdd($v_end_date);
            

            
            $VIEW_DATA['arr_all_report_data']   = $this->model->qry_all_report_data_7($v_unit, $v_spec,$v_begin_date,$v_end_date);
            
            $VIEW_DATA['report_group']      = 'C_SPEC_CODE,FK_RECORD_TYPE';
            $VIEW_DATA['report_group_name'] = 'C_SPEC_NAME,C_RECORD_TYPE_NAME';
            
            $VIEW_DATA['report_title']      = 'BÁO CÁO CHI TIẾT TIẾP NHẬN HỒ SƠ';
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            $VIEW_DATA['report_code']       = 'report_7';
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['report_type']         = 7;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
    * BÁO CÁO CHI TIẾT HỒ SƠ XỬ LÝ CHẬM TIẾN ĐỘ
    */
    private function _report8()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[8];
        if (get_request_var('print') == 1)
        {
            $v_unit = get_request_var('unit','');
            $v_ou   = get_request_var('ou','');
            
            $v_period     = get_request_var('period','day');
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            $v_month      = get_request_var('month','');
            $v_year       = get_request_var('year','');
            $v_quarter    = get_request_var('quarter','');
            
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]}<br>";
            if($v_period == 'day')
            {
                if($v_begin_date != '')
                {
                    $report_subtitle .= "Từ ngày: $v_begin_date";
                }
                if($v_end_date != '')
                {
                    $report_subtitle .= " đến ngày $v_end_date";
                }
                 
            }
            else if($v_period == 'month')
            {
                $report_subtitle .= "Tháng $v_month Năm $v_year";
            }
            else if($v_period == 'quarter')
            {
                if($v_quarter != '')
                {
                    $v_quarter_roman = $this->integerToRoman($v_quarter);
                    $report_subtitle .= "Quý $v_quarter_roman Năm $v_year";
                }
            }
            else if($v_period == 'year')
            {
                $report_subtitle .= "Năm $v_year";
            }
            
            $v_begin_date = jwdate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date   = jwdate::ddmmyyyy_to_yyyymmdd($v_end_date);
            
            $VIEW_DATA['arr_all_report_data']   = $this->model->qry_all_report_data_8($v_unit,$v_ou,$v_begin_date,$v_end_date,$v_month,$v_year,$v_quarter);
            
            $VIEW_DATA['report_group']      = 'C_CUR_VILLAGE_ID,C_GROUP_CODE';
            $VIEW_DATA['report_group_name'] = 'C_OU_NAME,C_GROUP_NAME';
            
            //tao report title
            $VIEW_DATA['report_title']      = 'BÁO CÁO CHI TIẾT HỒ SƠ XỬ LÝ CHẬM TIẾN ĐỘ';
            
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            
            $VIEW_DATA['report_code']       = 'report_8';
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['arr_year']            = $this->arr_year;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['arr_all_group']       = $this->model->qry_all_group();
            
            $VIEW_DATA['report_type']         = 8;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
     * bao cao chi tiet tra ket qua ho so
     */
    private function _report9()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[9];
        if (get_request_var('print') == 1)
        {
            
            $v_unit       = get_request_var('unit','');
            $v_spec       = get_request_var('spec','');
            
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]}<br>";
            if($v_begin_date != '')
            {
                $report_subtitle .= "Từ ngày: $v_begin_date";
            }
            if($v_end_date != '')
            {
                $report_subtitle .= " đến ngày $v_end_date";
            }
                 
            
            $v_begin_date = jwdate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date   = jwdate::ddmmyyyy_to_yyyymmdd($v_end_date);
            

            
            $VIEW_DATA['arr_all_report_data']   = $this->model->qry_all_report_data_9($v_unit, $v_spec,$v_begin_date,$v_end_date);
            
            $VIEW_DATA['report_group']      = 'C_SPEC_CODE,FK_RECORD_TYPE';
            $VIEW_DATA['report_group_name'] = 'C_SPEC_NAME,C_RECORD_TYPE_NAME';
            
            $VIEW_DATA['report_title']      = 'BÁO CÁO CHI TIẾT HỒ SƠ QUÁ HẠN TRẢ KẾT QUẢ';
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            $VIEW_DATA['report_code']       = 'report_9';
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['report_type']         = 9;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
     * bao cao tong hop phi, le phi
     */
    private function _report10()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[10];
        if (get_request_var('print') == 1)
        {
            
            $v_unit       = get_request_var('unit','');
            $v_spec       = get_request_var('spec','');
            
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]}<br>";
            if($v_begin_date != '')
            {
                $report_subtitle .= "Từ ngày: $v_begin_date";
            }
            if($v_end_date != '')
            {
                $report_subtitle .= " đến ngày $v_end_date";
            }
            
            $v_begin_date = jwdate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date   = jwdate::ddmmyyyy_to_yyyymmdd($v_end_date);
            
            $VIEW_DATA['arr_all_report_data'] = $this->model->qry_all_report_data_10($v_unit, $v_spec,$v_begin_date, $v_end_date);
            
//            $VIEW_DATA['report_group']      = 'C_SPEC_CODE,FK_RECORD_TYPE';
//            $VIEW_DATA['report_group_name'] = 'C_SPEC_NAME,C_RECORD_TYPE_NAME';
            
            $VIEW_DATA['report_title']      = 'BÁO CÁO TỔNG HỢP PHÍ, LỆ PHÍ';
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            $VIEW_DATA['report_code']       = 'report_10';
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['report_type']         = 10;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
     * bao cao tong hop phi, le phi theo TTHC
     */
    private function _report11()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[11];
        if (get_request_var('print') == 1)
        {
            
            $v_unit        = get_request_var('unit','');
            $v_spec        = get_request_var('spec','');
            $v_record_type = get_request_var('record_type','');
            
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]}<br>";
            if($v_begin_date != '')
            {
                $report_subtitle .= "Từ ngày: $v_begin_date";
            }
            if($v_end_date != '')
            {
                $report_subtitle .= " đến ngày $v_end_date";
            }
            
            $v_begin_date = jwdate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date   = jwdate::ddmmyyyy_to_yyyymmdd($v_end_date);
            
            $VIEW_DATA['arr_all_report_data'] = $this->model->qry_all_report_data_11($v_record_type,$v_unit, $v_spec,$v_begin_date, $v_end_date);
            $VIEW_DATA['report_group']      = 'C_SPEC_CODE,C_RECORD_TYPE_CODE';
            $VIEW_DATA['report_group_name'] = 'C_SPEC_NAME,C_RECORD_TYPE_NAME';
            
            $VIEW_DATA['report_title']      = 'BÁO CÁO TỔNG HỢP PHÍ, LỆ PHÍ THEO TTHC';
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            $VIEW_DATA['report_code']       = 'report_11';
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            
            $VIEW_DATA['arr_all_record_type_with_spec_code'] = $this->model->qry_all_record_type_with_spec_code();
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['report_type']         = 11;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
     * bao cao tong hop phi, le phi theo TTHC
     */
    private function _report12()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[12];
        if (get_request_var('print') == 1)
        {
            
            $v_unit        = get_request_var('unit','');
            $v_spec        = get_request_var('spec','');
            $v_record_type = get_request_var('record_type','');
            
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            //tao report sub title
            $arr_all_unit = $this->model->qry_all_unit();
            $report_subtitle = "Đơn vị: {$arr_all_unit[$v_unit]}<br>";
            if($v_begin_date != '')
            {
                $report_subtitle .= "Từ ngày: $v_begin_date";
            }
            if($v_end_date != '')
            {
                $report_subtitle .= " đến ngày $v_end_date";
            }
            
            $v_begin_date = jwdate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date   = jwdate::ddmmyyyy_to_yyyymmdd($v_end_date);
            
            $VIEW_DATA['arr_all_report_data'] = $this->model->qry_all_report_data_12($v_record_type,$v_unit, $v_spec,$v_begin_date, $v_end_date);
            
            $VIEW_DATA['report_group']      = 'C_SPEC_CODE,PK_RECORD_TYPE';
            $VIEW_DATA['report_group_name'] = 'C_SPEC_NAME,C_RECORD_TYPE_NAME';
            
            $VIEW_DATA['report_title']      = 'BÁO CÁO TỔNG HỢP PHÍ, LỆ PHÍ THEO TTHC';
            $VIEW_DATA['report_subtitle']   = $report_subtitle;
            $VIEW_DATA['report_code']       = 'report_12';
            
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            
            $VIEW_DATA['arr_all_record_type_with_spec_code'] = $this->model->qry_all_record_type_with_spec_code();
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['arr_all_unit']        = $this->model->qry_all_unit();
            $VIEW_DATA['report_type']         = 12;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
     * 
     */
    private function _report20()  
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[20];
        if (get_request_var('pdf') == 1)
        {
            $now = date_create($this->model->getDate());
            $v_begin_date = get_request_var('begin_date','');
            $v_end_date   = get_request_var('end_date','');
            $v_group_code = get_request_var('group','');
            require_once SERVER_ROOT . 'libs/tcpdf/zreport.php';
            $VIEW_DATA['arr_all_report_data']   = $this->model->qry_all_report_data_20($v_begin_date,$v_end_date,$v_group_code);
            $VIEW_DATA['report_code']           = 'report_20';
            $VIEW_DATA['now']                   = $now;
            
            $this->view->render('dsp_pdf_report_20', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_group']         = $this->arr_all_group;
            $VIEW_DATA['arr_year']              = $this->arr_year;
            $VIEW_DATA['arr_all_report_type']   = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_business_unit'] = $this->model->qry_all_business_unit();
            $VIEW_DATA['report_type']           = 20;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
     
    /**
     * Quản trị hệ thống kết xuất Báo cáo kết quả tiếp nhận hồ sơ theo ngày/tuần/tháng/năm
     * @param type $type
     */
//    private function _report6()
//    {
//        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[6];
//        if (get_request_var('pdf') == 1)
//        {
//            //Chu ky bao cao
//            $v_period   = get_request_var('period');
//            $group_code = get_request_var('group',0);
//
//            //Lay danh sach Linh vuc
//            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
//            $model_data   = $this->model->qry_all_report_data_6($v_period, $arr_all_spec,$group_code);
//
//            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
//            $VIEW_DATA['report_priord']       = $v_period;
//            $VIEW_DATA['arr_all_report_data'] = $model_data['arr_all_report_data'];
//            $VIEW_DATA['report_title']        = 'BÁO CÁO KẾT QUẢ TIẾP NHẬN HỒ SƠ';
//            $VIEW_DATA['report_subtitle']     = $model_data['report_subtitle'];
//            $VIEW_DATA['report_code']         = 'report_6';
//
//            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
//        }
//        else
//        {
//            $VIEW_DATA['arr_all_group']          = $this->arr_all_group;
//            $VIEW_DATA['arr_year']               = $this->arr_year;
//            $VIEW_DATA['arr_all_report_type']   = $this->arr_all_report_type;
//            $VIEW_DATA['report_type']           = 6;
//
//            $this->view->render('dsp_report_option', $VIEW_DATA);
//        }
//    }

    /**
     * 3. Lãnh đạo kết xuất Bảng tổng hợp báo cáo kết quả xử lý hồ sơ
     */
    private function _gcount($array, $k)
    {
        for ($j = 0; $j < sizeof($array); $j++)
        {
            if ($array[$j]['C_GROUP_CODE'] == $k)
            {
                return $array[$j]['C_COUNT'];
            }
        }
        return 0;
    }

    private function _report13()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[13];
        if (get_request_var('pdf') == 1)
        {
            //Ngày báo cáo
            $v_date              = Date('Y-m-d');
            $arr_all_group       = $this->model->qry_all_group(CONST_USE_ADODB_CACHE_FOR_REPORT);
            $arr_all_report_data = $this->model->qry_all_report_data_13($v_date);

            $VIEW_DATA['arr_all_report_data'] = $arr_all_report_data;
            $VIEW_DATA['report_title']        = 'BÁO CÁO VỀ THỦ TỤC HÀNH CHÍNH QUÁ HẠN';
            $VIEW_DATA['report_subtitle']     = 'Ngày ' . Date('d-m-Y');
            $VIEW_DATA['report_code']         = 'report_13';
            $VIEW_DATA['arr_all_group']       = $arr_all_group;

            $this->view->render('dsp_pdf_report_13', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_group']       = $this->arr_all_group;
            $VIEW_DATA['arr_all_spec']        = $this->model->qry_all_spec();
            
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['report_type']         = 13;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

    private function _report14()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[14];
        if (get_request_var('pdf') == 1)
        {
            //Ngày báo cáo
            $v_date              = Date('Y-m-d');
            $arr_all_group       = $this->model->qry_all_group(CONST_USE_ADODB_CACHE_FOR_REPORT);
            $arr_all_report_data = $this->model->qry_all_report_data_14($v_date);

            $VIEW_DATA['arr_all_report_data'] = $arr_all_report_data;
            $VIEW_DATA['report_title']        = "BÁO CÁO TÌNH HÌNH BỔ SUNG HỒ SƠ THỦ TỤC HÀNH CHÍNH";
            $VIEW_DATA['report_subtitle']     = 'Ngày ' . Date('d-m-Y');
            $VIEW_DATA['report_code']         = 'report_14';
            $VIEW_DATA['arr_all_group']       = $arr_all_group;

            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_group']          = $this->arr_all_group;
            $VIEW_DATA['arr_all_spec']        = $this->model->qry_all_spec();
            
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['report_type']         = 14;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

    private function _report15()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[15];   
        if (get_request_var('pdf') == 1)
        {
            $group_code = get_request_var('group','');
            $spec_code = get_request_var('spec','');
            
            //Kỳ báo cáo
//            $v_begin_date = get_request_var('begin_date', date('d-m-Y'));
//            $v_end_date   = get_request_var('end_date', date('d-m-Y'));
            
            $v_begin_date = get_request_var('begin_date', '');
            $v_end_date   = get_request_var('end_date', '');

            $v_begin_date_yyyymmdd = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date_yyyymmdd   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);

            $arr_all_group       = $this->model->qry_all_group(CONST_USE_ADODB_CACHE_FOR_REPORT);
            $arr_all_report_data = $this->model->qry_all_report_data_15($v_begin_date_yyyymmdd, $v_end_date_yyyymmdd,$group_code,$spec_code);
            
            $VIEW_DATA['report_subtitle']     = '';
            if($v_begin_date_yyyymmdd != '')
            {
                $VIEW_DATA['report_subtitle']     = 'Từ ngày ' . $v_begin_date;
            }
            if($v_end_date_yyyymmdd != '')
            {
                $VIEW_DATA['report_subtitle']     .= ' đến ngày ' . $v_end_date;
            }
            
            $VIEW_DATA['arr_all_report_data'] = $arr_all_report_data;
            $VIEW_DATA['report_title']        = "BÁO CÁO THỦ TỤC HÀNH CHÍNH BỊ TỪ CHỐI";
            $VIEW_DATA['report_code']         = 'report_15';
            $VIEW_DATA['arr_all_group']       = $arr_all_group;

            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_group']          = $this->arr_all_group;
            $VIEW_DATA['arr_all_spec']        = $this->model->qry_all_spec();
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['report_type']         = 15;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

    /**
     * BÁO CÁO Danh sách hồ sơ tiếp nhận và tiến độ giải quyết
     */
    private function _report16()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[16];
        if (get_request_var('pdf') == 1)
        {
            $v_spec_code      = get_request_var('spec_code', '');
            $v_record_type_id = get_request_var('record_type', 0);
            $group_code       = get_request_var('group', '');
            $group_level      = get_request_var('group_level', '');

            $v_begin_date = get_request_var('begin_date', '');
            $v_end_date   = get_request_var('end_date', '');

            $v_begin_date_yyyymmdd = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date_yyyymmdd   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);

            $arr_model_data = $this->model->qry_all_report_data_16($v_spec_code, $v_record_type_id, $v_begin_date_yyyymmdd, $v_end_date_yyyymmdd,$group_code,$group_level);

            $arr_report_filter   = $arr_model_data['report_filter'];
            $arr_all_report_data = $arr_model_data['report_data'];

            $VIEW_DATA['arr_report_filter']   = $arr_report_filter;
            $VIEW_DATA['arr_all_report_data'] = $arr_all_report_data;
            $VIEW_DATA['report_title']        = "BÁO CÁO";
            $VIEW_DATA['report_subtitle']     = 'Danh sách hồ sơ tiếp nhận và tiến độ giải quyết';
            $VIEW_DATA['report_code']         = 'report_16';

            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_group']                      = $this->arr_all_group;
            $VIEW_DATA['arr_all_record_type_with_spec_code'] = $this->model->qry_all_record_type_with_spec_code();
            $VIEW_DATA['arr_all_report_type']                = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']                       = $this->model->qry_all_spec();
            $VIEW_DATA['report_type']                        = 16;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

//    private function _report7()
//    {
//        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[7];
//        if (get_request_var('pdf') == 1)
//        {
//            $v_begin_date          = get_request_var('begin_date','');
//            $v_end_date            = get_request_var('end_date','');
//            $v_spec                = get_request_var('spec');
//            $v_begin_date_yyyymmdd = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
//            $v_end_date_yyyymmdd   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
//
//            //Lay danh sach Linh vuc
//            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
//
//            //Du lieu bao cao
//            $arr_model_data = $this->model->qry_all_report_data_7($v_begin_date_yyyymmdd, $v_end_date_yyyymmdd);
//
//            //Report data
//            $arr_all_report_data = Array();
//            foreach ($arr_all_spec as $v_spec_code => $v_spec_name)
//            {
//                if ($v_spec && $v_spec != $v_spec_code)
//                {
//                    continue;
//                }
//                $v_sum   = isset($arr_model_data[$v_spec_code]['C_TOTAL_RECORD']) ? $arr_model_data[$v_spec_code]['C_TOTAL_RECORD'] : 0;
//                $v_fee   = isset($arr_model_data[$v_spec_code]['C_TOTAL_FEE']) ? $arr_model_data[$v_spec_code]['C_TOTAL_FEE'] : 0;
//                $v_cost  = isset($arr_model_data[$v_spec_code]['C_TOTAL_COST']) ? $arr_model_data[$v_spec_code]['C_TOTAL_COST'] : 0;
//                $v_total = isset($arr_model_data[$v_spec_code]['C_SUM']) ? $arr_model_data[$v_spec_code]['C_SUM'] : 0;
//
//                $row                   = array();
//                $row['RN']             = 1;
//                $row['C_SPEC_NAME']    = $v_spec_name;
//                $row['C_TOTAL_RECORD'] = $v_sum;
//                $row['C_TOTAL_FEE']    = $v_fee;
//                $row['C_TOTAL_COST']   = $v_cost;
//                $row['C_SUM']          = $v_total;
//
//                array_push($arr_all_report_data, $row);
//            }
//
//            $VIEW_DATA['arr_all_report_data'] = $arr_all_report_data;
//            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
//            $VIEW_DATA['report_title']        = "BÁO CÁO VỀ PHÍ, LỆ PHÍ ĐÃ THU";
//            
//            $VIEW_DATA['report_subtitle'] = '';
//            if($v_begin_date != '')
//            {
//                $VIEW_DATA['report_subtitle'] .= 'Từ ngày ' . $v_begin_date;
//            }
//            if($v_end_date != '')
//            {
//                $VIEW_DATA['report_subtitle'] .= ' đến ngày ' . $v_end_date;
//            }
//            
//            $VIEW_DATA['report_code']         = 'report_7';
//            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
//        }
//        else
//        {
//            $VIEW_DATA['arr_all_spec']        = $this->model->qry_all_spec();
//            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
//            $VIEW_DATA['report_type']         = 7;
//
//            $this->view->render('dsp_report_option', $VIEW_DATA);
//        }
//    }

    private function _report7b()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type['7b'];
        if (get_request_var('pdf') == 1)
        {
            $v_begin_date          = get_request_var('begin_date', Date('d-m-Y'));
            $v_end_date            = get_request_var('end_date', Date('d-m-Y'));
            $v_spec                = get_request_var('spec', '');
            $v_record_type         = get_request_var('record_type', '');
            $v_begin_date_yyyymmdd = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date_yyyymmdd   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);

            $arr_all_report_data = $this->model->qry_all_report_data_7b($v_record_type,$v_spec, $v_begin_date_yyyymmdd, $v_end_date_yyyymmdd);

            $VIEW_DATA['arr_all_report_data'] = $arr_all_report_data;
            $VIEW_DATA['report_title']        = "BÁO CÁO CHI TIẾT DANH SÁCH THU PHÍ, LỆ PHÍ";
            $VIEW_DATA['report_subtitle']     = 'Từ ngày ' . $v_begin_date . ' đến ngày ' . $v_end_date;
            $VIEW_DATA['report_code']         = 'report_7b';

            $this->view->render('dsp_common_pdf_report_with_subtotal', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_record_type_with_spec_code'] = $this->model->qry_all_record_type_with_spec_code();
            $VIEW_DATA['arr_all_spec']        = $this->model->qry_all_spec();
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['report_type']         = '7b';

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

    private function _report7c()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type['7c'];
        if (get_request_var('pdf') == 1)
        {
            //Bao cao chi tiet phi, le phi theo tung thu tuc (Loai hs)
            $v_begin_date = get_request_var('begin_date', Date('d-m-Y'));
            $v_end_date   = get_request_var('end_date', Date('d-m-Y'));

            $v_begin_date_yyyymmdd = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date_yyyymmdd   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
        }
        else
        {
            $VIEW_DATA['arr_all_record_type_with_spec_code'] = $this->model->qry_all_record_type_with_spec_code();
            $VIEW_DATA['arr_all_report_type']                = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']                       = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            $VIEW_DATA['report_type']                        = '7c';

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }
    /**
     * bao cao tra qua han
     */
    private function _report18()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[18];
        if (get_request_var('pdf'))
        {
            $begin_date = get_request_var('begin','');
            $end_date   = get_request_var('end','');
            $spec_code  = get_request_var('spec','');
            
            $now        = date_create($this->model->getDate());

            $VIEW_DATA['now']            = $now;
            $VIEW_DATA['begin']          = $begin_date;
            $VIEW_DATA['end']            = $end_date;
            
            $begin_date = jwDate::ddmmyyyy_to_yyyymmdd($begin_date);
            $end_date   = jwDate::ddmmyyyy_to_yyyymmdd($end_date);
            
            $VIEW_DATA['arr_all_record'] = $this->model->qry_all_report_data_18($begin_date, $end_date,$spec_code);
            $this->view->render('dsp_pdf_report_18', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['report_type']                  = 18;
            $VIEW_DATA['arr_all_spec']                 = $this->model->qry_all_spec();
            $this->view->template->arr_all_report_type = $this->arr_all_report_type;
            $this->view->template->current_report_type = 18;
            $this->view->render('dsp_option_18', $VIEW_DATA);
        }
    }

    /**
     * Chỉ đổi 1->4 tương đươgn các quý
     * @param int $int
     * @return string Roman
     */
    private function integerToRoman($int)
    {
        switch (intval($int))
        {
            case 1:
                return 'I';
            case 2:
                return 'II';
            case 3:
                return 'III';
            case 4:
                return 'IV';
        }
    }
    
   
    /**
     * BÁO CÁO chi tiết hồ sơ trả kết quả quá hạn
     */
    private function _report19()
    {
        $VIEW_DATA['repoer_title'] = $this->arr_all_report_type[19];
       if (get_request_var('pdf'))
        {
            $begin_date = get_request_var('txt_begin_date','');
            $end_date   = get_request_var('txt_end_date','');
            $spec_code  = get_request_var('sel_spec','');           
            $now        = date_create($this->model->getDate());
            $speco_code = get_request_var('sel_group','');
            $group_leve = get_request_var('group_leve','');
            
            $VIEW_DATA['now']            = $now;
            $VIEW_DATA['begin']          = $begin_date;
            $VIEW_DATA['end']            = $end_date;
            
            $begin_date = jwDate::ddmmyyyy_to_yyyymmdd($begin_date);
            $end_date   = jwDate::ddmmyyyy_to_yyyymmdd($end_date);
            
            $arr_all_record                     = $this->model->get_record_biz_days_exceed($speco_code,$group_leve);
            $VIEW_DATA['arr_all_record']        = $this->model->get_step_biz_days_exceed($arr_all_record,$speco_code,$group_leve);
            $VIEW_DATA['report_code']           = 'report_19';
            $this->view->render('dsp_pdf_report_19', $VIEW_DATA);
       }
        else
        {
            $VIEW_DATA['arr_all_village']                       = $this->model->qry_all_village();
            $VIEW_DATA['report_type']                           = 19;            
            $VIEW_DATA['arr_all_record_type_with_spec_code']    = $this->model->qry_all_record_type_with_spec_code();
            $VIEW_DATA['arr_all_spec']                          = $this->model->qry_all_spec();
            $this->view->template->arr_all_report_type          = $this->arr_all_report_type;
            $this->view->template->current_report_type          = 19;
            $this->view->render('dsp_option_19', $VIEW_DATA);
        }
    }
}

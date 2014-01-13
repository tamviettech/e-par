<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

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

    public function __construct()
    {
        //in pdf khá lâu
        set_time_limit(60 * 5);
        parent::__construct('r3', 'report');

        if (DEBUG_MODE < 10)
            $this->model->db->debug = 0;

        $this->view->template->show_left_side_bar = TRUE;

        //Danh muc bao cao
        $this->arr_all_report_type            = $this->model->assoc_list_get_all_by_listtype_code('DANH_MUC_BAO_CAO', CONST_USE_ADODB_CACHE_FOR_REPORT);
        $this->view->template->reportbook_url = $this->view->get_controller_url('reportbook', 'r3');
    }

    function __destruct()
    {
        $this->model->db->debug = DEBUG_MODE;
    }

    public function main()
    {
        $this->option(0);
    }

    public function type($type = '0')
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
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['report_type']         = strval($type);
            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

    /**
     * Quản trị hệ thống kết xuất Báo cáo kết quả tiếp nhận hồ sơ theo ngày/tuần/tháng/năm
     * @param type $type
     */
    private function _report6()
    {
        if (get_request_var('pdf') == 1)
        {
            //Chu ky bao cao
            $v_period = get_request_var('period');

            //Lay danh sach Linh vuc
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            $model_data   = $this->model->qry_all_report_data_6($v_period, $arr_all_spec);

            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['report_priord']       = $v_period;
            $VIEW_DATA['arr_all_report_data'] = $model_data['arr_all_report_data'];
            $VIEW_DATA['report_title']        = 'BÁO CÁO KẾT QUẢ TIẾP NHẬN HỒ SƠ';
            $VIEW_DATA['report_subtitle']     = $model_data['report_subtitle'];
            $VIEW_DATA['report_code']         = 'report_6';

            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['report_type']         = 6;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

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

    private function _report12()
    {
        if (get_request_var('pdf') == 1)
        {
            //Ngày báo cáo
            $v_date = Date('Y-m-d');

            $arr_all_group       = $this->model->qry_all_group(CONST_USE_ADODB_CACHE_FOR_REPORT);
            $arr_all_report_data = $this->model->qry_all_report_data_12($v_date);

            $VIEW_DATA['arr_all_report_data'] = $arr_all_report_data;
            $VIEW_DATA['report_title']        = 'BÁO CÁO VỀ THỦ TỤC HÀNH CHÍNH CHẬM TIẾN ĐỘ';
            $VIEW_DATA['report_subtitle']     = 'Ngày ' . Date('d-m-Y');
            $VIEW_DATA['report_code']         = 'report_12';
            $VIEW_DATA['arr_all_group']       = $arr_all_group;

            $this->view->render('dsp_pdf_report_12', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['report_type']         = 12;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

    private function _report13()
    {

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
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['report_type']         = 13;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

    private function _report14()
    {
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
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['report_type']         = 14;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

    private function _report15()
    {
        if (get_request_var('pdf') == 1)
        {
            //Kỳ báo cáo
            $v_begin_date = get_request_var('begin_date', date('d-m-Y'));
            $v_end_date   = get_request_var('end_date', date('d-m-Y'));

            $v_begin_date_yyyymmdd = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date_yyyymmdd   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);

            $arr_all_group       = $this->model->qry_all_group(CONST_USE_ADODB_CACHE_FOR_REPORT);
            $arr_all_report_data = $this->model->qry_all_report_data_15($v_begin_date_yyyymmdd, $v_end_date_yyyymmdd);

            $VIEW_DATA['arr_all_report_data'] = $arr_all_report_data;
            $VIEW_DATA['report_title']        = "BÁO CÁO THỦ TỤC HÀNH CHÍNH BỊ TỪ CHỐI";
            $VIEW_DATA['report_subtitle']     = 'Từ ngày ' . $v_begin_date . ' đến ngày ' . $v_end_date;
            $VIEW_DATA['report_code']         = 'report_15';
            $VIEW_DATA['arr_all_group']       = $arr_all_group;

            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
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
        if (get_request_var('pdf') == 1)
        {
            $v_spec_code      = get_request_var('spec_code', '');
            $v_record_type_id = get_request_var('record_type', 0);

            $v_begin_date = get_request_var('begin_date', '');
            $v_end_date   = get_request_var('end_date', '');

            $v_begin_date_yyyymmdd = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date_yyyymmdd   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);

            $arr_model_data = $this->model->qry_all_report_data_16($v_spec_code, $v_record_type_id, $v_begin_date_yyyymmdd, $v_end_date_yyyymmdd);

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
            $VIEW_DATA['arr_all_record_type_with_spec_code'] = $this->model->qry_all_record_type_with_spec_code();
            $VIEW_DATA['arr_all_report_type']                = $this->arr_all_report_type;
            $VIEW_DATA['arr_all_spec']                       = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            $VIEW_DATA['report_type']                        = 16;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

    private function _report7()
    {
        if (get_request_var('pdf') == 1)
        {
            $v_begin_date          = get_request_var('begin_date', Date('d-m-Y'));
            $v_end_date            = get_request_var('end_date', Date('d-m-Y'));
            $v_spec                = get_request_var('spec');
            $v_begin_date_yyyymmdd = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date_yyyymmdd   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);

            //Lay danh sach Linh vuc
            $arr_all_spec = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);

            //Du lieu bao cao
            $arr_model_data = $this->model->qry_all_report_data_7($v_begin_date_yyyymmdd, $v_end_date_yyyymmdd);

            //Report data
            $arr_all_report_data = Array();
            foreach ($arr_all_spec as $v_spec_code => $v_spec_name)
            {
                if ($v_spec && $v_spec != $v_spec_code)
                {
                    continue;
                }
                $v_sum   = isset($arr_model_data[$v_spec_code]['C_TOTAL_RECORD']) ? $arr_model_data[$v_spec_code]['C_TOTAL_RECORD'] : 0;
                $v_fee   = isset($arr_model_data[$v_spec_code]['C_TOTAL_FEE']) ? $arr_model_data[$v_spec_code]['C_TOTAL_FEE'] : 0;
                $v_cost  = isset($arr_model_data[$v_spec_code]['C_TOTAL_COST']) ? $arr_model_data[$v_spec_code]['C_TOTAL_COST'] : 0;
                $v_total = isset($arr_model_data[$v_spec_code]['C_SUM']) ? $arr_model_data[$v_spec_code]['C_SUM'] : 0;

                $row                   = array();
                $row['RN']             = 1;
                $row['C_SPEC_NAME']    = $v_spec_name;
                $row['C_TOTAL_RECORD'] = $v_sum;
                $row['C_TOTAL_FEE']    = $v_fee;
                $row['C_TOTAL_COST']   = $v_cost;
                $row['C_SUM']          = $v_total;

                array_push($arr_all_report_data, $row);
            }

            $VIEW_DATA['arr_all_report_data'] = $arr_all_report_data;
            $VIEW_DATA['arr_all_spec']        = $arr_all_spec;
            $VIEW_DATA['report_title']        = "BÁO CÁO VỀ PHÍ, LỆ PHÍ ĐÃ THU";
            $VIEW_DATA['report_subtitle']     = 'Từ ngày ' . $v_begin_date . ' đến ngày ' . $v_end_date;
            $VIEW_DATA['report_code']         = 'report_7';
            $this->view->render('dsp_common_pdf_report', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_spec']        = $this->model->qry_all_spec();
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['report_type']         = 7;

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

    private function _report7b()
    {
        if (get_request_var('pdf') == 1)
        {
            $v_begin_date          = get_request_var('begin_date', Date('d-m-Y'));
            $v_end_date            = get_request_var('end_date', Date('d-m-Y'));
            $v_spec                = get_request_var('spec', '');
            $v_begin_date_yyyymmdd = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
            $v_end_date_yyyymmdd   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);

            $arr_all_report_data = $this->model->qry_all_report_data_7b($v_spec, $v_begin_date_yyyymmdd, $v_end_date_yyyymmdd);

            $VIEW_DATA['arr_all_report_data'] = $arr_all_report_data;
            $VIEW_DATA['report_title']        = "BÁO CÁO CHI TIẾT DANH SÁCH THU PHÍ, LỆ PHÍ";
            $VIEW_DATA['report_subtitle']     = 'Từ ngày ' . $v_begin_date . ' đến ngày ' . $v_end_date;
            $VIEW_DATA['report_code']         = 'report_7b';

            $this->view->render('dsp_common_pdf_report_with_subtotal', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_spec']        = $this->model->qry_all_spec();
            $VIEW_DATA['arr_all_report_type'] = $this->arr_all_report_type;
            $VIEW_DATA['report_type']         = '7b';

            $this->view->render('dsp_report_option', $VIEW_DATA);
        }
    }

    private function _report7c()
    {
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
     * Bao cao tong hop
     */
    private function _report3()
    {
        if (get_request_var('pdf'))
        {
            $now = date_create($this->model->getDate());

            $type        = get_request_var('type', 'Y');
            $year        = get_request_var('year', $now->format('Y'));
            $begin_month = get_request_var('begin_month', $now->format('m'));
            $end_month   = get_request_var('end_month', $now->format('m'));
            $v_subtitle  = '';

            switch ($type)
            {
                case 'm':
                    //Thang hien tai
                    $v_subtitle = "tháng $end_month năm $year";
                    break;
                    
                case 'Q':
                    $v_quarter  = $this->integerToRoman(intval($end_month / 3));
                    $v_subtitle = "quý $v_quarter năm $year";
                    break;
                    
                case 'Y':
                    //ca nam
                    $v_subtitle = "năm $year";
                    break;
                    
                case '1to6':
                    //6 thang dau nam
                    $v_subtitle = "6 tháng đầu năm $year";
                    break;
                    
                case '7to12':
                    //6 thang cuoi nam
                    $v_subtitle = "6 tháng cuối năm $year";
                    break;
                    
                default:
                    //Thang hien tai
                    $v_subtitle = "tháng $end_month năm $year";
                    break;
            }

            require_once SERVER_ROOT . 'libs/tcpdf/zreport.php';
            $VIEW_DATA['arr_all_spec'] = $this->model->qry_report999($year, $begin_month, $end_month);
            $VIEW_DATA['now']          = $now;
            $VIEW_DATA['subtitle']     = $v_subtitle;
            $this->view->render('dsp_pdf_report_3', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['arr_all_report_type']          = $this->arr_all_report_type;
            $VIEW_DATA['report_type']                  = 3;
            $this->view->template->arr_all_report_type = $this->arr_all_report_type;
            $this->view->template->current_report_type = 3;
            $this->view->render('dsp_option_3', $VIEW_DATA);
        }
    }

    /**
     * bao cao tra qua han
     */
    private function _report18()
    {
        if (get_request_var('pdf'))
        {
            require_once SERVER_ROOT . 'libs/tcpdf/zreport.php';
            $begin_date = date_create_from_format('d-m-Y', get_request_var('begin'))->format('Y-m-d');
            $end_date   = date_create_from_format('d-m-Y', get_request_var('end'))->format('Y-m-d');
            $now        = date_create($this->model->getDate());

            $VIEW_DATA['arr_all_record'] = $this->model->qry_all_due($begin_date, $end_date);
            $VIEW_DATA['now']            = $now;
            $VIEW_DATA['begin']          = get_request_var('begin');
            $VIEW_DATA['end']            = get_request_var('end');
            $this->view->render('dsp_pdf_report_18', $VIEW_DATA);
        }
        else
        {
            $VIEW_DATA['report_type']                  = 18;
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

}
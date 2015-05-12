<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class liveboard_Controller extends Controller
{

    /**
     *
     * @var \liveboard_Model 
     */
    public $model;

    function __construct()
    {
        parent::__construct('r3', 'liveboard');
        $this->view->template->show_left_side_bar = FALSE;
        
        $this->_arr_roles           = json_decode(CONST_ALL_R3_ROLES,true);
        $this->view->role_text      = $this->_arr_roles;
        
        $this->view->DATETIME_NOW   = $this->model->get_datetime_now();
    }

    function main()
    {
        //Danh sach tat ca cac xa
        $arr_all_village = $this->model->qry_all_village();
        
        $VIEW_DATA['arr_all_village'] = $arr_all_village;
        $this->view->render('dsp_main', $VIEW_DATA);
    }

    public function dsp_liveboard()
    {
        $VIEW_DATA['scope'] = '0,1,2,3';
        $VIEW_DATA['title'] = 'Bảng theo dõi tình hình giải quyết thủ tục hành chính ngày ' . date('d-m-Y');
        $this->view->render('dsp_liveboard', $VIEW_DATA);
    }

    public function dsp_liveboard_huyen()
    {
        $VIEW_DATA['scope'] = '2,3';
        $VIEW_DATA['title'] = 'Bảng theo dõi tình hình giải quyết thủ tục hành chính tiếp nhận tại huyện ngày ' . date('d-m-Y');
        $this->view->render('dsp_liveboard', $VIEW_DATA);
    }

    public function dsp_liveboard_xa($village_id=NULL)
    {
        if ($village_id != NULL)
        {
            $VIEW_DATA['title'] = 'Bảng theo dõi tình hình giải quyết thủ tục hành chính tiếp nhận tại ' . get_request_var('v') . ' ngày ' . date('d-m-Y');
            $VIEW_DATA['v_village_id'] = $village_id;
        }
        else
        {
            $VIEW_DATA['title'] = 'Bảng theo dõi tổng hợp tình hình giải quyết thủ tục hành chính tiếp nhận tại xã ngày ' . date('d-m-Y');
            $VIEW_DATA['v_village_id'] = 0;
        }
        
        $VIEW_DATA['scope'] = '0,1';
        
        $this->view->render('dsp_liveboard', $VIEW_DATA);
    }

    /**
     * Danh muc TTHC
     */
    public function dsp_tthc()
    {
        $this->model->scope        = '0,1,2,3';
        $VIEW_DATA['arr_all_type'] = $this->model->qry_all_record_type();
        $arr_workflow              = array();
        foreach ($VIEW_DATA['arr_all_type'] as &$type)
        {
            $dom_workflow   = @simplexml_load_file(SERVER_ROOT . 'apps/r3/xml-config/'
                    . $type['C_CODE'] . '/' . $type['C_CODE'] . '_workflow.xml');
            $type['C_TIME'] = $dom_workflow ? $dom_workflow->attributes()->totaltime : '';
        }
        $this->view->render('dsp_tthc', $VIEW_DATA);
    }

    public function arp_liveboard($scope = '0,2,3')
    {
        $this->model->scope                               = replace_bad_char($scope);
        $VIEW_DATA['scope']                               = $scope;
        
        $v_village_id = get_request_var('village_id',0);
        $VIEW_DATA['v_village_id']                 = $v_village_id;
        
        //Lay danh sach loai hs
        $VIEW_DATA['arr_all_record_type']                 = $this->model->qry_all_record_type();
        //Tiep nhan trong ngay
        $VIEW_DATA['arr_count_today_receive_record']      = $this->model->qry_count_today_receive_record($v_village_id);
        //Da ban giao trong ngay
        $VIEW_DATA['arr_count_today_handover_record']     = $this->model->qry_count_today_handover_record($v_village_id);
        //Dang xu ly
        $VIEW_DATA['arr_count_execing_record']            = $this->model->qry_count_execing_record($v_village_id);
        //Dung tien do
        $VIEW_DATA['arr_count_in_schedule_record']        = $this->model->qry_count_in_schedule_record($v_village_id);
        //Cham tien do
        $VIEW_DATA['arr_count_over_deadline_record']      = $this->model->qry_count_over_deadline_record($v_village_id);
        //Qua han
        $VIEW_DATA['arr_count_expried_record']            = $this->model->qry_count_expried_record($v_village_id);
        //Bo sung
        $VIEW_DATA['arr_count_supplement_record']         = $this->model->qry_count_supplement_record($v_village_id);
        //Cho tra kq
        $VIEW_DATA['arr_count_waiting_for_return_record'] = $this->model->qry_count_waiting_for_return_record($v_village_id);
        //Da tra kq
        $VIEW_DATA['arr_count_returned_record']           = $this->model->qry_count_returned_record($v_village_id);
        //Dang tamh dung
        $VIEW_DATA['arr_count_pausing_record']            = $this->model->qry_count_pausing_record($v_village_id);

        $this->view->render('dsp_apr_liveboard', $VIEW_DATA);
    }

    //end func
    /**
     * Lấy danh dánh hồ sơ theo tiêu chí đã chọn
     * @param string $args : type_record-status (mã HS và tiêu chí chọn)
     */
    public function dsp_list_detail_liveboard($args = '')
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
        $arr_status             = array(
                                1  => 'Danh sách các hồ sơ',
                                2  => 'Danh sách hồ sơ mới tiếp nhận',
                                3  => 'Danh sách hồ sơ đã bàn giao',
                                4  => 'Danh sách hồ sơ đang thụ lý',
                                5  => 'Danh sách hồ sơ đúng tiến độ',
                                6  => 'Danh sách hồ sơ chậm tiến độ',
                                7  => 'Danh sách hồ sơ quá hạn',
                                8  => 'Danh sách hồ sơ phải bổ sung',
                                9  => 'Danh sách hồ sơ đang tạm dừng (chờ bổ sung/ thuế)',
                                10 => 'Danh sách hồ sơ chờ trả',
                                11 => 'Danh sách hồ sơ đã trả'
                                ); 
        $v_status = $v_type_record_id = '';
        if($args == '')
        {
            $this->_error();
            return;
        }
        
        $args                = replace_bad_char($args);
        $arr_args            = explode('-', $args);
        $v_type_record_id    = isset($arr_args[0]) ? trim($arr_args[0]) : '';
        $v_status            = isset($arr_args[1]) ? trim($arr_args[1]) : '';
        $v_village_id        = isset($arr_args[2]) ? trim($arr_args[2]) : 0 ;
        
        //Lay tat cac danh sach cac loai thu tuc
        $arr_all_record_type = $this->model->qry_all_record_type();        
        if( ! array_key_exists($v_type_record_id,$arr_all_record_type) OR ! array_key_exists($v_status, $arr_status))
        {
             $this->_error();
            return false ;
        }
        
        $VIEW_DATA=array();
        $VIEW_DATA['type_record_name'] = $arr_all_record_type[$v_type_record_id]['C_NAME'];
        $v_type_record_code            = $arr_all_record_type[$v_type_record_id]['C_CODE'];
        $VIEW_DATA['type_record_code'] = $v_type_record_code;
        
        $VIEW_DATA['arr_all_record']   = $this->model->qry_all_record_by_role($v_type_record_id,$v_status,$v_village_id);
        
        $VIEW_DATA['record_status']    = $arr_status[$v_status];
        $this->view->render('dsp_list_detail_liveboard',$VIEW_DATA);
    }
    private  function _error()
    {
        $v_html = '<div class="div_error" style="margin-top:20px;font-size:118px">
                        <p>Xảy ra lỗi trong quá trình lấy thông tin. Xin vui lòng thử lại!.
                        <a href="javascript::void()" onclick="try{window.parent.hidePopWin();}catch(e){window.close();};">
                            Trở lại       
                        </a>
                        </p>
                   </div>';
        echo  $v_html;
    }
 
    //Láy thông tin chi tiết từng hồ sơ
    public function dsp_single_record_statistics($record_id)
    {
        $record_id = get_request_var('hdn_item_id', $record_id);
        is_id_number($record_id) OR $record_id = 0;
        //Thông tin bản khai
        $VIEW_DATA['arr_single_record'] = $this->model->qry_single_record($record_id);

        $dom_processing = simplexml_load_string($VIEW_DATA['arr_single_record']['C_XML_PROCESSING']);

        //Thong tin tien do
        $v_record_type = $VIEW_DATA['arr_single_record']['C_RECORD_TYPE_CODE'];
        if ($VIEW_DATA['arr_single_record']['C_CLEAR_DATE'] == NULL)
        {
            $dom_step = simplexml_load_file($this->view->get_xml_config($v_record_type, 'workflow'));
        }
        else
        {
            $dom_step = simplexml_load_string($VIEW_DATA['arr_single_record']['C_XML_WORKFLOW']);
        }
        $dom_workflow   = $dom_step;
        $r              = xpath($dom_step, '//step/@time');
        $step_days_list = '';
        foreach ($r as $time)
        {
            $step_days_list .= ($step_days_list != '') ? ";$time" : $time;
        }
        $arr_step_formal_date              = $this->model->formal_record_step_days_to_date($record_id, $step_days_list);
        $VIEW_DATA['arr_step_formal_date'] = $arr_step_formal_date;

        //Lay danh sach step, ngay bat dau, ket thuc thuc te cua step, số ngày đã tiêu tốn thực tế để hoàn thành step
        $steps                            = xpath($dom_workflow, "//step[not(@no_chain = 'true')]");
        $index                            = -1;
        $arr_step_infact_formal_days_diff = array();
        foreach ($steps as $step)
        {
            $index++;
            $v_first_task_code = strval($step->task[0]->attributes()->code);
            $v_last_task_code  = strval($step->task[sizeof($step->task) - 1]->attributes()->code);

            //So ngay phai hoan thanh step theo quy dinh
            $v_formal_step_time = floatval(str_replace(',', '.', $step->attributes()->time));

            //=>Ngay bat dau thuc te cua step, la ngay thuc hien prev_task
            //$v_prev_task_code         = get_xml_value($dom_workflow, "//task[contains(@next,'$v_first_task_code')]/@code");
            //$v_step_begin_date_infact = get_xml_value($dom_processing, "//step[@code='$v_prev_task_code']/datetime");
            $v_step_begin_date_infact = '';
            foreach (xpath($dom_processing, "//step") as $item)
            {
                if (strval($item->attributes()->code) == $v_first_task_code)
                {
                    break;
                }
                else
                {
                    $v_step_begin_date_infact = (string) $item->datetime;
                }
            }

            //=>Ngay ket thuc thuc te cua step
            $v_step_end_date_infact = get_xml_value($dom_processing, "//step[@code='$v_last_task_code']/datetime");

            //=>Số ngày làm việc tiêu tốn thực tế để hoàn thành step
            //$v_step_spent_days_infact       = $this->model->days_between_two_date($v_step_begin_date_infact, $v_step_end_date_infact);
            $begin_count              = false;
            $begin_count_2            = true;
            $v_step_spent_days_infact = 0;
            $v_start                  = '';
            $v_end                    = '';
            $v_next_step              = isset($steps[$index + 1]) ? $steps[$index + 1] : null;
            $v_next_step_first_task   = '';
            if ($v_next_step)
            {
                $v_next_step_first_task = isset($v_next_step->task[0]) ? strval($v_next_step->task[0]->attributes()->code) : '';
            }
            foreach ($dom_processing->children() as $item)
            {
                if ($item->getName() == 'step')
                {
                    $v_end      = (string) $item->datetime;
                    $v_date_inv = $this->model->days_between_two_date($v_start, $v_end);
                    if ((string) $item->attributes()->code == $v_first_task_code)
                    {
                        $begin_count = true;
                    }
                    elseif ((string) $item->attributes()->code == $v_next_step_first_task)
                    {
                        break;
                    }
                    if (strpos((string) $item->attributes()->code, _CONST_THONG_BAO_BO_SUNG_ROLE) !== false)
                    {
                        $begin_count_2 = false;
                    }
                    if ($begin_count && $begin_count_2)
                    {
                        $v_step_spent_days_infact += $v_date_inv;
                    }
                    $v_start = (string) $item->datetime;
                }
                elseif ($item->getName() == 'action' && $item->attributes()->do == 'unpause')
                {
                    $begin_count_2 = true;
                }
            }
            //=> Step nhanh hay cham bao nhieu ngày?            
            $v_step_infact_formal_days_diff = $v_formal_step_time - $v_step_spent_days_infact;

            $arr_step_infact_formal_days_diff[md5($v_last_task_code)] = $v_step_infact_formal_days_diff;
        }
        $VIEW_DATA['arr_step_infact_formal_days_diff'] = $arr_step_infact_formal_days_diff;

        //Tai lieu mem
        $VIEW_DATA['arr_all_record_file'] = $this->model->qry_all_record_file($record_id);


        //Tai lieu
        $VIEW_DATA['arr_all_doc'] = $this->model->qry_all_record_doc($record_id);

        $this->view->render('dsp_single_record_statistics', $VIEW_DATA);
    }
    
    
    //####### Thống kê theo dõi hồ sơ tổng hợp theo xã###
    public function dsp_all_xa()
    {
        $VIEW_DATA['scope'] = '3';
        $VIEW_DATA['title'] = 'Bảng theo dõi tình hình giải quyết thủ tục hành chính ngày ' . date('d-m-Y');
        $this->view->render('dsp_all_xa',$VIEW_DATA);
    }
    /**
     * Hiển thị thông tin đếm các trạng thái của tất cả các xã
     */
    public function dsp_all_xa_liveboard()
    {
        $VIEW_DATA = array();
        //Get all village
        $VIEW_DATA['arr_all_village'] = $this->model->qry_all_village();

        //Tiep nhan trong ngay
        $VIEW_DATA['arr_count_today_receive_record']      = $this->model->qry_count_today_receive_record_xa();
        //Da ban giao trong ngay
        $VIEW_DATA['arr_count_today_handover_record']     = $this->model->qry_count_today_handover_record_xa();
        //Dang xu ly
        $VIEW_DATA['arr_count_execing_record']            = $this->model->qry_count_execing_record_xa();
        //Dung tien do
        $VIEW_DATA['arr_count_in_schedule_record']        = $this->model->qry_count_in_schedule_record_xa();
        //Cham tien do
        $VIEW_DATA['arr_count_over_deadline_record']      = $this->model->qry_count_over_deadline_record_xa();
        //Qua han
        $VIEW_DATA['arr_count_expried_record']            = $this->model->qry_count_expried_record_xa();
        //Bo sung
        $VIEW_DATA['arr_count_supplement_record']         = $this->model->qry_count_supplement_record_xa();
        //Cho tra kq
        $VIEW_DATA['arr_count_waiting_for_return_record'] = $this->model->qry_count_waiting_for_return_record_xa();
        //Da tra kq
        $VIEW_DATA['arr_count_returned_record']           = $this->model->qry_count_returned_record_xa();
        //Dang tamh dung
        $VIEW_DATA['arr_count_pausing_record']            = $this->model->qry_count_pausing_record_xa();
        
        $this->view->render('dsp_all_xa_liveboard',$VIEW_DATA);
    }
    
    
    //CHưa dùng
//    public function dsp_list_detail_village_liveboard($args)
//    {
//        $VIEW_DATA = array();
//         //Kiem tra session
//        session::init();
//        $login_name = session::get('login_name');
//        
//        if ($login_name == NULL)
//        {
//            session::destroy();
//            header('location:' . SITE_ROOT . 'login.php');
//            exit;
//        }
//        $arr_status             = array(
//                                1  => 'Danh sách các hồ sơ',
//                                2  => 'Danh sách hồ sơ mới tiếp nhận',
//                                3  => 'Danh sách hồ sơ đã bàn giao',
//                                4  => 'Danh sách hồ sơ đang thụ lý',
//                                5  => 'Danh sách hồ sơ đúng tiến độ',
//                                6  => 'Danh sách hồ sơ chậm tiến độ',
//                                7  => 'Danh sách hồ sơ quá hạn',
//                                8  => 'Danh sách hồ sơ phải bổ sung',
//                                9  => 'Danh sách hồ sơ đang tạm dừng (chờ bổ sung/ thuế)',
//                                10 => 'Danh sách hồ sơ chờ trả',
//                                11 => 'Danh sách hồ sơ đã trả'
//                                ); 
//        $v_status = $v_type_record_id = '';
//        if($args == '')
//        {
//            $this->_error();
//            return;
//        }
//        //$v_village - v_record_id: ma vung+ trang thai dieu kien loc
//        $args                = replace_bad_char($args); 
//        $arr_args            = explode('-', $args);
//        $v_village_id           = isset($arr_args[0]) ? trim($arr_args[0]) : '';
//        $v_status            = isset($arr_args[1]) ? trim($arr_args[1]) : '';
//        
//        //Lay tat cac xa
//        $arr_village = $this->model->qry_count_village_id($v_village_id);
//        if((count($arr_village) != 1)  OR ! (array_key_exists($v_status, $arr_status)))
//        {
//             $this->_error();
//            return false ;
//        }
//        
//        $VIEW_DATA=array();
//        $VIEW_DATA['arr_village']      = $arr_village;
//        $VIEW_DATA['arr_all_record']   = $this->model->qry_all_record_by_role($v_type_record_id = '',$v_status,$v_village_id);
//        
//        $VIEW_DATA['record_status']    = $arr_status[$v_status];
//        
//        //Dinh so nhan phan biet hien thi tat ca thu tuc cua ca xa
//        $VIEW_DATA['all_xa'] = 'all_xa';
//        $this->view->render('dsp_list_detail_liveboard',$VIEW_DATA);
//    }

}
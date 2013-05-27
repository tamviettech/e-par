<?php 
/**
// File name   : record_Controller.php
// Version     : 1.0.0.1
// Begin       : 2012-12-01
// Last Update : 2010-12-25
// Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
//Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

// E-PAR is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// E-PAR is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// See LICENSE.TXT file for more information.
*/
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class record_Controller extends Controller {

    //Roles
    protected $_arr_roles = array(
                //BP Mot-Cua
                _CONST_TIEP_NHAN_ROLE                            => 'Tiếp nhận'
                ,_CONST_BAN_GIAO_ROLE                             => 'Bàn giao'
                ,_CONST_BO_SUNG_ROLE                              => 'Bổ sung'
                ,_CONST_TRA_THONG_BAO_NOP_THUE_ROLE               => 'Trả TB thuế'
                ,_CONST_NHAN_BIEN_LAI_NOP_THUE_ROLE               => 'Nhận BL thuế'
                ,_CONST_THU_PHI_ROLE                              => 'Thu phí'
                ,_CONST_TRA_KET_QUA_ROLE                          => 'Trả KQ'
                ,_CONST_IN_PHIEU_TIEP_NHAN_ROLE                   => 'In lại phiếu TN'
                ,_CONST_CHUYEN_LEN_HUYEN_ROLE                     => 'Chuyển HS lên Huyện'
                ,_CONST_TRA_HO_SO_VE_XA_ROLE                      => 'Trả HS về xã'
                ,_CONST_XAC_NHAN_HO_SO_LIEN_THONG_ROLE            => 'Giải quyết/xác nhận HS liên thông'
                ,_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE      => 'Hồ sơ Internet'

    			//BO SUNG
    			,_CONST_THONG_BAO_BO_SUNG_ROLE			          => 'Thông báo bổ sung'

                //Bo phan Thue
                ,_CONST_NOP_HO_SO_SANG_CHI_CUC_THUE_ROLE          => 'Chuyển HS sang chi cục thuế'
                ,_CONST_NHAN_THONG_BAO_CUA_CHI_CUC_THUE_ROLE      => 'Nhận TB của chi cục thuế'
                ,_CONST_CHUYEN_THONG_BAO_THUE_VE_BP_MOT_CUA_ROLE  => 'Chuyển TB thuế về "Môt-cửa"'

                //Phong chuyen mon
                ,_CONST_PHAN_CONG_ROLE                            => 'Phân công thụ lý'
                ,_CONST_PHAN_CONG_LAI_ROLE                        => 'Thay đổi thụ lý'
                ,_CONST_THU_LY_ROLE                               => 'Thụ lý'
                ,_CONST_CHUYEN_HO_SO_LEN_SO_ROLE                  => 'Chuyển HS lên Sở'
                ,_CONST_NHAN_HO_SO_TU_SO_ROLE                     => 'Nhận HS từ Sở'
                ,_CONST_CHUYEN_YEU_CAU_XAC_NHAN_XUONG_XA_ROLE     => 'Chuyển giải quyết/xác nhận xuống xã'
                ,_CONST_THU_LY_HO_SO_LIEN_THONG_ROLE              => 'Thụ lý HS liên thông'

                ,_CONST_YEU_CAU_THU_LY_LAI_ROLE                   => 'Yêu cầu thụ lý lại'
                ,_CONST_XET_DUYET_ROLE                            => 'Xét duyệt hồ sơ'
                ,_CONST_XET_DUYET_BO_SUNG_ROLE                    => 'Xét duyệt hồ sơ bổ sung'
                ,_CONST_CHUYEN_LAI_BUOC_TRUOC_ROLE                => 'Yêu cầu chuyển hồ sơ'

                //Lanh dao don vi
                ,_CONST_KY_ROLE                                   => 'Ký duyệt hồ sơ'
                ,_CONST_Y_KIEN_LANH_DAO_ROLE                      => 'Ý kiến lãnh đạo'

                //Chung
                ,_CONST_TRA_CUU_ROLE                              => 'Tra cứu'
                ,_CONST_BAO_CAO_ROLE                              => 'Báo cáo'
                ,'REJECT'                                         => 'Từ chối HS'
            );
    protected $_active_role;
    
    protected $_arr_user_role = array();    

    protected $_record_type;

    protected $_activity_filter = array(
                0 => 	'Tất cả hồ sơ'
                ,1 => 	'Hồ sơ vừa tiếp nhận'
                ,2 => 	'Hồ sơ chờ bổ sung'
                ,10 => 	'Hồ sơ đang tạm dừng'
                ,3 => 	'Hồ sơ bị từ chối'
                ,4 => 	'Hồ sơ đang giải quyết'
                ,5 => 	'Hồ sơ đang trình ký'
                ,6 => 	'Hồ sơ chờ trả kết quả'
                ,7 => 	'Hồ sơ đã trả kết quả'
                ,8 => 	'Hồ sơ đang chậm tiến độ'
                ,9 => 	'Hồ sơ quá hạn trả kết quả'
                
    );

    function __construct()
    {
        parent::__construct('r3', 'record');
        $this->view->template->show_left_side_bar = FALSE;
        //$this->view->template->arr_roles = $this->model->qry_all_user_role(Session::get('user_code'));// $this->_arr_roles;
        $this->view->template->controller_url = $this->view->get_controller_url();
        $this->view->template->activity_filter = $this->_activity_filter;
        $this->view->role_text=$this->_arr_roles;

        //Kiem tra session
        session::init();
        $login_name = session::get('login_name');
        if ($login_name == NULL)
        {
            session::destroy();
            header('location:' . SITE_ROOT . 'login.php');
            exit;
        }

        $menu = Array();

        //Kiem tra quyen tren cac role da duoc phan cong
        //HS Internet
        if ($this->check_permission(_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE))
        {
            $menu[_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE] = $this->_arr_roles[_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE];
            $this->_arr_user_role[] = _CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE;
        }
        $arr_my_role = $this->model->qry_all_user_role(Session::get('user_code'));
        foreach ($this->_arr_roles as $key => $val)
        {
            if ($this->check_permission($key) && in_array($key, $arr_my_role))
            {
                $menu[$key] = $val;
                $this->_arr_user_role[] = strtoupper($key);
            }
        }

        if ($this->check_permission(_CONST_Y_KIEN_LANH_DAO_ROLE))
        {
            $menu[_CONST_Y_KIEN_LANH_DAO_ROLE] = $this->_arr_roles[_CONST_Y_KIEN_LANH_DAO_ROLE];
            $this->_arr_user_role[] = _CONST_Y_KIEN_LANH_DAO_ROLE;
        }

        //Cac quyen khong phai la role
        //Tra cuu, bao cao
        if ($this->check_permission(_CONST_TRA_CUU_ROLE))
        {
            $menu[_CONST_TRA_CUU_ROLE] = $this->_arr_roles[_CONST_TRA_CUU_ROLE];
            $this->_arr_user_role[] = _CONST_TRA_CUU_ROLE;
        }
        if ($this->check_permission(_CONST_BAO_CAO_ROLE))
        {
            $menu[_CONST_BAO_CAO_ROLE] = $this->_arr_roles[_CONST_BAO_CAO_ROLE];
            $this->_arr_user_role[] = _CONST_BAO_CAO_ROLE;
        }
        if ($this->check_permission(_CONST_BAO_CAO_ROLE))
        {
            $menu[_CONST_BAO_CAO_ROLE] = $this->_arr_roles[_CONST_BAO_CAO_ROLE];
            $this->_arr_user_role[] = _CONST_BAO_CAO_ROLE;
        }

        $this->view->template->arr_roles  = $menu;
        $this->view->arr_roles  = $menu;
        //$this->view->template->role = 'R3';

        //Nếu Database nằm trên server khac: Ngày giờ hiện tại sẽ lấy từ DB
        $this->view->DATETIME_NOW = $this->model->get_datetime_now();
    }

    private function _regular_role($role)
    {
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_option($role);

        //Nếu không chọn loại HS, lấy loại HS đầu tiên trong danh sách
        $v_record_type_code = $this->_record_type;
        if ($v_record_type_code == '')
        {
            $arr_key = array_keys($VIEW_DATA['arr_all_record_type']);
            $v_record_type_code = array_shift($arr_key);
        }

        $this->view->template->active_role = strtoupper($role);

        $arr_all_record                     = $this->model->qry_all_record_by_role($role, $v_record_type_code);
        $VIEW_DATA['record_type_code']      = $v_record_type_code;
        $VIEW_DATA['active_role']           = strtoupper($role);
        $VIEW_DATA['active_role_text']      = $this->_arr_roles[strtoupper($role)];

        $v_dsp_file = 'dsp_all_record_' . strtolower($role);
        $VIEW_DATA['MY_TASK'] = $v_record_type_code . _CONST_XML_RTT_DELIM . strtoupper($role);
        if (sizeof($arr_all_record) > 0)
        {
            $VIEW_DATA['MY_TASK']           = $arr_all_record[0]['C_NEXT_TASK_CODE'];
        }
        $VIEW_DATA['arr_all_record']        = $arr_all_record;
        
        $v_view_file = SERVER_ROOT . 'apps' .DS . $this->app_name . DS . 'modules'.DS . $this->module_name . DS . $this->module_name . '_views'. DS . $v_dsp_file . '.php';
        if (file_exists($v_view_file))
        {
            $this->view->render($v_dsp_file, $VIEW_DATA);
        }
        else
        {
            $this->view->render('dsp_all_record_regular_role', $VIEW_DATA);
        }
    }

    public function main($role='')
    {
        $this->dsp_all_record($role);
    }

    public function dsp_all_record($role = '')
    {
        if (!isset($role) OR $role == '')
        {
            $role = $this->_get_my_first_role();
        }

        if (method_exists($this, $role))
        {
            $this->check_permission(strtoupper($role)) OR die($this->access_denied());

            Cookie::set('active_role', $role);
            Session::set('active_role', $role);

            $this->_active_role = $role;
            $this->_record_type = trim($this->get_post_var('sel_record_type'));
            $this->view->template->active_role =  $role;
            $this->view->template->controller_url = $this->view->get_controller_url();
            $this->view->active_role =  $role;
            $this->$role();
        }
    }

    public function dsp_single_record($record_id)
    {

        //Kiem tra quyen
        $this->check_permission(_CONST_TIEP_NHAN_ROLE) OR die($this->access_denied());

        $this->view->template->active_role =  Session::get('active_role');

        $v_record_type = trim($this->get_post_var('sel_record_type'));
        $MY_TASK = trim($this->get_post_var('MY_TASK'));

        $v_xml_workflow_file_name = $this->view->get_xml_config($v_record_type, 'workflow');

        $VIEW_DATA['arr_single_record']     = $this->model->qry_single_record($record_id, $v_record_type, $v_xml_workflow_file_name);
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_option(_CONST_TIEP_NHAN_ROLE);
        $VIEW_DATA['record_type_code']      = $v_record_type;
        $VIEW_DATA['MY_TASK']               = $MY_TASK;

        $VIEW_DATA['arr_all_record_file']   = $this->model->qry_all_record_file($record_id);

        $this->view->render('dsp_single_record', $VIEW_DATA);
    }

    public function dsp_print_ho_for_citizen($record_id)
    {
        $stmt = 'Select R.*, RT.C_NAME as RECORD_TYPE_NAME, RT.C_CODE as RECORD_TYPE_CODE, RT.C_SCOPE
                    From view_record R Left Join t_r3_record_type as RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                    Where R.PK_RECORD=?';
        $params = array($record_id);

        $this->model->db->debug=0;
        $VIEW_DATA['arr_single_record']     = $this->model->db->getRow($stmt, $params);

        $this->view->render('dsp_print_ho_for_citizen', $VIEW_DATA);
    }
    
    public function dsp_print_supplement_ho_for_citizen($record_id)
    {
        $stmt = 'Select R.*, RT.C_NAME as RECORD_TYPE_NAME, RT.C_CODE as RECORD_TYPE_CODE
                    From view_record R Left Join t_r3_record_type as RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                    Where R.PK_RECORD=?';
        $params = array($record_id);
        
        $this->model->db->debug=0;
        $VIEW_DATA['arr_single_record']     = $this->model->db->getRow($stmt, $params);
        
        $this->view->render('dsp_print_supplemt_ho_for_citizen', $VIEW_DATA);
    }
    
    public function dsp_print_guide_for_citizen()
    {
        $v_record_type_code = get_request_var('record_type_code');
        $v_record_type_name = get_request_var('record_type_name');
        
        $VIEW_DATA['v_record_type_code'] = $v_record_type_code;
        $VIEW_DATA['v_record_type_name'] = $v_record_type_name;
        
        $this->view->render('dsp_print_guide_for_citizen', $VIEW_DATA);
    }

    public function update_record()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->update_record();
    }

    public function delete_record()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->delete_record();
    }

    /**
     * In bien ban ban giao ho so
     * @param unknown_type $record_id_list
     */
    public function dsp_print_ho_for_bu($record_id_list)
    {
        $v_record_type_code = get_request_var('record_type_code');
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BAN_GIAO_ROLE;
        $v_type             = get_request_var('type',1);

        $arr_all_record                     = $this->model->qry_all_ho_record($record_id_list);
        $VIEW_DATA['arr_all_record']        = $arr_all_record;
        $VIEW_DATA['arr_single_task_info']  = $this->model->qry_single_task_info($v_task_code);

        if ($v_type ==1)
        {
            $this->view->render('dsp_print_ho_for_bu', $VIEW_DATA);
        }
        elseif ($v_type == 2)
        {
            $this->view->render('dsp_print_handover_back', $VIEW_DATA);
        }
    }
    
    /**
     * In bien ban banjao ho so bo sung cho phong chuyen mon
     * @param unknown_type $record_id_list
     */
    public function dsp_print_supplement_ho_for_bu($record_id_list)
    {
        $v_record_type_code = get_request_var('record_type_code');
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE;
        
        $VIEW_DATA['arr_all_record']        = $this->model->qry_all_record_bo_sung($v_record_type_code, 2);
        
        //Phong nhan ban giao: La phong yeu cau bo sung
        $v_ho_receive_group_name = $this->model->qry_ho_receive_group_name($record_id_list);
        
        $arr_single_task_info = $this->model->qry_single_task_info($v_task_code);
        
        $arr_single_task_info['C_RECEIVE_HO_GROUP_NAME'] = $v_ho_receive_group_name;
        $VIEW_DATA['arr_single_task_info']  = $arr_single_task_info;
        
        $this->view->render('dsp_print_supplement_ho_for_bu', $VIEW_DATA);
    }
    
    public function dsp_print_ho_for_tax($record_id_list)
    {
        $v_record_type_code = get_request_var('record_type_code');
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_NOP_HO_SO_SANG_CHI_CUC_THUE_ROLE;

        $arr_all_record                     = $this->model->qry_all_ho_for_tax_record($record_id_list, $v_task_code);
        $VIEW_DATA['arr_all_record']        = $arr_all_record;
        $VIEW_DATA['arr_single_task_info']  = $this->model->qry_single_task_info($v_task_code);
        
        $this->view->render('dsp_print_ho_for_tax', $VIEW_DATA);
    }

    public function dsp_print_record_list_to_handover_back()
    {
        $record_id_list     = get_request_var('record_id_list');
        $v_record_type_code = get_request_var('record_type_code');

        $arr_all_record = $this->model->qry_all_ho_back_record($record_id_list);
        $v_task_code        = $arr_all_record[0]['C_NEXT_TASK_CODE'];
        
        $VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);
        $VIEW_DATA['arr_all_record']       = $arr_all_record;
        
        $this->view->render('dsp_print_handover_back', $VIEW_DATA);
    }

    public function do_handover_record()
    {
        if (!isset($this->model->goback_url))
        {
            $this->model->goback_url = $this->view->get_role_url(strtolower(_CONST_BAN_GIAO_ROLE));
        }
        $this->model->do_handover_record();
    }
    public function do_handover_supplement_record()
    {
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_supplement_record_2';
        $this->model->do_handover_supplement_record();
        //$this->model->do_handover_record();
    }

    public function statistics($record_id)
    {
        $record_id = get_request_var('hdn_item_id',$record_id);
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
        $dom_workflow = $dom_step;
        $r = $dom_step->xpath('//step/@time');
        $step_days_list = '';
        foreach ($r as $time)
        {
            $step_days_list .= ($step_days_list != '') ? ";$time" : $time;
        }
        $arr_step_formal_date = $this->model->formal_record_step_days_to_date($record_id, $step_days_list);
        $VIEW_DATA['arr_step_formal_date'] = $arr_step_formal_date;
        
        //Lay danh sach step, ngay bat dau, ket thuc thuc te cua step, số ngày đã tiêu tốn thực tế để hoàn thành step
        $steps = $dom_workflow->xpath("//step[not(@no_chain = 'true')]");
        $arr_step_infact_formal_days_diff = array();
        foreach ($steps as $step)
        {
            $v_first_task_code = strval($step->task[0]->attributes()->code);
            $v_last_task_code  = strval($step->task[sizeof($step->task) - 1]->attributes()->code);
            
            //So ngay phai hoan thanh step theo quy dinh
            $v_formal_step_time = floatval(str_replace(',', '.', $step->attributes()->time));  
            
            //=>Ngay bat dau thuc te cua step, la ngay thuc hien prev_task
            $v_prev_task_code = get_xml_value($dom_workflow, "//task[contains(@next,'$v_first_task_code')]/@code");
            
            $v_step_begin_date_infact = get_xml_value($dom_processing, "//step[@code='$v_prev_task_code']/datetime");
            
            //=>Ngay ket thuc thuc te cua step
            $v_step_end_date_infact = get_xml_value($dom_processing, "//step[@code='$v_last_task_code']/datetime");
            
            //=>Số ngày làm việc tiêu tốn thực tế để hoàn thành step
            $v_step_spent_days_infact = $this->model->days_between_two_date($v_step_begin_date_infact, $v_step_end_date_infact);
            
            //=> Step nhanh hay cham bao nhieu ngày?            
            $v_step_infact_formal_days_diff = $v_formal_step_time - $v_step_spent_days_infact;           
            
            $arr_step_infact_formal_days_diff[md5($v_last_task_code)] = $v_step_infact_formal_days_diff;
        }
        $VIEW_DATA['arr_step_infact_formal_days_diff'] = $arr_step_infact_formal_days_diff;

        //Tai lieu mem
        $VIEW_DATA['arr_all_record_file']   = $this->model->qry_all_record_file($record_id);

        //Ý kiến
        $VIEW_DATA['arr_all_comment']   = $this->model->qry_all_record_comment($record_id);

        //Tai lieu
        $VIEW_DATA['arr_all_doc']       = $this->model->qry_all_record_doc($record_id);

        $this->view->render('dsp_single_record_statistics', $VIEW_DATA);
    }

    /**
     * Hiển thị màn hình thêm ý kiến cho 1 hồ sơ
     */
    public function dsp_add_comment()
    {
        $this->view->render('dsp_add_comment', NULL);
    }

    /**
     * Hiển thị màn hình thêm tài liệu cho 1 hồ sơ
     */
    public function dsp_add_doc()
    {
        $this->view->render('dsp_add_doc', NULL);
    }

    public function do_add_doc()
    {
    	$arr_all_doc = $this->model->do_add_doc();

    	$html = '';
    	for ($i=0; $i<sizeof($arr_all_doc); $i++)
    	{
    	    $v_disabled = ($arr_all_doc[$i]['user_code'] == Session::get('user_code')) ? '':' disabled';
        	$html .= '<tr data-did="d_' . $arr_all_doc[$i]['doc_id'] . '" data-user="' .  $arr_all_doc[$i]['user_code'] . '">';
        	$html .= '<td style="text-align: center;">';
        	$html .= '<input type="checkbox" name="chk_doc" value="' . $arr_all_doc[$i]['doc_id'] . '"'
        	                . $v_disabled . ' data-user="' . $arr_all_doc[$i]['user_code'] . '"/>';
        	$html .= '</td>';
        	$html .= '<td style="text-align: center;">' . $arr_all_doc[$i]['doc_no'] . '</td>';
        	$html .= '<td>' . $arr_all_doc[$i]['description'] . '</td>';
        	$html .= '<td>' . $arr_all_doc[$i]['issuer'] . '</td>';
        	$html .= '<td style="text-align: center;">' . $arr_all_doc[$i]['user_name'] . '</td>';
        	$html .= '<td>' . $arr_all_doc[$i]['create_date'] . '</td>';
        	$html .= '<td>';
            if ($arr_all_doc[$i]['xml_file_list'] != NULL)
            {
                $df = simplexml_load_string('<root>' . $arr_all_doc[$i]['xml_file_list'] . '</root>');
                $rows = $df->xpath('//row');
                foreach ($rows as $row)
                {
                    $v_file_name    = $row->attributes()->C_FILE_NAME;
                    $v_file_path    = SITE_ROOT . 'uploads/r3/' . $v_file_name;
                    $v_file_extension = array_pop(explode('.', $v_file_name));
                    $html .= '<img src="' . SITE_ROOT . 'public/images/' . $v_file_extension . '-icon.png" width="16px" height="16px"/>';
                    $html .= '<a href="' . $v_file_path .'" target="_blank">' .$v_file_name . '</a><br/>';
                };
            }
        	$html .= '</td>';
            $html .= '</tr>';
        }

        echo '<script src="' . SITE_ROOT . 'public/js/jquery/jquery.min.js" type="text/javascript"></script>';
        echo '<script>$("#tbl_doc_header", window.parent.document).after(\'' . $html . '\');window.parent.hidePopWin();</script>';
    	//echo json_encode($arr_all_new_doc);
    }

    public function do_delete_doc()
    {
        $this->model->do_delete_doc();
    }

    //Thong bao so luong ho so can xu ly theo Loai ho so
    public function notice($role)
    {
        $arr_all_notice = $this->model->qry_all_notice($role);
        echo json_encode($arr_all_notice);
    }

    //Thong bao so luong ho so can bo sung theo trang thai bo sung
    public function supplement_notice($status)
    {
        $arr_all_notice = $this->model->qry_all_supplement_notice($status);
        echo json_encode($arr_all_notice);
    }

    /**
     * Dem so luong HS can xu ly theo Role
     * @param type $role
     */
    public function count_processing_record_by_role($role)
    {
        echo json_encode($this->model->count_processing_record_by_role(strtoupper($role)));
    }

    public function dsp_json_record_comment($record_id)
    {
        $arr_all_comment = $this->model->qry_all_supplement_notice($status);
        echo json_encode($arr_all_notice);
    }

    /**
     * Hien thi man hinh phan cong thu ly ho so
     * @param int $record_id ID ho so
     */
    public function dsp_allot($record_id_list)
    {
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE;

        //Danh sách hồ sơ đã chọn để phân công
        $arr_all_record = $this->model->qry_all_record_for_task($record_id_list, $v_task_code);

        //Lấy chính xác mã công việc
        if (sizeof($arr_all_record) > 0)
        {
            $v_task_code = $arr_all_record[0]['C_NEXT_TASK_CODE'];
        }

        $VIEW_DATA['MY_TASK']        = $v_task_code;
        $VIEW_DATA['record_id_list'] = $record_id_list;
        $VIEW_DATA['arr_all_record'] = $arr_all_record;

        //Mã công việc tiếp theo
        $v_exec_task_code = $this->model->get_next_task_code($v_task_code);

        //Thoi gian thuc hien thu ly
        $v_exec_task_time = $this->model->db->getOne('Select C_TASK_TIME From t_r3_user_task Where C_TASK_CODE=?', array($v_exec_task_code));
        $VIEW_DATA['exec_task_time'] = $v_exec_task_time;
        //Tên nhóm thụ lý
        $VIEW_DATA['group_name'] = $this->model->get_group_name_by_task_code($v_exec_task_code);
        //Danh sách cán bộ tham gia vào bước thụ lý
        $VIEW_DATA['arr_all_exec_user'] = $this->model->qry_all_user_on_task($v_exec_task_code);

        $this->view->render('dsp_allot_record', $VIEW_DATA);
    }

    public function dsp_exec($record_id_list)
    {
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';

        //$task_code ??????????
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE;

        $VIEW_DATA['record_id_list'] = $record_id_list;
        $VIEW_DATA['arr_all_record'] = $this->model->qry_all_record_for_task($record_id_list, $v_task_code);
        $VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);

        $this->view->render('dsp_exec_record', $VIEW_DATA);
    }

    public function dsp_inter_exec($record_id_list)
    {
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';

        //$task_code ??????????
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE;

        $VIEW_DATA['record_id_list'] = $record_id_list;
        $VIEW_DATA['arr_all_record'] = $this->model->qry_all_record_for_task($record_id_list, $v_task_code);
        $VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);

        $this->view->render('dsp_exec_record', $VIEW_DATA);
    }
    public function dsp_reexec($record_id_list)
    {
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';

        //$task_code ??????????
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_YEU_CAU_THU_LY_LAI_ROLE;

        $VIEW_DATA['record_id_list'] = $record_id_list;
        $VIEW_DATA['arr_all_record'] = $this->model->qry_all_record_for_task($record_id_list, $v_task_code);
        $VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);

        $this->view->render('dsp_exec_record', $VIEW_DATA);
    }

    /**
     * Hien thi man hinh thu phi 1 ho so
     * @param int $record_id ID ho so
     */
    public function dsp_charging($record_id)
    {
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';

        $VIEW_DATA['record_id'] = $record_id;
        $v_xml_workflow_file_name = $this->view->get_xml_config($v_record_type_code, 'workflow');

        $arr_single_record = $this->model->qry_single_record($record_id, $v_record_type_code, $v_xml_workflow_file_name);
        $v_task_code = $arr_single_record['C_NEXT_TASK_CODE'];

        //next user
        $arr_all_next_user = $this->model->qry_all_user_on_next_task($v_task_code);

        $VIEW_DATA['arr_single_record']     = $arr_single_record;
        $VIEW_DATA['record_type_code']      = $v_record_type_code;
        $VIEW_DATA['arr_all_next_user']     = $arr_all_next_user;

        $this->view->render('dsp_charging_record', $VIEW_DATA);
    }

    public function do_exec_record()
    {
        $this->model->do_exec_record();
    }

    /**
     * Hiển thị màn hình thay đổi phân công thụ lý
     * @param type $record_id
     */
    public function dsp_reallot($record_id)
    {
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE;

        $VIEW_DATA['record_id'] = $record_id;
        $VIEW_DATA['arr_single_record'] = $this->model->qry_single_record($record_id);
        $VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);

        //Ma cong viec THU_LY
        $v_exec_task_code = $VIEW_DATA['arr_single_record']['C_NEXT_TASK_CODE'];//$v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE;
        $VIEW_DATA['arr_all_exec_user'] = $this->model->qry_all_user_on_task($v_exec_task_code);

        //Thoi gian thuc hien thu ly
        $v_exec_task_time = $this->model->db->getOne('Select C_TASK_TIME From t_r3_user_task Where C_TASK_CODE=?', array($v_exec_task_code));
        $VIEW_DATA['exec_task_time'] = $v_exec_task_time;

        $this->view->render('dsp_reallot_record', $VIEW_DATA);
    }

    public function do_allot_record()
    {
        $this->model->do_allot_record();
    }
    public function do_reallot_record()
    {
        $this->model->do_reallot_record();
    }

    public function dsp_approval($record_id_list)
    {
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_ROLE;

        $VIEW_DATA['record_id_list'] = $record_id_list;

        $arr_all_record = $this->model->qry_all_record_for_task($record_id_list, $v_task_code);
        //Gia tri chinh xac cua TASK_CODE (cong viec chuan bi thuc hien)
        if (count($arr_all_record) > 0)
        {
            $v_task_code = $arr_all_record[0]['C_NEXT_TASK_CODE'];
        }
        
        $VIEW_DATA['arr_all_record'] = $arr_all_record;
        $VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);

        $VIEW_DATA['arr_all_next_user'] = $this->model->qry_all_user_on_next_task($v_task_code);

        $this->view->render('dsp_approval_record', $VIEW_DATA);
    }
    /**
     * Hiển thị màn hình phê duyệt HS bổ sung
     * @param string $record_id_list
     */
    public function dsp_approval_supplement($record_id_list)
    {
    	$v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';
    	$v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_BO_SUNG_ROLE;

    	$VIEW_DATA['record_id_list'] = $record_id_list;
    	$arr_all_record = $this->model->qry_all_record_for_task($record_id_list, $v_task_code);
    	//Gia tri chinh xac cua TASK_CODE (cong viec chuan bi thuc hien)
    	if (count($arr_all_record) > 0)
    	{
    	    $v_task_code = $arr_all_record[0]['C_NEXT_TASK_CODE'];
    	}
    	$VIEW_DATA['arr_all_record'] = $arr_all_record;
    	$VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);
    	$VIEW_DATA['v_is_approve_supplement_record'] = '1';

    	//Next ?
    	$VIEW_DATA['arr_all_next_user'] = $this->model->qry_all_user_on_next_task($v_task_code);

    	$this->view->render('dsp_approval_record', $VIEW_DATA);
    }

    public function do_approval_record()
    {
        $this->model->do_approval_record();
    }

    public function do_reject_record()
    {
    	$this->model->do_reject_record();
    }

    public function do_rollback_record()
    {
        $this->model->do_rollback_record();
    }

    public function do_sign_record()
    {
        $this->model->goback_url = $this->view->get_role_url(strtolower(_CONST_KY_ROLE));
        $this->model->do_sign_record();
    }
    public function do_charging_record()
    {
        $this->model->do_charging_record();
    }

    public function do_return_record()
    {
        $this->model->goback_url = $this->view->get_role_url(strtolower(_CONST_TRA_KET_QUA_ROLE));
        $this->model->do_return_record();
    }

    private function _dsp_supplement_record($status)
    {
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_option();

        $v_record_type_code = $this->get_post_var('sel_record_type',array_shift(array_keys($VIEW_DATA['arr_all_record_type'])));

        $VIEW_DATA['record_type_code'] = $v_record_type_code;
        //1. Lay danh sach HS Phai bo sung, Chua thong bao
        $VIEW_DATA['arr_all_record']        = $this->model->qry_all_record_bo_sung($v_record_type_code, $status);

        $this->view->render('dsp_supplement_record_' . $status , $VIEW_DATA);
    }

    public function dsp_supplement_record_0()
    {
        $this->_dsp_supplement_record(0);
    }

    public function dsp_supplement_record_1()
    {
        $this->_dsp_supplement_record(1);
    }

    public function dsp_supplement_record_2()
    {
        $this->_dsp_supplement_record(2);
    }

    public function do_announce_record()
    {
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_supplement_record_0';
        $this->model->do_announce_record();
    }

    /**
     * Hiển thị thông tin chi tiết 1 HS để thực hiện bổ sung
     */
    public function dsp_single_record_supplement($record_id)
    {
        $this->view->template->active_role =  Session::get('active_role');

        $VIEW_DATA['arr_single_record']     = $this->model->qry_single_record($record_id, NULL, NULL);
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_option();

        $this->view->render('dsp_single_record_supplement', $VIEW_DATA);
    }

    public function do_supplement_record()
    {
        $this->model->do_supplement_record();
    }

    public function do_send_to_tax()
    {
        $this->model->goback_url = $this->view->get_role_url(strtolower(_CONST_NOP_HO_SO_SANG_CHI_CUC_THUE_ROLE));
        $this->model->do_send_to_tax();
    }

    /**
     *Nhận thông báo từ chi cục thuế
     */
    public function do_receive_tax()
    {
        $this->model->goback_url = $this->view->get_role_url(strtolower(_CONST_NHAN_THONG_BAO_CUA_CHI_CUC_THUE_ROLE));
        $this->model->do_receive_tax();
    }

    /**
     *Nhận biên lai (chứng mình đã nộp thuế) từ công dân
     */
    public function do_receive_tax_receipt()
    {
        $this->model->goback_url = $this->view->get_role_url(strtolower(_CONST_NHAN_BIEN_LAI_NOP_THUE_ROLE));
        $this->model->do_receive_tax_receipt();
    }


    public function do_submit_tax()
    {
        $this->model->goback_url = $this->view->get_role_url(strtolower(_CONST_CHUYEN_THONG_BAO_THUE_VE_BP_MOT_CUA_ROLE));
        $this->model->do_submit_tax();
    }

    public function do_return_tax_message()
    {
        $this->model->goback_url = $this->view->get_role_url(strtolower(_CONST_TRA_THONG_BAO_NOP_THUE_ROLE));
        $this->model->do_return_tax_message();
    }

    public function do_add_comment()
    {
        $arr_all_comment = $this->model->do_add_comment();
        echo json_encode($arr_all_comment);
    }

    public function do_delete_comment()
    {
        $this->model->do_delete_comment();
    }

    /**
     * Hiển thị màn hình từ chối HS
     * Do trưởng phòng chuyên môn thực hiện, ngay sau khi được bàn giao (Chưa phân công thụ lý)
     */
    public function dsp_reject($record_id_list)
    {
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_ROLE;

        $VIEW_DATA['record_id_list'] = $record_id_list;
        $VIEW_DATA['arr_all_record'] = $this->model->qry_all_record_for_allot($record_id_list);
        $VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);

        $this->view->render('dsp_reject_record', $VIEW_DATA);
    }

    /**
     * Hiển thị màn hình RollBack Ho So  (tra HS ve 1 cua)
     * @param type $record_id_list
     */
    public function dsp_rollback($record_id_list)
    {
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';

        $v_role = get_request_var('role');

        if ($v_role == '')
        {
            $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_ROLE;
        }
        else
        {
            $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . $v_role;
        }

        //echo '$v_task_code='.$v_task_code;

        $VIEW_DATA['record_id_list'] = $record_id_list;
        $VIEW_DATA['arr_all_record'] = $this->model->qry_all_record_for_task($record_id_list, $v_task_code);
        $VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);

        $this->view->render('dsp_rollback_record', $VIEW_DATA);
    }

    public function dsp_resend_confirmation_request_record($record_id_list)
    {
        $v_user_code = Session::get('user_code');
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';
        $v_task_code        = _CONST_XML_RTT_DELIM . _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE;

        $VIEW_DATA['record_id_list'] = $record_id_list;
        $VIEW_DATA['arr_all_record'] = $arr_all_record = $this->model->qry_all_record_for_task($record_id_list, $v_task_code);
        $VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);

        //Danh sach nguoi nhan yeu cau xac nhan lai
        //Mã công việc tiếp theo
        if (sizeof($arr_all_record) > 0)
        {
            $v_exec_task_code = $this->model->db->getOne(
                                'Select distinct C_NEXT_TASK_CODE From t_r3_user_task Where C_TASK_CODE Like ? And C_USER_LOGIN_NAME=?'
                                , array('%' . $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_CHUYEN_YEU_CAU_XAC_NHAN_XUONG_XA_ROLE
                                        , $v_user_code
                                  )
                            );
        }
        $VIEW_DATA['arr_all_exec_user'] = $this->model->qry_all_user_on_task($v_exec_task_code);

        $this->view->render('dsp_resend_confirmation_request_record', $VIEW_DATA);
    }
    public function do_resend_confirmation_request_record()
    {
        $this->model->do_resend_confirmation_request_record();
    }

    /*------------------------------------------------------------------------*/

    public function ho_so($role='')
    {
        if ($role == '')
        {
            $role = $this->_arr_user_role[0];
        }
        if (method_exists($this, $role))
        {
            $this->dsp_all_record($role);
        }
        else
        {
            $this->_regular_role($role);
        }
    }

    private function _get_my_first_role()
    {
        //return isset($this->_arr_user_role[0]) ? $this->_arr_user_role[0] : '';
        foreach ($this->_arr_user_role as $key)
        {
            if ($this->check_permission(strtoupper($key)))
            {
                return $key;
            }
        }

        return '';
    }

    /* Bo phan mot cua */
    private function tiep_nhan()
    {
        $this->_regular_role(__FUNCTION__);
    }

    private function ban_giao()
    {
        $this->_regular_role(__FUNCTION__);return FALSE;
        
        $role = _CONST_BAN_GIAO_ROLE;
        //error_reporting(E_ALL);
        //$this->model->db->debug=1;
        
        //Liennd Update 2013-02-22: In ban giao theo noi nhan (khong theo thu tuc)
        $arr_all_group = $this->model->qry_all_group_to_handover();
        
        $v_group_code = get_post_var('sel_group');
        if ($v_group_code == '')
        {
            $v_group_code = array_shift(array_keys($arr_all_group));
        }
        
        $arr_all_record = $this->model->qry_all_record_to_handover($v_group_code);       
        //echo 'Line:'.__LINE__.'<br>File:'.__FILE__;var_dump::display($arr_all_record);

        $VIEW_DATA['active_role']           = strtoupper($role);
        $VIEW_DATA['active_role_text']      = $this->_arr_roles[strtoupper($role)];
        $VIEW_DATA['arr_all_group']         = $arr_all_group;
        $VIEW_DATA['v_group']               = $v_group_code;
        
        $v_dsp_file = 'dsp_all_record_' . strtolower($role);
        $VIEW_DATA['MY_TASK'] =  _CONST_XML_RTT_DELIM . strtoupper($role);
        if (sizeof($arr_all_record) > 0)
        {
            $VIEW_DATA['MY_TASK']           = $arr_all_record[0]['C_NEXT_TASK_CODE'];
        }
        $VIEW_DATA['arr_all_record']        = $arr_all_record;
        
        $v_view_file = SERVER_ROOT . 'apps' .DS . $this->app_name . DS . 'modules'.DS . $this->module_name . DS . $this->module_name . '_views'. DS . $v_dsp_file . '.php';
        if (file_exists($v_view_file))
        {
            $this->view->render($v_dsp_file, $VIEW_DATA);
        }
        else
        {
            $this->view->render('dsp_all_record_regular_role', $VIEW_DATA);
        }
        
        
    }

    private function xac_nhan_ho_so_nop_qua_internet()
    {
        $role = strtoupper(__FUNCTION__);

        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_internet_record_type_option();

        //Nếu không chọn loại HS, lấy loại HS đầu tiên trong danh sách
        $v_record_type_code = $this->_record_type;
        if ($v_record_type_code == '')
        {
            $v_record_type_code = array_shift(array_keys($VIEW_DATA['arr_all_record_type']));
        }

        $arr_all_record                     = $this->model->qry_all_record_by_role($role, $v_record_type_code);
        $VIEW_DATA['record_type_code']      = $v_record_type_code;
        $VIEW_DATA['active_role']           = strtoupper($role);
        $VIEW_DATA['active_role_text']      = $this->_arr_roles[strtoupper($role)];

        $v_dsp_file = 'dsp_all_record_' . strtolower($role);

        $VIEW_DATA['MY_TASK'] = $v_record_type_code . _CONST_XML_RTT_DELIM . strtoupper($role);
        if (sizeof($arr_all_record) > 0)
        {
            $VIEW_DATA['MY_TASK']           = $arr_all_record[0]['C_NEXT_TASK_CODE'];
        }

        $VIEW_DATA['arr_all_record']        = $arr_all_record;

        $this->view->render('dsp_all_record_xac_nhan_ho_so_nop_qua_internet', $VIEW_DATA);
    }

    private function bo_sung()
    {
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_option();
        $VIEW_DATA['record_type_code']      = $this->_record_type;
        $VIEW_DATA['active_role']           = strtoupper(__FUNCTION__);
        $VIEW_DATA['active_role_text']      = $this->_arr_roles[strtoupper(__FUNCTION__)];

        /*
        //1. Lay danh sach HS Phai bo sung, Chua thong bao
        $VIEW_DATA['arr_all_record_0']        = $this->model->qry_all_record_bo_sung($this->_record_type, 0);

        //2. Lay danh sach HS Phai bo sung, Đã thong bao
        $VIEW_DATA['arr_all_record_1']        = $this->model->qry_all_record_bo_sung($this->_record_type, 1);

        //3. lay danh sach HS Phai bo Sung, Đã nhận bo sung
        $VIEW_DATA['arr_all_record_2']        = $this->model->qry_all_record_bo_sung($this->_record_type, 2);
         *
         */

        $v_dsp_file = 'dsp_all_record_' . strtolower(__FUNCTION__);

        $this->view->render($v_dsp_file, $VIEW_DATA);
    }

    private function thu_phi()
    {
        $this->_regular_role(__FUNCTION__);
    }

    private function tra_ket_qua()
    {
        $this->_regular_role(__FUNCTION__);
    }

    private function in_phieu_tiep_nhan()
    {
        $role = strval(__FUNCTION__);
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_option();

        //Nếu không chọn loại HS, lấy loại HS đầu tiên trong danh sách
        $v_record_type_code = $this->_record_type;
        if ($v_record_type_code == '')
        {
            $v_record_type_code = array_shift(array_keys($VIEW_DATA['arr_all_record_type']));
        }
        $arr_all_record                     = $this->model->qry_all_record_in_lai_phieu_tiep_nhan($v_record_type_code);
        $VIEW_DATA['record_type_code']      = $v_record_type_code;
        $VIEW_DATA['active_role']           = strtoupper($role);
        $VIEW_DATA['active_role_text']      = $this->_arr_roles[strtoupper($role)];

        $v_dsp_file = 'dsp_all_record_' . __FUNCTION__;

        if (sizeof($arr_all_record) > 0)
        {
            $VIEW_DATA['MY_TASK']           = $arr_all_record[0]['C_NEXT_TASK_CODE'];
        }

        $VIEW_DATA['arr_all_record']        = $arr_all_record;

        $this->view->render($v_dsp_file, $VIEW_DATA);

    }

    private function y_kien_lanh_dao()
    {
        $role = strval(__FUNCTION__);
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_option();

        //Nếu không chọn loại HS, lấy loại HS đầu tiên trong danh sách
        $v_record_type_code = $this->get_post_var('txt_record_type_code' ,'');


        $arr_all_record                     = $this->model->qry_all_record_y_kien_lanh_dao($v_record_type_code);
        $VIEW_DATA['record_type_code']      = strtoupper($v_record_type_code);
        $VIEW_DATA['active_role']           = strtoupper($role);
        $VIEW_DATA['active_role_text']      = $this->_arr_roles[strtoupper($role)];

        $v_dsp_file = 'dsp_all_record_' . __FUNCTION__;

        if (sizeof($arr_all_record) > 0)
        {
            $VIEW_DATA['MY_TASK']           = $arr_all_record[0]['C_NEXT_TASK_CODE'];
        }

        $VIEW_DATA['arr_all_record']        = $arr_all_record;

        $this->view->render($v_dsp_file, $VIEW_DATA);
    }

    private function nop_ho_so_sang_chi_cuc_thue()
    {
        $this->_regular_role(__FUNCTION__);
    }
    private  function nhan_thong_bao_cua_chi_cuc_thue()
    {
        $this->_regular_role(__FUNCTION__);
    }
    private function chuyen_thong_bao_thue_ve_bp_mot_cua()
    {
        $this->_regular_role(__FUNCTION__);
    }

    /* Bo phan chuyen mon */
    private function phan_cong()
    {
        $this->_regular_role(__FUNCTION__);
    }

    private function thu_ly()
    {
        $this->_regular_role(__FUNCTION__);
    }
    private function thu_ly_ho_so_lien_thong()
    {
        $this->_regular_role(__FUNCTION__);
    }
    private function yeu_cau_thu_ly_lai()
    {
        $this->_regular_role(__FUNCTION__);
    }

    private function xet_duyet()
    {
        $this->_regular_role(__FUNCTION__);
    }

	private function xet_duyet_bo_sung()
    {
    	$this->_regular_role(__FUNCTION__);
    }


    /* Lanh dao don vi */
    private function ky_duyet()
    {
        $this->_regular_role(__FUNCTION__);
    }

    private function bao_cao()
    {
        header('location:'.$this->view->get_controller_url('report',$this->app_name));
    }

    private function tra_thong_bao_nop_thue()
    {
        $this->_regular_role(__FUNCTION__);
    }

    private function nhan_bien_lai_nop_thue()
    {
        $this->_regular_role(__FUNCTION__);
    }

    private function chuyen_len_huyen()
    {
        $this->model->goback_url = $this->view->get_controller_url(strtolower(__FUNCTION__));
        $this->_regular_role(__FUNCTION__);
    }
    private function chuyen_xuong_xa()
    {
        $this->_regular_role(__FUNCTION__);
    }

    private function tra_ho_so_ve_xa()
    {
        $this->_regular_role(__FUNCTION__);
    }

    private function chuyen_yeu_cau_xac_nhan_xuong_xa()
    {
        $this->_regular_role(__FUNCTION__);
    }
    private function xac_nhan_lien_thong()
    {
        $this->_regular_role(__FUNCTION__);
    }

    private function phan_cong_lai()
    {
        $role = strtoupper(__FUNCTION__);

        $this->view->template->active_role =  $role;
        $this->view->template->controller_url = $this->view->get_controller_url();
        $this->view->active_role =  $role;

        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_option();

        //Nếu không chọn loại HS, lấy loại HS đầu tiên trong danh sách
        $v_record_type_code = $this->_record_type;
        if ($v_record_type_code == '')
        {
            $v_record_type_code = array_shift(array_keys($VIEW_DATA['arr_all_record_type']));
        }

        //Lay danh sach HS ma NSD da phan cong, chưa hoàn thành thụ lý
        $arr_all_record = $this->model->qry_all_alloted_record($v_record_type_code);

        $VIEW_DATA['arr_all_record']        = $arr_all_record;
        $VIEW_DATA['record_type_code']      = $v_record_type_code;
        $VIEW_DATA['active_role']           = strtoupper($role);
        $VIEW_DATA['active_role_text']      = $this->_arr_roles[strtoupper($role)];

        $v_dsp_file = 'dsp_all_record_' . strtolower($role);

        $this->view->render($v_dsp_file, $VIEW_DATA);
    }

    /**
     * Lấy ngày kết thúc việc thực hiện Công việc (step) theo quy định
     * @param type $begin_date_yyyymmdd ngày bắt đầu
     * @param type $days Số ngày quy định thực hiện công việc
     */
    public function get_step_formal_finish_date()
    {
        $begin_date_yyyymmddhhmmss = isset($_GET['begin_date_yyyymmddhhmmss']) ? replace_bad_char($_GET['begin_date_yyyymmddhhmmss']) : date('Y-m-d');
        $days = isset($_GET['days']) ? replace_bad_char($_GET['days']) : 0;

        $v_date = date('Y-m-d', strtotime($begin_date_yyyymmddhhmmss));
        $v_hour = intval(date('H', strtotime($begin_date_yyyymmddhhmmss)));

        $ret = '';
        if ($days == 0)
        {
            //Bat dau sang, ket thuc sang; Bat dau chieu, ket thuc chieu
            $ret = $v_date . chr(32);
            $ret .= ($v_hour < 12) ? _CONST_MORNING_END_WORKING_TIME : _CONST_AFTERNOON_END_WORKING_TIME;
        }
        elseif ($days == 0.5)
        {
            //Bat dau sang, ket thuc chieu;
            if ($v_hour < 12)
            {
                $ret = $v_date . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
            }
            //Bat dau chieu, ket thuc sang ngay lam viec tiep theo
            else
            {
                $ret = $this->model->next_working_day(1, $v_date) . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
            }
        }
        else
        {
            $days = intval($days);
            $this->model->db->debug=0;
            //Cong ngay lam viec,
            //bat dau sang, ket thuc sang; bat dau chieu, ket thuc chieu
            $ret = $this->model->next_working_day($days, $v_date) . chr(32);
            $ret .= ($v_hour < 12) ? _CONST_MORNING_END_WORKING_TIME : _CONST_AFTERNOON_END_WORKING_TIME;
        }

        $return = array(
            'date_yyyymmdd' => $ret,
            'date_ddmmyyyy' => jwDate::yyyymmdd_to_ddmmyyyy($ret, 1),
            'date_text' => $this->view->return_date_by_text($ret)
        );
        echo json_encode($return);
    }

    private function tra_cuu()
    {
        $this->view->template->show_left_side_bar = TRUE;
        $this->view->template->activity_filter = $this->_activity_filter;

        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_option();
        //Nếu không chọn loại HS, lấy loại HS đầu tiên trong danh sách
        $v_record_type_code = $this->_record_type;
        if ($v_record_type_code == '')
        {
            //$v_record_type_code = array_shift(array_keys($VIEW_DATA['arr_all_record_type']));
        }

        //Luu dieu kien loc
        $activity_filter = get_request_var('tt',0);

        $arr_all_record                     = $this->model->qry_all_record_for_lookup($v_record_type_code, $activity_filter) ;
        $VIEW_DATA['record_type_code']      = $v_record_type_code;
        $VIEW_DATA['active_role']           = strtoupper(__FUNCTION__);
        $VIEW_DATA['active_role_text']      = $this->_arr_roles[strtoupper(__FUNCTION__)];
        $VIEW_DATA['arr_all_record']        = $arr_all_record;

        $this->view->render('dsp_all_record_tra_cuu', $VIEW_DATA);
    }

    public function count_record_by_activity()
    {
        //echo json_encode($this->model->count_record_by_activity($activity));
        $a = array();
        foreach ($this->_activity_filter as $k=>$v)
        {
            $a[$k] = $this->model->count_record_by_activity($k);
        }

        echo json_encode($a);
    }

    public function dsp_sign($record_id_list)
    {
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_KY_ROLE;

        $VIEW_DATA['record_id_list'] = $record_id_list;

        $arr_all_record = $this->model->qry_all_record_for_task($record_id_list, $v_task_code);
        //Gia tri chinh xac cua TASK_CODE
        if (count($arr_all_record) > 0){
            $v_task_code = $arr_all_record[0]['C_NEXT_TASK_CODE'];

            //Xac dinh phong ban da trinh HS
            $v_xml_processing = $arr_all_record[0]['C_XML_PROCESSING'];
            $d = simplexml_load_string($v_xml_processing);
            $r = $d->xpath('//step[last()]/@code');

            $stmt = 'Select C_NAME From t_cores_group Where C_CODE In (Select C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE=?)';
            $params = array($r[0]);
            $VIEW_DATA['submit_group_name'] = $this->model->db->getOne($stmt, $params);
        }
        else
        {
            die();
        }

        $VIEW_DATA['arr_all_record'] = $arr_all_record;
        $VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);

        $VIEW_DATA['arr_all_next_user'] = $this->model->qry_all_user_on_next_task($v_task_code);

        $this->view->render('dsp_sign_record', $VIEW_DATA);
    }

    /**
     * Hiển thị màn hình gửi yêu cầu xác nhận hồ sơ xuống xã
     */
    public function dsp_send_confirmation_request($record_id_list)
    {
        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_CHUYEN_YEU_CAU_XAC_NHAN_XUONG_XA_ROLE;

        //Danh sách hồ sơ đã chọn để phân công
        $arr_all_record = $this->model->qry_all_record_for_task($record_id_list, $v_task_code);

        //Lấy chính xác mã công việc
        if (sizeof($arr_all_record) > 0)
        {
            $v_task_code = $arr_all_record[0]['C_NEXT_TASK_CODE'];
        }

        $VIEW_DATA['MY_TASK']        = $v_task_code;
        $VIEW_DATA['record_id_list'] = $record_id_list;
        $VIEW_DATA['arr_all_record'] = $arr_all_record;

        //Mã công việc tiếp theo
        $v_exec_task_code = $this->model->get_next_task_code($v_task_code);

        //Thoi gian thuc hien thu ly
        $v_exec_task_time = $this->model->db->getOne('Select C_TASK_TIME From t_r3_user_task Where C_TASK_CODE=?', array($v_exec_task_code));
        $VIEW_DATA['exec_task_time'] = $v_exec_task_time;
        //Tên nhóm thụ lý
        $VIEW_DATA['group_name'] = $this->model->get_group_name_by_task_code($v_exec_task_code);
        //Danh sách cán bộ tham gia vào bước thụ lý
        $VIEW_DATA['arr_all_exec_user'] = $this->model->qry_all_user_on_task($v_exec_task_code);

        $this->view->render('dsp_send_confirmation_request_record', $VIEW_DATA);
    }

    public function do_send_confirmation_request_record()
    {
        $this->model->do_send_confirmation_request_record();
    }

    public function dsp_send_confirmation_response($record_id_list)
    {
        if (!( preg_match( '/^\d*$/', trim($record_id_list)) == 1 ))
        {
            $record_id_list = isset($_POST['hdn_item_id']) ? $_POST['hdn_item_id'] : 0;
        }
        if (!( preg_match( '/^\d*$/', trim($record_id_list)) == 1 ))
        {
            $record_id_list = 0;
        }

        $v_record_type_code = isset($_REQUEST['record_type_code']) ? replace_bad_char($_REQUEST['record_type_code']) : '';
        $v_task_code        = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XAC_NHAN_HO_SO_LIEN_THONG_ROLE;

        $VIEW_DATA['record_id_list'] = $record_id_list;

        $arr_all_record = $this->model->qry_all_record_for_task($record_id_list, $v_task_code);
        //Gia tri chinh xac cua TASK_CODE
        if (count($arr_all_record) > 0){
            $v_task_code = $arr_all_record[0]['C_NEXT_TASK_CODE'];
        }
        $VIEW_DATA['arr_all_record'] = $arr_all_record;
        $VIEW_DATA['arr_single_task_info'] = $this->model->qry_single_task_info($v_task_code);

        $this->view->render('dsp_send_confirmation_response_record', $VIEW_DATA);
    }

    public function do_send_confirmation_response_record()
    {
        $this->model->do_send_confirmation_response_record();
    }


    /*Ho so tiep nhan qua internet */
    public function dsp_single_internet_record($record_id)
    {
        //Kiem tra quyen
        $this->check_permission(_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE) OR die($this->access_denied());

        $this->view->template->active_role =  Session::get('active_role');

        $v_record_type = trim($this->get_post_var('sel_record_type'));
        $MY_TASK = trim($this->get_post_var('MY_TASK'));

        $v_xml_workflow_file_name = $this->view->get_xml_config($v_record_type, 'workflow');

        $VIEW_DATA['arr_single_record']     = $this->model->qry_single_internet_record($record_id, $v_xml_workflow_file_name);
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_internet_record_type_option();
        $VIEW_DATA['record_type_code']      = $v_record_type;
        $VIEW_DATA['MY_TASK']               = $MY_TASK;

        $VIEW_DATA['arr_all_record_file']   = $this->model->qry_all_internet_record_file($record_id);

        $this->view->render('dsp_single_internet_record', $VIEW_DATA);
    }

    public function update_internet_record()
    {
        $this->model->goback_url = $this->view->get_role_url(strtolower(_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE)) ;
        $this->model->update_internet_record();
    }

    public function do_delete_internet_record()
    {
        $this->model->goback_url = $this->view->get_role_url(strtolower(_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE)) ;
        $this->model->do_delete_internet_record();
    }

    public function do_accept_internet_record()
    {
        $this->model->goback_url = $this->view->get_role_url(strtolower(_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE)) ;
        $this->model->do_accept_internet_record();
    }

    public function dsp_accept_internet_record($record_id)
    {
        //Kiem tra quyen
        $this->check_permission(_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE) OR die($this->access_denied());

        $this->view->template->active_role =  Session::get('active_role');

        $v_record_type = get_request_var('record_type_code');
        $v_xml_workflow_file_name = $this->view->get_xml_config($v_record_type, 'workflow');

        $VIEW_DATA['arr_single_record']     = $this->model->qry_single_internet_record($record_id,$v_xml_workflow_file_name);
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_internet_record_type_option();
        $VIEW_DATA['record_type_code']      = $v_record_type;
        $VIEW_DATA['v_xml_workflow_file_name']      = $v_xml_workflow_file_name;

        $VIEW_DATA['arr_all_record_file']   = $this->model->qry_all_internet_record_file($record_id);
        $this->view->render('dsp_accept_internet_record', $VIEW_DATA);
    }

    public function dooooooooooooooooooooo_update_next_task()
    {
        $this->model->dooooooooooooooooooooo_update_next_task();
    }


    public function arp_calc_return_date($working_days)
    {
        $v_format = get_request_var('format', 'yyyymmdd');

        if ($v_format == 'yyyymmdd')
        {
            $this->arp_calc_return_date_yyyymmdd($working_days);
        }
        else
        {
            $this->arp_calc_return_date_ddmmyyyy($working_days);
        }
    }
    public function arp_calc_return_date_yyyymmdd($working_days=1)
    {
        if (DEBUG_MODE < 10) {$this->model->db->debug = 0;}

        $v_return_date = $this->model->next_working_day($working_days);

        echo $v_return_date;

        $this->model->db->debug = DEBUG_MODE;
    }
    public function arp_calc_return_date_ddmmyyyy($working_days=1)
    {
        if (DEBUG_MODE < 10) {$this->model->db->debug = 0;}

        $v_return_date = $this->model->next_working_day($working_days);

        echo jwdate::yyyymmdd_to_ddmmyyyy($v_return_date, 1);

        $this->model->db->debug = DEBUG_MODE;
    }

    public function arp_calc_working_days()
    {
        if (DEBUG_MODE < 10) {$this->model->db->debug=0;}

        $date = jwDate::ddmmyyyy_to_yyyymmdd(get_request_var('return_date'));

        echo $this->model->days_between_two_date(Date('Y-m-d'), $date);

        $this->model->db->debug = DEBUG_MODE;
    }

    public function dsp_print_supplement_request()
    {
        $this->view->render('dsp_print_supplement_request');
    }
    public function dsp_print_reject_record()
    {
        $this->view->render('dsp_print_reject_record');
    }
    
    public function dsp_print_record_list_to_sign()
    {
        $v_record_list = get_request_var('record_id_list','');

        $VIEW_DATA['v_record_list'] = $v_record_list;

        $this->view->render('dsp_print_record_list_to_sign', $VIEW_DATA);
    }

    public function dsp_record_result($v_record_id_list)
    {
        $VIEW_DATA['v_record_id'] = $v_record_id_list;
        $VIEW_DATA['record_type'] = get_request_var('record_type');
        $this->view->render('dsp_record_result', $VIEW_DATA);
    }

    private function chuyen_ho_so_len_so()
    {
        $this->_regular_role(__FUNCTION__);
    }
    private function nhan_ho_so_tu_so()
    {
        $this->_regular_role(__FUNCTION__);
    }

    public function do_go_forward_record()
    {
        $v_record_id_list = get_post_var('hdn_item_id_list');
        $v_record_id_list = get_post_var('hdn_item_id_list');
        $this->model->goback_url = $this->view->get_controller_url(). 'dsp_all_record';
        $this->model->do_go_forward_record($v_record_id_list);
    }
    
    public function admin($object='')
    {
        switch ($object)
        {
            case 'record_type':
                echo 'Quan tri Loai HS o day';
                $v_action = get_request_var('action');
                switch ($v_action)
                {
                    case 'dsp_single_record_type':
                        echo 'dsp_single_record_type';
                        break;

                    case 'update_record_type':
                        echo 'update_record_type';
                        break;
                        
                    default: 
                        echo 'dsp_all_record_type';
                        break;
                }
                break;
                
            case 'workflow':
                echo 'quan tri quy trinh o day';
                break;
            
            default:
                die('Khong co gi o day!');
                break;
        }
    }
    
    public function dsp_print_announce_for_citizen($record_id)
    {
        $record_id = replace_bad_char($record_id);
        $VIEW_DATA['arr_single_record'] = $this->model->qry_single_record($record_id);
        
        $this->view->render('dsp_print_announce_for_citizen', $VIEW_DATA);
    }
}
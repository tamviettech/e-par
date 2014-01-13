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

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class record_type_Controller extends Controller
{

    /**
     *
     * @var \record_type_Model 
     */
    public $model;

    /**
     *
     * @var \View 
     */
    public $view;

    function __construct()
    {
        parent::__construct('r3', 'record_type');
        $this->view->template->show_left_side_bar = FALSE;

        $this->view->template->app_name = 'R3';

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

    private function _save_filter()
    {
        $v_filter        = $this->get_post_var('txt_filter');
        $v_rows_per_page = $this->get_post_var('sel_rows_per_page', _CONST_DEFAULT_ROWS_PER_PAGE);
        $v_page          = $this->get_post_var('sel_goto_page', 1);

        return array(
            'txt_filter'        => $v_filter,
            'sel_rows_per_page' => $v_rows_per_page,
            'sel_goto_page'     => $v_page,
        );
    }

    public function main()
    {
        $this->dsp_all_record_type();
    }

    public function dsp_all_record_type()
    {
        $arr_filter = $this->_save_filter();

        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_record_type($arr_filter);
        $VIEW_DATA['arr_filter']          = $arr_filter;

        $this->view->render('dsp_all_record_type', $VIEW_DATA);
    }

    public function delete_record_type()
    {
        $arr_filter = $this->_save_filter();

        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_record_type';
        $this->model->delete_record_type($arr_filter);
    }

    public function dsp_single_record_type()
    {
        $v_record_type_id = $this->get_post_var('hdn_item_id', 0);
        if (!( preg_match('/^\d*$/', trim($v_record_type_id)) == 1 ))
        {
            $v_record_type_id = 0;
        }


        $VIEW_DATA['arr_all_spec']           = $this->model->assoc_list_get_all_by_listtype_code('DANH_MUC_LINH_VUC');
        $VIEW_DATA['arr_single_record_type'] = $this->model->qry_single_record_type($v_record_type_id);
        $VIEW_DATA['arr_all_report_books']   = $this->model->qry_all_report_books($v_record_type_id);

        $arr_filter              = $this->_save_filter();
        $VIEW_DATA['arr_filter'] = $arr_filter;

        $this->view->render('dsp_single_record_type', $VIEW_DATA);
    }

    public function update_record_type()
    {
        $arr_filter = $this->_save_filter();

        $this->model->goback_url    = $this->view->get_controller_url() . 'dsp_all_record_type';
        $this->model->goforward_url = $this->view->get_controller_url() . 'dsp_single_record_type/0/';
        $this->model->update_record_type($arr_filter);
    }

    public function dsp_plaintext_form_struct()
    {
        $this->view->render('dsp_plaintext_form_struct');
    }

    public function update_plaintext_form_struct()
    {
        $v_xml_file_path = get_post_var('hdn_xml_file_path', '', 0);
        $v_xml_string    = get_post_var('txt_xml_string', '<root/>', 0);

        $r = write_xml_file($v_xml_string, $v_xml_file_path);

        $ok = TRUE;
        if ($r == 1)
        {
            $ok = FALSE;
            $v_message .= 'XML không well-form';
        }
        elseif ($r == 2)
        {
            $ok = FALSE;
            $v_message .= 'Không thể ghi được file dữ liệu!';
        }

        if ($ok)
        {
            $this->model->popup_exec_done();
        }
        else
        {
            $this->model->popup_exec_fail($v_message);
        }
    }

    private function create_temp_workflow()
    {
        error_reporting(E_ALL);
        $arr_all_rt = $this->model->db->getAssoc("SELECT TRIM(C_CODE) C_CODE, C_NAME FROM t_r3_record_type WHERE C_CODE like 'TN%' Order BY C_CODE");

        $v_xml_config_dir = SERVER_ROOT . "apps\\r3\\xml-config\\";

        foreach ($arr_all_rt as $code => $name)
        {
            $v_rt_dir = $v_xml_config_dir . $code . DS;
            if (!is_dir($v_rt_dir))
            {
                mkdir($v_rt_dir, '0777');
            }

            $v_xml_flow_string = '<?xml version="1.0"?>
            <process code="CODE_CODE_CODE" name="NAME_NAME_NAME" totaltime="20" version="1" fee="50.000">
                <step order="1" group="BP_MOT_CUA" name="Tiếp nhận hồ sơ" time="0.5" role="TIEP_NHAN">
                    <task code="CODE_CODE_CODE::TIEP_NHAN" name="Tiếp nhận hồ sơ" time="0" next="CODE_CODE_CODE::BAN_GIAO" single_user="true" />
                    <task code="CODE_CODE_CODE::BAN_GIAO" name="Bàn giao hồ sơ" time="0" next="CODE_CODE_CODE::TRA_KET_QUA" single_user="true" />
                </step>

                <step order="2" group="BP_MOT_CUA" name="Trả kết quả" time="0.5" role="TRA_KET_QUA" >
                    <task code="CODE_CODE_CODE::TRA_KET_QUA" name="Trả kết quả" time="1" next="NULL" single_user="true"/>
                    <!-- next="NULL": Kết thúc quy trình -->
                </step>
            </process>';

            $v_xml_flow_string = str_replace('CODE_CODE_CODE', $code, $v_xml_flow_string);
            $v_xml_flow_string = str_replace('NAME_NAME_NAME', $name, $v_xml_flow_string);

            $v_flow_file_path = $v_rt_dir . $code . '_workflow.xml';
            file_put_contents($v_flow_file_path, $v_xml_flow_string);
        }
    }

    private function create_new_flow_like_TN01()
    {
        error_reporting(E_ALL);
        $arr_all_rt = $this->model->db->getAssoc("SELECT TRIM(C_CODE) C_CODE, C_NAME FROM t_r3_record_type WHERE C_CODE IN ('TN02','TN04','TN05','TN06','TN07','TN08','TN30','TN31','TN33') Order By C_CODE");

        //echo 'Line: ' . __LINE__ . '<br>File: ' . __FILE__;var_dump::display($arr_all_rt);

        $v_xml_config_dir = SERVER_ROOT . "apps\\r3\\xml-config\\";
        foreach ($arr_all_rt as $code => $name)
        {
            $v_rt_dir = $v_xml_config_dir . $code . DS;
            if (!is_dir($v_rt_dir))
            {
                mkdir($v_rt_dir, '0777');
            }

            $v_org_TN01_flow_string = file_get_contents($v_xml_config_dir . 'TN01\\' . 'TN01_workflow.xml');
            $v_new_xml_flow_string  = str_replace('TN01', $code, $v_org_TN01_flow_string);

            $dom                       = simplexml_load_string(xml_add_declaration($v_new_xml_flow_string));
            $p                         = $dom->xpath('/process');
            $dom_p                     = $p[0];
            $dom_p->attributes()->name = $name;

            $v_new_flow_file_path = $v_rt_dir . $code . '_workflow.xml';
            file_put_contents($v_new_flow_file_path, $dom_p->saveXML());

            echo 'Line: ' . __LINE__ . '<br>File: ' . __FILE__;
            var_dump::display($dom_p->saveXML());
            /*
              $v_xml_flow_string = str_replace('CODE_CODE_CODE', $code, $v_xml_flow_string);
              $v_xml_flow_string = str_replace('NAME_NAME_NAME', $name, $v_xml_flow_string);
             *
             */
        }
    }

    public function create_lien_thong()
    {
        //Danh sach thu tuc lien thong
        $arr_cross_record_type = array(
            'LT20',
            'LT21',
            'LT22',
            'LT23',
            'LT24',
            'LT25',
            'LT26',
            'LT27',
        );

        $arr_xa_phuong = array(
            'X01' => 'XA_AN_HA',
            'X02' => 'XA_DINH_TRI',
            'X03' => 'XA_DUONG_DUC',
            'X04' => 'XA_DAI_LAM',
            'X05' => 'XA_DAO_MY',
            'X06' => 'XA_HUONG_LAC',
            'X07' => 'XA_HUONG_SON',
            'X08' => 'XA_MY_HA',
            'X09' => 'XA_MY_THAI',
            'X10' => 'XA_NGHIA_HOA',
            'X11' => 'XA_NGHIA_HUNG',
            'X12' => 'XA_PHI_MO',
            'X13' => 'XA_QUANG_THINH',
            'X14' => 'XA_TAN_DINH',
            'X15' => 'XA_TAN_HUNG',
            'X16' => 'XA_TAN_THANH',
            'X17' => 'XA_TAN_THINH',
            'X18' => 'XA_THAI_DAO',
            'X19' => 'XA_TIEN_LUC',
            'X20' => 'XA_XUAN_HUONG',
            'X21' => 'XA_XUONG_LAM',
            'X22' => 'XA_YEN_MY',
            'X23' => 'THI_TRAN_KEP',
            'X24' => 'THI_TRAN_VOI',
        );

        foreach ($arr_cross_record_type as $record_type_code)
        {
            $v_org_form_struct_file_name = $this->view->get_xml_config($record_type_code, 'form_struct');
            $v_org_workflow_file_name    = $this->view->get_xml_config($record_type_code, 'workflow');

            foreach ($arr_xa_phuong as $key => $val)
            {
                $v_copy_record_type_code = $record_type_code . '_' . $key;

                $v_folder = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . $v_copy_record_type_code;
                if (!is_dir($v_folder))
                {
                    mkdir($v_folder, '0777');
                }

                $v_new_form_struct_file_name = $this->view->get_xml_config($v_copy_record_type_code, 'form_struct', 0);
                $v_new_workflow_file_name    = $this->view->get_xml_config($v_copy_record_type_code, 'workflow', 0);

                //create form struct
                file_put_contents($v_new_form_struct_file_name, file_get_contents($v_org_form_struct_file_name));

                //create workflow
                $v_xml_workflow_string = file_get_contents($v_org_workflow_file_name);
                $v_xml_workflow_string = str_replace($record_type_code . _CONST_XML_RTT_DELIM, $v_copy_record_type_code . _CONST_XML_RTT_DELIM, $v_xml_workflow_string);
                $v_xml_workflow_string = str_replace('DIA_CHINH_CAP_XA', $val, $v_xml_workflow_string);

                file_put_contents($v_new_workflow_file_name, $v_xml_workflow_string);
            }
        }

        echo 'Done';
    }

    public function create_record_type_by_xml_config()
    {
        $v_config_dir = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config';
        $arr_dir = scandir($v_config_dir);
        $v_order = $this->model->get_max('t_r3_record_type', 'C_ORDER') + 1;

        //Lay danh sach hs da co
        $sql                           = "Select C_CODE From t_r3_record_type";
        $arr_existing_record_type_code = $this->model->db->getCol($sql);
        foreach ($arr_dir as $record_type_dir)
        {
            $v_xml_workflow_file_name = $v_config_dir . DS . $record_type_dir . DS . $record_type_dir . '_workflow.xml';
            //không insert thủ tục đã có
            $is_new                   = !in_array($record_type_dir, $arr_existing_record_type_code);
            //có phải là thư mục thủ tục
            $is_record_type           = !in_array($record_type_dir, array('.', '..', 'common', 'record_result'));
            //xxx_worflow.xml
            $exists_workflow          = file_exists($v_xml_workflow_file_name);

            if (!$is_new OR !$is_record_type OR !$exists_workflow)
            {
                continue;
            }
            $v_order++;
            $dom_workflow = simplexml_load_file($v_xml_workflow_file_name);

            $v_record_type_code = $record_type_dir;
            $v_record_type_name = get_xml_value($dom_workflow, "/process/@name");
            $v_spec_code        = preg_replace('/([0-9]+[A-Z0-9-_]*)/', '', $v_record_type_code);

            echo '<br/>' . $v_xml_workflow_file_name;
            echo '<br/>v_record_type_code: ' . $v_record_type_code;
            echo '<br/>v_record_type_name: ' . $v_record_type_name;
            echo '<br/>v_spec_code: ' . $v_spec_code;
            echo '<hr/>';

            $stmt   = 'Insert Into t_r3_record_type (C_CODE, C_NAME, C_XML_DATA,C_STATUS,C_SCOPE,C_ORDER, C_SEND_OVER_INTERNET,C_SPEC_CODE) Values (?,?,?,?,?,?,?,?)';
            $params = array($v_record_type_code, $v_record_type_name, '', 1, 0, $v_order, 0, $v_spec_code);

            $this->model->db->Execute($stmt, $params);
        }
    }

//end func
}
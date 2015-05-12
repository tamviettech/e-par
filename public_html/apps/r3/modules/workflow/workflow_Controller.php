<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>

<?php
class workflow_Controller extends Controller
{
    /**
     *
     * @var \workflow_Model 
     */
    public $model;
    protected $_arr_roles = array(
        //BP Mot-Cua
        _CONST_TIEP_NHAN_ROLE                       => 'Tiếp nhận'
        , _CONST_BAN_GIAO_ROLE                        => 'Bàn giao'
        , _CONST_BO_SUNG_ROLE                         => 'Bổ sung'
        , _CONST_TRA_THONG_BAO_NOP_THUE_ROLE          => 'Trả TB thuế'
        , _CONST_NHAN_BIEN_LAI_NOP_THUE_ROLE          => 'Nhận BL thuế'
        , _CONST_THU_PHI_ROLE                         => 'Thu phí'
        , _CONST_TRA_KET_QUA_ROLE                     => 'Trả KQ'
        , _CONST_IN_PHIEU_TIEP_NHAN_ROLE              => 'In lại phiếu TN'
        , _CONST_CHUYEN_LEN_HUYEN_ROLE                => 'Chuyển HS lên Huyện'
        , _CONST_TRA_HO_SO_VE_XA_ROLE                 => 'Trả HS về xã'
        , _CONST_XAC_NHAN_HO_SO_LIEN_THONG_ROLE       => 'Xác nhận hồ sơ liên thông'
        , _CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE => 'Hồ sơ Internet'

        //BO SUNG
        , _CONST_THONG_BAO_BO_SUNG_ROLE => 'Thông báo bổ sung'

        //Bo phan Thue
        , _CONST_NOP_HO_SO_SANG_CHI_CUC_THUE_ROLE         => 'Chuyển HS sang chi cục thuế'
        , _CONST_NHAN_THONG_BAO_CUA_CHI_CUC_THUE_ROLE     => 'Nhận TB của chi cục thuế'
        , _CONST_CHUYEN_THONG_BAO_THUE_VE_BP_MOT_CUA_ROLE => 'Chuyển TB thuế về "Môt-cửa"'

        //Phong chuyen mon
        , _CONST_PHAN_CONG_ROLE                        => 'Phân công thụ lý'
        , _CONST_PHAN_CONG_LAI_ROLE                    => 'Thay đổi thụ lý'
        , _CONST_THU_LY_ROLE                           => 'Thụ lý'
        , _CONST_CHUYEN_HO_SO_LEN_SO_ROLE              => 'Chuyển HS lên Sở'
        , _CONST_NHAN_HO_SO_TU_SO_ROLE                 => 'Nhận HS từ Sở'
        , _CONST_CHUYEN_YEU_CAU_XAC_NHAN_XUONG_XA_ROLE => 'Chuyển yêu cầu xác nhận xuống xã'
        , _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE          => 'Thụ lý HS liên thông'
        , _CONST_YEU_CAU_THU_LY_LAI_ROLE               => 'Yêu cầu thụ lý lại'
        , _CONST_XET_DUYET_ROLE                        => 'Xét duyệt hồ sơ'
        , _CONST_XET_DUYET_BO_SUNG_ROLE                => 'Xét duyệt hồ sơ bổ sung'
        , _CONST_CHUYEN_LAI_BUOC_TRUOC_ROLE            => 'Yêu cầu chuyển hồ sơ'

        //Lanh dao don vi
        , _CONST_KY_ROLE              => 'Ký duyệt hồ sơ'
        , _CONST_Y_KIEN_LANH_DAO_ROLE => 'Ý kiến lãnh đạo'

        //Chung
        , _CONST_TRA_CUU_ROLE => 'Tra cứu'
        , _CONST_BAO_CAO_ROLE => 'Báo cáo'
        , 'REJECT'            => 'Từ chối HS'
    );

    function __construct()
    {
        parent::__construct('r3', 'workflow');
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->app_name           = 'R3';
        //Kiem tra session
        session::init();
        //Kiem tra dang nhap
        session::check_login();
        
        intval(Session::get('is_admin')) > 0 OR die('Bạn không có quyền truy cập chức năng này');
        $login_name                               = session::get('login_name');
        if ($login_name == NULL)
        {
            session::destroy();
            header('location:' . SITE_ROOT . 'login.php');
            exit;
        }
    }

    public function main()
    {
        $this->dsp_single_workflow();
    }

    public function dsp_single_workflow()
    {
        $v_record_type_code = trim(get_request_var('sel_record_type'));

        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_record_type_option();
        $VIEW_DATA['record_type_code']    = $v_record_type_code;

        $VIEW_DATA['xml_user_task'] = $this->model->qry_all_user_task($v_record_type_code);

        $this->view->render('dsp_single_workflow', $VIEW_DATA);
    }

    public function assign_user_on_task()
    {
        $this->model->assign_user_on_task();
    }

    function remove_user_on_task()
    {
        $this->model->remove_user_on_task();
    }

    public function ui()
    {
        $v_record_type_code = trim(get_post_var('sel_record_type'));

        $VIEW_DATA['v_record_type_code']  = strtoupper($v_record_type_code);
        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_record_type_option();
        $VIEW_DATA['arr_all_role']        = $this->_arr_roles;
        $VIEW_DATA['arr_all_group']       = $this->model->qry_all_group();

        //danh sach xml result
        $VIEW_DATA['arr_all_xml_result'] = array();
        foreach (scandir(SERVER_ROOT . 'apps/r3/xml-config/record_result/') as $item)
        {
            if (strpos($item, 'xml'))
            {
                $VIEW_DATA['arr_all_xml_result'][] = $item;
            }
        }
        $this->view->render('dsp_single_workflow_ui', $VIEW_DATA);
    }

    public function do_update_step_order_by_ui()
    {
        $arr_step_order   = get_post_var('step', NULL);
        $v_xml_config_dir = SERVER_ROOT . "apps\\r3\\xml-config\\";

        if ($arr_step_order == NULL)
        {
            //Khong thay doi buoc, cong viec trong quy trinh
            $v_new_xml_flow = session::get('v_current_xml_flow');
        }
        else
        {
            //Khoi tao lai quy trinh moi
            $docDest = new DOMDocument();
            $docDest->loadXML(session::get('v_current_xml_flow'));
            $dels    = $docDest->getElementsByTagName('process');
            foreach ($dels as $del)
            {
                while ($del->hasChildNodes())
                {
                    $del->removeChild($del->childNodes->item(0));
                }
            }

            $docSource = new DOMDocument();
            $docSource->loadXML(session::get('v_current_xml_flow'));
            $xpath     = new DOMXPath($docSource);
            foreach ($arr_step_order as $v_step_order)
            {
                $result = $xpath->query("//step[position()=$v_step_order]")->item(0);
                $result = $docDest->importNode($result, true);
                $items  = $docDest->getElementsByTagName('process')->item(0);
                $items->appendChild($result);
            }

            //renext
            $xpath        = new DOMXPath($docDest);
            $v_count_task = $xpath->query("//task")->length;
            for ($i = 1; $i <= $v_count_task; $i++)
            {
                $current_task = $xpath->query("//task")->item($i - 1);
                if ($i < $v_count_task)
                {
                    $next_task        = $xpath->query("//task")->item($i);
                    $v_next_task_code = $next_task->getAttribute('code');
                }
                else
                {
                    $v_next_task_code = 'NULL';
                }

                $current_task->setAttribute('next', $v_next_task_code);
            }

            $v_new_xml_flow = $docDest->saveXML();
        }

        //ghi file
        $dom                  = simplexml_load_string(xml_add_declaration($v_new_xml_flow));
        $v_record_type_code   = get_xml_value($dom, '/process/@code');
        $v_rt_dir             = $v_xml_config_dir . $v_record_type_code . DS;
        $v_new_flow_file_path = $v_rt_dir . $v_record_type_code . '_workflow.xml';
        if (file_put_contents($v_new_flow_file_path, $v_new_xml_flow))
        {
            echo 'Cập nhật thành công';
        }
        else
        {
            echo 'Cập nhật thất bại';
        }
    }

    public function do_update_task_order_by_ui()
    {
        
    }

    /**
     * Hien thi form edit thong tin quy trinh
     */
    public function dsp_single_process()
    {
        $VIEW_DATA['v_record_type_code'] = get_request_var('record_type_code');
        $VIEW_DATA['v_record_type_name'] = get_request_var('record_type_name');
        $VIEW_DATA['v_total_time']       = get_request_var('total_time', 0);
        $VIEW_DATA['v_fee']              = get_request_var('fee', 0);

        $this->view->render('dsp_single_process', $VIEW_DATA);
    }

    public function do_update_process()
    {
        $v_xml_flow = session::get('v_current_xml_flow');
        $dom        = simplexml_load_string($v_xml_flow);
        $p          = $dom->xpath('/process');
        $dom_p      = $p[0];

        $v_new_total_time               = get_post_var('txt_total_time');
        $v_new_fee                      = get_post_var('txt_fee');
        $dom_p->attributes()->totaltime = $v_new_total_time;
        $dom_p->attributes()->fee       = $v_new_fee;

        $v_xml_flow = $dom_p->saveXML();
        session::set('v_current_xml_flow', $v_xml_flow);

        $this->model->popup_exec_done('[' . $v_new_total_time . ',' . $v_new_fee . ']');
    }

    function dsp_single_step()
    {
        $this->view->render('dsp_single_step');
    }

    public function do_update_step()
    {
        $v_step_id  = get_request_var('hdn_step_id');
        $v_xml_flow = session::get('v_current_xml_flow');
        $dom        = simplexml_load_string($v_xml_flow);
        $p          = $dom->xpath("//step[position()=$v_step_id]");
        $dom_p      = $p[0];

        $v_new_name  = get_post_var('txt_name');
        $v_new_group = get_post_var('txt_group');
        $v_new_time  = get_post_var('txt_time');

        $dom_p->attributes()->name  = $v_new_name;
        $dom_p->attributes()->group = $v_new_group;
        $dom_p->attributes()->time  = $v_new_time;

        $v_xml_flow = $dom->saveXML();
        session::set('v_current_xml_flow', $v_xml_flow);
        $this->model->popup_exec_done('[' . $v_step_id . ',"' . $v_new_name . '","' . $v_new_group . '",' . $v_new_time . ']');
    }

    public function dsp_all_task_in_step($step_id)
    {
        $VIEW_DATA['v_step_id'] = $step_id;
        $this->view->render('dsp_all_task_in_step', $VIEW_DATA);
    }

    public function dsp_single_task($step_id, $task_id)
    {
        
    }

    public function dsp_plaintext_workflow()
    {
        $this->view->render('dsp_plaintext_workflow');
    }

    public function update_workflow_service()
    {
        $arr_proc            = get_post_var('process', '', false);
        $xml_proc            = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><process author="Tam Viet"/>');
        //$xml_proc->addChild('process');
        $arr_proc_attributes = isset($arr_proc['@attributes']) ? $arr_proc['@attributes'] : array();
        $arr_step            = isset($arr_proc['step']) ? $arr_proc['step'] : array();
        $arr_no_chain        = isset($arr_proc['no_chain_step']) ? $arr_proc['no_chain_step'] : array();
        $arr_step            = array_merge($arr_step, $arr_no_chain);

        foreach ($arr_proc_attributes as $key => $value)
        {
            if (!isset($xml_proc->attributes()->{$key}))
            {
                $xml_proc->addAttribute($key, $value);
            }
            else
            {
                $xml_proc->attributes()->{$key} = $value;
            }
        }
        foreach ($arr_step as $v_step)
        {
            $xml_step      = $xml_proc->addChild('step');
            $arr_step_attr = isset($v_step['@attributes']) ? $v_step['@attributes'] : array();
            $arr_step_task = isset($v_step['task']) ? $v_step['task'] : array();
            foreach ($arr_step_attr as $key => $value)
            {
                $xml_step->addAttribute($key, $value);
            }
            if (count($arr_step_task == 1) && isset($arr_step_task['@attributes']))
            {
                $arr_step_task = array($arr_step_task);
            }
            foreach ($arr_step_task as $v_task)
            {
                $xml_task      = $xml_step->addChild('task');
                $arr_task_attr = isset($v_task['@attributes']) ? $v_task['@attributes'] : array();
                foreach ($arr_task_attr as $key => $value)
                {
                    if ($key != 'next_no_chain')
                    {
                        $xml_task->addAttribute($key, $value);
                    }
                }
            }
        }

        $domDocObj                     = new DOMDocument('1.0', 'utf-8');
        $domDocObj->loadXML($xml_proc->asXML());
        $domDocObj->formatOutput       = true;
        $domDocObj->preserveWhitespace = false;

        $_POST['txt_plaintext_workflow'] = $domDocObj->saveXML();
        $_POST['txt_plaintext_workflow'] = trim(preg_replace('/^[ ]+(?=<)/m', '$0$0', $_POST['txt_plaintext_workflow']));
        ob_get_clean();
        echo trim($this->model->update_workflow());
    }

    public function update_plaintext_workflow()
    {
        echo $v_message = $this->model->update_workflow();
        if ($v_message == '')
        {
            $this->model->popup_exec_done();
        }
        else
        {
            $this->model->popup_exec_fail($v_message);
        }
    }

    public function check_workflow_time()
    {
        $arr_all_rt = $this->model->db->getAssoc("SELECT TRIM(C_CODE) C_CODE, C_NAME FROM t_r3_record_type WHERE C_STATUS>0 Order By C_CODE");

        $v_xml_config_dir = SERVER_ROOT . "apps\\r3\\xml-config\\";
        foreach ($arr_all_rt as $code => $name)
        {
            $v_flow_file_path = $v_xml_config_dir . $code . DS . $code . '_workflow.xml';
            $dom_workflow     = simplexml_load_file($v_flow_file_path);

            $v_totaltime   = get_xml_value($dom_workflow, '/process/@totaltime');
            $steps         = $dom_workflow->xpath('//step');
            $v_time_by_sum = 0;
            foreach ($steps as $step)
            {
                $v_time_by_sum += (float) $step->attributes()->time;
            }

            echo '<br>-------------------------------------------';
            echo '<br>++' . $code;
            echo '<br>++Totaltime=' . $v_totaltime;
            echo '<br>++Sum=' . $v_time_by_sum;
            echo '<br>=>';
            if ($v_totaltime == $v_time_by_sum)
            {
                echo '<font style="color:blue;font-weight:bold">OK</font>';
            }
            else
            {
                echo '<font style="color:red;font-weight:bold">FAIL</font>';
            }
        }
    }

    function dsp_copy_assign($record_type_code)
    {
        //lấy lĩnh vực
        $record_type_code = replace_bad_char($record_type_code);
        $arr_single       = $this->model->qry_single_record_type('', $record_type_code);
        $v_spec_code      = isset($arr_single['C_SPEC_CODE']) ? $arr_single['C_SPEC_CODE'] : '';
        //lọc
        $v_search         = get_post_var('txt_search');

        //phan trang
        page_calc($v_start, $v_end);
        $limit  = $v_end - $v_start + 1;
        $offset = $v_start - 1;
        //lấy thủ tục cùng lĩnh vực và đã được phân công
        $where  = "C_SPEC_CODE='$v_spec_code' 
                And C_CODE <> '$record_type_code'
                And Exists(Select PK_USER_TASK From t_r3_user_task Where C_RECORD_TYPE_CODE=C_CODE)
                And (C_CODE Like '%$v_search%' Or C_NAME Like '%$v_search%')";

        $arr_spec_record_types = $this->model->qry_all_record_type($where, $limit, $offset);

        $VIEW_DATA['arr_spec_record_types'] = $arr_spec_record_types;
        $VIEW_DATA['id']                    = $arr_single['PK_RECORD_TYPE'];
        $this->view->render('dsp_copy_assign', $VIEW_DATA);
    }

    function copy_assign()
    {
        $src  = (int) get_post_var('src');
        $dest = (int) get_post_var('dest');
        echo $this->model->copy_assign($dest, $src);
    }

    /**
     * Tìm kiếm tất cả workflow xem có chỗ nào sai bizdone không
     */
    function find_misplace_biz_done()
    {
        $xmlconfig_path = SERVER_ROOT . 'apps/r3/xml-config/';
        $items          = scandir($xmlconfig_path);
        foreach ($items as $item)
        {
            $item_path = $xmlconfig_path . $item;
            if (!is_dir($item_path) || in_array($item, array('.', '..', 'common')))
            {
                continue;
            }
            $worflow_file = $item_path . '/' . $item . '_workflow.xml';
            if (!file_exists($worflow_file))
            {
                continue;
            }
            $dom_workflow = simplexml_load_file($worflow_file);
            $biz_done     = -1;
            $i            = 0;
            foreach (xpath($dom_workflow, '//step[not(@no_chain="true")]/task') as $task)
            {
                if (strval($task->attributes()->biz_done))
                {
                    $biz_done = $i;
                }
                if (strpos($task->attributes()->code, _CONST_THU_PHI_ROLE) !== false ||
                        strpos($task->attributes()->code, _CONST_TRA_KET_QUA_ROLE) !== false)
                {
                    if ($biz_done != -1 && $biz_done != ($i - 1))
                    {
                        echo $item . '<hr/>';
                    }
                    break;
                }
                $i++;
            }
        }
    }

    function dsp_switch_user()
    {
        $VIEW_DATA['user']       = get_request_var('user');
        $VIEW_DATA['task']       = get_request_var('task');
        $VIEW_DATA['keywords']   = get_post_var('txt_search');
        $VIEW_DATA['group_code'] = get_request_var('group_code');
        
        page_calc($v_start, $v_end);
        $v_start               = $v_start - 1;
        $limit                 = $v_end - $v_start;
       
        $tables = ' t_cores_user u
                        LEFT JOIN t_cores_user_group UG
                        ON u.PK_USER = UG.FK_USER';
        $conds  = " u.C_STATUS > 0
                    And UG.FK_GROUP=(SELECT PK_GROUP FROM t_cores_group WHERE C_CODE='{$VIEW_DATA['group_code']}')
                    AND u.C_LOGIN_NAME <> '{$VIEW_DATA['user']}'";
        
        if ($VIEW_DATA['keywords'])
        {
            $conds .= " And u.C_NAME Like '%{$VIEW_DATA['keywords']}%'";
        }

        $count = $this->model->db->GetOne("Select Count(*) From $tables Where $conds");

        $VIEW_DATA['arr_all_user'] = $this->model->db->GetAll("Select u.*, $count As TOTAL_RECORD from $tables Where $conds 
            Limit $limit Offset $v_start");
        $this->view->render('dsp_switch_user', $VIEW_DATA);
    }

    function switch_user()
    {
        $task = get_post_var('hdn_task');
        $src  = get_post_var('hdn_src');
        $dest = get_post_var('hdn_dest');
        $this->model->switch_user($task, $dest, $src);
    }

    /**
     * Hien thi man hinh chon thu tuc de nhận PUSH phân công
     * @param type $record_type_code
     */
    
    public function dsp_push_assign($record_type_code)
    {
        //lấy lĩnh vực
        $record_type_code = replace_bad_char($record_type_code);
        $arr_single       = $this->model->qry_single_record_type('', $record_type_code);
        $v_spec_code      = isset($arr_single['C_SPEC_CODE']) ? $arr_single['C_SPEC_CODE'] : '';
        //lọc
        $v_search         = get_post_var('txt_search','');

        //phan trang
        page_calc($v_start, $v_end);
        $limit  = $v_end - $v_start + 1;
        $offset = $v_start - 1;
        //lấy thủ tục cùng lĩnh vực và đã được phân công
        $where  = " C_SPEC_CODE='$v_spec_code' 
                    And C_CODE <> '$record_type_code'
                    And C_SCOPE = {$arr_single['C_SCOPE']}";
        if ($v_search != '')
        {
            $where .= " And (C_CODE Like '%$v_search%' Or C_NAME Like '%$v_search%')";
        }

        $arr_spec_record_types = $this->model->qry_all_record_type($where, $limit, $offset);

        $VIEW_DATA['arr_spec_record_types'] = $arr_spec_record_types;
        $VIEW_DATA['id']                    = $arr_single['PK_RECORD_TYPE'];
        
        $this->view->render('dsp_push_assign', $VIEW_DATA);
    }
    
    public function do_push_assign()
    {
        $this->model->do_push_assign();
    }
}

<?php
/**


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

class workflow_Model extends Model
{

    /**
     *
     * @var \ADOConnection 
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }

    public function qry_all_record_type_option()
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "Select C_CODE, (C_CODE + ' - ' + C_NAME) as C_NAME
                    From t_r3_record_type
                    Where C_STATUS > 0
                    Order By C_CODE";
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select C_CODE, Concat(C_CODE, ' - ', C_NAME) as C_NAME
                    From t_r3_record_type
                    Where C_STATUS > 0
                    Order By C_CODE";
        }

        return $this->db->getAssoc($stmt, null);
    }

    /**
     * Chuyển công tác
     * @param string $task Mã công việc 
     * @param string $dest Người tham gia
     * @param string $src Người bị chuyển đi
     */
    function switch_user($task, $dest, $src)
    {
        $v_role = get_role($task);
        $v_task_code = $task;
        
        //1. Can bo moi tiep quan ho so
        $this->db->Execute("Update t_r3_record Set C_NEXT_USER_CODE=Replace(C_NEXT_USER_CODE, ?, ?) 
            Where C_NEXT_TASK_CODE=?", array($src, $dest, $task));
        
        $this->db->Execute("Update t_r3_record Set C_NEXT_CO_USER_CODE=Replace(C_NEXT_CO_USER_CODE, ?, ?)
            Where C_NEXT_TASK_CODE=?", array($src, $dest, $task));

        $this->db->Execute("Update t_r3_record Set C_NEXT_NO_CHAIN_USER_CODE=Replace(C_NEXT_NO_CHAIN_USER_CODE, ?, ?) 
           Where C_NEXT_NO_CHAIN_TASK_CODE=?", array($src, $dest, $task));

        //2. Thay doi phan cong
        $this->db->Execute("Update t_r3_user_task Set C_USER_LOGIN_NAME=? 
            Where C_USER_LOGIN_NAME=? And C_TASK_CODE=?", array($dest, $src, $task));
        
        $v_record_type_code = $this->db->getOne('Select C_RECORD_TYPE_CODE From t_r3_user_task Where C_TASK_CODE=?', array($task));
        
        //LienND update 2014-01-07: kem theo cac cong viec lien quan.
        //2.1  Neu doi can bo TIEP_NHAN ==> Phai doi ca bo sung        
        if ($v_role == _CONST_TIEP_NHAN_ROLE)
        {
            $v_bo_sung_task_code = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE;
        
            $stmt = 'Update t_r3_user_task Set C_USER_LOGIN_NAME=? Where C_USER_LOGIN_NAME=? And C_RECORD_TYPE_CODE=? And C_TASK_CODE like ?';
            $params = array($dest, $src, $v_record_type_code, '%' . $v_bo_sung_task_code);
            $this->db->Execute($stmt, $params);
            
            //Tiep quan ho so dang bo sung
            $stmt = 'Update t_r3_record '
                    . ' Set C_NEXT_USER_CODE=Replace(C_NEXT_USER_CODE, ?, ?) '
                    . ' Where C_NEXT_TASK_CODE like ? '
                    . '     And (C_NEXT_USER_CODE = ? Or C_NEXT_USER_CODE Like ?) '
                    . '     And FK_RECORD_TYPE=(Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE=?)';   
            $params = array($src, $dest, '%' . $v_bo_sung_task_code, $src,  '%,' . $src . ',%', $v_record_type_code);
            $this->db->Execute($stmt, $params);
            
        }

        //2.2 Neu doi buoc xet duyet ==> Doi luon xet duyet bo sung
        if ($v_role == _CONST_XET_DUYET_ROLE)
        {
            $v_xet_duyet_bo_sung_task_code  = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_BO_SUNG_ROLE;
            
            $stmt = 'Update t_r3_user_task Set C_USER_LOGIN_NAME=? Where C_USER_LOGIN_NAME=? And C_RECORD_TYPE_CODE=? And C_TASK_CODE like ?';
            $params = array($dest, $src, $v_record_type_code, '%' . $v_xet_duyet_bo_sung_task_code);
            $this->db->Execute($stmt, $params);
            
            //Tiep quan ho so dang XET DUYET BO SUNG
            $stmt = 'Update t_r3_record '
                    . ' Set C_NEXT_USER_CODE=Replace(C_NEXT_USER_CODE, ?, ?) '
                    . ' Where C_NEXT_TASK_CODE like ? '
                    . '     And (C_NEXT_USER_CODE = ? Or C_NEXT_USER_CODE Like ?) '
                    . '     And FK_RECORD_TYPE=(Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE=?)';   
            $params = array($src, $dest, '%' . $v_xet_duyet_bo_sung_task_code, $src,  '%,' . $src . ',%', $v_record_type_code);
            $this->db->Execute($stmt, $params);
        }
            
        //2.3 Neu doi THU_LY ==> Doi ca thu ly lai
        if ($v_role == _CONST_THU_LY_ROLE)
        {
            $v_thu_ly_lai_task_code  = str_replace(_CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE, _CONST_XML_RTT_DELIM . _CONST_YEU_CAU_THU_LY_LAI_ROLE, $v_task_code);

            $stmt = 'Update t_r3_user_task Set C_USER_LOGIN_NAME=? Where C_USER_LOGIN_NAME=? And C_RECORD_TYPE_CODE=? And C_TASK_CODE like ?';
            $params = array($dest, $src, $v_record_type_code, '%' . $v_thu_ly_lai_task_code);
            $this->db->Execute($stmt, $params);
            
            //Tiep quan ho so dang THU LY LAI
            $stmt = 'Update t_r3_record '
                    . ' Set C_NEXT_USER_CODE=Replace(C_NEXT_USER_CODE, ?, ?) '
                    . ' Where C_NEXT_TASK_CODE like ? '
                    . '     And (C_NEXT_USER_CODE = ? Or C_NEXT_USER_CODE Like ?) '
                    . '     And FK_RECORD_TYPE=(Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE=?)';   
            $params = array($src, $dest, '%' . $v_thu_ly_lai_task_code, $src,  '%,' . $src . ',%', $v_record_type_code);
            $this->db->Execute($stmt, $params);
        }

        if ($v_role == _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE)
        {
            $v_thu_ly_lai_ho_so_lien_thong_task_code  = str_replace(_CONST_XML_RTT_DELIM . _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE, _CONST_XML_RTT_DELIM . _CONST_YEU_CAU_THU_LY_LAI_ROLE, $v_task_code);

            $stmt = 'Update t_r3_user_task Set C_USER_LOGIN_NAME=? Where C_USER_LOGIN_NAME=? And C_RECORD_TYPE_CODE=? And C_TASK_CODE like ?';
            $params = array($dest, $src, $v_record_type_code, '%' . $v_thu_ly_lai_ho_so_lien_thong_task_code);
            $this->db->Execute($stmt, $params);
            
            
            //Tiep quan ho so dang THU LY LIEN THONG
            $stmt = 'Update t_r3_record '
                    . ' Set C_NEXT_USER_CODE=Replace(C_NEXT_USER_CODE, ?, ?) '
                    . ' Where C_NEXT_TASK_CODE like ? '
                    . '     And (C_NEXT_USER_CODE = ? Or C_NEXT_USER_CODE Like ?) '
                    . '     And FK_RECORD_TYPE=(Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE=?)';   
            $params = array($src, $dest, '%' . $v_thu_ly_lai_ho_so_lien_thong_task_code, $src,  '%,' . $src . ',%', $v_record_type_code);
            $this->db->Execute($stmt, $params);
        }
            
        //2.4 PHAN_CONG ==> THAY DOI PHAN CONG
        if ($v_role == _CONST_PHAN_CONG_ROLE)
        {
            $v_thay_doi_phan_cong_task_code  = str_replace(_CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE, _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_LAI_ROLE, $v_task_code);

            $stmt = 'Update t_r3_user_task Set C_USER_LOGIN_NAME=? Where C_USER_LOGIN_NAME=? And C_RECORD_TYPE_CODE=? And C_TASK_CODE like ?';
            $params = array($dest, $src, $v_record_type_code, '%' . $v_thay_doi_phan_cong_task_code);
            $this->db->Execute($stmt, $params);
        }
        
        //3. Thay doi XML next
        //Danh sach HS can cap nhat next
        //3.1 Update Next user name
        $v_to_user_name = $this->db->getOne('Select C_NAME From t_cores_user Where C_LOGIN_NAME=?', Array($dest));
        $sql = "Update t_r3_record
                Set C_XML_PROCESSING = UpdateXML(C_XML_PROCESSING,  '/data/next_task/@user_name[@user=\"{$src}\"]', 'user_name=\"$v_to_user_name\"')
                Where ExtractValue(C_XML_PROCESSING, '/data/next_task[@code=''$v_task_code''][1]/@user[1]') ='$src'";
        $this->db->Execute($sql);
        //3.2 Update next user_code
        $sql = "Update t_r3_record
                Set C_XML_PROCESSING = UpdateXML(C_XML_PROCESSING,  '/data/next_task/@user[@user=\"{$src}\"]', 'user=\"$dest\"')
                Where ExtractValue(C_XML_PROCESSING, '/data/next_task[@code=''$v_task_code''][1]/@user[1]') ='$src'";
        $this->db->Execute($sql);
        //4 Lưu trữ người thay đổi vào log
        $stmt = "Insert Into t_r3_swap_user_log
                    (C_FROM_USER_CODE,
                     C_TO_USER_CODE,
                     C_SWAP_DATE,
                     C_ROLE,
                     C_RECORD_TYPE_CODE
                     )
                Values (?,
                        ?,
                        NOW(),
                        ?,
                        ?)";
        $this->db->Execute($stmt,array($src,$dest,$v_role,$v_record_type_code));
        
        $this->popup_exec_done();        
    }

    public function qry_all_user_in_workflow($p_record_type_code)
    {
        $stmt = 'Select UT.* From t_r3_user_task UT Where C_RECORD_TYPE_CODE=?';

        return $this->db->GetAll($stmt, array($p_record_type_code));
    }

    public function qry_all_group()
    {
        $sql = "Select C_CODE, C_NAME From t_cores_group";
        return $this->db->getAll($sql);
    }

    public function update_workflow()
    {
        $v_xml_flow_file_path = get_post_var('hdn_xml_flow_file_path', '', 0);
        $v_xml_string         = get_post_var('txt_plaintext_workflow', '<process/>', 0);

        $ok        = TRUE;
        $v_message = 'Cập nhật dữ liệu thất bại!. ';
        //Kiem tra xml welform
        $dom_flow  = @simplexml_load_string($v_xml_string);

        if ($dom_flow !== FALSE)
        {
            //Kiem tra task noi tiep nhau
            $v_next_task_is_valid = TRUE;
            //$tasks = $dom_flow->xpath('//task');
            $tasks                = $dom_flow->xpath("//task[not(../@no_chain = 'true')]");
            for ($i = 0, $n = count($tasks); $i < $n; $i++)
            {
                $task   = $tasks[$i];
                $v_next = strval($task->attributes()->next);
                //Chi lay task chinh
                $v_next = trim(preg_replace('/\[([A-Z0-9::_]*)\]/', '', $v_next));
                $v_next = str_replace(_CONST_FINISH_NO_CHAIN_STEP_TASK, NULL, $v_next);
                if ($i < ($n - 1))
                {
                    $next_task_obj    = $tasks[$i + 1];
                    $v_next_task_code = strval($next_task_obj->attributes()->code);
                }
                else
                {
                    $v_next_task_code = NULL;
                }
                if ((strpos($v_next_task_code, $v_next) === false) && ($v_next_task_code != NULL))
                {
                    $v_message .= $v_next . ' -> ' . $v_next_task_code;
                    $ok = FALSE;
                    break;
                }
            }

            //CHECK NO_CHAIN task
            $tasks = $dom_flow->xpath("//task[../@no_chain = 'true']");
            for ($i = 0, $n = count($tasks); $i < $n; $i++)
            {
                $task   = $tasks[$i];
                $v_next = strval($task->attributes()->next);
                //Chi lay task chinh
                $v_next = trim(preg_replace('/\[([A-Z0-9::_]*)\]/', '', $v_next));
                $v_next = str_replace(_CONST_FINISH_NO_CHAIN_STEP_TASK, NULL, $v_next);
                if ($i < ($n - 1))
                {
                    $next_task_obj    = $tasks[$i + 1];
                    $v_next_task_code = strval($next_task_obj->attributes()->code);
                }
                else
                {
                    $v_next_task_code = NULL;
                }
                if (($v_next != $v_next_task_code) && ($v_next_task_code != NULL))
                {
                    $v_message .= $v_next . ' -> ' . $v_next_task_code;
                    $ok = FALSE;
                    break;
                }
            }

            //Kiem tra totaltime
            $v_totaltime   = (float) get_xml_value($dom_flow, '/process/@totaltime');
            if ($v_totaltime > 0)
            {
                $v_time_by_sum = 0;
                $steps         = $dom_flow->xpath('//step');
                foreach ($steps as $step)
                {
                    $v_time_by_sum += (float) $step->attributes()->time;
                }
                if ($v_totaltime != $v_time_by_sum)
                {
                    $ok = FALSE;
                    $v_message .= 'Sai TotalTime';
                }
            }
        }
        else
        {
            $ok = FALSE;
            $v_message .= 'XML không well-form';
        }

        //Ghi file
        if ($ok)
        {
            $v_dir = dirname($v_xml_flow_file_path);
            if (!is_dir($v_dir))
            {
                @mkdir($v_dir);
            }
            $r = file_put_contents($v_xml_flow_file_path, $v_xml_string);
            if ($r === FALSE OR $r === 0)
            {
                $ok = FALSE;
                $v_message .= 'Không thể ghi được file dữ liệu!';
            }
        }


        if ($ok)
        {
            //Xoa het thong tin phan cong hien tai
            $stmt   = "Delete From t_r3_user_task Where C_RECORD_TYPE_CODE=?";
            $params = array(get_xml_value($dom_flow, '/process/@code'));
            @$this->db->Execute($stmt, $params);

            return '';
        }
        else
        {
            return $v_message;
        }
    }

    public function assign_user_on_task()
    {
        $v_root_ou             = Session::get('root_ou_id');
        $v_record_type_code    = isset($_POST['record_tye_code']) ? $this->replace_bad_char($_POST['record_tye_code']) : '';
        $v_user_code           = isset($_POST['user_code']) ? $this->replace_bad_char($_POST['user_code']) : '';
        $v_task_code           = isset($_POST['task_code']) ? $this->replace_bad_char($_POST['task_code']) : '';
        $v_group_code          = isset($_POST['group_code']) ? $this->replace_bad_char($_POST['group_code']) : '';
        $v_next_task_code      = isset($_POST['next_task_code']) ? $this->replace_bad_char($_POST['next_task_code']) : '';
        $v_prev_task_code      = isset($_POST['prev_task_code']) ? $this->replace_bad_char($_POST['prev_task_code']) : '';
        $v_step_time           = isset($_POST['step_time']) ? $this->replace_bad_char($_POST['step_time']) : '0';
        $v_task_time           = isset($_POST['task_time']) ? $this->replace_bad_char($_POST['task_time']) : '';
        $v_first_task          = isset($_POST['first_task']) ? $this->replace_bad_char($_POST['first_task']) : $v_task_code;
        $v_prev_step_last_task = isset($_POST['prev_step_last_task']) ? $this->replace_bad_char($_POST['prev_step_last_task']) : $v_task_code;

        $v_no_chain = toStrictBoolean(get_post_var('no_chain'));

        $v_step_time = intval($v_step_time);
        $v_task_time = intval($v_task_time);

        $v_task_code      = str_replace(_CONST_HTML_RTT_DELIM, _CONST_XML_RTT_DELIM, $v_task_code);
        $v_next_task_code = str_replace(_CONST_HTML_RTT_DELIM, _CONST_XML_RTT_DELIM, $v_next_task_code);
        $v_prev_task_code = str_replace(_CONST_HTML_RTT_DELIM, _CONST_XML_RTT_DELIM, $v_prev_task_code);
        $v_first_task     = str_replace(_CONST_HTML_RTT_DELIM, _CONST_XML_RTT_DELIM, $v_first_task);

        //LienND update 2013-02-04: Voi NEXT_TASK, phan ra task chinh, task phu (task song song)
        //1. Lay danh sach task phu
        preg_match_all('/\[([A-Z0-9::_]+)\]/', $v_next_task_code, $arr_all_no_chain_task);
        $arr_all_no_chain_task = $arr_all_no_chain_task[0];


        //2. Lay task chinh
        $v_next_task_code = trim(preg_replace('/\[([A-Z0-9::_]+)\]/', '', $v_next_task_code));

        if ($v_record_type_code != '' && $v_user_code != '' && $v_task_code != '')
        {
            //Kiem tra cac buoc phu thuoc
            //Kiem tra task nay co user nao chưa?
            $stmt                 = 'Select Count(*) From t_r3_user_task Where C_TASK_CODE=?';
            $params               = array($v_task_code);
            $v_count_current_user = $this->db->getOne($stmt, $params);
            //1.0 Neu chua
            if ($v_count_current_user == 0)
            {
                $stmt   = 'Update t_r3_user_task Set C_NEXT_TASK_CODE=? Where C_TASK_CODE=?';
                $params = array($v_task_code, $v_prev_task_code);
                $this->db->Execute($stmt, $params);
            }

            //LienND 2012-08-28: Them thong tin TASK dau tien cua STEP, TIME cua STEP
            //A. Task dau tien cua step
            //step[task[@code='TN01::BAN_GIAO']]/task[1]/@code
            //B. TIME (So ngay thuc hien) cua step
            //step[task[@code='TN01::XET_DUYET']]/@time

            $stmt           = 'Insert Into t_r3_user_task
                      (
                         C_RECORD_TYPE_CODE, C_TASK_CODE, C_USER_LOGIN_NAME
                        ,C_GROUP_CODE, C_NEXT_TASK_CODE,C_STEP_TIME, C_TASK_TIME
                        ,C_STEP_FIRST_TASK,C_PREV_STEP_LAST_TASK
                      )
                      Values(
                        ?,?,?,?
                        ,?,?,?,?,?
                      )';
            $params         = array($v_record_type_code, $v_task_code, $v_user_code
                , $v_group_code, $v_next_task_code, $v_step_time, $v_task_time
                , $v_first_task, $v_prev_step_last_task);
            $this->db->Execute($stmt, $params);
            $v_user_task_id = $this->get_last_inserted_id('t_r3_user_task');
            //Neu co task song song   
            if ($v_no_chain)
            {
                $stmt   = 'Update t_r3_user_task Set C_NO_CHAIN=\'1\' Where PK_USER_TASK=?';
                $params = array($v_user_task_id);
                $this->db->Execute($stmt, $params);
            }

            $v_no_chain_task_code = '';
            if (count($arr_all_no_chain_task) > 0)
            {
                foreach ($arr_all_no_chain_task as $v_no_chain_task_code)
                {
                    $v_no_chain_task_code = trim(trim($v_no_chain_task_code, '['), ']');
                    //$v_no_chain_task_code_list .= ($v_no_chain_task_code_list != '') ? ',' . $v_no_chain_task_code : $v_no_chain_task_code;
                }
                //$v_no_chain_task_code_list =  $v_no_chain_task_code_list . ',';

                $stmt   = 'Update t_r3_user_task Set C_NEXT_NO_CHAIN_TASK_CODE=? Where PK_USER_TASK=?';
                $params = array($v_no_chain_task_code, $v_user_task_id);
                $this->db->Execute($stmt, $params);
            }

            $v_role = get_role($v_task_code);
            //Nếu được phân công tiếp nhận, thì cũng làm bổ sung luôn
            if ($v_role == _CONST_TIEP_NHAN_ROLE)
            {
                $v_couple_task_code = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE;
                $stmt               = 'Insert Into t_r3_user_task(C_RECORD_TYPE_CODE, C_TASK_CODE, C_USER_LOGIN_NAME,C_GROUP_CODE, C_NEXT_TASK_CODE,C_STEP_TIME, C_TASK_TIME, C_STEP_FIRST_TASK, C_PREV_STEP_LAST_TASK, FK_PARENT) Values(?,?,?,?,?,?,?,?,?,?)';
                $params             = array($v_record_type_code, $v_couple_task_code, $v_user_code, $v_group_code, $v_next_task_code, $v_step_time, $v_task_time, $v_first_task, $v_prev_step_last_task, $v_user_task_id);
                //$this->db->debug=0;
                $this->db->Execute($stmt, $params);
            }
            
            //Nếu được phân công tiếp nhận, thì cũng làm bổ sung luôn
            if ($v_role == _CONST_TIEP_NHAN_ROLE)
            {
                $v_couple_task_code = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE;
                $stmt               = 'Insert Into t_r3_user_task(C_RECORD_TYPE_CODE, C_TASK_CODE, C_USER_LOGIN_NAME,C_GROUP_CODE, C_NEXT_TASK_CODE,C_STEP_TIME, C_TASK_TIME, C_STEP_FIRST_TASK, C_PREV_STEP_LAST_TASK, FK_PARENT) Values(?,?,?,?,?,?,?,?,?,?)';
                $params             = array($v_record_type_code, $v_couple_task_code, $v_user_code, $v_group_code, $v_next_task_code, $v_step_time, $v_task_time, $v_first_task, $v_prev_step_last_task, $v_user_task_id);
                //$this->db->debug=0;
                $this->db->Execute($stmt, $params);
            }
            
            //Nếu được phân công duyệt, thì cũng làm duyệt HS bổ sung
            if ($v_role == _CONST_XET_DUYET_ROLE)
            {
                $v_uniID            = strtoupper(uniqid());
                $v_couple_task_code = $v_uniID . _CONST_XML_RTT_DELIM . $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_BO_SUNG_ROLE;
                $stmt               = 'Insert Into t_r3_user_task(C_RECORD_TYPE_CODE, C_TASK_CODE, C_USER_LOGIN_NAME,C_GROUP_CODE, C_NEXT_TASK_CODE, C_STEP_TIME, C_TASK_TIME, C_STEP_FIRST_TASK, C_PREV_STEP_LAST_TASK, FK_PARENT) Values(?,?,?,?,?,?,?,?,?,?)';
                $params             = array($v_record_type_code, $v_couple_task_code, $v_user_code, $v_group_code, $v_next_task_code, $v_step_time, $v_task_time, $v_first_task, $v_prev_step_last_task, $v_user_task_id);
                //$this->db->debug=0;
                $this->db->Execute($stmt, $params);
            }

            //Nếu được phân công thụ lý thì cũng làm YEU_CAU_THU_LY_LAI
            if ($v_role == _CONST_THU_LY_ROLE)
            {
                $v_couple_task_code = str_replace(_CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE, _CONST_XML_RTT_DELIM . _CONST_YEU_CAU_THU_LY_LAI_ROLE, $v_task_code);
                $stmt               = 'Insert Into t_r3_user_task( C_RECORD_TYPE_CODE, C_TASK_CODE, C_USER_LOGIN_NAME,C_GROUP_CODE, C_NEXT_TASK_CODE, C_STEP_TIME, C_TASK_TIME, C_STEP_FIRST_TASK, C_PREV_STEP_LAST_TASK, FK_PARENT) Values(?,?,?,?,?,?,?,?,?,?)';
                $params             = array($v_record_type_code, $v_couple_task_code, $v_user_code, $v_group_code, $v_next_task_code, $v_step_time, $v_task_time, $v_first_task, $v_prev_step_last_task, $v_user_task_id);
                //$this->db->debug=0;
                $this->db->Execute($stmt, $params);
            }
            if ($v_role == _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE)
            {
                $v_couple_task_code = str_replace(_CONST_XML_RTT_DELIM . _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE, _CONST_XML_RTT_DELIM . _CONST_YEU_CAU_THU_LY_LAI_ROLE, $v_task_code);
                $stmt               = 'Insert Into t_r3_user_task(C_RECORD_TYPE_CODE, C_TASK_CODE, C_USER_LOGIN_NAME,C_GROUP_CODE, C_NEXT_TASK_CODE, C_STEP_TIME, C_TASK_TIME, C_STEP_FIRST_TASK, C_PREV_STEP_LAST_TASK, FK_PARENT) Values(?,?,?,?,?,?,?,?,?,?)';
                $params             = array($v_record_type_code, $v_couple_task_code, $v_user_code, $v_group_code, $v_next_task_code, $v_step_time, $v_task_time, $v_first_task, $v_prev_step_last_task, $v_user_task_id);
                //$this->db->debug=0;
                $this->db->Execute($stmt, $params);
            }

            //Nếu được được "Phân công thụ lý" thì cũng phải "Thay đổi phân công thụ lý"
            if ($v_role == _CONST_PHAN_CONG_ROLE)
            {
                $v_couple_task_code = str_replace(_CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE, _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_LAI_ROLE, $v_task_code);
                $stmt               = 'Insert Into t_r3_user_task( C_RECORD_TYPE_CODE, C_TASK_CODE, C_USER_LOGIN_NAME,C_GROUP_CODE, C_NEXT_TASK_CODE, C_STEP_TIME, C_TASK_TIME, C_STEP_FIRST_TASK, C_PREV_STEP_LAST_TASK, FK_PARENT) Values(?,?,?,?,?,?,?,?,?,?)';
                $params             = array($v_record_type_code, $v_couple_task_code, $v_user_code, $v_group_code, $v_next_task_code, $v_step_time, $v_task_time, $v_first_task, $v_prev_step_last_task, $v_user_task_id);
                //$this->db->debug=0;
                $this->db->Execute($stmt, $params);
            }
        }
    }

    public function remove_user_on_task()
    {
        $v_record_type_code = isset($_POST['record_tye_code']) ? $this->replace_bad_char($_POST['record_tye_code']) : '';
        $v_user_code        = isset($_POST['user_code']) ? $this->replace_bad_char($_POST['user_code']) : '';
        $v_task_code        = isset($_POST['task_code']) ? $this->replace_bad_char($_POST['task_code']) : '';
        $v_next_task_code   = isset($_POST['next_task']) ? $this->replace_bad_char($_POST['next_task']) : '';
        $v_prev_task_code   = isset($_POST['prev_task']) ? $this->replace_bad_char($_POST['prev_task']) : '';
        if ($v_record_type_code != '' && $v_user_code != '' && $v_task_code != '')
        {
            $v_task_code = str_replace(_CONST_HTML_RTT_DELIM, _CONST_XML_RTT_DELIM, $v_task_code);

            //1. Task nay con user nao khong
            $stmt                = 'Select Count(*) From t_r3_user_task Where C_RECORD_TYPE_CODE=? And C_TASK_CODE=? And C_USER_LOGIN_NAME <> ? ';
            $params              = array($v_record_type_code, $v_task_code, $v_user_code);
            $v_count_remain_user = $this->db->getOne($stmt, $params);
            if ($v_count_remain_user == 0)
            {
                $stmt   = 'Update t_r3_user_task Set C_NEXT_TASK_CODE=? Where C_TASK_CODE=?';
                $params = array(
                    str_replace(_CONST_HTML_RTT_DELIM, _CONST_XML_RTT_DELIM, $v_next_task_code)
                    , str_replace(_CONST_HTML_RTT_DELIM, _CONST_XML_RTT_DELIM, $v_prev_task_code)
                );

                $this->db->Execute($stmt, $params);
            }

            $params          = array($v_record_type_code, $v_task_code, $v_user_code);
            $v_user_task_id  = $this->db->GetOne('Select PK_USER_TASK From t_r3_user_task 
                Where C_RECORD_TYPE_CODE=? And C_TASK_CODE=? And C_USER_LOGIN_NAME=?', $params);
            $stmt            = 'Delete From t_r3_user_task Where C_RECORD_TYPE_CODE=? And C_TASK_CODE=? And C_USER_LOGIN_NAME=?';
            $params          = array($v_record_type_code, $v_task_code, $v_user_code);
            $this->db->debug = 0;
            $this->db->Execute($stmt, $params);
            $this->db->Execute("Delete From t_r3_user_task Where FK_PARENT=$v_user_task_id");

            $v_role = get_role($v_task_code);

            //Nếu được phân công tiep nhan, thì cũng làm bổ sung luôn
            if ($v_role == _CONST_TIEP_NHAN_ROLE)
            {
                $v_couple_task_code = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE;
                $stmt               = 'Delete From t_r3_user_task Where C_RECORD_TYPE_CODE=? And C_TASK_CODE=? And C_USER_LOGIN_NAME=?';
                $params             = array($v_record_type_code, $v_couple_task_code, $v_user_code);

                $this->db->debug = 0;
                $this->db->Execute($stmt, $params);
            }

            //Nếu được phân công duyệt, thì cũng làm phê duyệt HS bổ sung
            if ($v_role == _CONST_XET_DUYET_ROLE)
            {
                $v_couple_task_code = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_BO_SUNG_ROLE;
                $stmt               = 'Delete From t_r3_user_task Where C_RECORD_TYPE_CODE=? And C_TASK_CODE like ? And C_USER_LOGIN_NAME=?';
                $params             = array($v_record_type_code, '%' . $v_couple_task_code . '%', $v_user_code);
                $this->db->debug    = 0;
                $this->db->Execute($stmt, $params);
            }
            echo $this->db->Affected_Rows();
        }
    }

    public function qry_all_user_task($record_type_code)
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt   = "Select Convert(Xml, (Select UT.*
                                            , U.C_NAME as C_USER_NAME
                                            ,U.C_JOB_TITLE
                                            From t_r3_user_task UT Left Join t_cores_user U On UT.C_USER_LOGIN_NAME=U.C_LOGIN_NAME
                                            Where C_RECORD_TYPE_CODE=?
                                            Order By UT.PK_USER_TASK
                                            FOR XML RAW, ROOT('rows')
                                        ), 1) a";
            $params = array($record_type_code);

            return $this->db->getOne($stmt, $params);
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select
                        UT.*
                        , U.C_NAME as C_USER_NAME
                        ,U.C_JOB_TITLE
                    From t_r3_user_task UT Left Join t_cores_user U On UT.C_USER_LOGIN_NAME=U.C_LOGIN_NAME
                    Where C_RECORD_TYPE_CODE=?
                    Order By UT.PK_USER_TASK";

            $params = array($record_type_code);

            $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
            $arr_all_user_task = $this->db->GetAll($stmt, $params);
            $xml               = '<rows>';
            for ($i = 0; $i < sizeof($arr_all_user_task); $i++)
            {
                $rows = $arr_all_user_task[$i];
                $xml .= '<row ';
                foreach ($rows as $key => $val)
                {
                    $xml .= $key . '="' . $val . '" ';
                }
                $xml .= ' />';
            }
            $xml .= '</rows>';

            return $xml;
        }
    }

    function qry_single_record_type($id = '', $code = '')
    {
        return $this->db->GetRow("Select * 
            From t_r3_record_type 
            Where PK_RECORD_TYPE=? Or C_CODE=?", array($id, $code));
    }

    function qry_all_record_type($where = '1=1', $limit = 0, $offset = 0)
    {
        $count     = $this->db->GetOne("Select Count(*) From t_r3_record_type Where $where");
        $sql_limit = $limit ? "Limit $limit Offset $offset" : '';
        return $this->db->GetAll("Select *, $count As TOTAL_RECORD, @rownum:=@rownum + 1 AS RN 
            From t_r3_record_type 
            Where $where $sql_limit");
    }

    /**
     * Copy phân công từ thủ tục mã $src đến $dest
     * @param int $dest
     * @param int $src
     * @return string Thông báo lỗi hoặc null nếu thành công
     */
    function copy_assign($dest, $src)
    {
        $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
        $arr_single_dest = $this->qry_single_record_type($dest);
        $arr_single_src  = $this->qry_single_record_type($src);
        $v_no_match      = "Sao chép thất bại! TASK của quy trình nguồn phải là tập con của quy trình đích";

        if (!$arr_single_dest)
            return "Không tìm thấy thủ tục có mã: $dest";
        if (!$arr_single_src)
            return "Không tìm thấy thủ tục có mã: $src";
        if (!$dom_workflow_src  = $this->get_dom_workflow($arr_single_src['C_CODE']))
            return "C_XML_WORKFLOW của {$arr_single_src['C_CODE']} Bị hỏng hoặc không có";
        if (!$dom_workflow_dest = $this->get_dom_workflow($arr_single_dest['C_CODE']))
            return "C_XML_WORKFLOW của {$arr_single_dest['C_CODE']} Bị hỏng hoặc không có";

        $v_workflow_code_src  = $arr_single_src['C_CODE'];
        $v_workflow_code_dest = $arr_single_dest['C_CODE'];

        //xoá hết phân công cũ
        $this->db->Execute("Delete From t_r3_user_task Where C_RECORD_TYPE_CODE=?", array($v_workflow_code_dest));

        $select_fields  = 'PK_USER_TASK, C_RECORD_TYPE_CODE, C_TASK_CODE
                , C_USER_LOGIN_NAME, C_GROUP_CODE, C_NEXT_TASK_CODE, C_STEP_TIME
                , C_TASK_TIME, C_STEP_FIRST_TASK, C_PREV_STEP_LAST_TASK, C_NO_CHAIN
                , C_NEXT_NO_CHAIN_TASK_CODE, FK_PARENT';
        $insert_fields  = 'C_RECORD_TYPE_CODE, C_TASK_CODE
                , C_USER_LOGIN_NAME, C_GROUP_CODE, C_NEXT_TASK_CODE, C_STEP_TIME
                , C_TASK_TIME, C_STEP_FIRST_TASK, C_PREV_STEP_LAST_TASK, C_NO_CHAIN
                , C_NEXT_NO_CHAIN_TASK_CODE, FK_PARENT';
        $values         = '?' . str_repeat(',?', 11);
        $arr_all_assign = $this->db->GetAll("Select $select_fields    
            From t_r3_user_task 
            Where C_RECORD_TYPE_CODE=? And FK_PARENT Is Null", array($v_workflow_code_src));
        if (!$arr_all_assign)
        {
            return "Thủ tục nguồn chưa được phân công!";
        }
        //Duyệt và copy
        foreach ($arr_all_assign as $assign)
        {
            $id = $assign['PK_USER_TASK'];
            unset($assign['PK_USER_TASK']);
            //Sửa lại taskcode cho đúng thủ tục mới

            foreach ($assign as $k => $v)
            {
                if (!$assign[$k])
                    continue;
                $assign[$k] = str_replace($v_workflow_code_src, $v_workflow_code_dest, $v);
            }
            $this->db->Execute("Insert Into t_r3_user_task($insert_fields) Values($values)", $assign);
            $v_user_task_id = $this->db->Insert_ID('t_r3_user_task');
            //copy cả task liên quan
            $arr_related    = $this->db->GetAll("Select $select_fields From t_r3_user_task Where FK_PARENT={$id}");
            foreach ($arr_related as $related)
            {
                $related['FK_PARENT'] = $v_user_task_id;
                unset($related['PK_USER_TASK']);
                foreach ($related as $k => $v)
                {
                    if (!$assign[$k])
                        continue;
                    $related[$k] = str_replace($v_workflow_code_src, $v_workflow_code_dest, $v);
                }
                $this->db->Execute("Insert Into t_r3_user_task($insert_fields) Values($values)", $related);
            }
        }
        return null;
    }

    /**
     * 
     * @param string $record_type_code
     * @return \SimpleXMLElement Object xml của quy trình
     */
    function get_dom_workflow($record_type_code)
    {
        $record_type_code = strtoupper($record_type_code);
        $file_path        = SERVER_ROOT . "apps/r3/xml-config/{$record_type_code}/{$record_type_code}_workflow.xml";
        $xml_workflow     = file_exists($file_path) ? file_get_contents($file_path) : '<data/>';
        return simplexml_load_string($xml_workflow);
    }

}

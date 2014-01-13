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

////step[1]/task[1]/@code
class record_Model extends Model
{

    /**
     * @var boolean 
     */
    private $nsd_la_can_bo_cap_xa;

    /**
     * @var array
     */
    public $arr_can_bo_cap_xa;

    /**
     * @var \ADOConnection 
     */
    public $db;

    function __construct()
    {
        parent::__construct();
        $this->nsd_la_can_bo_cap_xa = Session::get('la_can_bo_cap_xa');
    }

    private function _get_xml_workflow_file_path($v_record_type_code)
    {
        return SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . $v_record_type_code . DS . $v_record_type_code . '_workflow' . '.xml';
    }

    private function _get_xml_processing($record_id)
    {
        $stmt   = 'Select C_XML_PROCESSING From view_record Where PK_RECORD=?';
        $params = array($record_id);

        return $this->db->getOne($stmt, $params);
    }

    function qry_all_record_accept_by($v_user_code, $v_record_type_code)
    {
        page_calc($v_start, $v_end);
        $v_start = $v_start - 1;
        $v_limit = $v_end - $v_start;

        $condition_query = "R.C_DELETED = 0 And R.C_CLEAR_DATE Is Null And R.C_REJECTED = 0 
            And R.C_CREATE_BY='$v_user_code' And R.C_RECORD_NO Like '$v_record_type_code-%'";

        //Dem tong ban ghi
        $v_total_record = $this->db->getOne("Select Count(*) TOTAL_RECORD From t_r3_record R Where $condition_query");
        $sql            = "SELECT
                        @rownum:=@rownum + 1 AS RN
                        ,a.*
                        , 1 as C_OWNER
                        ,$v_total_record as TOTAL_RECORD
                        ,CASE
                            WHEN (DATEDIFF(NOW(), a.C_DOING_STEP_DEADLINE_DATE)>0) THEN
                                (SELECT -1 * (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())<=0
                                    AND DATEDIFF(WD.C_DATE, a.C_DOING_STEP_DEADLINE_DATE)>0 )
                            ELSE
                                (SELECT (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())>=0
                                    AND DATEDIFF(WD.C_DATE, a.C_DOING_STEP_DEADLINE_DATE)<0 )
                        END AS C_DOING_STEP_DAYS_REMAIN
                        ,CASE
                            WHEN (DATEDIFF(NOW(),a.C_RETURN_DATE)>0) THEN
                                (SELECT -1 * (COUNT(*))
                                FROM view_working_date WD
                                WHERE  DATEDIFF(WD.C_DATE, NOW())<=0
                                    AND DATEDIFF(WD.C_DATE, a.C_RETURN_DATE)>0 )
                            ELSE
                                (SELECT (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())>=0
                                    AND DATEDIFF(WD.C_DATE, a.C_RETURN_DATE)<0 )
                        END AS C_RETURN_DAYS_REMAIN
                    FROM (
                        SELECT
                            R.PK_RECORD
                            ,R.FK_RECORD_TYPE
                            ,R.C_RECORD_NO
                            , CAST(R.C_RECEIVE_DATE  AS CHAR(19)) AS C_RECEIVE_DATE
                            , CAST(R.C_RETURN_DATE  AS CHAR(19)) AS C_RETURN_DATE
                            ,R.C_RETURN_PHONE_NUMBER
                            ,R.C_XML_DATA
                            ,R.C_XML_PROCESSING
                            ,R.C_DELETED
                            ,R.C_CLEAR_DATE
                            ,R.C_XML_WORKFLOW
                            ,R.C_RETURN_EMAIL
                            ,R.C_REJECTED
                            ,R.C_REJECT_REASON
                            ,R.C_CITIZEN_NAME
                            ,R.C_ADVANCE_COST
                            ,R.C_CREATE_BY
                            ,R.C_NEXT_TASK_CODE
                            ,R.C_NEXT_USER_CODE
                            ,R.C_NEXT_CO_USER_CODE
                            ,R.C_LAST_TASK_CODE
                            ,R.C_LAST_USER_CODE
                            , CAST(R.C_DOING_STEP_BEGIN_DATE  AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
                            ,R.C_DOING_STEP_DEADLINE_DATE
                            ,R.C_BIZ_DAYS_EXCEED
                            ,(SELECT @rownum:=0)
                            ,R.C_PAUSE_DATE
                            ,R.C_UNPAUSE_DATE
                            ,(Select C_STEP_TIME From t_r3_user_task UT 
                                Where R.C_NEXT_TASK_CODE=UT.C_TASK_CODE Limit 1) As C_STEP_TIME
                        FROM t_r3_record R 
                        
                        WHERE $condition_query
                        ORDER BY C_RECEIVE_DATE DESC
                        Limit $v_start, $v_limit
                    ) a";
        //Left Join t_r3_user_task UT On R.C_NEXT_TASK_CODE=UT.C_TASK_CODE And R.C_NEXT_USER_CODE=UT.C_USER_LOGIN_NAME
        return $this->db->getAll($sql);
    }

    function return_internet_record()
    {

        $v_goback    = get_post_var('hdn_goback');
        $v_item_list = get_post_var('hdn_item_id_list', 0);

        $table = 't_r3_internet_record';
        $where = "(C_COMMENT <> '' Or C_COMMENT Is Not NULL) And C_IS_REAL_RECORD<>1";
        $this->db->Execute("Update $table Set C_CLEAR_DATE=NOW() Where $where");
        if ($this->db->ErrorNo())
        {
            $this->exec_fail($v_goback, $this->db->ErrorMsg());
        }
        else
        {
            $html = '<html><head></head><body>';
            $html .= '<form name="frmMain" action="' . $v_goback . '" method="POST">';
            $html .= '</form>';
            $html .= '<script type="text/javascript">document.frmMain.submit();</script>';
            $html .= '</body></html>';

            header('Content-length: ' . strlen($html));
            header('Connection: close');
            ignore_user_abort(1);
            echo $html;
            flush();

            set_time_limit(120);
            //Gui email cho cong dan

            $arr_records = $this->db->GetAll("Select R.*, RT.C_NAME As C_RECORD_TYPE_NAME
                From t_r3_internet_record R 
                Inner Join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                Where $where
                ");

            foreach ($arr_records as $arr_single_record)
            {
                file_get_contents(SITE_ROOT . 'nop_ho_so/svc_gui_lai_email/' . $arr_single_record['C_RECORD_NO']);
            }
        }
    }

    private function _qry_all_blacklist_rule()
    {
        $stmt = "Select 
                    PK_RULE, 
                    C_NAME, 
                    C_OWNER_NAME, 
                	C_RECORD_TYPE_CODE, 
                	C_RULE_CONTENT, 
                	C_ACTION
                From t_r3_blacklist_rule
                Where C_STATUS='1' 
                    And (DATEDIFF(C_BEGIN_DATE, Now()) <=0)   
                    And (DATEDIFF(C_END_DATE, Now()) >=0) 
                Order By C_ORDER";
        return $this->db->getAll($stmt);
    }

    /**
     * Lấy thông tin về bước xử lý tiếp theo, dựa trên quy trình và thông tin phân công CB vào quy trình
     * @param string $current_task_code Mã công việc đang thực hiện
     * @return array Mảng thông tin về công việc tiếp theo
     * @access Private
     */
    private function _qry_next_task_info($current_task_code)
    {
        $v_user_code       = Session::get('user_code');
        $current_task_code = replace_bad_char($current_task_code);

        $stmt = "Select
                    a.C_TASK_CODE As C_NEXT_TASK_CODE
                  , U.C_NAME AS C_NEXT_USER_NAME
                  , a.C_USER_LOGIN_NAME AS C_NEXT_USER_LOGIN_NAME
                  , U.C_JOB_TITLE AS C_NEXT_USER_JOB_TITLE
                  , a.C_GROUP_CODE AS C_NEXT_GROUP_CODE
                From t_cores_user U
                    Right join
                    (
                        Select
                          takeover.C_TASK_CODE
                        , takeover.C_USER_LOGIN_NAME
                        , takeover.C_GROUP_CODE
                        From t_r3_user_task sender
                        Left join t_r3_user_task takeover
                         On sender.C_NEXT_TASK_CODE = takeover.C_TASK_CODE
                        Where sender.C_TASK_CODE = ?
                        And sender.C_USER_LOGIN_NAME = ?
                    ) a
                    On a.C_USER_LOGIN_NAME = U.C_LOGIN_NAME";

        $params            = array($current_task_code, $v_user_code);
        $a                 = $this->db->getRow($stmt, $params); //Chi lay nguoi dau tien
        //LienND update 2013-01-29
        $v_count           = $this->db->getOne("Select Count(*) From ($stmt) a", $params);
        $a['C_TOTAL_USER'] = $v_count;

        //Công việc tiếp theo thuộc về một bước khác??
        $stmt              = 'Select distinct C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE=?';
        $v_next_group_code = $this->db->getOne($stmt, $current_task_code);

        $stmt             = 'Select distinct C_NEXT_TASK_CODE From t_r3_user_task Where C_TASK_CODE=?';
        $v_next_task_code = $this->db->getOne($stmt, $current_task_code);

        $stmt             = 'Select COUNT(*)
                    From t_r3_user_task
                    Where C_GROUP_CODE=?
                        And C_TASK_CODE=?';
        $params           = array($v_next_group_code, $v_next_task_code);
        $a['IS_NEW_STEP'] = ($this->db->getOne($stmt, $params) == 0) ? TRUE : FALSE;

        return $a;
    }

    private function _insert_record_processing_step($record_id, $xml_step)
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt   = 'Exec [dbo].[insert_r3_record_processing_step] ?, ?';
            $params = array($record_id, $xml_step);
            $this->db->Execute($stmt, $params);
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt             = 'Select C_XML_PROCESSING From t_r3_record Where PK_RECORD=?';
            $params           = array($record_id);
            $v_xml_processing = $this->db->getOne($stmt, $params);

            $v_xml_processing = xml_add_declaration($v_xml_processing);
            $doc              = new DOMDocument();
            $doc->loadXML($v_xml_processing);
            $f                = $doc->createDocumentFragment();
            $f->appendXML($xml_step);
            $doc->documentElement->appendChild($f);
            $v_xml_processing = xml_add_declaration($doc->saveXML(), 0);

            $stmt   = 'Update t_r3_record Set C_XML_PROCESSING=? Where PK_RECORD=?';
            $params = array($v_xml_processing, $record_id);
            $this->db->Execute($stmt, $params);
        }
    }

    /**
     * 
     * @param int $v_record_id 
     * @param string $xml_next_task
     * @param bool $is_no_chain Có phải nhánh không
     * @param bool $force_update Update trong mọi trường hợp
     */
    private function _update_next_task_info($v_record_id, $xml_next_task, $is_no_chain = FALSE, $force_update = false)
    {
        $v_record_id = replace_bad_char($v_record_id);

        $xml_next_task = xml_add_declaration($xml_next_task);
        $dom           = simplexml_load_string($xml_next_task);

        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt   = 'Exec [dbo].[update_r3_next_task_info] ?,?';
            $params = array($v_record_id, $xml_next_task);
            $this->db->Execute($stmt, $params);
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            //File cau hinh???
            $stmt                     = 'Select rt.C_CODE
                                    From t_r3_record_type rt
                                        Left Join view_record r
                                        On rt.PK_RECORD_TYPE=r.FK_RECORD_TYPE
                                    Where r.PK_RECORD=?';
            $params                   = array($v_record_id);
            $v_record_type_code       = $this->db->getOne($stmt, $params);
            $v_xml_workflow_file_name = $this->_get_xml_workflow_file_path($v_record_type_code);
            $dom_xml_workflow         = simplexml_load_file($v_xml_workflow_file_name);

            //Neu la NO_CHAIN task
            if ($is_no_chain)
            {
                $v_executing_task_code = $this->db->getOne("Select C_NEXT_NO_CHAIN_TASK_CODE From t_r3_record Where PK_RECORD=$v_record_id");
                if ($v_executing_task_code == _CONST_FINISH_NO_CHAIN_STEP_TASK)
                {
                    $stmt   = 'Update t_r3_record Set C_NEXT_NO_CHAIN_TASK_CODE=NULL, C_NEXT_NO_CHAIN_USER_CODE=NULL Where PK_ERCORD=?';
                    $params = array($v_record_id);
                    $this->db->Execute($stmt, $params);
                }
                else
                {
                    $arr_next_no_chain_task_info = $this->_qry_next_task_info($v_executing_task_code);
                    $v_next_no_chain_task_code   = $arr_next_no_chain_task_info['C_NEXT_TASK_CODE'];
                    $v_next_no_chain_user_code   = ',' . $arr_next_no_chain_task_info['C_NEXT_USER_LOGIN_NAME'] . ',';

                    $stmt   = "Update t_r3_record Set C_NEXT_NO_CHAIN_TASK_CODE=?, C_NEXT_NO_CHAIN_USER_CODE=? Where PK_RECORD=?";
                    $params = array($v_next_no_chain_task_code, $v_next_no_chain_user_code, $v_record_id);
                    $this->db->Execute($stmt, $params);
                }
            }
            else
            {
                //Thuc hien task chinh 
                //Mã chính xác công việc đang thực hiện 
                $v_executing_task_code = $this->db->getOne("Select C_NEXT_TASK_CODE From t_r3_record Where PK_RECORD=$v_record_id");
                //ma cong viec tiep theo
                $v_next_task_code      = get_xml_value($dom, '//next_task[1]/@code[1]');
                //die(get_role($v_executing_task_code));
                //Group name
                $sql                   = "SELECT
                            C_NAME
                        FROM t_cores_group
                        WHERE C_CODE = (SELECT
                                            C_GROUP_CODE
                                        FROM t_r3_user_task
                                        WHERE C_TASK_CODE = '$v_next_task_code'
                                        LIMIT 1)";
                $v_group_name          = $this->db->getOne($sql);
                //Step time
                $v_step_time           = $this->db->getOne("Select C_STEP_TIME From t_r3_user_task Where C_TASK_CODE='$v_next_task_code'");
                //Add Attribute 
                $dom_next_task         = xpath($dom, '//next_task', XPATH_DOM);
                //Add @group_code
                $v_group_code          = $this->db->getOne("SELECT C_GROUP_CODE FROM t_r3_user_task WHERE C_TASK_CODE = '$v_next_task_code'");
                if ($dom_next_task->attributes()->group_code)
                {
                    $dom_next_task->attributes()->group_code = $v_group_code;
                }
                else
                {
                    $dom_next_task->addAttribute('group_code', $v_group_code);
                }

                //add @group_name
                if ($dom_next_task->attributes()->group_name)
                {
                    $dom_next_task->attributes()->group_name = $v_group_name;
                }
                else
                {
                    $dom_next_task->addAttribute('group_name', $v_group_name);
                }

                //add @step_time
                if ($dom_next_task->attributes()->step_time)
                {
                    $dom_next_task->attributes()->step_time = $v_step_time;
                }
                else
                {
                    $dom_next_task->addAttribute('step_time', $v_step_time);
                }
                //Phi
                if (!$dom_next_task->attributes()->fee)
                {
                    $dom_processing    = simplexml_load_string($this->db->getOne("Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=$v_record_id"));
                    $v_fee             = get_xml_value($dom_processing, "//step[fee !=''][last()]/fee");
                    $v_fee_description = get_xml_value($dom_processing, "//step[fee !=''][last()]/fee_description");

                    if ($v_fee != NULL && $v_fee != '' && $v_fee != '0')
                    {
                        $dom_next_task->addAttribute('fee', $v_fee);
                        $dom_next_task->addAttribute('fee_description', $v_fee_description);
                    }
                }

                $xml_next_task = $dom->asXML();
                $xml_next_task = xml_remove_declaration($xml_next_task);

                //Update next task
                $stmt   = "Update t_r3_record
                            Set C_XML_PROCESSING=(Select UpdateXML(C_XML_PROCESSING,'//next_task',?))
                            Where PK_RECORD=?";
                $params = array($xml_next_task, $v_record_id);
                $this->db->Execute($stmt, $params);

                // Neu @v_task_code la task dau tien cua step
                $stmt   = "Select COUNT(*) From t_r3_user_task Where C_STEP_FIRST_TASK=C_TASK_CODE And C_TASK_CODE=?";
                $params = array($v_next_task_code);

                if ($this->db->getOne($stmt, $params) > 0)
                {
                    //Cong them ngay cho buoc sau ????
                    $v_addition_time = 0;
                    if (get_role($v_next_task_code) == _CONST_KY_ROLE)
                    {
                        //Kiem tra auto_add_time?
                        $v_auto_add_time = get_xml_value($dom_xml_workflow, "//task[@code='$v_next_task_code']/@auto_add_time");
                        if (strtolower($v_auto_add_time) == 'true')
                        {
                            $v_return_date = $this->db->getOne("Select C_RETURN_DATE From view_record Where PK_RECORD=$v_record_id");

                            //So ngay con lai den luc tra KQ:
                            $v_days_to_return_date = $this->days_between_two_date($this->getDate(), $v_return_date);
                            $v_remain_days         = $v_days_to_return_date;

                            //Tính toán số ngày còn dư
                            $arr_steps = xpath($dom_xml_workflow, '//step');
                            for ($i = count($arr_steps) - 1; $i >= 0; $i--)
                            {
                                $dom_step = $arr_steps[$i];
                                $v_remain_days -= (double) $dom_step->attributes()->time;
                                foreach ($dom_step->children() as $dom_task)
                                {
                                    if ($dom_task->attributes()->code == $v_next_task_code)
                                    {
                                        break 2;
                                    }
                                }
                            }
                            if ($v_remain_days > 0)
                            {
                                $v_addition_time = $v_remain_days;
                            }
                        }
                    }//end add time
                    $v_step_deadline_date = $this->_step_deadline_calc($v_step_time + $v_addition_time);

                    //Thi cap nhat deadline cua step
                    $stmt   = 'Update t_r3_record Set C_DOING_STEP_DEADLINE_DATE=? Where PK_RECORD=?';
                    $params = array($v_step_deadline_date, $v_record_id);
                    $this->db->Execute($stmt, $params);
                }
                //XML -> Column
                $stmt   = "Update t_r3_record Set
                                C_NEXT_TASK_CODE = ExtractValue(C_XML_PROCESSING, '//next_task[1]/@code[1]')
                                ,C_NEXT_USER_CODE = ExtractValue(C_XML_PROCESSING, '//next_task[1]/@user[1]')
                                ,C_NEXT_CO_USER_CODE = ExtractValue(C_XML_PROCESSING, '//next_task[1]/@co_user[1]')
                                ,C_LAST_TASK_CODE = ExtractValue(C_XML_PROCESSING, '//step[last()]/@code[1]')
                                ,C_LAST_USER_CODE = ExtractValue(C_XML_PROCESSING, '//step[last()]/user_code[1]')
                        Where PK_RECORD=?";
                $params = array($v_record_id);
                $this->db->Execute($stmt, $params);

                //Kiem tra Kiem tra xem co cong viec dang NO_CHAIN o buoc sau khong?
                $v_next_no_chain_task_code = $this->db->GetOne("Select C_NEXT_NO_CHAIN_TASK_CODE From t_r3_user_task Where C_TASK_CODE='$v_executing_task_code'");
                if ($v_next_no_chain_task_code != NULL && strlen($v_next_no_chain_task_code) > 0)
                {
                    //Lay danh sach can bo thuc hien no_chain task
                    $stmt                      = "Select Concat(',', Group_Concat(C_USER_LOGIN_NAME SEPARATOR ','),',') C_USER_LOGIN_NAME
                            From t_r3_user_task 
                            Where C_TASK_CODE = ?";
                    $params                    = array($v_next_no_chain_task_code);
                    $v_no_chain_user_code_list = $this->db->GetOne($stmt, $params);

                    $stmt   = 'Update t_r3_record Set C_NEXT_NO_CHAIN_TASK_CODE=?, C_NEXT_NO_CHAIN_USER_CODE=? Where PK_RECORD=?';
                    $params = array($v_next_no_chain_task_code, $v_no_chain_user_code_list, $v_record_id);
                    $this->db->Execute($stmt, $params);
                }//end no_chain
                //Neu chua hoan thanh nghiep vu, Role tiep theo la thu phi, tra ket qua, biz_done ---> Danh dau hoan thanh nghiep vu
                $v_next_role = strtoupper(get_role($v_next_task_code));
                $v_biz_done  = get_xml_value($dom_xml_workflow, "//task[@code ='$v_executing_task_code']/@biz_done");
                if ((strtolower($v_biz_done) == 'true') OR ($v_next_role == _CONST_THU_PHI_ROLE) OR ($v_next_role == _CONST_TRA_KET_QUA_ROLE))
                {
                    //Hoan thanh nghiep vu nhanh/cham bao nhieu ngay ?
                    $v_return_days_remain = $this->_return_days_remain_calc($v_record_id);

                    $stmt   = 'Update t_r3_record Set C_BIZ_DAYS_EXCEED=?,C_DOING_STEP_DEADLINE_DATE=NULL Where PK_RECORD=? And C_BIZ_DAYS_EXCEED Is Null';
                    $params = array($v_return_days_remain, $v_record_id);
                    $this->db->Execute($stmt, $params);

                    //gửi thư
//                    $arr_single_record = $this->qry_single_record($v_record_id);
//                    if ($arr_single_record['C_RETURN_EMAIL'])
//                    {
//                        require dirname(__FILE__) . '/classes/announce.inc.php';
//                        $mail = new announce_biz_done($arr_single_record['C_RETURN_EMAIL'], $arr_single_record);
//                        $mail->send();
//                    }
                }

                //Neu Unpause: Tinh so ngay tu ngay pause -> unpause: Cong them vao ngay hen tra ket qua
                //Kiem tra PAUSE
                $v_is_pause = get_xml_value($dom_xml_workflow, "//task[@code='$v_executing_task_code']/@pause");
                if (strtolower($v_is_pause) == 'true' OR
                        get_role($v_executing_task_code) == _CONST_THONG_BAO_BO_SUNG_ROLE OR
                        get_role($v_executing_task_code) == _CONST_BO_SUNG_ROLE OR
                        $dom->attributes()->pause == 'true')
                {
                    //Them action vao C_XML_PROCESSING
                    $v_xml_processing = $this->db->getOne("Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=$v_record_id");
                    $v_ref_seq        = get_xml_value(simplexml_load_string($v_xml_processing), "//step[@code='$v_executing_task_code'][last()]/@seq");
                    //Insert Step
                    $v_action_id      = uniqid();
                    $action           = '<action do="pause">';
                    $action .= '<id>' . $v_action_id . '</id>';
                    $action .= '<ref_seq>' . $v_ref_seq . '</ref_seq>';
                    $action .= '</action>';
                    $this->_insert_record_processing_step($v_record_id, $action);

                    $sql = "Update t_r3_record Set C_PAUSE_DATE=Now(), C_UNPAUSE_DATE=Null Where PK_RECORD=$v_record_id";
                    $this->db->Execute($sql);
                }

                //Kiem tra UNPAUSE
                $v_is_unpause = get_xml_value($dom_xml_workflow, "//task[@code='$v_executing_task_code']/@unpause");
                if (strtolower($v_is_unpause) == 'true' OR get_role($v_executing_task_code) == _CONST_BO_SUNG_ROLE)
                {
                    //Them action vao C_XML_PROCESSING
                    $v_xml_processing = $this->db->getOne("Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=$v_record_id");
                    $v_ref_seq        = get_xml_value(simplexml_load_string($v_xml_processing), "//step[@code='$v_executing_task_code'][last()]/@seq");
                    //Insert Step
                    $v_action_id      = uniqid();
                    $action           = '<action do="unpause">';
                    $action .= '<id>' . $v_action_id . '</id>';
                    $action .= '<ref_seq>' . $v_ref_seq . '</ref_seq>';
                    $action .= '</action>';
                    $this->_insert_record_processing_step($v_record_id, $action);

                    $sql = "Update t_r3_record Set C_UNPAUSE_DATE=Now() Where PK_RECORD=$v_record_id";
                    $this->db->Execute($sql);

                    //Cong them vao ngay tra ket qua
                    $sql               = "Select
                                C_RETURN_DATE
                                ,(Select
                                       Count(*)
                                   From t_cores_calendar
                                   Where C_OFF = 0
                                         And datediff(C_PAUSE_DATE, C_DATE) <= 0
                                         And Datediff(C_UNPAUSE_DATE, C_DATE) > 0) As C_PAUSED_DAYS
                            From view_processing_record
                            Where PK_RECORD = $v_record_id";
                    $r                 = $this->db->getRow($sql);
                    $v_paused_days     = $r[C_PAUSED_DAYS];
                    $v_old_return_date = $r[C_RETURN_DATE];

                    //Tinh ra ngay tra ket qua moi
                    $v_new_return_date = $this->next_working_day($v_paused_days, $v_old_return_date);
                    $sql               = "Update t_r3_record 
                            Set C_RETURN_DATE='$v_new_return_date' 
                            Where PK_RECORD=$v_record_id";
                    $this->db->Execute($sql);
                }
            }//end main task
        }//end MYSQL
        return true;
    }

    /**
     * Kiểm tra hồ sơ có trong tay cán bộ không, chỉ truyền 1 trong 2 tham số, tham số còn lại để null/false
     * @param int $record_id t_r3_record.PK_RECORD
     * @param string $record_no t_r3_record.C_RECORD_NO
     * @return boolean
     */
    protected function _check_inhand_record($record_id = null, $record_no = null)
    {
        if ($record_id || $record_no)
        {
            $v_user_code = Session::get('user_code');
            $params      = array($v_user_code, "%,$v_user_code,%", "%,$v_user_code,%", "%,$v_user_code,%");
            $where       = " (C_NEXT_USER_CODE=? 
            Or C_NEXT_USER_CODE Like ? 
            OR C_NEXT_CO_USER_CODE like ?
            Or C_NEXT_NO_CHAIN_USER_CODE Like ?)";
            if ($record_id)
            {
                $where .= " And PK_RECORD=? ";
                $params[] = $record_id;
            }
            elseif ($record_no)
            {
                $where .= " And C_RECORD_NO=? ";
                $params[] = $record_no;
            }
            $sql = "Select Exists(Select PK_RECORD From view_processing_record Where $where)";
            return (bool) $this->db->GetOne($sql, $params);
        }
        return false;
    }

    private function _do_rollback_step($v_record_id)
    {
        //Rollback
        //get last step info
        if (!$this->_check_inhand_record($v_record_id))
        {
            return;
        }
        $v_xml_processing = $this->db->getOne('Select C_XML_PROCESSING 
            From view_processing_record 
            Where PK_RECORD=?', array($v_record_id));
        if (!$v_xml_processing)
        {
            return;
        }
        $d                = simplexml_load_string($v_xml_processing);
        $v_last_task_code = xpath($d, "//step[last()]/@code", XPATH_STRING);

        $stmt             = 'Select C_TASK_CODE From t_r3_user_task Where C_NEXT_TASK_CODE=?';
        $v_prev_task_code = $this->db->getOne($stmt, array($v_last_task_code));

        //Re-Next
        $arr_next_task_info     = $this->_qry_next_task_info($v_prev_task_code);
        $v_next_task_code       = $arr_next_task_info['C_NEXT_TASK_CODE'];
        $v_next_user_login_name = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
        $v_next_user_name       = $arr_next_task_info['C_NEXT_USER_NAME'];
        $v_next_user_job_title  = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];

        if (!$v_next_task_code)
        {
            return;
        }

        $xml_next_task = '<next_task ';
        $xml_next_task .= ' code="' . $v_next_task_code . '"';
        $xml_next_task .= ' user="' . $v_next_user_login_name . '"';
        $xml_next_task .= ' user_name="' . $v_next_user_name . '"';
        $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
        $xml_next_task .= ' reason="' . $v_reason . '"';
        $xml_next_task .= ' />';

        //Step log
        $v_step_seq = uniqid();
        $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
        $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
        $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
        $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
        $step .= '<datetime>' . $this->getDate() . '</datetime>';
        $step .= '<reason>' . $v_reason . '</reason>';
        $step .= '</step>';
        $this->_insert_record_processing_step($v_record_id, $step);
        $this->_update_next_task_info($v_record_id, $xml_next_task);
    }

    /**
     * Tinh ngay ket thuc cua step
     * @param Int $days So ngay cua step
     * @param datetime $begin_datetime Bat dau tinh tu ngay
     */
    private function _step_deadline_calc($days, $begin_datetime = NULL)
    {
        if ($begin_datetime == NULL)
        {
            $begin_datetime = $this->get_datetime_now();
        }
        $v_begin_hour = Date('H', strtotime($begin_datetime));

        //Tinh ket qua theo so ngay cua thu tuc
        if ($days == 0) //Thu tuc 0 ngay
        {
            if ($v_begin_hour < 12) //Nhận sáng trả sáng
            {
                $v_end_date = substr($begin_datetime, 0, 10) . chr(32) . _CONST_MORNING_END_WORKING_TIME;
            }
            else //Nhận chiều trả chiều
            {
                $v_end_date = substr($begin_datetime, 0, 10) . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
            }
        }
        elseif ($days == 0.5) //Thủ tục 0.5 ngày
        {
            if ($v_begin_hour < 12) //Nhận sáng trả chiều
            {
                $v_end_date = substr($begin_datetime, 0, 10) . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
            }
            else //Nhận chiều trả sáng ngày làm việc tiếp theo
            {
                $v_end_date = $this->next_working_day(1, $begin_datetime) . chr(32) . _CONST_MORNING_END_WORKING_TIME;
            }
        }
        else //Cộng số ngày làm việc, Nhận sáng trả sáng, nhận chiều trả chiều
        {
            $v_end_date = $this->next_working_day($days, $begin_datetime);
            if ($v_begin_hour < 12) //Nhận sáng trả sáng
            {
                $v_end_date .= chr(32) . _CONST_MORNING_END_WORKING_TIME;
            }
            else //Nhận chiều trả chiều
            {
                $v_end_date .= chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
            }
        }

        return $v_end_date;
    }

    /**
     * Tính số ngày đã dùng để hoàn thành xử lý nghiệp vụ đối với 1 HỒ SƠ
     * HỒ SƠ được coi là hoàn thành nghiệp vụ nếu bước sau là THU_PHI, hoac TRA KET QUA
     * @param type $record_id
     */
    private function _return_days_remain_calc($v_record_id)
    {
        //Hoan thanh nghiep vu nhanh/cham bao nhieu ngay ?
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = 'Select
                        Case
                            When (DATEDIFF(day, getdate(),a.C_RETURN_DATE)<0) then
                                (Select -1 * (COUNT(*))
                                FROM t_cores_calendar
                                Where C_OFF=0
                                    And DATEDIFF(day, C_DATE, GETDATE())>0
                                    And DATEDIFF(day, C_DATE, a.C_RETURN_DATE)<=0 )
                            else
                                (Select (COUNT(*))
                                FROM t_cores_calendar
                                Where C_OFF=0
                                    And DATEDIFF(day, C_DATE, GETDATE())<0
                                    And DATEDIFF(day, C_DATE, a.C_RETURN_DATE)>=0 )
                        End as C_RETURN_DAYS_REMAIN
                    From view_record a
                    Where PK_RECORD=?';
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = 'Select
                        Case
                            When (DATEDIFF(Now(),a.C_RETURN_DATE)>0) then
                                (Select -1 * (COUNT(*))
                                FROM t_cores_calendar
                                Where C_OFF=0
                                    And DATEDIFF(C_DATE, Now())<=0
                                    And DATEDIFF(C_DATE, a.C_RETURN_DATE)>0 )
                            else
                                (Select (COUNT(*))
                                FROM t_cores_calendar
                                Where C_OFF=0
                                    And DATEDIFF(C_DATE, Now())>=0
                                    And DATEDIFF(C_DATE, a.C_RETURN_DATE)<0 )
                        End as C_RETURN_DAYS_REMAIN
                    From view_record a
                    Where PK_RECORD=?';
        }
        else
        {
            return NULL; //Chua ho tro cac DB khac.
        }

        $params               = array($v_record_id);
        $v_return_days_remain = $this->db->getOne($stmt, $params);

        return $v_return_days_remain;
    }

    function qry_all_ou($conditions)
    {
        $sql = " SELECT * FROM t_cores_ou WHERE $conditions ORDER BY C_ORDER";
        return $this->db->GetAll($sql);
    }

    private function _build_from_and_where_query_by_record_activity($p_activity_filter)
    {
        $v_user_code            = Session::get('user_code');
        $v_village_id_condition = '';
        if ($this->nsd_la_can_bo_cap_xa)
        {
            $v_village_id_condition = "FK_VILLAGE_ID=" . Session::get('village_id') . " And ";
        }
        elseif (($v_village_id = (int) get_request_var('village')) > 0)
        {
            $v_village_id_condition = "FK_VILLAGE_ID = $v_village_id AND ";
        }
        elseif ($v_village_id == -1)
        {
            $v_village_id_condition = "FK_VILLAGE_ID > 0 AND ";
        }

        //neu la tra cuu hs lien thong: chi lay hs lien thong
        if (strtoupper(Session::get('active_role')) == _CONST_TRA_CUU_LIEN_THONG_ROLE)
        {
            $scope = "SELECT c_scope 
                        FROM t_r3_record_type 
                        WHERE pk_record_type = fk_record_type";
            $v_village_id_condition .= " ($scope)=1 AND ";
        }
        elseif (strtoupper(Session::get('active_role')) == _CONST_TRA_CUU_TAI_XA_ROLE)
        {
            $scope = "SELECT c_scope 
                        FROM t_r3_record_type 
                        WHERE pk_record_type = fk_record_type";
            $v_village_id_condition .= " ($scope)=0 AND ";
        }

        //Trang thai HS
        $v_from_and_where  = " view_record Where $v_village_id_condition 1>0 ";
        $p_activity_filter = (int) $p_activity_filter;
        switch ($p_activity_filter)
        {
            case 1:
                //--1: Vua tiep nhan
                $v_from_and_where = " view_processing_record Where $v_village_id_condition C_LAST_TASK_CODE like '%"
                        . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE . "'";
                break;
            case 2:
                //--2: Cho bo sung
                $v_from_and_where = " view_processing_record Where $v_village_id_condition C_NEXT_TASK_CODE like '%"
                        . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE . "'";
                break;
            case 3:
                //3: Bi tu choi
                $v_from_and_where = " view_record Where $v_village_id_condition C_REJECTED=1";
                break;
            case 4:
                //--4: Dang giai quyet - dang nam o phong chuyen mon
                $v_from_and_where = " view_processing_record r
                                            LEFT OUTER JOIN (SELECT fk_record, c_done FROM t_r3_record_supplement WHERE c_done = 0) rs
                                                ON rs.FK_RECORD =r.PK_RECORD 
                                            Where $v_village_id_condition
                                                        r.C_BIZ_DAYS_EXCEED IS NULL
                        				AND (r.C_NEXT_TASK_CODE IS NOT NULL)
                        				AND r.C_CLEAR_DATE IS NULL
                                                        AND rs.FK_RECORD IS NULL
                        				AND r.C_IS_PAUSING <> 1";
                break;
            case 5:
                //--5: Dang trinh ky
                $v_from_and_where = "view_processing_record Where $v_village_id_condition C_NEXT_TASK_CODE like '%"
                        . _CONST_XML_RTT_DELIM . _CONST_KY_ROLE . "'";
                break;
            case 6:
                //--6: Cho tra ket qua
                $v_from_and_where = " view_processing_record Where $v_village_id_condition
                                (   C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                                    Or C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                                 )";
                break;
            case 7:
                //--7: Da tra ket qua
                $v_from_and_where = " view_record Where $v_village_id_condition C_CLEAR_DATE Is Not Null And C_REJECTED <> 1";
                break;
            case 8:
                //--8: Chua tra, Cham tien do - dang nam o phong chuyen mon, (Khong nam trong buoc thu phi, tra ket qua
                $v_from_and_where = " view_processing_record Where $v_village_id_condition
                                    C_BIZ_DAYS_EXCEED Is Null
                                    And (Datediff(C_DOING_STEP_DEADLINE_DATE, Now()) < 0)
                                    And (C_NEXT_TASK_CODE Is Not Null)
                                    And C_IS_PAUSING <> 1";
                $v_from_and_where .= " And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'";
                $v_from_and_where .= " And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'";
                break;
            case 9:
                //--9: HS qua han
                $v_from_and_where = " view_processing_record Where $v_village_id_condition C_BIZ_DAYS_EXCEED Is Null
                And (datediff(C_RETURN_DATE, Now()) < 0) And C_IS_PAUSING <> 1";
                break;
            case 10:
                //--10: Ho so dang tam dung: cho bo sung, nghia vu tai chinh
                $v_from_and_where = " view_processing_record Where $v_village_id_condition C_IS_PAUSING=1";
                break;
            case 11:
                //--11: Khôi phục hồ sơ bị xoá
                $v_from_and_where = " t_r3_record Where $v_village_id_condition C_DELETED=1";
                break;
            default:
                //0: Tat ca hs
                $v_from_and_where = " t_r3_record Where $v_village_id_condition 1>0 ";
                break;
        }

        //LienND update 2012-12-21: Với HỒ SƠ liên thông, xã nào chỉ tra cứu HỒ SƠ của xã đó
        if ($this->nsd_la_can_bo_cap_xa)
        {
            $v_from_and_where .= " And FK_VILLAGE_ID = " . Session::get('village_id');
        }

        if (check_permission('THEO_DOI_GIAM_SAT_TOAN_BO_HO_SO', $this->app_name) == FALSE)
        {
            #$v_from_and_where .= " And LENGTH(ExtractValue(C_XML_PROCESSING, '//step[contains(user_code,''$v_user_code'')]/user_code')) > 0";
        }

        return $v_from_and_where;
    }

    //End: private method section

    /* --------------------------------------------------------------------------------------/

      /**
     * Lấy danh sách Role của một user
     * @param string $user_code
     * @return array
     */
    public function qry_all_user_role($user_code)
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "Select distinct right(C_TASK_CODE, PATINDEX ( '%[A-Z0-9_][" . _CONST_XML_RTT_DELIM . "]%' , reverse(C_TASK_CODE))) C_ROLE
                    From t_r3_user_task  Where C_USER_LOGIN_NAME=?";
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select distinct Right(C_TASK_CODE, LOCATE('" . _CONST_XML_RTT_DELIM . "', REVERSE(C_TASK_CODE)) - 1) C_ROLE
                    From t_r3_user_task Where C_USER_LOGIN_NAME=?";
        }
        $params = array($user_code);

        if (DEBUG_MODE < 10)
        {
            $this->db->debug = 0;
        }
        $ret             = $this->db->getCol($stmt, $params);
        $this->db->debug = DEBUG_MODE;
        return $ret;
    }

    /** Lay sach sach HS phai bo sung
     *
     * @param type $record_type_code Ma loai HS
     * @param Int $filter Tieu chi loc
     *      o   0: Chua thong bao cho Cong dan
     *      o   1: Da thong bao cho Cong dan
     *      o   2: Da nhan lai giay to bo sung
     *
     */
    public function qry_all_record_bo_sung($record_type_code, $filter = 0)
    {
        $v_user_code      = Session::get('user_code');
        $record_type_code = replace_bad_char($record_type_code);
        //cấp xã village_id > 0
        //cấp huyện village_id = 0
        $v_village_id     = $this->nsd_la_can_bo_cap_xa ? (int) Session::get('ou_id') : 0;

        //Xem theo trang
        page_calc($v_start, $v_end);

        //chỉ lấy thủ tục đúng cấp
        //Cấp xã được lấy xã, liên thông xã-huyện, huyện-xã
        $sql_record_type = "Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE='$record_type_code'";
        if ($this->nsd_la_can_bo_cap_xa)
        {
            $sql_record_type .= " And C_SCOPE In(0,1,3) ";
        }
        else
        {
            //cap huyen
            $sql_record_type .= " And C_SCOPE In(1,2,3) ";
        }

        $v_record_type_id = $this->db->getOne($sql_record_type);
        if ($record_type_code != '')
        {
            if ($this->is_mssql())
            {
                $stmt   = 'Exec sp_r3_supplement_record_get_all @role=?,@p_record_type_code=?,@p_filter=?,@p_user_code=?,@p_index_begin=?,@p_index_end=?';
                $params = array(_CONST_BO_SUNG_ROLE, $record_type_code, $filter, $v_user_code, $v_start, $v_end);

                return $this->db->GetAll($stmt, $params);
            }
            elseif ($this->is_mysql())
            {
                $v_start = $v_start - 1;
                $v_limit = $v_end - $v_start;

                $v_task_code     = $record_type_code . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE;
                $condition_query = " R.FK_RECORD_TYPE=$v_record_type_id
                                    AND (R.C_NEXT_TASK_CODE like '%$v_task_code')
                                    AND (R.C_NEXT_USER_CODE='$v_user_code')
                                    AND (RS.C_DONE=0)
                                    AND R.FK_VILLAGE_ID = $v_village_id ";

                if ($filter == 0) //--0. Chua thong bao
                {
                    $condition_query .= ' And RS.C_ANNOUNCE_DATE is null And RS.C_RECEIVE_DATE is null';
                }
                elseif ($filter == 1) //--1. Da thong bao, chua nhan lai giay to bo sung
                {
                    $condition_query .= 'And RS.C_ANNOUNCE_DATE is Not null And RS.C_RECEIVE_DATE is null';
                }
                elseif ($filter == 2) //---2. Da nhan lai giay to bo sung
                {
                    $condition_query .= ' And RS.C_ANNOUNCE_DATE Is Not Null And RS.C_RECEIVE_DATE Is Not Null';
                }



                //Dem tong ban ghi
                $v_total_record = $this->db->getOne("Select Count(*) TOTAL_RECORD
                                                    From view_processing_record R Right Join t_r3_record_supplement RS On RS.FK_RECORD=R.PK_RECORD
                                                    Where $condition_query");
                $sql            = "SELECT
                        @rownum:=@rownum + 1 AS RN
                        ,a.*
                        , Case When a.C_NEXT_USER_CODE='$v_user_code' Then 1 Else 0 End as C_OWNER
                        ,$v_total_record as TOTAL_RECORD
                        ,CASE
                            WHEN (DATEDIFF(NOW(), a.C_DOING_STEP_DEADLINE_DATE)>0) THEN
                                (SELECT -1 * (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())<=0
                                    AND DATEDIFF(WD.C_DATE, a.C_DOING_STEP_DEADLINE_DATE)>0 )
                            ELSE
                                (SELECT (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())>=0
                                    AND DATEDIFF(WD.C_DATE, a.C_DOING_STEP_DEADLINE_DATE)<0 )
                        END AS C_DOING_STEP_DAYS_REMAIN
                        ,CASE
                            WHEN (DATEDIFF(NOW(),a.C_RETURN_DATE)>0) THEN 
                                (SELECT -1 * (COUNT(*))
                                FROM view_working_date WD
                                WHERE  DATEDIFF(WD.C_DATE, NOW())<=0
                                    AND DATEDIFF(WD.C_DATE, a.C_RETURN_DATE)>0 )
                            ELSE
                                (SELECT (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())>=0
                                    AND DATEDIFF(WD.C_DATE, a.C_RETURN_DATE)<0 )
                        END AS C_RETURN_DAYS_REMAIN
                    FROM (
                        SELECT
                            R.PK_RECORD
                            ,R.FK_RECORD_TYPE
                            ,R.C_RECORD_NO
                            , CAST(R.C_RECEIVE_DATE  AS CHAR(19)) AS C_RECEIVE_DATE
                            , CAST(R.C_RETURN_DATE  AS CHAR(19)) AS C_RETURN_DATE
                            ,R.C_RETURN_PHONE_NUMBER
                            ,R.C_XML_DATA
                            ,R.C_XML_PROCESSING
                            ,R.C_DELETED
                            ,R.C_CLEAR_DATE
                            ,R.C_XML_WORKFLOW
                            ,R.C_RETURN_EMAIL
                            ,R.C_REJECTED
                            ,R.C_REJECT_REASON
                            ,R.C_CITIZEN_NAME
                            ,R.C_ADVANCE_COST
                            ,R.C_CREATE_BY
                            ,R.C_NEXT_TASK_CODE
                            ,R.C_NEXT_USER_CODE
                            ,R.C_NEXT_CO_USER_CODE
                            ,R.C_LAST_TASK_CODE
                            ,R.C_LAST_USER_CODE
                            , CAST(R.C_DOING_STEP_BEGIN_DATE  AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
                            ,R.C_DOING_STEP_DEADLINE_DATE
                            ,R.C_BIZ_DAYS_EXCEED
                            ,UT.C_TASK_CODE
                            ,UT.C_STEP_TIME
                            ,(SELECT @rownum:=0)
                            ,R.C_PAUSE_DATE
                            ,R.C_UNPAUSE_DATE
                        FROM view_processing_record R Right Join t_r3_record_supplement RS On RS.FK_RECORD=R.PK_RECORD
                            Left Join t_r3_user_task UT On R.C_NEXT_TASK_CODE=UT.C_TASK_CODE And R.C_NEXT_USER_CODE=UT.C_USER_LOGIN_NAME
                        WHERE $condition_query
                        ORDER BY R.C_RECEIVE_DATE DESC
                        Limit $v_start, $v_limit
                    ) a";

                return $this->db->getAll($sql);
            }
        }
        else
        {
            return array();
        }
    }

    /**
     * Lay danh sach HS theo role (+ mã loại thủ tục)
     * Danh sách được lấy ra để chuẩn bị thực hiện một công việc nào đó
     * @param string $role
     * @param string $record_type_code
     * @return array Record set
     */
    public function qry_all_record_by_role($role, $record_type_code = '')
    {
        $v_user_code  = Session::get('user_code');
        $v_village_id = Session::get('village_id');

        //Xem theo trang
        page_calc($v_start, $v_end);

        //Một số role lấy cùng kết quả danh sách
        $v_real_role = $role;
        if (strtoupper($role) == strtoupper(_CONST_TIEP_NHAN_ROLE))
        {
            $role = _CONST_BAN_GIAO_ROLE;
        }

        //Mã loại thủ tục mặc định
        if ($record_type_code == '')
        {
            //Lấy mã của thủ tục đầu tiên trong danh sách thủ tục phân công cho NSD
            $stmt = 'Select RT.C_CODE
                    From t_r3_record_type RT Right Join (Select distinct UT.C_RECORD_TYPE_CODE
                                            From t_r3_user_task UT
                                            Where UT.C_USER_LOGIN_NAME=?) as RTU
                                            On RT.C_CODE=RTU.C_RECORD_TYPE_CODE
                    Where RT.C_STATUS > 0
                    Order By RT.C_CODE';

            $record_type_code = $this->db->GetOne($stmt, array($v_user_code));
        }

        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt   = 'Exec sp_r3_record_get_all_by_role ?,?,?,?,?';
            $params = array($role, $record_type_code, $v_user_code, $v_start, $v_end);
            return $this->db->GetAll($stmt, $params);
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;
            if (strtoupper($role) == _CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE)
            {
                $stmt   = 'Select *
                            , NULL as C_XML_PROCESSING
                            ,0 as RN
                        From t_r3_internet_record R
                        Where FK_RECORD_TYPE=(Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE=?)
                            And R.C_DELETED=0
                            And R.C_IS_REAL_RECORD=1';
                $params = array($record_type_code);
                return $this->db->getAll($stmt, $params);
            }
            elseif (strtoupper($role) == _CONST_KIEM_TRA_TRUOC_HO_SO_ROLE)
            {
                $stmt   = "Select *
                            , NULL as C_XML_PROCESSING
                            ,0 as RN
                        From t_r3_internet_record R
                        Where FK_RECORD_TYPE=(Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE=?)
                            And R.C_DELETED=0
                            And R.C_IS_REAL_RECORD<>1
                            And R.C_CLEAR_DATE Is Null";
                $params = array($record_type_code);
                return $this->db->getAll($stmt, $params);
            }
            else
            {
                $v_task_code = $record_type_code . _CONST_XML_RTT_DELIM . $role;

                //Lay ID & pham vi thu tuc
                $arr_record_type_info = $this->db->getRow("Select PK_RECORD_TYPE, C_SCOPE From t_r3_record_type Where C_CODE='$record_type_code'");

                //ID thu tuc
                $v_record_type_id = $arr_record_type_info['PK_RECORD_TYPE'];
                //$this->db->getOne("Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE='$record_type_code'");                
                //Pham vi thu tuc
                $v_scope          = $arr_record_type_info['C_SCOPE'];
                //$this->db->getOne("Select C_SCOPE From t_r3_record_type Where PK_RECORD_TYPE=$v_record_type_id");

                if (strtoupper($v_real_role) == strtoupper(_CONST_TIEP_NHAN_ROLE) && $v_scope == 1)
                {
                    $v_task_code              = $record_type_code . _CONST_XML_RTT_DELIM . $v_real_role;
                    //lay task code cua ban giao (tiep nhan => bangiao)
                    $v_xml_workflow_file_path = $this->_get_xml_workflow_file_path($record_type_code);
                    $xml_workflow             = file_exists($v_xml_workflow_file_path) ? file_get_contents($v_xml_workflow_file_path) : '';
                    $dom_workflow             = simplexml_load_string($xml_workflow);
                    $v_task_code_handover     = '';
                    if ($dom_workflow)
                    {
                        $v_task_code_handover = get_xml_value($dom_workflow, "//task[contains(@code, 'BAN_GIAO')][1]/@code");
                    }
                    $condition_query = " FK_RECORD_TYPE='$v_record_type_id' 
                        And C_NEXT_TASK_CODE = '$v_task_code_handover' 
                        And FK_VILLAGE_ID=$v_village_id
                        And (C_NEXT_USER_CODE='$v_user_code' Or C_NEXT_USER_CODE Like '%,$v_user_code,%')";
                }
                elseif (strtoupper($v_real_role) == strtoupper(_CONST_TRA_KET_QUA_ROLE) && ($v_scope == 1))
                {
                    $v_task_code     = $record_type_code . _CONST_XML_RTT_DELIM . $v_real_role;
                    $condition_query = " FK_RECORD_TYPE='$v_record_type_id' And (C_NEXT_TASK_CODE like '%$v_task_code' And (C_CREATE_BY='$v_user_code' Or C_NEXT_USER_CODE='$v_user_code'))";
                }
                else
                {
                    $condition_query = " 
                                    FK_RECORD_TYPE='$v_record_type_id'
                                    AND (
                                            (C_NEXT_TASK_CODE like '%$v_task_code' AND (C_NEXT_USER_CODE='$v_user_code' Or C_NEXT_USER_CODE Like '%,$v_user_code,%' OR C_NEXT_CO_USER_CODE like '%,$v_user_code,%'))
                                            OR (C_NEXT_NO_CHAIN_TASK_CODE Like '%$v_task_code' And C_NEXT_NO_CHAIN_USER_CODE Like '%,$v_user_code,%') 
                                        )";
                    if ($this->nsd_la_can_bo_cap_xa && in_array($v_scope, array(0, 1)))
                    {
                        $condition_query .= "  And FK_VILLAGE_ID=$v_village_id  ";
                    }
                }

                //Loc theo xã ?
                $v_xa_tiep_nhan = get_post_var('sel_village_filter', '');
                if ($v_xa_tiep_nhan != '')
                {
                    $condition_query .= " And FK_VILLAGE_ID='$v_xa_tiep_nhan'";
                }
                //Dem tong ban ghi
                $v_total_record = $this->db->getOne("Select Count(*) TOTAL_RECORD From view_processing_record R Where $condition_query");


                $sql = "Select
                            @rownum:=@rownum + 1 As RN
                            , CASE WHEN (R.C_NEXT_USER_CODE = '$v_user_code' Or R.C_NEXT_USER_CODE like '%,$v_user_code,%' OR R.C_NEXT_NO_CHAIN_USER_CODE like '%,$v_user_code,%') THEN 1 ELSE 0 END AS C_OWNER
                            , $v_total_record AS TOTAL_RECORD
                            ,CASE
                                WHEN (DATEDIFF(NOW(), R.C_DOING_STEP_DEADLINE_DATE)>0) THEN
                                    (SELECT -1 * (COUNT(*))
                                    FROM view_working_date WD
                                    WHERE DATEDIFF(WD.C_DATE, NOW())<=0
                                        AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)>0 )
                                ELSE
                                    (SELECT (COUNT(*))
                                    FROM view_working_date WD
                                    WHERE DATEDIFF(WD.C_DATE, NOW())>=0
                                        AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)<0 )
                            END AS C_DOING_STEP_DAYS_REMAIN
                            ,CASE
                                WHEN (DATEDIFF(NOW(),R.C_RETURN_DATE)>0) THEN
                                    (SELECT -1 * (COUNT(*))
                                    FROM view_working_date WD
                                    WHERE  DATEDIFF(WD.C_DATE, NOW())<=0
                                        AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)>0 )
                                ELSE
                                    (SELECT (COUNT(*))
                                    FROM view_working_date WD
                                    WHERE DATEDIFF(WD.C_DATE, NOW())>=0
                                        AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)<0 )
                            END AS C_RETURN_DAYS_REMAIN
                            , R.PK_RECORD
                            , R.FK_RECORD_TYPE
                            , R.C_RECORD_NO
                            , CAST(R.C_RECEIVE_DATE As CHAR(19)) AS C_RECEIVE_DATE
                            , CAST(R.C_RETURN_DATE As CHAR(19)) AS C_RETURN_DATE
                            , R.C_RETURN_PHONE_NUMBER
                            , R.C_XML_DATA
                            , R.C_XML_PROCESSING
                            , R.C_DELETED
                            , R.C_CLEAR_DATE
                            , R.C_XML_WORKFLOW
                            , R.C_RETURN_EMAIL
                            , R.C_REJECTED
                            , R.C_REJECT_REASON
                            , R.C_CITIZEN_NAME
                            , R.C_ADVANCE_COST
                            , R.C_CREATE_BY
                            , R.C_NEXT_TASK_CODE
                            , R.C_NEXT_USER_CODE
                            , R.C_NEXT_CO_USER_CODE
                            , R.C_LAST_TASK_CODE
                            , R.C_LAST_USER_CODE
                            , CAST(R.C_DOING_STEP_BEGIN_DATE As CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
                            , R.C_DOING_STEP_DEADLINE_DATE
                            , R.C_BIZ_DAYS_EXCEED
                            , a.C_TASK_CODE
                            , a.C_STEP_TIME
                            , R.C_NEXT_NO_CHAIN_TASK_CODE
                            ,(Select C_CONTENT  FROM t_r3_record_comment
                                Where FK_RECORD=R.PK_RECORD
                                Order By C_CREATE_DATE DESC
                                Limit 1
                            ) C_LAST_RECORD_COMMENT
                            ,C_PAUSE_DATE
                            ,C_UNPAUSE_DATE
                        From
                        (
                            Select
                                RID.`PK_RECORD`
                              , UT.C_TASK_CODE
                              , UT.C_STEP_TIME
                              , (SELECT @rownum:=0)
                            From
                            (
                                Select
                                    PK_RECORD
                                  , C_NEXT_TASK_CODE
                                  , C_NEXT_USER_CODE
                                From view_processing_record
                                Where $condition_query
                                Limit $v_start, $v_limit
                            ) RID Left join t_r3_user_task UT On (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE And RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
                        ) a Left join view_processing_record R On a.PK_RECORD=R.PK_RECORD
                        Order by R.C_RECEIVE_DATE DESC
                        ";
                return $this->db->getAll($sql);
            }
        }

        return array();
    }

    /**
     * Lấy danh sách Loại hồ sơ, mà NSD đã được phân công vào quy trình xử lý
     */
    public function qry_all_record_type_option($role = '')
    {
        $role        = strtoupper($role);
        $v_user_code = Session::get('user_code');

        if (check_permission('THEO_DOI_GIAM_SAT_TOAN_BO_HO_SO', $this->app_name))
        {
            $v_from_qry = "From t_r3_record_type RT Right Join (Select distinct UT.C_RECORD_TYPE_CODE
                                            From t_r3_user_task UT) as RTU
                                            On RT.C_CODE=RTU.C_RECORD_TYPE_CODE
                    Where RT.C_STATUS > 0";
        }
        else
        {
            $arr_role = array('', _CONST_TRA_CUU_ROLE, _CONST_Y_KIEN_LANH_DAO_ROLE
                , _CONST_BAO_CAO_ROLE, _CONST_TRA_CUU_LIEN_THONG_ROLE);
            if (in_array($role, $arr_role))
            {
                $cond       = " RT.C_STATUS > 0 ";
                $v_from_qry = "From t_r3_record_type RT Right Join (Select distinct UT.C_RECORD_TYPE_CODE
                                            From t_r3_user_task UT
                                            Where UT.C_USER_LOGIN_NAME='$v_user_code') as RTU
                                            On RT.C_CODE=RTU.C_RECORD_TYPE_CODE
                            Where $cond";
            }
            else
            {
                $v_from_qry = "From t_r3_record_type RT Right Join (Select distinct UT.C_RECORD_TYPE_CODE
                                            From t_r3_user_task UT
                                            Where UT.C_USER_LOGIN_NAME='$v_user_code' and UT.C_TASK_CODE Like '%::$role') as RTU
                                            On RT.C_CODE=RTU.C_RECORD_TYPE_CODE
                            Where RT.C_STATUS > 0";
            }

            /*
              $v_from_qry = "From t_r3_record_type RT Right Join (Select distinct UT.C_RECORD_TYPE_CODE
              From t_r3_user_task UT
              Where UT.C_USER_LOGIN_NAME='$v_user_code') as RTU
              On RT.C_CODE=RTU.C_RECORD_TYPE_CODE
              Where RT.C_STATUS > 0
              Order By RT.C_CODE";
             */
        }
        $other_clause = '';
        if (strtoupper(Session::get('active_role')) == _CONST_TRA_CUU_LIEN_THONG_ROLE)
        {
            $other_clause .= " AND RT.c_scope = 1 ";
        }
        if (strtoupper(Session::get('active_role')) == _CONST_TRA_CUU_TAI_XA_ROLE)
        {
            $other_clause .= " AND RT.c_scope = 0 ";
        }

        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "Select 
                        RT.C_CODE
                        , (RT.C_CODE + ' - ' + RT.C_NAME) as C_NAME
                        , RT.C_SCOPE 
                    $other_clause
                    $v_from_qry";
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select 
                        RT.C_CODE
                        , Concat(RT.C_CODE, ' - ',  RT.C_NAME) as C_NAME
                        , RT.C_SCOPE 
                    $v_from_qry
                    $other_clause
                    ORDER BY RT.c_code";
        }

        return $this->db->getAssoc($stmt);
    }

    /**
     * Lấy danh sách HỒ SƠ cho phép nộp qua Internet mà cán bộ được phân công xử lý
     */
    public function qry_all_internet_record_type_option()
    {
        $v_user_code = Session::get('user_code');
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "Select RT.C_CODE, (RT.C_CODE + ' - ' + RT.C_NAME) as C_NAME
                    From t_r3_record_type RT Right Join (Select distinct UT.C_RECORD_TYPE_CODE
                                            From t_r3_user_task UT
                                            Where UT.C_USER_LOGIN_NAME=?) as RTU
                                            On RT.C_CODE=RTU.C_RECORD_TYPE_CODE
                    Where RT.C_STATUS > 0
                        And RT.C_SEND_OVER_INTERNET='1'
                    Order By RT.C_CODE";
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select 
                        RT.C_CODE
                        , Concat(RT.C_CODE, ' - ',  RT.C_NAME) as C_NAME
                        ,RT.C_SCOPE
                    From t_r3_record_type RT Right Join (Select distinct UT.C_RECORD_TYPE_CODE
                                            From t_r3_user_task UT
                                            Where UT.C_USER_LOGIN_NAME=?) as RTU
                                            On RT.C_CODE=RTU.C_RECORD_TYPE_CODE
                    Where RT.C_STATUS > 0
                        And RT.C_SEND_OVER_INTERNET='1'
                    Order By RT.C_CODE";
        }
        $params = array($v_user_code);
        return $this->db->getAssoc($stmt, $params);
    }

//end func

    /**
     * Lay thong tin chi tiet cua mot Ho so
     */
    public function qry_single_record($p_record_id = '', $v_record_type_code = '', $v_xml_workflow_file_name = '')
    {
        $v_record_id = $p_record_id ? (int) $p_record_id : (int) get_post_var('hdn_item_id');

        if ($v_record_id > 0)
        {
            if (DATABASE_TYPE == 'MSSQL')
            {
                $stmt = 'SELECT
                			R.[PK_RECORD]
    				        ,r.[FK_RECORD_TYPE]
    				        ,r.[C_RECORD_NO]
    				        , Convert(varchar(19), r.[C_RECEIVE_DATE], 120) as [C_RECEIVE_DATE]
    				        , Convert(varchar(19), r.[C_RETURN_DATE], 120) as [C_RETURN_DATE]
    				        ,r.[C_RETURN_PHONE_NUMBER]
    				        ,r.[C_XML_DATA]
    				        ,r.[C_XML_PROCESSING]
    				        ,r.[C_DELETED]
                			, Convert(varchar(19), r.[C_CLEAR_DATE], 120) as [C_CLEAR_DATE]
    				        ,r.[C_XML_WORKFLOW]
    				        ,r.[C_RETURN_EMAIL]
    				        ,r.[C_REJECTED]
    				        ,r.[C_REJECT_REASON]
    				        ,r.[C_CITIZEN_NAME]
    				        ,r.[C_ADVANCE_COST]
    				        ,r.[C_CREATE_BY]
    				        ,R.[C_NEXT_TASK_CODE]
    				        ,r.[C_NEXT_USER_CODE]
    				        ,r.[C_NEXT_CO_USER_CODE]
    				        ,r.[C_LAST_TASK_CODE]
    				        ,r.[C_LAST_USER_CODE]
    				        ,Convert(varchar(19), r.[C_DOING_STEP_BEGIN_DATE], 120) as [C_DOING_STEP_BEGIN_DATE]
    				        ,r.[C_DOING_STEP_DEADLINE_DATE]
    				        ,r.[C_BIZ_DAYS_EXCEED]
    	                    ,RT.C_NAME as C_RECORD_TYPE_NAME
    	                    ,RT.C_CODE as C_RECORD_TYPE_CODE
                       	From [dbo].[view_record] R Left Join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                        Where R.PK_RECORD=?';
            }
            elseif (DATABASE_TYPE == 'MYSQL')
            {

                $stmt = 'Select
                              R.PK_RECORD
                            , R.FK_RECORD_TYPE
                            , R.C_RECORD_NO
                            , Cast(R.C_RECEIVE_DATE  AS CHAR(19)) AS C_RECEIVE_DATE
                            , Cast(R.C_RETURN_DATE  AS CHAR(19)) AS C_RETURN_DATE
                            , R.C_RETURN_PHONE_NUMBER
                            , R.C_XML_DATA
                            , R.C_XML_PROCESSING
                            , R.C_DELETED
                            , Cast(R.C_CLEAR_DATE  AS CHAR(19)) AS C_CLEAR_DATE
                            , R.C_XML_WORKFLOW
                            , R.C_RETURN_EMAIL
                            , R.C_REJECTED
                            , R.C_REJECT_REASON
                            , R.C_CITIZEN_NAME
                            , R.C_ADVANCE_COST
                            , R.C_CREATE_BY
                            , R.C_NEXT_TASK_CODE
                            , R.C_NEXT_USER_CODE
                            , R.C_NEXT_CO_USER_CODE
                            , R.C_LAST_TASK_CODE
                            , R.C_LAST_USER_CODE
                            , Cast(R.C_DOING_STEP_BEGIN_DATE  AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
                            , R.C_DOING_STEP_DEADLINE_DATE
                            , R.C_BIZ_DAYS_EXCEED
                            , RT.C_NAME as C_RECORD_TYPE_NAME
                            , RT.C_CODE as C_RECORD_TYPE_CODE
                            , R.XML_RECORD_RESULT
                        From t_r3_record R Left join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                        Where R.PK_RECORD=?';
            }

            $params    = array($v_record_id);
            $ret_array = $this->db->getRow($stmt, $params);

            $ret_array['C_TOTAL_TIME'] = $this->days_between_two_date($ret_array['C_RECEIVE_DATE'], $ret_array['C_RETURN_DATE']);

            return $ret_array;
        }
        else //Them moi
        {
            //Tinh toan ngay tra ket qua
            if (file_exists($v_xml_workflow_file_name))
            {
                $dom          = simplexml_load_file($v_xml_workflow_file_name);
                $r            = xpath($dom, "/process/@totaltime[1]", XPATH_STRING);
                $v_total_time = str_replace(',', '.', $r); //Dau thap phan la dau cham "."

                $v_return_date = $this->_step_deadline_calc($v_total_time);

                $v_hour_now = $this->get_hour_now();
                //Thu tuc 0 ngay
                if ($v_total_time < 0)
                {
                    $v_return_date = '';
                }
                elseif ($v_total_time == '0')
                {
                    //Nhan sang tra sang
                    if ($v_hour_now <= intval(_CONST_MORNING_END_WORKING_TIME))
                    {
                        $v_return_date = $this->get_date_yyyymmdd_now() . chr(32) . _CONST_MORNING_END_WORKING_TIME;
                    }
                    else //Nhan chieu tra chieu
                    {
                        $v_return_date = $this->get_date_yyyymmdd_now() . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
                    }
                }
                elseif ($v_total_time == '0.5') //Thu tuc 0.5 ngay
                {
                    //Nhan sang tra chieu
                    if ($v_hour_now <= intval(_CONST_MORNING_END_WORKING_TIME))
                    {
                        $v_return_date = $v_return_date = $this->get_date_yyyymmdd_now() . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
                    }
                    else //Nhan chieu tra sang hom sau
                    {
                        $v_return_date = $this->date_which_diff_day_yyyymmdd(1) . chr(32) . _CONST_MORNING_END_WORKING_TIME;
                    }
                }
                //Thủ tục 1 ngày: QUA ĐÊM
                elseif ($v_total_time == 1)
                {
                    //Nhận sáng: trả sáng ngày làm việc tiếp theo
                    if ($v_hour_now <= intval(_CONST_MORNING_END_WORKING_TIME))
                    {
                        $v_return_date = $this->date_which_diff_day_yyyymmdd(1) . chr(32) . _CONST_MORNING_END_WORKING_TIME;
                    }
                    else //Nhận chiều: Trả buổi chiều, ngày làm việc tiếp theo
                    {
                        $v_return_date = $this->date_which_diff_day_yyyymmdd(1) . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
                    }
                }
                //2 ngày trở lên: Cộng số ngày làm việc, Nhận sáng trả sáng, nhận chiều trả chiều
                else
                {
                    $noon          = ($v_hour_now <= intval(_CONST_MORNING_END_WORKING_TIME)) ? _CONST_MORNING_END_WORKING_TIME : _CONST_AFTERNOON_END_WORKING_TIME;
                    $v_total_time  = intval($v_total_time);
                    $v_return_date = $this->date_which_diff_day_yyyymmdd($v_total_time) . chr(32) . $noon;
                }

                $ret_array = array(
                    'C_RETURN_DATE' => $v_return_date,
                    'C_TOTAL_TIME'  => $v_total_time
                );
            }
            else
            {
                $ret_array = NULL;
            }

            return $ret_array;
        }
    }

    /**
     * Lấy danh sách file đính kèm của 1 hồ sơ
     * @param int $item_id: ID cua HS
     */
    public function qry_all_record_file($item_id)
    {
        if (intval($item_id) < 1)
        {
            return NULL;
        }
        $stmt = 'Select PK_RECORD_FILE, C_FILE_NAME FROM t_r3_record_file Where FK_RECORD=?';
        return $this->db->getAll($stmt, array($item_id));
    }

    /**
     * Danh sach y kien
     * @param type $record_id
     */
    public function qry_all_record_comment($record_id)
    {
        $v_record_id = get_post_var('hdn_item_id', $record_id);
        is_id_number($v_record_id) OR $v_record_id = 0;

        $stmt   = 'Select
                    RC.PK_RECORD_COMMENT
                    ,RC.FK_RECORD
                    ,RC.C_USER_CODE
                    ,RC.C_SUBJECT
                    ,RC.C_CONTENT
                    ,' . $this->build_convert_date_query('RC.C_CREATE_DATE', 103) . ' C_CREATE_DATE
                    ,U.C_NAME as C_USER_NAME
                    ,U.C_JOB_TITLE as C_USER_JOB_TITLE
                    ,RC.C_TYPE
                From t_r3_record_comment RC left Join t_cores_user U On RC.C_USER_CODE=U.C_LOGIN_NAME
                Where RC.FK_RECORD=?
                Order By C_CREATE_DATE Desc';
        $params = array($v_record_id);

        return $this->db->getAll($stmt, $params);
    }

    public function update_record()
    {
        $v_record_id           = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : 0;
        $v_record_type_code    = isset($_POST['sel_record_type']) ? replace_bad_char($_POST['sel_record_type']) : '';
        $v_record_no           = isset($_POST['txt_record_no']) ? replace_bad_char($_POST['txt_record_no']) : '';
        $v_return_phone_number = isset($_POST['txt_return_phone_number']) ? replace_bad_char($_POST['txt_return_phone_number']) : '';
        $v_receive_date        = isset($_POST['txt_receive_date']) ? replace_bad_char($_POST['txt_receive_date']) : '';
        $v_return_date         = isset($_POST['hdn_return_date']) ? replace_bad_char($_POST['hdn_return_date']) : '';
        $v_return_email        = isset($_POST['txt_return_email']) ? replace_bad_char($_POST['txt_return_email']) : '';
        $v_village_id          = Session::get('village_id');

        $v_xml_data = isset($_POST['XmlData']) ? $_POST['XmlData'] : '<root/>';

        //blacklist
        $v_is_black_listed = FALSE;
        $dom_xml_data      = simplexml_load_string($v_xml_data);
        if ($v_record_id < 1)     //Chi kiem tra luc tiep nhan
        {
            $arr_all_blacklist_rule = $this->_qry_all_blacklist_rule();
            $v_rule_owner           = $v_rule_name            = '';
            for ($i = 0, $n = count($arr_all_blacklist_rule); $i < $n; $i++)
            {
                $v_rule_applied_to             = trim($arr_all_blacklist_rule[$i]['C_RECORD_TYPE_CODE']); //Loại thủ tục mà luật này có tác dụng
                $v_record_type_code_is_matched = (($v_rule_applied_to == $v_record_type_code ) OR ($v_rule_applied_to == ''));
                $arr_rule_content              = json_decode($arr_all_blacklist_rule[$i]['C_RULE_CONTENT'], 1);
                if (is_array($arr_rule_content))
                {
                    $v_xpath = '/data[';
                    foreach ($arr_rule_content as $rule)
                    {
                        $v_tag      = $rule['tag'];
                        $v_operator = $rule['operator'];
                        $v_value    = $rule['value'];

                        $v_xpath .= ($v_xpath != '/data[') ? ' and' : '';
                        $v_xpath .= " item[@id='$v_tag']/value='$v_value'";
                    }
                    $v_xpath .= ']';
                    $r = xpath($dom_xml_data, $v_xpath);
                    if ((count($r) > 0) && $v_record_type_code_is_matched)
                    {
                        $v_is_black_listed = TRUE;
                        $v_rule_owner      = $arr_all_blacklist_rule[$i]['C_OWNER_NAME'];
                        $v_rule_name       = $arr_all_blacklist_rule[$i]['C_NAME'];

                        break; //for $arr_all_blacklist_rule
                    }
                }
            }

            if ($v_is_black_listed)
            {
                echo '<center><h4>Hồ sơ không được phép tiếp nhận!';
                echo '<br/>theo yêu cầu <i>' . $v_rule_name . '</i>';
                echo '<br/>của <i>' . $v_rule_owner . '</i>';
                echo '<br>Xin bấm <a href="' . $this->goback_url . '">vào đây </a>để trở về màn hình tiếp nhận</h4>';
                exit;
            }

            //Đăng ký thế chấp & xoá đăng ký thế chấp
            //Kiem tra luat tu dong
            $v_count_unlock                   = $v_count_lock                     = 0;
            $v_auto_lock_unlock_xml_file_path = SERVER_ROOT . 'apps/r3/xml-config/common/auto_lock_unlock.xml';
            $dom_auto_lock_unlock             = simplexml_load_file($v_auto_lock_unlock_xml_file_path);
            $rules                            = xpath($dom_auto_lock_unlock, "//rule[lock/record_type_code='$v_record_type_code']");
            foreach ($rules as $rule)
            {
                $v_rule_name             = $rule->attributes()->name;
                $v_lock_tag_id           = $rule->lock->id;
                $v_lock_record_type_code = $rule->lock->record_type_code;

                $v_unlock_tag_id           = $rule->unlock->id;
                $v_unlock_record_type_code = $rule->unlock->record_type_code;

                $v_tag_value = get_xml_value($dom_xml_data, "item[@id='$v_lock_tag_id'][last()]/value");
                if ($v_tag_value != '')
                {
                    //Dem tra so lan unlock
                    $stmt = "SELECT
                              COUNT(*) AS C_COUNT
                            FROM view_record 
                            WHERE FK_RECORD_TYPE = (SELECT PK_RECORD_TYPE
                                                    FROM t_r3_record_type
                                                    WHERE C_CODE = '$v_unlock_record_type_code')
                            AND ExtractValue(C_XML_DATA, '//item[@id=''$v_unlock_tag_id'']/value[1]') = '$v_tag_value'";

                    $v_count_unlock = $this->db->getOne($stmt);

                    //Dem so lan lock
                    $stmt         = "SELECT
                              COUNT(*) AS C_COUNT
                            FROM VIRE_RECORD 
                            WHERE FK_RECORD_TYPE = (SELECT PK_RECORD_TYPE
                                                    FROM t_r3_record_type
                                                    WHERE C_CODE = '$v_lock_record_type_code')
                            AND ExtractValue(C_XML_DATA, '//item[@id=''$v_lock_tag_id'']/value[1]') = '$v_tag_value'";
                    $v_count_lock = $this->db->getOne($stmt);
                }
                else
                {
                    $v_count_lock   = $v_count_unlock = 0;
                }

                if ($v_count_unlock < $v_count_lock)
                {
                    echo '<center><h4>Hồ sơ không được phép tiếp nhận!';
                    echo '<br/>theo yêu cầu <i>' . $v_rule_name . '</i>';
                    echo '<br>Xin bấm <a href="' . $this->goback_url . '">vào đây </a>để trở về màn hình tiếp nhận</h4>';
                    exit;
                    break;
                }
            }
        }
        //End: blacklist
        //Change date format
        $v_receive_date = jwDate::ddmmyyyy_to_yyyymmdd($v_receive_date, TRUE);

        //Calc task code
        //$v_xml_workflow_file_name = $v_record_type_code . '_workflow.xml';
        //$v_xml_workflow_file_path = __DIR__ . '/../../xml-config' . DS . $v_record_type_code . DS . $v_xml_workflow_file_name;
        $v_xml_workflow_file_path = $this->_get_xml_workflow_file_path($v_record_type_code);

        if (!file_exists($v_xml_workflow_file_path))
        {
            $this->exec_done($this->goback_url);
            exit;
        }

        $v_user_code = Session::get('user_code');

        $dom = simplexml_load_file($v_xml_workflow_file_path);
        if ($v_record_id < 1)
        {
            $v_current_task = xpath($dom, "//step[1]/task[1]/@code", XPATH_STRING);

            $stmt = 'Insert Into t_r3_record(
                            FK_RECORD_TYPE
                            ,C_RECORD_NO
                            ,C_RECEIVE_DATE
                            ,C_RETURN_DATE
                            ,C_RETURN_PHONE_NUMBER
                            ,C_XML_DATA
                            ,C_DELETED
                            ,C_RETURN_EMAIL
                            ,FK_VILLAGE_ID
                        ) Values (
                            (Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE=?)
                            ,?
                            ,?
                            ,?
                            ,?
                            ,?
                            ,0
                            ,?
                            ,?
                        )';

            $params = array(
                $v_record_type_code
                , $v_record_no //  . '_' . $ttt
                , $v_receive_date
                , $v_return_date
                , $v_return_phone_number
                , $v_xml_data
                , $v_return_email
                , $v_village_id
            );

            $this->db->Execute($stmt, $params);

            $v_record_id = $this->get_last_inserted_id('t_r3_record', 'PK_RECORD');

            //Next task
            //LienND update 22/10/2012
            //Neu task tiếp theo có nhiều người bàn giao, thì người bàn giao phải là người nhập HỒ SƠ này
            $stmt              = 'Select Count(*)
                    From t_r3_user_task
                    Where C_TASK_CODE=(Select distinct C_NEXT_TASK_CODE From t_r3_user_task Where C_TASK_CODE=?)';
            $params            = array($v_current_task);
            $v_count_next_user = $this->db->getOne($stmt, $params);

            if ($v_count_next_user < 2)
            {
                $arr_next_task_info = $this->_qry_next_task_info($v_current_task);
            }
            else
            {
                //Nếu có nhiều người bàn giao: Ai tiếp nhận, người đó bàn giao
                $sql                = "Select
                            C_TASK_CODE as C_NEXT_TASK_CODE
                            , C_NAME as C_NEXT_USER_NAME
                            , C_USER_LOGIN_NAME as C_NEXT_USER_LOGIN_NAME
                            , C_JOB_TITLE as C_NEXT_USER_JOB_TITLE
                        From t_cores_user U Right Join (
                                Select takeover.C_TASK_CODE, takeover.C_USER_LOGIN_NAME
                                From t_r3_user_task sender Left Join t_r3_user_task takeover On sender.C_NEXT_TASK_CODE=takeover.C_TASK_CODE
                                Where sender.C_TASK_CODE='$v_current_task'
                                    And takeover.C_USER_LOGIN_NAME='$v_user_code'
                            ) a  On a.C_USER_LOGIN_NAME=U.C_LOGIN_NAME";
                $arr_next_task_info = $this->db->getRow($sql);

                //Cong viec tiep theo thuoc Step moi?
                $v_next_group_code = $this->db->getOne("Select C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE='$v_current_task'");
                $v_task_code       = $this->db->getOne("Select C_NEXT_TASK_CODE From t_r3_user_task Where C_TASK_CODE='$v_current_task'");

                $stmt                      = 'Select COUNT(*)
                        From t_r3_user_task
                        Where C_GROUP_CODE=?
                            And C_TASK_CODE=?';
                $params                    = array($v_next_group_code, $v_task_code);
                $v_count_next_task_in_step = $this->db->getOne($stmt, $params);

                $arr_next_task_info['IS_NEW_STEP'] = ($v_count_next_task_in_step == 0) ? TRUE : FALSE;
            }

            $v_next_task            = $arr_next_task_info['C_NEXT_TASK_CODE'];
            $v_next_user_login_name = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
            $v_next_user_name       = $arr_next_task_info['C_NEXT_USER_NAME'];
            $v_next_user_job_title  = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];

            //Init XML Processing
            $v_group_code = $this->db->getOne("Select C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE='$v_next_task'");
            $v_group_name = $this->db->getOne("Select C_NAME From t_cores_group Where C_CODE='$v_group_code'");
            $v_step_time  = $this->db->getOne("Select C_STEP_TIME From t_r3_user_task Where C_TASK_CODE='$v_next_task'");

            $next = '<next_task code="' . $v_next_task . '" user="' . $v_next_user_login_name
                    . '" user_name="' . $v_next_user_name
                    . '" user_job_title="' . $v_next_user_job_title
                    . '" group_name="' . $v_group_name
                    . '" step_time="' . $v_step_time
                    . '" co_user=""
                    />';

            $step = '<step seq="' . uniqid() . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';

            $xml_processing = '<?xml version="1.0" standalone="yes"?><data org_return_date="' . $v_return_date . '">' . $next . $step . '</data>';

            $stmt   = 'Update t_r3_record
                     Set C_XML_PROCESSING=?
                     Where PK_RECORD=?';
            $params = array($xml_processing, $v_record_id);
            $this->db->Execute($stmt, $params);

//            require_once dirname(__FILE__) . '/classes/announce.inc.php';
//
//            $arr_single_record = $this->qry_single_record($v_record_id);
//            if ($arr_single_record['C_RETURN_EMAIL'])
//            {
//                $mail = new announce_accept($arr_single_record['C_RETURN_EMAIL'], $arr_single_record);
//                $mail->send();
//            }
        }
        else  //Update
        {
            $stmt   = 'Update t_r3_record Set
                            FK_RECORD_TYPE          = (Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE=?)
                            ,C_RECORD_NO            = ?
                            ,C_RETURN_PHONE_NUMBER  = ?
                            ,C_XML_DATA             = ?
                            ,C_RETURN_EMAIL         = ?
                        Where PK_RECORD = ? And FK_VILLAGE_ID = ? ';
            $params = array(
                $v_record_type_code
                , $v_record_no
                , $v_return_phone_number
                , $v_xml_data
                , $v_return_email
                , $v_record_id
                , $v_village_id
            );
            $this->db->Execute($stmt, $params);
        }

        $v_doing_step_deadline_date = $this->_step_deadline_calc($v_step_time);
        //Chấp nhận lưu trữ thừa DB, tăng tốc độ Query khi Tra cứu, báo cáo
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt   = "Update t_r3_record Set
                        C_CITIZEN_NAME = C_XML_DATA.value('(//item[@id=''txtName'']/value/text())[1]','nvarchar(200)')
                        ,C_ADVANCE_COST = IsNull(C_XML_DATA.value('(//item[@id=''txtCost'']/value/text())[1]','nvarchar(50)'),0)
                        ,C_CREATE_BY= C_XML_PROCESSING.value('(//step[1]/user_code/text())[1]', 'nvarchar(100)')
                        ,C_NEXT_TASK_CODE=C_XML_PROCESSING.value('(//next_task[1]/@code)[1]', 'nvarchar(500)')
                        ,C_NEXT_USER_CODE=C_XML_PROCESSING.value('(//next_task[1]/@user)[1]', 'nvarchar(100)')
                        ,C_NEXT_CO_USER_CODE=C_XML_PROCESSING.value('(//next_task[1]/@co_user)[1]', 'nvarchar(1000)')
                        ,C_LAST_TASK_CODE=C_XML_PROCESSING.value('(//step[last()]/@code)[1]', 'nvarchar(500)')
                        ,C_LAST_USER_CODE=C_XML_PROCESSING.value('(//step[last()]/user_code)[1]', 'nvarchar(500)')
                        ,C_DOING_STEP_DEADLINE_DATE='$v_doing_step_deadline_date'
                        ,C_DOING_STEP_BEGIN_DATE=getDate()
                    Where PK_RECORD=?";
            $params = array($v_record_id);
            $this->db->Execute($stmt, $params);
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt   = "Update t_r3_record Set
                            C_CITIZEN_NAME = ExtractValue(C_XML_DATA, '//item[@id=''txtName'']/value[1]')
                            ,C_ADVANCE_COST = Case ExtractValue(C_XML_DATA, '//item[@id=''txtCost'']/value[1]') When '' Then '0' Else ExtractValue(C_XML_DATA, '//item[@id=''txtCost'']/value[1]') End
                            ,C_CREATE_BY = ExtractValue(C_XML_PROCESSING, '//step[1]/user_code[1]')
                            ,C_NEXT_TASK_CODE = ExtractValue(C_XML_PROCESSING, '//next_task[1]/@code[1]')
                            ,C_NEXT_USER_CODE = ExtractValue(C_XML_PROCESSING, '//next_task[1]/@user[1]')
                            ,C_NEXT_CO_USER_CODE = ExtractValue(C_XML_PROCESSING, '//next_task[1]/@co_user[1]')
                            ,C_LAST_TASK_CODE = ExtractValue(C_XML_PROCESSING, '//step[last()]/@code[1]')
                            ,C_LAST_USER_CODE = ExtractValue(C_XML_PROCESSING, '//step[last()]/user_code[1]')
                            ,C_DOING_STEP_DEADLINE_DATE='$v_doing_step_deadline_date'
                            ,C_DOING_STEP_BEGIN_DATE=Now()
                    Where PK_RECORD=? And FK_VILLAGE_ID = ? ";
            $params = array($v_record_id, $v_village_id);
            $this->db->Execute($stmt, $params);
        }
        else
        {
            //Chưa hỗ trợ DB khác
        }

        //Upload file
        $count = count($_FILES['uploader']['name']);
        for ($i = 0; $i < $count; $i++)
        {
            if ($_FILES['uploader']['error'][$i] == 0)
            {
                $v_file_name = $_FILES['uploader']['name'][$i];
                $v_tmp_name  = $_FILES['uploader']['tmp_name'][$i];

                $v_file_ext = array_pop(explode('.', $v_file_name));

                if (in_array($v_file_ext, explode(',', _CONST_RECORD_FILE_ACCEPT)))
                {
                    if (move_uploaded_file($v_tmp_name, SERVER_ROOT . "uploads" . DS . 'r3' . DS . $v_file_name))
                    {
                        $stmt   = 'Insert Into t_r3_record_file(FK_RECORD, C_FILE_NAME) Values(?,?)';
                        $params = array($v_record_id, $v_file_name);
                        $this->db->Execute($stmt, $params);
                    }
                }
            }
        }
        //Delete File
        //Xoa file dinh kem
        $v_deleted_file_id_list = ltrim($_POST['hdn_deleted_file_id_list'], ',');
        if ($v_deleted_file_id_list != '')
        {
            $sql = "Delete From t_r3_record_file Where PK_RECORD_FILE in ($v_deleted_file_id_list)";
            $this->db->Execute($sql);
        }

        $arr_filter = array(
            'sel_record_type' => $v_record_type_code,
        );

        $this->exec_done($this->goback_url, $arr_filter);
    }

//end func update_record

    public function get_my_first_role()
    {
        $role = Cookie::get('active_role');
        return ($role != NULL) ? $role : 'tiep_nhan';
    }

//end func get_my_first_role

    /**
     * Lấy mã chính xác công việc tiếp theo, khi biết mã công việc hiện tại
     * @param string $current_task_code Mã công việc hiện tại (đang - chuẩn bị - thực hiện)
     * @return string Mã công việc tiếp theo
     */
    public function get_next_task_code($task_code)
    {
        $task_code   = replace_bad_char($task_code);
        $v_user_code = Session::get('user_code');
        $stmt        = 'Select C_NEXT_TASK_CODE From t_r3_user_task Where C_TASK_CODE=? And C_USER_LOGIN_NAME=?';
        return $this->db->getOne($stmt, array($task_code, $v_user_code));
    }

    public function get_group_name_by_task_code($task_code)
    {
        $task_code = replace_bad_char($task_code);

        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = 'Select C_NAME From t_cores_group Where C_CODE=(Select Top 1 C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE=?)';
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = 'Select C_NAME From t_cores_group Where C_CODE=(Select C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE=? Limit 1)';
        }
        else
        {
            return NULL;
        }

        return $this->db->getOne($stmt, array($task_code));
    }

    /**
     * 
     * @param bool $delete true=Xoá, false=khôi phục
     */
    public function delete_record($delete = true)
    {
        $v_item_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : 0;
        $delete         = $delete ? 1 : 0;
        $clear_date     = $delete ? 'NOW()' : 'NULL';
        if ($v_item_id_list != '')
        {
            $sql = "Update t_r3_record Set C_DELETED=$delete, C_CLEAR_DATE=$clear_date
                    Where PK_RECORD In ($v_item_id_list) And C_REJECTED = 0";
            $this->db->Execute($sql);
        }

        $v_record_type_code = isset($_POST['sel_record_type']) ? replace_bad_char($_POST['sel_record_type']) : '';
        $arr_filter         = array(
            'sel_record_type' => $v_record_type_code,
        );

        $this->exec_done($this->goback_url, $arr_filter);
    }

    public function qry_all_take_over_group()
    {
        $v_user_login_name = replace_bad_char(Session::get('user_login_name'));
        $v_task_code       = replace_bad_char(_CONST_XML_RTT_DELIM . _CONST_BAN_GIAO_ROLE);

        $sql = "Select G.C_GROUP_CODE
                    ,(Select C_NAME FROM t_cores_group Where C_CODE=G.C_GROUP_CODE) C_GROUP_NAME
                From t_r3_user_task G Left Join t_r3_user_task U On G.C_TASK_CODE=U.C_NEXT_TASK_CODE
                Where U.C_USER_LOGIN_NAME='$v_user_login_name'
                    And U.C_TASK_CODE like '%$v_task_code'";

        return $this->db->getAssoc($sql);
    }

    /**
     * Lay thong tin ve task ma NSD chuan bi thuc hien
     * @param type $task_code
     * @return type
     */
    public function qry_single_task_info($task_code)
    {
        $v_user_login_name = replace_bad_char(Session::get('user_login_name'));

        $sql = "Select G.C_GROUP_CODE
                        ,(Select C_NAME FROM t_cores_group Where C_CODE=G.C_GROUP_CODE) C_GROUP_NAME
                        ,G.C_RECORD_TYPE_CODE
                        ,(Select C_NAME FROM t_r3_record_type Where C_CODE=G.C_RECORD_TYPE_CODE) C_RECORD_TYPE_NAME
                From t_r3_user_task G Left Join t_r3_user_task U On G.C_TASK_CODE=U.C_NEXT_TASK_CODE
                Where U.C_USER_LOGIN_NAME='$v_user_login_name'
                    And U.C_TASK_CODE like '%$task_code'";

        return $this->db->getRow($sql);
    }

    /**
     * Lay danh sach User thuc hien mot cong viec cu the
     * @param string $task_code ma cong viec
     */
    public function qry_all_user_on_task($task_code)
    {
        $v_user_code        = Session::get('user_code');
        $v_village_id       = Session::get('village_id');
        $v_record_type_code = $this->db->GetOne('Select C_RECORD_TYPE_CODE From t_r3_user_task Where C_TASK_CODE=?', array($task_code));
        $v_scope            = $this->db->GetOne("Select C_SCOPE From t_r3_record_type Where C_CODE=?", array($v_record_type_code));

        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt   = 'Select UT.C_TASK_CODE
                            ,UT.C_USER_LOGIN_NAME
                            ,U.C_NAME
                            ,U.C_JOB_TITLE
                            ,U.PK_USER
                            ,U.FK_OU
                            ,(Select C_NAME
                                From t_cores_group
                                Where C_CODE=UT.C_GROUP_CODE
                            ) as C_GROUP_NAME
                            ,(Select C_CODE
                                From t_cores_group
                                Where C_CODE=UT.C_GROUP_CODE
                            ) as C_GROUP_CODE
                            ,UT.C_STEP_TIME
                            ,Convert(Xml, (Select C_CODE,C_NAME From t_cores_group G Where G.PK_GROUP In (Select FK_GROUP From t_cores_user_group Where FK_USER=U.PK_USER) For xml raw),1) as C_XML_GROUP_CODE
                    From t_r3_user_task UT Left Join t_cores_user U On UT.C_USER_LOGIN_NAME=U.C_LOGIN_NAME
                    Where UT.C_TASK_CODE = ?
                    Order By U.C_ORDER';
            $params = array($task_code);
            return $this->db->getAll($stmt, $params);
        }
        else
        {
            $stmt = "Select UT.C_TASK_CODE
                            ,UT.C_USER_LOGIN_NAME
                            ,U.C_NAME
                            ,U.C_JOB_TITLE
                            ,U.PK_USER
                            ,(Select C_NAME
                                From t_cores_group
                                Where C_CODE=UT.C_GROUP_CODE
                            ) as C_GROUP_NAME
                            ,(Select C_CODE
                                From t_cores_group
                                Where C_CODE=UT.C_GROUP_CODE
                            ) as C_GROUP_CODE
                            ,UT.C_STEP_TIME
                            ,(Select GROUP_CONCAT('<row'
                                                , CONCAT(' C_CODE=\"', C_CODE, '\"')
                                                , CONCAT(' C_NAME=\"', C_NAME, '\"')
                                                , ' /> '
                                                SEPARATOR ''
                                             )
                             From t_cores_group G
                             Where G.PK_GROUP In (Select FK_GROUP From t_cores_user_group Where FK_USER=U.PK_USER)
                            ) AS C_XML_GROUP_CODE
                            ,U.FK_OU
                    From t_r3_user_task UT Left Join t_cores_user U On UT.C_USER_LOGIN_NAME=U.C_LOGIN_NAME
                    Where UT.C_TASK_CODE = ?";
                //Neu hs cap xa: Xa nao xa do xu ly
                if ($v_scope == 0)
                {
                    $stmt .= " And (U.FK_OU=$v_village_id)";
                }
                
                /*
            	If ($v_village_id > 0)
            	{
            		$stmt .= " And (U.FK_OU=$v_village_id Or $v_scope > 0)";
            	}
            	else
            	{
            		$stmt .= " And ($v_scope > 0)";
            	}
            	*/
                
				$stmt .= " Order By U.C_ORDER";
            $params               = array($task_code);
            $arr_all_user_on_task = $this->db->getAll($stmt, $params);
            return $arr_all_user_on_task;
        }
    }

    /**
     * Danh sách cán bộ tham gia vào công việc tiếp theo
     * @param string $v_task_code mã công việc đang thực hiện
     * @return array
     */
    public function qry_all_user_on_next_task($v_task_code)
    {
        $v_next_task_code = $this->get_next_task_code($v_task_code);
        return $this->qry_all_user_on_task($v_next_task_code);
    }

    /**
     * Lay danh sach HỒ SƠ cần bàn giao từ MộtCửa lên phòng chuyên môn
     * @param type $record_id_list
     * @return type
     */
    public function qry_all_ho_record($record_id_list)
    {
        $record_id_list = replace_bad_char($record_id_list);

        if ($record_id_list != '')
        {
            $v_task_code = _CONST_XML_RTT_DELIM . _CONST_BAN_GIAO_ROLE;
            $stmt        = "Select R.PK_RECORD
                        ,R.C_RECORD_NO
                        ,R.C_RECEIVE_DATE
                        ,R.C_RETURN_DATE
                        ,R.C_CITIZEN_NAME
                        ,RT.C_CODE as C_RECORD_TYPE_CODE
                        ,RT.C_NAME as C_RECORD_TYPE_NAME
                        ,R.C_XML_DATA
                        ,RT.C_SCOPE
                    From view_processing_record R Left Join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                    Where R.PK_RECORD IN ($record_id_list) And R.C_NEXT_TASK_CODE like '%$v_task_code'";
            //$this->db->debug=0;
            return $this->db->getAll($stmt);
        }

        return array();
    }

    public function qry_all_ho_for_tax_record($record_id_list, $task_code)
    {
        $record_id_list = replace_bad_char($record_id_list);

        if ($record_id_list != '')
        {
            $stmt = "Select R.PK_RECORD
                        ,R.C_RECORD_NO
                        ,R.C_RECEIVE_DATE
                        ,R.C_RETURN_DATE
                        ,R.C_CITIZEN_NAME
                        ,RT.C_CODE as C_RECORD_TYPE_CODE
                        ,RT.C_NAME as C_RECORD_TYPE_NAME
                        ,R.C_XML_DATA
                    From view_processing_record R Left Join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                    Where R.PK_RECORD IN ($record_id_list) And (R.C_NEXT_TASK_CODE like '%$task_code' OR R.C_NEXT_NO_CHAIN_TASK_CODE Like '%$task_code')";
            return $this->db->getAll($stmt);
        }

        return array();
    }

    /**
     * Lấy danh sách HỒ SƠ bàn giao từ phòng chuyên môn về Một Cửa
     * @param type $record_id_list
     */
    public function qry_all_ho_back_record($record_id_list)
    {
        $record_id_list = replace_bad_char($record_id_list);

        if ($record_id_list != '')
        {
            $v_task_code            = _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_ROLE;
            $v_task_code_supplement = _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_BO_SUNG_ROLE;
            $stmt                   = "Select R.PK_RECORD
                        ,R.C_RECORD_NO
                        ,R.C_RECEIVE_DATE
                        ,R.C_RETURN_DATE
                        ,R.C_CITIZEN_NAME
                        ,R.C_NEXT_TASK_CODE
                        ,RT.C_CODE as C_RECORD_TYPE_CODE
                        ,RT.C_NAME as C_RECORD_TYPE_NAME
                        ,R.C_XML_DATA
                    From view_processing_record R Left Join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                    Where R.PK_RECORD IN ($record_id_list) And (R.C_NEXT_TASK_CODE like '%$v_task_code' Or R.C_NEXT_TASK_CODE like '%$v_task_code_supplement')";
            return $this->db->getAll($stmt);
        }

        return array();
    }

    public function qry_all_record_for_allot($record_id_list, $v_task_code = '')
    {
        $record_id_list = replace_bad_char($record_id_list);

        if ($record_id_list != '')
        {
            if ($v_task_code == '')
            {
                $v_task_code = _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE;
            }
            $stmt = "Select R.PK_RECORD
                        ,R.C_RECORD_NO
                        ,R.C_RECEIVE_DATE
                        ,R.C_RETURN_DATE
                        ,R.C_CITIZEN_NAME
                        ,RT.C_CODE as C_RECORD_TYPE_CODE
                        ,RT.C_NAME as C_RECORD_TYPE_NAME
                        , R.C_NEXT_TASK_CODE
                        ,R.C_PAUSE_DATE
                        ,R.C_UNPAUSE_DATE
                        ,R.C_XML_PROCESSING
                    From view_processing_record R Left Join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                    Where R.PK_RECORD IN ($record_id_list)
                        And (R.C_NEXT_TASK_CODE like '%$v_task_code')";
            return $this->db->getAll($stmt);
        }

        return array();
    }

    public function qry_all_record_for_task($record_id_list, $task_code = '')
    {
        $record_id_list = replace_bad_char($record_id_list);
        $v_user_code    = Session::get('user_code');

        if ($task_code == '')
        {
            $task_code = _CONST_BAN_GIAO_ROLE;
        }

        $stmt = "Select R.PK_RECORD
                    ,R.C_RECORD_NO
                    ,R.C_RECEIVE_DATE
                    ,R.C_RETURN_DATE
                    ,R.C_CITIZEN_NAME
                    ,RT.C_CODE as C_RECORD_TYPE_CODE
                    ,RT.C_NAME as C_RECORD_TYPE_NAME
                    ,R.C_XML_DATA
                    ,R.C_XML_PROCESSING
                    ,R.C_NEXT_TASK_CODE
                    ,R.C_PAUSE_DATE
                    ,R.C_UNPAUSE_DATE
                From view_processing_record R Left Join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                Where (R.C_NEXT_USER_CODE='$v_user_code' Or R.C_NEXT_USER_CODE like '%,$v_user_code,%' Or R.C_NEXT_USER_CODE Is Null Or R.C_NEXT_NO_CHAIN_USER_CODE Like '%,$v_user_code,%')
                    And (R.C_NEXT_TASK_CODE like '%$task_code' Or R.C_NEXT_NO_CHAIN_TASK_CODE Like '%$task_code')";

        if ($record_id_list != '')
        {
            $stmt .= " And R.PK_RECORD IN ($record_id_list)";
        }

        return $this->db->getAll($stmt);
    }

    /**
     * Lay danh sach HS theo ID
     * @param string $record_id_list
     */
    public function qry_all_record_by_id($record_id_list)
    {
        $record_id_list = replace_bad_char($record_id_list);

        $stmt = "Select
                    R.PK_RECORD
                    , R.C_RECORD_NO
                    , R.C_RECEIVE_DATE
                    , R.C_RETURN_DATE
                    , R.C_CITIZEN_NAME
                    , RT.C_CODE          as C_RECORD_TYPE_CODE
                    , RT.C_NAME          as C_RECORD_TYPE_NAME
                    , R.C_XML_DATA
                    , RT.C_SCOPE
                    , R.C_NEXT_TASK_CODE
                From view_processing_record R
                    Left Join t_r3_record_type RT
                      On R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                Where R.PK_RECORD IN ($record_id_list)";

        $arr_all_record = $this->db->getAll($stmt);

        $arr_group_info       = array();
        $arr_single_task_info = array();
        if (sizeof($arr_all_record) > 0)
        {
            $v_next_task_code   = $arr_all_record[0]['C_NEXT_TASK_CODE'];
            $v_record_type_code = $arr_all_record[0]['C_RECORD_TYPE_CODE'];

            /*
              $dom_xml_flow = simplexml_load_file($this->_get_xml_workflow_file_path($v_record_type_code));

              $v_current_group_code = get_xml_value($dom_xml_flow, "//step[task[@code='$v_next_task_code']]/@group");
              $v_next_group_code    = get_xml_value($dom_xml_flow, "//step[task[@code='$v_next_task_code']]/following-sibling::step[not(@no_chain = 'true')][1]/@group");
             */

            //Ma phong ban giao
            $v_current_group_code                   = $this->db->getOne("Select C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE='$v_next_task_code'");
            $arr_group_info['C_CURRENT_GROUP_CODE'] = $v_current_group_code;

            //Ma phong nhan ban giao
            $v_next_group_code                   = $this->db->getOne("Select C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE=(Select C_NEXT_TASK_CODE From t_r3_user_task Where C_TASK_CODE='$v_next_task_code')");
            $arr_group_info['C_NEXT_GROUP_CODE'] = $v_next_group_code;

            $arr_group_info['C_CURRENT_GROUP_NAME'] = $this->db->getOne("Select C_NAME From t_cores_group Where C_CODE='$v_current_group_code'");
            $arr_group_info['C_NEXT_GROUP_NAME']    = $this->db->getOne("Select C_NAME From t_cores_group Where C_CODE='$v_next_group_code'");


            $arr_single_task_info = $this->qry_single_task_info($v_next_task_code);
        }

        $MODEL_DATA['arr_all_record']       = $arr_all_record;
        $MODEL_DATA['arr_group_info']       = $arr_group_info;
        $MODEL_DATA['arr_single_task_info'] = $arr_single_task_info;
        return $MODEL_DATA;
    }

    /*
     * Ban giao ho so cho phong chuyen mon
     */

    public function do_handover_record()
    {
        $v_item_id_list     = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';
        $v_record_type_code = isset($_POST['sel_record_type']) ? replace_bad_char($_POST['sel_record_type']) : '';

        $v_item_id_list != '' OR DIE();
        $arr_handover_record = explode(',', $v_item_id_list);

        
        
        //Lay ID cua ho dau tien trong danh sach
        $v_first_record_id     = $arr_handover_record[0];
        //Pham vi thu tuc
        $stmt  = 'Select
                    RT.C_SCOPE
                    ,R.C_NEXT_TASK_CODE
                From t_r3_record_type RT
                    Left Join t_r3_record R
                      On RT.PK_RECORD_TYPE = R.FK_RECORD_TYPE
                Where R.PK_RECORD = ?';
        $params                = array($v_first_record_id);
        $arr_first_record_info = $this->db->getRow($stmt, $params);
        $v_scope               = $arr_first_record_info['C_SCOPE'];
        $v_current_task        = $arr_first_record_info['C_NEXT_TASK_CODE'];
        
        //1. Thông tin về công việc tiếp theo
        //1.1 Mã công việc tiếp theo
        $v_next_task_code = $this->get_next_task_code($v_current_task);
        if (!$v_next_task_code)
        {
            $this->exec_fail($this->goback_url, 'Lỗi tại:' . __FILE__ . '-' . __FUNCTION__);
        }
        
        
        //1.2 Danh sách người thực hiện công việc tiếp theo
        $arr_all_next_user = $this->qry_all_user_on_task($v_next_task_code);
        if (sizeof($arr_all_next_user) > 1)
        {
            if ($v_scope == 0) //Thu tuc cap xa
            {
                $v_next_task_user_code = '';
                foreach ($arr_all_next_user as $next_user)
                {
                    if ($next_user['FK_OU'] == Session::get('ou_id'))
                    {
                        $v_next_task_user_code .= ($v_next_task_user_code != '') ? ',' . $next_user['C_USER_LOGIN_NAME'] : $next_user['C_USER_LOGIN_NAME'];
                    }
                }
            }
            else
            {
                $v_next_task_user_code = '';
                foreach ($arr_all_next_user as $next_user)
                {
                    $v_next_task_user_code .= ($v_next_task_user_code != '') ? ',' . $next_user['C_USER_LOGIN_NAME'] : $next_user['C_USER_LOGIN_NAME'];
                }
            }
            $v_next_task_user_code = ',' . $v_next_task_user_code . ',';

            $v_next_task_user_name = '';
            $v_next_user_job_title = '';
            $v_step_time           = $arr_all_next_user[0]['C_STEP_TIME'];
            $v_group_code          = $arr_all_next_user[0]['C_GROUP_CODE'];
        }
        else
        {
            $v_next_task_user_code = $arr_all_next_user[0]['C_USER_LOGIN_NAME'];
            $v_next_task_user_name = $arr_all_next_user[0]['C_NAME'];
            $v_next_user_job_title = $arr_all_next_user[0]['C_JOB_TITLE'];
            $v_group_name          = $arr_all_next_user[0]['C_GROUP_NAME'];
            $v_step_time           = $arr_all_next_user[0]['C_STEP_TIME'];
            $v_group_code          = $arr_all_next_user[0]['C_GROUP_CODE'];
        }

        foreach ($arr_handover_record as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }
            $this->db->Execute('Update t_r3_record Set C_ROLLBACKABLE=1 Where PK_RECORD=?', array($v_record_id));
            //Next task
            //Neu buoc tiep theo la tra hs ve xa cua thu tuc Lien thong Xã -> Huyện
            if (is_sub_string($v_current_task, $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TRA_HO_SO_VE_XA_ROLE) && $v_scope == 1)
            {
                //Tim xa nop hs
                $stmt   = 'Select 
                            C_LOGIN_NAME
                            ,C_NAME
                            ,C_JOB_TITLE
                        From t_cores_user
                        Where C_LOGIN_NAME=(Select C_CREATE_BY From t_r3_record Where PK_RECORD=?)';
                $params                = array($v_record_id);
                $arr_create_user_info  = $this->db->getRow($stmt, $params);
                $v_next_task_user_code = $arr_create_user_info['C_LOGIN_NAME'];
                $v_next_task_user_name = $arr_create_user_info['C_NAME'];
                $v_next_user_job_title = $arr_create_user_info['C_JOB_TITLE'];
            }
            if (!$v_next_task_code)
            {
                continue;
            }

            $xml_next_task = '<next_task ';
            $xml_next_task .= ' code="' . $v_next_task_code . '"';
            $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
            $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
            $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
            $xml_next_task .= ' group_name="' . $v_group_name . '"';
            $xml_next_task .= ' group_code="' . $v_group_code . '"';
            $xml_next_task .= ' step_time="' . $v_step_time . '"';
            $xml_next_task .= ' />';
            //Insert Step
            $v_step_seq    = uniqid();
            $v_dead_line   = $this->db->GetOne("Select C_DOING_STEP_DEADLINE_DATE From t_r3_record
                Where PK_RECORD=?", array($v_record_id));
            $step          = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '<to_group_code>' . $v_group_code . '</to_group_code>'; //LienND bo sung ngay 07-12-2012, lưu thừa để báo cáo
            $step .= "<deadline>$v_dead_line</deadline>";
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task);
        }
        $this->exec_done($this->goback_url, array('sel_record_type' => $v_record_type_code));
    }

    public function do_handover_supplement_record()
    {
        $v_item_id_list     = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';
        $v_record_type_code = isset($_POST['sel_record_type']) ? replace_bad_char($_POST['sel_record_type']) : '';

        $arr_handover_record = explode(',', $v_item_id_list);

        $v_item_id_list != '' OR DIE('Do not hurt me!');

        //Kiem tra pham vi ho so
        //Xác định phạm vi thủ tục
        $stmt           = 'Select C_SCOPE From t_r3_record_type Where C_CODE=?';
        $v_scope        = $this->db->getOne($stmt, array($v_record_type_code));
        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE;

        foreach ($arr_handover_record as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }
            if ($v_scope != 0) //Không phải là thủ tục cấp xã
            {
                //Kiểm tra yêu cầu của phòng chuyên môn: sau khi bổ sung chuyển đến bước duyệt bổ sung hay duyệt lại từ đầu
                $v_goto = $this->db->getOne('Select C_GOTO From t_r3_record_supplement Where FK_RECORD=? And C_DONE=0', array($v_record_id));
                if ($v_goto == 0)
                {
                    //Duyệt lại từ đầu
                    $v_xml_processing = $this->db->getOne('Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=?', array($v_record_id));
                    $dom_processing   = simplexml_load_string($v_xml_processing);
                    //Mã chính xác của công việc sau khi 1 cửa bàn giao
                    $found            = false;
                    $step             = false;
                    foreach (xpath($dom_processing, '//step') as $step)
                    {
                        if ($found)
                        {
                            break;
                        }
                        if (strpos($step->attributes()->code, _CONST_BAN_GIAO_ROLE) !== false)
                        {
                            $found = true;
                        }
                    }
                    if (!$step)
                    {
                        continue;
                    }
                    $v_next_task_code      = $step->attributes()->code;
                    $v_next_task_user_code = $step->user_code;
                    $v_next_task_user_name = $step->user_name;
                    $v_next_user_job_title = $step->user_job_title;
                    $xml_next_task         = '<next_task ';
                    $xml_next_task .= ' code="' . $v_next_task_code . '"';
                    $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
                    $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
                    $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
                    $xml_next_task .= ' />';

                    if (!$v_next_task_code)
                    {
                        continue;
                    }

                    //Insert Step
                    $v_step_seq = uniqid();
                    $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
                    $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
                    $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
                    $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                    $step .= '<datetime>' . $this->getDate() . '</datetime>';
                    $step .= '</step>';
                    $this->_insert_record_processing_step($v_record_id, $step);
                    $this->_update_next_task_info($v_record_id, $xml_next_task);
                }
                elseif ($v_goto == 1)
                {
                    //Chuyển đến bước Phê duyệt hồ sơ bổ sung
                    //LienND cap nhat 23/10/2012: Tìm người yêu cầu bổ sung
                    $v_xml_data           = $this->db->getOne("Select c_XML_PROCESSING From view_processing_record Where PK_RECORD=$v_record_id");
                    $dom_processing       = simplexml_load_string($v_xml_data);
                    $v_approval_task_code = _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_ROLE;
                    $v_supplement_code    = _CONST_RECORD_APPROVAL_SUPPLEMENT;
                    $xpath                = "//step[contains(@code,'$v_approval_task_code') and promote='$v_supplement_code'][last()]";
                    $step                 = xpath($dom_processing, $xpath, XPATH_DOM);

                    //Nguoi yeu cau bo sung la nguoi se phe duyet bo sung
                    $v_next_user_code      = $step->user_code;
                    $v_next_user_name      = $step->user_name;
                    $v_next_user_job_title = $step->user_job_title;

                    //Xac dinh chinh xac ma cong viec "XET_DUYET_BO_SUNG"
                    $v_task_like      = _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_BO_SUNG_ROLE;
                    $sql              = "Select C_TASK_CODE
                            From t_r3_user_task
                            Where C_USER_LOGIN_NAME='$v_next_user_code'
                                And C_TASK_CODE like '%$v_task_like'
                                And C_RECORD_TYPE_CODE='$v_record_type_code'";
                    $v_next_task_code = $this->db->getOne($sql);

                    $xml_next_task = '<next_task ';
                    $xml_next_task .= ' code="' . $v_next_task_code . '"';
                    $xml_next_task .= ' user="' . $v_next_user_code . '"';
                    $xml_next_task .= ' user_name="' . $v_next_user_name . '"';
                    $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
                    $xml_next_task .= ' />';
                    $this->_update_next_task_info($v_record_id, $xml_next_task);

                    //Insert Step
                    $v_step_seq = uniqid();
                    $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
                    $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
                    $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
                    $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                    $step .= '<datetime>' . $this->getDate() . '</datetime>';
                    $step .= '</step>';
                    $this->_insert_record_processing_step($v_record_id, $step);
                }
            }
            else
            {
                //Nếu là thủ tục cấp xã, sau khi bổ sung, chuyển lại cho Cán bộ thụ lý
                $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BAN_GIAO_ROLE;
                //Insert Step
                $v_step_seq     = uniqid();
                $step           = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
                $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
                $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
                $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                $step .= '<datetime>' . $this->getDate() . '</datetime>';
                $step .= '</step>';
                $this->_insert_record_processing_step($v_record_id, $step);

                //Next task
                $arr_next_task_info = $this->_qry_next_task_info($v_current_task);

                $v_next_task_code      = $arr_next_task_info['C_NEXT_TASK_CODE'];
                $v_next_task_user_code = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
                $v_next_task_user_name = $arr_next_task_info['C_NEXT_USER_NAME'];
                $v_next_user_job_title = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];

                $xml_next_task = '<next_task ';
                $xml_next_task .= ' code="' . $v_next_task_code . '"';
                $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
                $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
                $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
                $xml_next_task .= ' />';
                $this->_update_next_task_info($v_record_id, $xml_next_task);
            }//endif SCope
            //Xoa thong tin ve HS bo sung
            $stmt   = 'Update t_r3_record_supplement Set C_DONE=1 Where FK_RECORD=?';
            $params = array($v_record_id);
            $this->db->Execute($stmt, $params);
        }

        $this->exec_done($this->goback_url, array('sel_record_type' => $_POST['sel_record_type']));
    }

    public function do_allot_record()
    {
        //Ma loai HS
        $v_record_type_code = isset($_POST['hdn_record_type_code']) ? replace_bad_char($_POST['hdn_record_type_code']) : '';

        ($v_record_type_code != '') OR DIE();

        $v_user_code = Session::get('user_code');

        //Danh sach HS phan cong
        $v_record_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';

        //CB thu ly chinh
        $v_exec_user_code = isset($_POST['sel_exec_user']) ? replace_bad_char($_POST['sel_exec_user']) : '';
        $v_exec_user_name = isset($_POST['hdn_direct_exec_user_name']) ? replace_bad_char($_POST['hdn_direct_exec_user_name']) : '';

        //Danh sach CB phoi hop thu ly
        $v_co_exec_user_code_list = isset($_POST['hdn_co_exec_user_code_list']) ? replace_bad_char($_POST['hdn_co_exec_user_code_list']) : '';

        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE;
        $arr_record_id  = explode(',', $v_record_id_list);
        foreach ($arr_record_id as $v_record_id)
        {
            //$v_current_task ?????
            //HS đã phân công chưa? Chưa phân công thì phân công, đã phân công thì PHAN CONG LAI
            $sql   = "Select Count(*)
                    From view_processing_record
                    Where PK_RECORD=$v_record_id
                        And C_LAST_TASK_CODE like '%$v_current_task'
                        AND C_LAST_USER_CODE='$v_user_code'";
            $check = $this->db->getOne($sql);

            if ($check == 0)
            {
                $stmt           = 'Select C_NEXT_TASK_CODE From view_processing_record Where PK_RECORD=?';
                $v_current_task = $this->db->getOne($stmt, array($v_record_id));
            }
            else
            {
                $stmt           = 'Select C_LAST_TASK_CODE From view_processing_record Where PK_RECORD=?';
                $v_current_task = $this->db->getOne($stmt, array($v_record_id));
            }

            //Next task
            $arr_next_task_info       = $this->_qry_next_task_info($v_current_task);
            $v_next_task_code         = $arr_next_task_info['C_NEXT_TASK_CODE'];
            $v_next_task_user_code    = $v_exec_user_code;
            $v_next_task_user_name    = $v_exec_user_name;
            $v_next_task_co_user_code = ',' . $v_co_exec_user_code_list . ',';

            if (!$v_next_task_code)
            {
                continue;
            }

            $xml_next_task = '<next_task ';
            $xml_next_task .= ' code="' . $v_next_task_code . '"';
            $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
            $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
            $xml_next_task .= ' user_job_title=""';
            $xml_next_task .= ' co_user="' . $v_next_task_co_user_code . '"';
            $xml_next_task .= ' />';


            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task);
        }

        $this->popup_exec_done();
    }

    public function do_reallot_record()
    {
        $this->do_allot_record();
    }

    public function qry_all_supplement_notice($status)
    {
        $status = replace_bad_char($status);

        $v_role = '%' . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE;

        $stmt = 'Select RT.C_CODE as record_type_code
                    , RT.C_NAME as record_type_name
                    , a.COUNT_RECORD as count_record
                From t_r3_record_type RT Right Join (
                        Select COUNT(*) COUNT_RECORD, FK_RECORD_TYPE
                        From view_processing_record R Right Join t_r3_record_supplement RS On R.PK_RECORD=RS.FK_RECORD
                        Where (R.C_NEXT_TASK_CODE like ?)
                            And R.C_NEXT_USER_CODE=?
                            And RS.C_DONE <> 1';
        if ($status == 0)
        {
            $stmt .= ' And RS.C_ANNOUNCE_DATE is null And RS.C_RECEIVE_DATE is null';
        }
        elseif ($status == 1)
        {
            $stmt .= ' And RS.C_ANNOUNCE_DATE is Not null And RS.C_RECEIVE_DATE is null ';
        }
        elseif ($status == 2)
        {
            $stmt .= ' And RS.C_ANNOUNCE_DATE is Not null And RS.C_RECEIVE_DATE is Not null';
        }

        $stmt .= ' Group By FK_RECORD_TYPE ) a
                On RT.PK_RECORD_TYPE=a.FK_RECORD_TYPE';

        $params = array($v_role, Session::get('user_login_name'));

        $this->db->debug = 0;
        return $this->db->getAll($stmt, $params);
    }

    /**
     * ĐẾM TỔNG HS đang chờ giải quyết theo Role
     * @param type $role
     * @return type
     */
    public function count_processing_record_by_role($role)
    {
        if (DEBUG_MODE < 10)
        {
            $this->db->debug = 0;
        }

        $v_user_code  = Session::get('user_code');
        $v_village_id = Session::get('village_id');
        $task         = $role         = strtoupper(replace_bad_char($role));

        $v_real_role = strtoupper($role);
        $params      = array();
        switch ($v_real_role)
        {
            case _CONST_TIEP_NHAN_ROLE:
            	//Dem so ho so tiep nhan = So ho so vua tiep nhan + So ho so bi tra ve
            	
                $task_tiep_nhan                 = '%' . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;
                $task_chuyen_lai_buoc_truoc     = '%' . _CONST_XML_RTT_DELIM . _CONST_CHUYEN_LAI_BUOC_TRUOC_ROLE;
                $task_ban_giao     				= '%' . _CONST_XML_RTT_DELIM . _CONST_BAN_GIAO_ROLE;
                
                
                //$v_task_code_handover = "ExtractValue(C_XML_PROCESSING, '//step[Contains(@code, \"" . _CONST_BAN_GIAO_ROLE . "\")][1]/@code')";
                $stmt = "Select ? role,  COUNT(*) count
	                    From view_processing_record
	                    Where (C_NEXT_USER_CODE = '$v_user_code' Or C_NEXT_USER_CODE Like '%,$v_user_code,%')
	                    	And (C_LAST_TASK_CODE like '$task_tiep_nhan' Or C_LAST_TASK_CODE like '$task_chuyen_lai_buoc_truoc')
	                    	And FK_VILLAGE_ID = $v_village_id
                			And C_NEXT_TASK_CODE like '$task_ban_giao'";
                
                $params               = array($v_real_role);
                break;
                
            case _CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE:
                $stmt                 = "Select ? role, Count(*) count
                    From t_r3_internet_record
                    Where (C_NEXT_TASK_CODE like ?)
                        And (C_NEXT_USER_CODE=? Or C_NEXT_USER_CODE Like '%,$v_user_code,%')
                        And (C_DELETED Is Null Or C_DELETED = 0)
                        And C_IS_REAL_RECORD=1";
                $params               = array(_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE, '%' . _CONST_XML_RTT_DELIM . $task, $v_user_code);
                break;
                
            case _CONST_KIEM_TRA_TRUOC_HO_SO_ROLE:
                $stmt                 = "Select ? role, Count(*) count
                    From t_r3_internet_record
                    Where (C_NEXT_USER_CODE=? Or C_NEXT_USER_CODE Like '%,$v_user_code,%')
                        And (C_DELETED Is Null Or C_DELETED = 0)
                        And C_IS_REAL_RECORD<>1
                        And C_CLEAR_DATE Is Null";
                $params               = array(_CONST_KIEM_TRA_TRUOC_HO_SO_ROLE, $v_user_code);
                break;
                
            case _CONST_IN_PHIEU_TIEP_NHAN_ROLE:
                $stmt                 = 'Select ? role, Count(*) count From view_processing_record Where C_CREATE_BY=?';
                $params               = array(_CONST_IN_PHIEU_TIEP_NHAN_ROLE, $v_user_code);
                break;
                
            case _CONST_PHAN_CONG_LAI_ROLE:
                $task                 = '%' . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE;
                $stmt                 = "Select ? role,  COUNT(*) count
                    From view_processing_record
                    Where C_LAST_TASK_CODE like ?
                    And C_LAST_USER_CODE=?";
                $params               = array(_CONST_PHAN_CONG_LAI_ROLE, $task, $v_user_code);
                break;
                
            case _CONST_BO_SUNG_ROLE:
            	$task                 = '%' . _CONST_XML_RTT_DELIM . $task;
            	$stmt                 = "Select ? role,  COUNT(*) count
						            	From view_processing_record R left join t_r3_record_supplement RS on R.PK_RECORD=RS.FK_RECORD
						            	Where (
							            	(C_NEXT_TASK_CODE like ? And (C_NEXT_USER_CODE=? Or C_NEXT_USER_CODE Like '%,$v_user_code,%' Or C_NEXT_CO_USER_CODE like '%,$v_user_code,%'))
							            	OR (C_NEXT_NO_CHAIN_TASK_CODE Like ? And C_NEXT_NO_CHAIN_USER_CODE Like '%,$v_user_code,%')
							            	)
							            	AND RS.C_DONE <> 1";
            	$params               = array($role, $task, $v_user_code, $task);
            	
            	break;
                
            case _CONST_TRA_KET_QUA_ROLE:
                $stmt                 = "
                    Select ? role,  COUNT(*) count
                    From view_processing_record R
                    Where R.C_NEXT_TASK_CODE Like ?
                                And R.FK_VILLAGE_ID=?
                                And (R.C_NEXT_USER_CODE=? 
                                        Or C_NEXT_USER_CODE Like ? 
                                        Or R.C_NEXT_USER_CODE Is Null 
                                        Or C_NEXT_CO_USER_CODE like ?)
                ";
                $params               = array(_CONST_TRA_KET_QUA_ROLE, '%' . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE
                    , $v_village_id, $v_user_code, "%,$v_user_code,%", "%,$v_user_code,%");
                break;
                
            default:
                $task                 = '%' . _CONST_XML_RTT_DELIM . $task;
                $stmt                 = "Select ? role,  COUNT(*) count
                    From view_processing_record
                    Where ( 
                            (C_NEXT_TASK_CODE like ? And (C_NEXT_USER_CODE=? Or C_NEXT_USER_CODE Like '%,$v_user_code,%' Or C_NEXT_CO_USER_CODE like '%,$v_user_code,%'))
                            OR (C_NEXT_NO_CHAIN_TASK_CODE Like ? And C_NEXT_NO_CHAIN_USER_CODE Like '%,$v_user_code,%') 
                          )";
                $params               = array($role, $task, $v_user_code, $task);
        }
        $ret             = $this->db->getRow($stmt, $params);
        $this->db->debug = DEBUG_MODE;
        return $ret;
    }

    public function do_exec_record()
    {
        //Ma loai HS
        $v_record_type_code = isset($_POST['hdn_record_type_code']) ? replace_bad_char($_POST['hdn_record_type_code']) : '';

        ($v_record_type_code != '') OR DIE();

        //Ket qua thu ly
        $v_exec_value = isset($_POST['hdn_exec_value']) ? replace_bad_char($_POST['hdn_exec_value']) : '';
        ($v_exec_value != '') OR DIE();

        //Phi
        $v_fee             = isset($_POST['txt_fee']) ? replace_bad_char($_POST['txt_fee']) : '';
        $v_fee_description = isset($_POST['txt_fee_description']) ? replace_bad_char($_POST['txt_fee_description']) : '';

        //Danh sach ma HS
        $v_record_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';

        //Ly do chua duyet HS
        $v_reason = isset($_POST['txt_reason']) ? replace_bad_char($_POST['txt_reason']) : '';

        $arr_record_id = explode(',', $v_record_id_list);

        //Xác định phạm vi thủ tục
        $stmt    = 'Select C_SCOPE From t_r3_record_type Where C_CODE=?';
        $v_scope = $this->db->getOne($stmt, array($v_record_type_code));

        foreach ($arr_record_id as $v_record_id)
        {
            $stmt    = 'Select Count(*) From view_processing_record Where PK_RECORD=? And (C_NEXT_USER_CODE like ? OR C_NEXT_USER_CODE=?)';
            $v_count = $this->db->getOne($stmt, array($v_record_id, '%,' . Session::get('user_code') . ',%', Session::get('user_code')));
            if ($v_count < 1)
            {
                continue;
            }

            //$v_current_task ?????
            $stmt           = 'Select C_NEXT_TASK_CODE From view_processing_record Where PK_RECORD=?';
            $v_current_task = $this->db->getOne($stmt, array($v_record_id));

            $v_step_seq = uniqid();
            $v_xml_step_log = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $v_xml_step_log .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $v_xml_step_log .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $v_xml_step_log .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $v_xml_step_log .= '<datetime>' . $this->getDate() . '</datetime>';

            if ($v_exec_value != _CONST_RECORD_APPROVAL_ACCEPT)
            {
                $v_xml_step_log .= '<promote>' . $v_exec_value . '</promote>';
                $v_xml_step_log .= '<reason>' . $v_reason . '</reason>';
            }
            else
            {
                $v_xml_step_log .= '<fee>' . $v_fee . '</fee>';
                $v_xml_step_log .= '<fee_description>' . $v_fee_description . '</fee_description>';
            }

            $v_xml_step_log .= '</step>';

            //Tính toán về công việc tiếp theo
            if ($v_scope != 0) //Không phải thủ tục cấp xã
            {
                //Next task
                $arr_next_task_info = $this->_qry_next_task_info($v_current_task);

                $v_next_task_code           = $arr_next_task_info['C_NEXT_TASK_CODE'];
                $v_next_task_user_code      = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
                $v_next_task_user_name      = $arr_next_task_info['C_NEXT_USER_NAME'];
                $v_next_task_user_job_title = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];

                $xml_next_task        = '<next_task';
                $xml_next_task .= ' code="' . $v_next_task_code . '"';
                //LienND update 2013-01-29
                //Neu cong viec tiep theo la: XET_DUYET & co nhieu nguoi XET_DUYET -> Tìm lại đúng người PHAN_CONG, người PHAN_CONG sẽ là người XET_DUYET
                $v_approval_task_code = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_ROLE;
                if (is_sub_string($v_next_task_code, $v_approval_task_code) && ($arr_next_task_info['C_TOTAL_USER'] > 1))
                {
                    $dom_processing    = simplexml_load_string($this->db->getOne('Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=?', array($v_record_id)));
                    $v_allot_task_code = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE;
                    $step              = xpath($dom_processing, "//step[contains(@code,'$v_allot_task_code')][last()]", XPATH_DOM);

                    $xml_next_task .= ' user="' . $step->user_code . '"';
                    $xml_next_task .= ' user_name="' . $step->user_name . '"';
                    $xml_next_task .= ' user_job_title="' . $step->user_job_title . '"';
                }
                else
                {
                    $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
                    $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
                    $xml_next_task .= ' user_job_title="' . $v_next_task_user_job_title . '"';
                }

                if ($v_exec_value != _CONST_RECORD_APPROVAL_ACCEPT)
                {
                    //Đề xuất & lý do không phê duyệt hồ sơ
                    $xml_next_task .= ' promote="' . $v_exec_value . '"';
                    $xml_next_task .= ' reason="' . $v_reason . '"';
                }
                else
                {
                    //HS được đề nghị duyêt: Tính phí/lệ phí
                    $xml_next_task .= ' fee="' . $v_fee . '"';
                    $xml_next_task .= ' fee_description="' . $v_fee_description . '"';
                }
                $xml_next_task .= ' />';

                if (!$v_next_task_code)
                {
                    continue;
                }
                $this->_insert_record_processing_step($v_record_id, $v_xml_step_log);
                $this->_update_next_task_info($v_record_id, $xml_next_task);
            }
            elseif ($v_scope == 0) //là thủ tục cấp xã
            {
                //Neu duyet, tìm bước thiếp kế tiếp theo quy trình
                if ($v_exec_value == _CONST_RECORD_APPROVAL_ACCEPT)
                {
                    //Nguoi duyet, la nguoi thuoc nhom LANH_DAO_CAP_XA & cung OU voi can bo thu ly.
                    //Next task
                    $arr_next_task_info         = $this->_qry_next_task_info($v_current_task);
                    $v_next_task_code           = $arr_next_task_info['C_NEXT_TASK_CODE'];
                    $v_next_task_user_code      = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
                    $v_next_task_user_name      = $arr_next_task_info['C_NEXT_USER_NAME'];
                    $v_next_task_user_job_title = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];

                    //Lay ten dang nhap cua chu tich ky duyet
                    $v_ou_id                    = Session::get('ou_id');
                    $sql                        = "Select 
                                                        C_LOGIN_NAME
                                                        ,C_NAME
                                                        ,C_JOB_TITLE
                                                    From t_cores_user
                                                    Where FK_OU = $v_ou_id
                                                          And PK_USER In(Select
                                                                             FK_USER
                                                                         From t_cores_user_group UG
                                                                             Left Join t_cores_group G
                                                                               On UG.FK_GROUP = G.PK_GROUP
                                                                    có     Where G.C_CODE = 'LANH_DAO_CAP_XA'
                                                                         )";
                    $arr_single_next_user       = $this->db->getRow($sql);
                    $v_next_task_user_code      = $arr_single_next_user['C_LOGIN_NAME'];
                    $v_next_task_user_name      = $arr_single_next_user['C_NAME'];
                    $v_next_task_user_job_title = $arr_single_next_user['C_JOB_TITLE'];

                    $xml_next_task = '<next_task';
                    $xml_next_task .= ' code="' . $v_next_task_code . '"';
                    $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
                    $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
                    $xml_next_task .= ' user_job_title="' . $v_next_task_user_job_title . '"';
                    $xml_next_task .= ' fee="' . $v_fee . '"';
                    $xml_next_task .= ' fee_description="' . $v_fee_description . '"';
                    $xml_next_task .= ' />';

                    $this->_insert_record_processing_step($v_record_id, $v_xml_step_log);
                    $this->_update_next_task_info($v_record_id, $xml_next_task);
                }
                //Neu yeu cau bo sung, tra ve 1 cua de bo sung
                elseif ($v_exec_value == _CONST_RECORD_APPROVAL_SUPPLEMENT)
                {
                    //Yêu cầu bổ sung, về Một-Cửa để yêu cầu bổ sung
                    //1. Tim lai nguoi tiep nhan
                    //NEXT_TASK = 'BO SUNG'
                    //1. Tim ra nguoi tiep nhan
                    $v_xml_processing = $this->db->getOne("Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=$v_record_id");
                    $dom_processing   = simplexml_load_string($v_xml_processing);
                    $v_code           = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;
                    $next_user_info   = xpath($dom_processing, "//step[@code='$v_code'][last()]", XPATH_DOM);

                    $xml_next_task = '<next_task ';
                    $xml_next_task .= ' code="' . $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE . '"';
                    $xml_next_task .= ' user="' . $next_user_info->user_code . '"';
                    $xml_next_task .= ' user_name="' . $next_user_info->user_name . '"';
                    $xml_next_task .= ' user_job_title="' . $next_user_info->user_job_title . '"';
                    $xml_next_task .= ' promote="' . $v_approval_value . '"';
                    $xml_next_task .= ' reason="' . $v_reason . '"';

                    $xml_next_task .= ' />';

                    if (!$v_next_task_code)
                    {
                        continue;
                    }

                    //Chuyển hồ sơ vào danh sách bổ sung
                    $v_goto = isset($_POST['rad_after_supplement']) ? replace_bad_char($_POST['rad_after_supplement']) : '1';
                    $stmt   = 'Insert Into t_r3_record_supplement(FK_RECORD, C_GOTO) Values(?,?)';
                    $this->db->Execute($stmt, array($v_record_id, $v_goto));
                    
                    $this->_insert_record_processing_step($v_record_id, $v_xml_step_log);
                    $this->_update_next_task_info($v_record_id, $xml_next_task);
                }
                //Neu tu choi, tra ve 1 cua => tra ket qua cho cong dan
                elseif ($v_exec_value == _CONST_RECORD_APPROVAL_REJECT)
                {
                    //Từ chối, về Một-Cửa để trả công dân
                    //1. Tim ra nguoi tiep nhan
                    $v_xml_processing = $this->db->getOne("Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=$v_record_id");
                    $dom_processing   = simplexml_load_string($v_xml_processing);
                    $v_code           = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;
                    $next_user_info   = xpath($dom_processing, "//step[@code='$v_code'][last()]", XPATH_DOM);

                    $xml_next_task = '<next_task ';
                    $xml_next_task .= ' code="' . $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . '"';
                    $xml_next_task .= ' user="' . $next_user_info->user_code . '"';
                    $xml_next_task .= ' user_name="' . $next_user_info->user_name . '"';
                    $xml_next_task .= ' user_job_title="' . $next_user_info->user_job_title . '"';
                    $xml_next_task .= ' promote="' . $v_approval_value . '"';
                    $xml_next_task .= ' reason="' . $v_reason . '"';
                    $xml_next_task .= ' />';

                    if (!$v_next_task_code)
                    {
                        continue;
                    }

                    //Ghi log HS da bi tu choi
                    $stmt   = 'Update t_r3_record Set C_REJECTED=1,C_REJECT_REASON=? Where PK_RECORD=?';
                    $params = array($v_reason, $v_record_id);
                    $this->db->Execute($stmt, $params);

                    //Step log
                    //Lay ca ten GROUP
                    $stmt         = 'Select C_NAME From t_cores_group Where C_CODE=(Select Top 1 C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE=?)';
                    $params       = array($v_current_task);
                    $v_group_name = $this->db->getOne($stmt, $params);

                    $v_step_seq = uniqid();
                    $v_xml_step_log = '<step seq="' . $v_step_seq . '" code="REJECT">';
                    $v_xml_step_log .= '<user_code>' . Session::get('user_code') . '</user_code>';
                    $v_xml_step_log .= '<user_name>' . Session::get('user_name') . '</user_name>';
                    $v_xml_step_log .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                    $v_xml_step_log .= '<datetime>' . $this->getDate() . '</datetime>';
                    $v_xml_step_log .= '<reason>' . $v_reason . '</reason>';
                    $v_xml_step_log .= '<group_name>' . $v_group_name . '</group_name>';
                    $v_xml_step_log .= '</step>';
                    $this->_insert_record_processing_step($v_record_id, $v_xml_step_log);
                    $this->_update_next_task_info($v_record_id, $xml_next_task);
                }
            }//end if $v_scope
        }//end for

        $this->popup_exec_done();
    }

    public function do_approval_record()
    {
        //Ma loai HS
        $v_record_type_code = get_post_var('hdn_record_type_code');
        ($v_record_type_code != '') OR DIE();

        //Ket qua thu ly
        $v_approval_value = get_post_var('rad_approval');
        ($v_approval_value != '') OR DIE();

        //Danh sach ma HS
        $v_record_id_list = get_post_var('hdn_item_id_list');
        ($v_record_id_list != '') OR DIE();

        //Ly do chua duyet HS
        $v_reason = get_post_var('txt_reason');

        //Phí, lệ phí
        $v_fee             = get_post_var('txt_fee');
        $v_fee_description = get_post_var('txt_fee_description');

        $arr_record_id = explode(',', $v_record_id_list);

        foreach ($arr_record_id as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }
            //hồ sơ ở trạng thái chờ xét duyệt
            $arr_single_record = $this->db->GetRow("Select C_NEXT_TASK_CODE 
                From t_r3_record 
                Where PK_RECORD=?", array($v_record_id));
            if (strpos($arr_single_record['C_NEXT_TASK_CODE'], _CONST_XET_DUYET_ROLE) === false)
            {
                continue;
            }
            $this->db->Execute('Update t_r3_record Set C_ROLLBACKABLE=1 Where PK_RECORD=?', array($v_record_id));
            //$v_current_task ?????
            $stmt           = 'Select C_NEXT_TASK_CODE From view_processing_record Where PK_RECORD=?';
            $v_current_task = $this->db->getOne($stmt, array($v_record_id));

            //Xu ly ket qua xet duyet
            if ($v_approval_value == _CONST_RECORD_APPROVAL_ACCEPT)
            {
                //Phe duyet, chuyen den buoc tiep theo
                if (!isset($_POST['rad_signer']))
                {
                    //Khong phai la ky duyet
                    //Next task
                    $arr_next_task_info = $this->_qry_next_task_info($v_current_task);

                    //Neu la Xet duyet bo sung
                    if (get_post_var('hdn_is_approve_supplement_record', 0) == 1)
                    {
                        //Xac dinh YEU_CAU_BO_SUNG sinh ra boi TASK nao
                        $v_create_from_task = $this->db->getOne("Select C_CREATE_FROM_TASK From t_r3_record_supplement Where FK_RECORD=$v_record_id Order By PK_RECORD_SUPPLEMENT Desc");
                        If ($v_create_from_task != NULL)
                        {
                            $arr_next_task_info = $this->_qry_next_task_info($v_create_from_task);
                        }
                    }

                    $v_next_task_code      = $arr_next_task_info['C_NEXT_TASK_CODE'];
                    $v_next_task_user_code = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
                    $v_next_task_user_name = $arr_next_task_info['C_NEXT_USER_NAME'];
                    $v_next_user_job_title = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];
                }
                else
                {
                    $arr_next_task_info    = $this->_qry_next_task_info($v_current_task);
                    $v_next_task_code      = $arr_next_task_info['C_NEXT_TASK_CODE'];
                    $v_next_task_user_code = replace_bad_char($_POST['rad_signer']);
                    $arr_single_user_info  = $this->db->getRow('Select C_NAME, C_JOB_TITLE From t_cores_user Where C_LOGIN_NAME=?', array($v_next_task_user_code));
                    $v_next_task_user_name = $arr_single_user_info['C_NAME'];
                    $v_next_user_job_title = $arr_single_user_info['C_JOB_TITLE'];
                }

                $xml_next_task = '<next_task ';
                $xml_next_task .= ' code="' . $v_next_task_code . '"';
                $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
                $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
                $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
                $xml_next_task .= ' fee="' . $v_fee . '"';
                $xml_next_task .= ' fee_description="' . $v_fee_description . '"';
                $xml_next_task .= ' />';
                if (!$v_next_task_code)
                {
                    continue;
                }

                $v_next_role = get_role($v_next_task_code);
                if ($v_next_role == _CONST_THU_PHI_ROLE OR $v_next_role == _CONST_TRA_KET_QUA_ROLE)
                {
                    //Hoan thanh nghiep vu nhanh/cham bao nhieu ngay ?
                    $v_return_days_remain = $this->_return_days_remain_calc($v_record_id);

                    $stmt   = 'Update t_r3_record Set C_BIZ_DAYS_EXCEED=? Where PK_RECORD=?';
                    $params = array($v_return_days_remain, $v_record_id);
                    $this->db->Execute($stmt, $params);
                }

                //Step log
                $v_step_seq = uniqid();
                $v_deadline = $this->db->GetOne("Select C_DOING_STEP_DEADLINE_DATE
                    From t_r3_record Where PK_RECORD=?", array($v_record_id));
                $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
                $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
                $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
                $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                $step .= '<datetime>' . $this->getDate() . '</datetime>';
                $step .= '<fee>' . $v_fee . '</fee>';
                $step .= '<fee_description>' . $v_fee_description . '</fee_description>';
                $step .= '<to_group_code>' . $arr_next_task_info['C_NEXT_GROUP_CODE'] . '</to_group_code>';
                $step .= "<deadline>$v_deadline</deadline>";
                $step .= '</step>';
                $this->_insert_record_processing_step($v_record_id, $step);
                $this->_update_next_task_info($v_record_id, $xml_next_task);
            }
            elseif ($v_approval_value == _CONST_RECORD_APPROVAL_REJECT)
            {
                //Từ chối, về Một-Cửa để trả công dân
                //1. Tim ra nguoi tiep nhan
                $v_xml_processing = $this->db->getOne("Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=$v_record_id");
                $dom_processing   = simplexml_load_string($v_xml_processing);
                $v_code           = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;
                $next_user_info   = xpath($dom_processing, "//step[@code='$v_code'][last()]", XPATH_DOM);

                $xml_next_task = '<next_task ';
                $xml_next_task .= ' code="' . $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . '"';
                $xml_next_task .= ' user="' . $next_user_info->user_code . '"';
                $xml_next_task .= ' user_name="' . $next_user_info->user_name . '"';
                $xml_next_task .= ' user_job_title="' . $next_user_info->user_job_title . '"';
                $xml_next_task .= ' promote="' . $v_approval_value . '"';
                $xml_next_task .= ' reason="' . $v_reason . '"';
                $xml_next_task .= ' />';
                //Ghi log HS da bi tu choi
                $stmt          = 'Update t_r3_record Set C_REJECTED=1,C_REJECT_REASON=? Where PK_RECORD=?';
                $params        = array($v_reason, $v_record_id);
                $this->db->Execute($stmt, $params);

                //Step log
                //Lay ca ten GROUP
                $stmt         = 'Select C_NAME From t_cores_group Where C_CODE In (Select C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE=?)';
                $params       = array($v_current_task);
                $v_group_name = $this->db->getOne($stmt, $params);

                $v_step_seq = uniqid();
                $step       = '<step seq="' . $v_step_seq . '" code="REJECT">';
                $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
                $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
                $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                $step .= '<datetime>' . $this->getDate() . '</datetime>';
                $step .= '<reason>' . $v_reason . '</reason>';
                $step .= '<group_name>' . $v_group_name . '</group_name>';
                $step .= '</step>';
                $this->_insert_record_processing_step($v_record_id, $step);
                $this->_update_next_task_info($v_record_id, $xml_next_task);
            }
            elseif ($v_approval_value == _CONST_RECORD_APPROVAL_REEXEC)
            {
                //Rollback
                //get last step info
                $v_xml_processing = $this->db->getOne('Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=?', array($v_record_id));
                $d                = simplexml_load_string($v_xml_processing);

                $v_exec_task_code = _CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE;
                $xquery           = "//step[contains(@code,'$v_exec_task_code')][last()]";
                $r                = xpath($d, $xquery);
                if (sizeof($r) == 1)
                {
                    $step = $r[0];
                }
                else
                {
                    $v_exec_task_code = _CONST_XML_RTT_DELIM . _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE;
                    $xquery           = "//step[contains(@code,'$v_exec_task_code')][last()]";
                    $r                = xpath($d, $xquery);
                    $step             = $r[0];
                }

                //echo 'Line:'.__LINE__.'<br>File:'.__FILE__;var_dump::display($step);exit;

                $v_last_task_code       = $step->attributes()->code;
                $v_next_user_login_name = $step->user_code;
                $v_next_user_name       = $step->user_name;
                $v_next_user_job_title  = $step->user_job_title;

                //Re-Next
                //Mã chính xác của công việc: YEU_CAU_THU_LY_LAI
                $stmt               = 'Select C_TASK_CODE From t_r3_user_task where C_RECORD_TYPE_CODE=? And C_TASK_CODE = ?';
                $v_execed_task_code = is_sub_string($v_last_task_code, _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE) ? _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE : _CONST_THU_LY_ROLE;
                $v_execed_task_code = _CONST_XML_RTT_DELIM . $v_execed_task_code;
                $v_reexec_task_code = str_replace($v_execed_task_code, _CONST_XML_RTT_DELIM . _CONST_YEU_CAU_THU_LY_LAI_ROLE, $v_last_task_code);
                $params             = array($v_record_type_code, $v_reexec_task_code);
                $v_next_task_code   = $this->db->getOne($stmt, $params);

                if (!$v_next_task_code)
                {
                    $this->popup_exec_fail(__FUNCTION__ . '-' . __LINE__ . ': Quy trình xử lý hiện tại không thực hiện được chức năng này!');
                }

                $xml_next_task = '<next_task ';
                $xml_next_task .= ' code="' . $v_next_task_code . '"';
                $xml_next_task .= ' user="' . $v_next_user_login_name . '"';
                $xml_next_task .= ' user_name="' . $v_next_user_name . '"';
                $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
                $xml_next_task .= ' reason="' . $v_reason . '"';
                $xml_next_task .= ' />';
                $this->_update_next_task_info($v_record_id, $xml_next_task);

                //Step log
                $v_step_seq = uniqid();
                $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
                $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
                $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
                $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                $step .= '<datetime>' . $this->getDate() . '</datetime>';
                $step .= '<reason>' . $v_reason . '</reason>';
                $step .= '</step>';
                $this->_insert_record_processing_step($v_record_id, $step);
            }
            elseif ($v_approval_value == _CONST_RECORD_APPROVAL_SUPPLEMENT)
            {
                //Yêu cầu bổ sung, về Một-Cửa để yêu cầu bổ sung
                //1. Tim lai nguoi tiep nhan
                //NEXT_TASK = 'BO SUNG'
                //1. Tim ra nguoi tiep nhan
                $v_xml_processing = $this->db->getOne("Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=$v_record_id");
                $dom_processing   = simplexml_load_string($v_xml_processing);
                $v_code           = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;

                $next_user_info = xpath($dom_processing, "//step[1]", XPATH_DOM);

                $xml_next_task = '<next_task ';
                $xml_next_task .= ' code="' . $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE . '"';
                $xml_next_task .= ' user="' . $next_user_info->user_code . '"';
                $xml_next_task .= ' user_name="' . $next_user_info->user_name . '"';
                $xml_next_task .= ' user_job_title="' . $next_user_info->user_job_title . '"';
                $xml_next_task .= ' promote="' . $v_approval_value . '"';
                $xml_next_task .= ' reason="' . $v_reason . '"';
                $xml_next_task .= ' />';

                //Chuyển hồ sơ vào danh sách bổ sung
                $v_goto = get_post_var('rad_after_supplement', '1');
                $stmt   = 'Insert Into t_r3_record_supplement(FK_RECORD, C_GOTO,C_CREATE_FROM_TASK) Values(?,?,?)';
                $this->db->Execute($stmt, array($v_record_id, $v_goto, $v_current_task));

                //Step log
                $v_step_seq = uniqid();
                $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
                $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
                $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
                $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                $step .= '<datetime>' . $this->getDate() . '</datetime>';
                $step .= '<promote>' . $v_approval_value . '</promote>';
                $step .= '<reason>' . $v_reason . '</reason>';
                $step .= '</step>';
                $this->_insert_record_processing_step($v_record_id, $step);
                $this->_update_next_task_info($v_record_id, $xml_next_task);
            }
        }

        $this->popup_exec_done();
    }

    public function do_reject_record()
    {
        //Ma loai HS
        $v_record_type_code = isset($_POST['hdn_record_type_code']) ? replace_bad_char($_POST['hdn_record_type_code']) : '';

        ($v_record_type_code != '') OR DIE();

        //Danh sach ma HS
        $v_record_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';

        //Ly do chua duyet HS
        $v_reason = isset($_POST['txt_reason']) ? replace_bad_char($_POST['txt_reason']) : '';

        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_ROLE;
        $arr_record_id  = explode(',', $v_record_id_list);

        $v_approval_value = _CONST_RECORD_APPROVAL_REJECT;

        foreach ($arr_record_id as $v_record_id)
        {
            //Từ chối, về Một-Cửa để trả công dân
            //1. Tim ra nguoi tiep nhan
            $v_xml_processing = $this->db->getOne("Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=$v_record_id");
            $dom_processing   = simplexml_load_string($v_xml_processing);
            $v_code           = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;
            $next_user_info   = xpath($dom_processing, "//step[@code='$v_code'][last()]", XPATH_DOM);

            $xml_next_task = '<next_task ';
            $xml_next_task .= ' code="' . $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . '"';
            $xml_next_task .= ' user="' . $next_user_info->user_code . '"';
            $xml_next_task .= ' user_name="' . $next_user_info->user_name . '"';
            $xml_next_task .= ' user_job_title="' . $next_user_info->user_job_title . '"';
            $xml_next_task .= ' promote="' . $v_approval_value . '"';
            $xml_next_task .= ' reason="' . $v_reason . '"';
            $xml_next_task .= ' />';

            //Ghi log HS da bi tu choi
            $stmt   = 'Update t_r3_record Set C_REJECTED=1,C_REJECT_REASON=?,C_REJECT_DATE=' . $this->build_getdate_function() . ' Where PK_RECORD=?';
            $params = array($v_reason, $v_record_id);
            $this->db->Execute($stmt, $params);

            //Step log
            //Lay ca ten GROUP
            if ($this->is_mssql())
            {
                $stmt = 'Select C_NAME From t_cores_group Where C_CODE=(Select Top 1 C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE=?)';
            }
            elseif ($this->is_mysql())
            {
                $stmt = 'Select C_NAME From t_cores_group Where C_CODE=(Select C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE=? Limit 1)';
            }

            $params       = array($v_current_task);
            $v_group_name = $this->db->getOne($stmt, $params);

            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="REJECT">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '<reason>' . $v_reason . '</reason>';
            $step .= '<group_name>' . $v_group_name . '</group_name>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task);
        }

        $this->popup_exec_done();
    }

    /**
     * Quay về bước xử lý trước
     */
    public function do_rollback_record()
    {
        //Ma loai HS
        $v_record_type_code = isset($_POST['hdn_record_type_code']) ? replace_bad_char($_POST['hdn_record_type_code']) : '';
        $v_reason           = isset($_POST['txt_reason']) ? replace_bad_char($_POST['txt_reason']) : '';
        $errors             = '';

        ($v_record_type_code != '') OR DIE();
        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_CHUYEN_LAI_BUOC_TRUOC_ROLE;

        //Danh sach ma HS
        $v_record_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';
        $arr_record_id    = explode(',', $v_record_id_list);
        foreach ($arr_record_id as $v_record_id)
        {
            //get last step info
            $arr_single_record = $this->db->getRow('Select C_XML_PROCESSING
                    , C_RECORD_NO
                    , C_NEXT_TASK_CODE, C_ROLLBACKABLE
                From t_r3_record 
                Where PK_RECORD=?', array($v_record_id));
            $v_inhand          = ($this->_check_inhand_record($v_record_id) || Session::get('is_admin')) ? true : false;
            $v_rollbackable    = $arr_single_record['C_ROLLBACKABLE'];
            if (!$v_inhand)
            {
                $errors .= 'Tài khoản hiện tại: ' . Session::get('user_code') . " không có quyền thực hiện chức năng này!";
                continue;
            }
            elseif (!$v_rollbackable)
            {
                $errors .= 'Trả hồ sơ ' . $arr_single_record['C_RECORD_NO'] . " thất bại. Lý do: vừa tiếp nhận hoặc vừa bị trả lại";
                continue;
            }
            //Rollback

            $v_xml_processing   = $arr_single_record['C_XML_PROCESSING'];
            $v_record_type_code = substr($arr_single_record['C_RECORD_NO'], 0, strpos($arr_single_record['C_RECORD_NO'], '-'));
            $dom_workflow       = simplexml_load_file(SERVER_ROOT . 'apps/r3/xml-config/'
                    . $v_record_type_code . '/' . $v_record_type_code . '_workflow.xml');
            if (!$dom_workflow)
            {
                continue;
            }
            /* @var $step SimpleXMLElement */
            $last_step = new SimpleXMLElement('<data/>');
            foreach ($dom_workflow->xpath('//step[not(@no_chain="true")]') as $step)
            {
                /* @var $tasks \SimpleXMLElement[] */
                $tasks = $step->children();
                foreach ($tasks as $task)
                {
                    echo '<hr/>';
                    if (strval($task->attributes()->code) == $arr_single_record['C_NEXT_TASK_CODE'])
                    {
                        break 2;
                    }
                }
                $last_step = $step;
            }
            $last_step_tasks = $last_step->children();
            $last_step_task  = $last_step_tasks[count($last_step_tasks) - 1];
            $d               = simplexml_load_string($v_xml_processing);
            if (!$last_step_task)
            {
                continue;
            }
            $last_process = xpath($d, "(//step[@code='{$last_step_task->attributes()->code}'])[last()]", XPATH_DOM);

            $xml_next_task = '<next_task ';
            $xml_next_task .= ' code="' . $last_process->attributes()->code . '"';
            $xml_next_task .= ' user="' . $last_process->user_code . '"';
            $xml_next_task .= ' user_name="' . $last_process->user_name . '"';
            $xml_next_task .= ' user_job_title="' . $last_process->user_job_title . '"';
            $xml_next_task .= ' reason="' . $v_reason . '"';
            $xml_next_task .= ' />';
            if (!strval($last_process->attributes()->code))
            {
                continue;
            }

            //Step log
            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '<reason>' . $v_reason . '</reason>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task);

            $this->db->Execute("Update t_r3_record Set
                    C_NEXT_TASK_CODE=?, C_NEXT_USER_CODE=?, C_NEXT_CO_USER_CODE=?
                    , C_DOING_STEP_BEGIN_DATE=?, C_DOING_STEP_DEADLINE_DATE=?, C_ROLLBACKABLE=0
                Where PK_RECORD=?"
                    , array($last_process->attributes()->code, $last_process->user_code, null
                , $this->getDate(), $last_process->deadline, $v_record_id));
        }
        if ($errors)
        {
            $this->popup_exec_fail($errors);
        }
        else
        {
            $this->popup_exec_done();
        }
    }

    function do_sign_record()
    {

        $v_item_id_list     = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';
        $v_record_type_code = isset($_POST['sel_record_type']) ? replace_bad_char($_POST['sel_record_type']) : '';

        $arr_record_id = explode(',', $v_item_id_list);

        $v_item_id_list != '' OR DIE();

        $v_approval_value = isset($_POST['rad_approval']) ? replace_bad_char($_POST['rad_approval']) : '';
        ($v_approval_value != '') OR DIE();

        //Ly do chua duyet HS
        $v_reason = isset($_POST['txt_reason']) ? replace_bad_char($_POST['txt_reason']) : '';

        foreach ($arr_record_id as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }
            $v_current_task = $this->db->getOne('Select C_NEXT_TASK_CODE From view_processing_record Where PK_RECORD=?', array($v_record_id));
            if (strpos($v_current_task, _CONST_KY_ROLE) === false)
            {
                continue;
            }
            //Xu ly ket qua xet duyet
            if ($v_approval_value == _CONST_RECORD_APPROVAL_ACCEPT)
            {
                //1. Thông tin về công việc tiếp theo
                //1.1 Mã công việc tiếp theo
                $v_next_task_code = $this->get_next_task_code($v_current_task);

                //Neu cong viec tiep theo la ban giao (Giao Moc)
                //Va co nhieu nguoi ban giao, Thi nguoi thuc hien la CB da thu ly HS nay
                $arr_all_next_user = $this->qry_all_user_on_task($v_next_task_code);
                if (get_role($v_next_task_code) == _CONST_BAN_GIAO_ROLE && (sizeof($arr_all_next_user) > 1))
                {
                    $dom_processing = simplexml_load_string($this->_get_xml_processing($v_record_id));

                    //Tim ra CB da thu ly HS nay
                    //1. Chinh xac ma cong viec
                    $v_xpath = "//step[contains(@code,'" . $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE . "')";

                    $v_xpath .= " and (";
                    for ($jj = 0; $jj < sizeof($arr_all_next_user); $jj++)
                    {
                        $v_next_user_code = $arr_all_next_user[$jj]['C_USER_LOGIN_NAME'];
                        $v_xpath .= ($jj == 0) ? " contains(user_code, '$v_next_user_code')" : " or contains(user_code, '$v_next_user_code')";
                    }
                    $v_xpath .= ")";
                    $v_xpath .= "][last()]/@code";
                    $v_exec_task_code = get_xml_value($dom_processing, $v_xpath);

                    $v_xpath               = "//step[@code='$v_exec_task_code'][last()]/user_code";
                    $v_next_task_user_code = get_xml_value($dom_processing, $v_xpath);

                    $v_xpath               = "//step[@code='$v_exec_task_code'][last()]/user_name";
                    $v_next_task_user_name = get_xml_value($dom_processing, $v_xpath);

                    $v_xpath               = "//step[@code='$v_exec_task_code'][last()]/user_job_title";
                    $v_next_user_job_title = get_xml_value($dom_processing, $v_xpath);

                    $v_group_name = $arr_all_next_user[0]['C_GROUP_NAME'];
                    $v_step_time  = $arr_all_next_user[0]['C_STEP_TIME'];
                    $v_group_code = $arr_all_next_user[0]['C_GROUP_CODE'];
                }
                else
                {
                    //1.2 Danh sách người thực hiện công việc tiếp theo
                    $arr_all_next_user = $this->qry_all_user_on_task($v_next_task_code);
                    if (sizeof($arr_all_next_user) > 1)
                    {
                        $v_next_task_user_code = '';
                        foreach ($arr_all_next_user as $next_user)
                        {
                            $v_next_task_user_code .= ($v_next_task_user_code != '') ? ',' . $next_user['C_USER_LOGIN_NAME'] : $next_user['C_USER_LOGIN_NAME'];
                        }
                        $v_next_task_user_code = ',' . $v_next_task_user_code . ',';

                        $v_next_task_user_name = '';
                        $v_next_user_job_title = '';
                        $v_step_time           = $arr_all_next_user[0]['C_STEP_TIME'];
                        $v_group_code          = $arr_all_next_user[0]['C_GROUP_CODE'];
                    }
                    else
                    {
                        $v_next_task_user_code = $arr_all_next_user[0]['C_USER_LOGIN_NAME'];
                        $v_next_task_user_name = $arr_all_next_user[0]['C_NAME'];
                        $v_next_user_job_title = $arr_all_next_user[0]['C_JOB_TITLE'];
                        $v_group_name          = $arr_all_next_user[0]['C_GROUP_NAME'];
                        $v_step_time           = $arr_all_next_user[0]['C_STEP_TIME'];
                        $v_group_code          = $arr_all_next_user[0]['C_GROUP_CODE'];
                    }
                }

                //Next task
                $xml_next_task = '<next_task ';
                $xml_next_task .= ' code="' . $v_next_task_code . '"';
                $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
                $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
                $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
                $xml_next_task .= ' group_name="' . $v_group_name . '"';
                $xml_next_task .= ' group_code="' . $v_group_code . '"';
                $xml_next_task .= ' step_time="' . $v_step_time . '"';
                $xml_next_task .= ' />';
                if (!$v_next_task_code)
                {
                    continue;
                }

                //Log
                $v_step_seq = uniqid();
                $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
                $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
                $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
                $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                $step .= '<datetime>' . $this->getDate() . '</datetime>';
                $step .= '</step>';
                $this->_insert_record_processing_step($v_record_id, $step);
                $this->_update_next_task_info($v_record_id, $xml_next_task);

                //Phi, le phi
                $v_xml_processing  = $this->db->getOne('Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=?', array($v_record_id));
                $dom               = simplexml_load_string($v_xml_processing);
                //Phi
                $r                 = xpath($dom, "//next_task/@fee", XPATH_STRING);
                $v_fee             = (int) $r;
                //Dien giai
                $v_fee_description = xpath($dom, "//next_task/@fee_description", XPATH_STRING);

                $v_next_role = get_role($v_next_task_code);
                if ($v_next_role == _CONST_THU_PHI_ROLE OR $v_next_role == _CONST_TRA_KET_QUA_ROLE)
                {
                    //Hoan thanh nghiep vu nhanh/cham bao nhieu ngay ?
                    $v_return_days_remain = $this->_return_days_remain_calc($v_record_id);

                    $stmt   = 'Update t_r3_record Set C_BIZ_DAYS_EXCEED=?,C_DOING_STEP_DEADLINE_DATE=NULL Where PK_RECORD=?';
                    $params = array($v_return_days_remain, $v_record_id);
                    $this->db->Execute($stmt, $params);
                }
            }
            elseif ($v_approval_value == _CONST_RECORD_APPROVAL_REJECT)
            {
                //Từ chối, về Một-Cửa để trả công dân
                //1. Tim ra nguoi tiep nhan
                $v_xml_processing = $this->db->getOne("Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=$v_record_id");
                $dom_processing   = simplexml_load_string($v_xml_processing);
                $v_code           = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;

                $next_user_info = xpath($dom_processing, "//step[@code='$v_code'][last()]", XPATH_DOM);

                $xml_next_task = '<next_task ';
                $xml_next_task .= ' code="' . $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . '"';
                $xml_next_task .= ' user="' . $next_user_info->user_code . '"';
                $xml_next_task .= ' user_name="' . $next_user_info->user_name . '"';
                $xml_next_task .= ' user_job_title="' . $next_user_info->user_job_title . '"';
                $xml_next_task .= ' promote="' . $v_approval_value . '"';
                $xml_next_task .= ' reason="' . $v_reason . '"';
                $xml_next_task .= ' />';

                //Ghi log HS da bi tu choi
                $stmt   = 'Update t_r3_record Set C_REJECTED=1,C_REJECT_REASON=? Where PK_RECORD=?';
                $params = array($v_reason, $v_record_id);
                $this->db->Execute($stmt, $params);

                //Step log
                //Lay ca ten GROUP
                $v_group_name = $this->get_group_name_by_task_code($v_current_task);

                $v_step_seq = uniqid();
                $step       = '<step seq="' . $v_step_seq . '" code="REJECT">';
                $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
                $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
                $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                $step .= '<datetime>' . $this->getDate() . '</datetime>';
                $step .= '<reason>' . $v_reason . '</reason>';
                $step .= '<group_name>' . $v_group_name . '</group_name>';
                $step .= '</step>';
                $this->_insert_record_processing_step($v_record_id, $step);
                $this->_update_next_task_info($v_record_id, $xml_next_task);
            }
            elseif ($v_approval_value == _CONST_RECORD_APPROVAL_REEXEC)
            {
                //Yeu cau phong ban trinh lai
                //1. Tim phong ban da trinh HS
                $this->do_rollback_record();
            }
        }//end foreach
        $this->popup_exec_done();
    }

    /**
     * Tra ket qua
     */
    public function do_return_record()
    {
        $v_item_id_list      = get_post_var('hdn_item_id_list');
        $v_record_type_code  = get_post_var('sel_record_type');
        $v_xml_record_result = get_post_var('XmlData', '<root/>', 0);

        $arr_record_id  = explode(',', $v_item_id_list);
        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE;

        $v_item_id_list != '' OR DIE();

        foreach ($arr_record_id as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }
            //Tránh trường hợp bấm nhiều lần
            if ($this->db->GetOne("Select C_CLEAR_DATE From t_r3_record Where PK_RECORD=?", array($v_record_id)))
            {
                continue;
            }
            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);

            //Next task
            $xml_next_task = '<next_task />';
            $this->_update_next_task_info($v_record_id, $xml_next_task, false, true);

            //And DONE
            //Cap nhat XML WorkFlow vao database, đảm bảo các tiến độ xử lý phù hợp với quy định hiện hành.
            $file_path = SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS
                    . $v_record_type_code . DS . $v_record_type_code . '_workflow.xml';

            if (!is_file($file_path))
            {
                $v_common_record_type_code = $record_type_code          = preg_replace('/([0-9]+[A-Z]*)/', '00', $v_record_type_code);
                $file_path                 = SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS
                        . 'common' . DS . $v_common_record_type_code . '_workflow.xml';
            }

            $v_xml_workflow_file_content = file_exists($file_path) ? file_get_contents($file_path) : '<process/>';
            $v_xml_workflow_file_content = preg_replace('/<!--(.*)-->/Uis', '', $v_xml_workflow_file_content);
            $v_xml_workflow_file_content = str_replace("\n", '', $v_xml_workflow_file_content);
            $v_xml_workflow_file_content = str_replace("\r", '', $v_xml_workflow_file_content);

            $stmt   = 'Update t_r3_record Set C_XML_WORKFLOW=?, C_NEXT_TASK_CODE=NULL, C_CLEAR_DATE=?, XML_RECORD_RESULT=? Where PK_RECORD=?';
            $params = array($v_xml_workflow_file_content, $this->get_datetime_now(), $v_xml_record_result, $v_record_id);
            $this->db->Execute($stmt, $params);
        }

        $v_pop_win = get_post_var('pop_win', 0);
        if ($v_pop_win == 0)
        {
            $this->exec_done($this->goback_url, array('sel_record_type' => $_POST['sel_record_type']));
        }
        else
        {
            $this->popup_exec_done();
        }
    }

    /* Thong bao bo sung cho cong dan */

    public function do_announce_record()
    {
        $v_item_id_list     = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';
        $v_record_type_code = isset($_POST['sel_record_type']) ? replace_bad_char($_POST['sel_record_type']) : '';

        $arr_record_id  = explode(',', $v_item_id_list);
        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . 'THONG_BAO_BO_SUNG';

        $v_item_id_list != '' OR DIE();

        foreach ($arr_record_id as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }

            //Insert Step
            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);

            //Cap nhat thong tin trong bang Supplement
            //Ly do phai bo sung
            $stmt   = 'Update t_r3_record_supplement
                    Set C_ANNOUNCE_DATE=' . $this->build_getdate_function() . ' Where FK_RECORD=?';
            $params = array($v_record_id);
            $this->db->Execute($stmt, $params);

            //Danh dau ho so tam dung
            $stmt   = 'Update t_r3_record Set C_PAUSE_DATE=' . $this->build_getdate_function() . ', C_UNPAUSE_DATE=Null Where PK_RECORD=?';
            $params = array($v_record_id);
            $this->db->Execute($stmt, $params);

            //chuẩn bị gửi thư
//            $arr_single_record  = $this->db->GetRow("Select * From t_r3_record Where PK_RECORD=?", array($v_record_id));
//            $v_record_type_name = $this->db->GetOne("Select C_NAME From t_r3_record_type Where C_CODE=?", array($v_record_type_code));
//            if ($arr_single_record['C_RETURN_EMAIL'])
//            {
//                require_once dirname(__FILE__) . '/classes/announce.inc.php';
//                $mail = new announce_suppliment($arr_single_record['C_RETURN_EMAIL'], $arr_single_record, $v_record_type_name);
//                $mail->send();
//            }
        }

        $this->exec_done($this->goback_url, array('sel_record_type' => $_POST['sel_record_type']));
    }

    /*
     * Tiep nhan giay to bo sung ho do
     */

    public function do_supplement_record()
    {
        $v_record_id           = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : 0;
        $v_return_phone_number = isset($_POST['txt_return_phone_number']) ? replace_bad_char($_POST['txt_return_phone_number']) : '';
        $v_return_email        = isset($_POST['txt_return_email']) ? replace_bad_char($_POST['txt_return_email']) : '';
        $v_xml_data            = isset($_POST['XmlData']) ? $_POST['XmlData'] : '<root/>';
        $v_record_type_code    = isset($_POST['hdn_record_type_code']) ? $_POST['hdn_record_type_code'] : '';

        if (!$this->_check_inhand_record($v_record_id, $v_record_type_code))
        {
            $this->popup_exec_done(1);
        }

        $stmt   = 'Update t_r3_record Set
                        C_RETURN_PHONE_NUMBER  = ?
                        ,C_XML_DATA            = ?
                        ,C_RETURN_EMAIL        = ?
                    Where PK_RECORD = ?';
        $params = array(
            $v_return_phone_number
            , $v_xml_data
            , $v_return_email
            , $v_record_id
        );
        $this->db->Execute($stmt, $params);

        //Insert Step
        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE;

        $v_step_seq = uniqid();
        $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
        $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
        $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
        $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
        $step .= '<datetime>' . $this->getDate() . '</datetime>';
        $step .= '</step>';
        $this->_insert_record_processing_step($v_record_id, $step);

        //Cap nhat ngay nhan bo sung
        $stmt   = 'Update t_r3_record_supplement
                Set C_RECEIVE_DATE=' . $this->build_getdate_function() . ' Where FK_RECORD=?';
        $params = array($v_record_id);
        $this->db->Execute($stmt, $params);

        //Danh dau ho ho tiep tuc xy ly
        $stmt   = 'Update t_r3_record Set C_UNPAUSE_DATE=' . $this->build_getdate_function() . ' Where PK_RECORD=?';
        $params = array($v_record_id);
        $this->db->Execute($stmt, $params);

        //Tinh lai ngay tra ket qua
        $sql               = "Select
                    C_RETURN_DATE
                    ,(Select
                           Count(*)
                       From t_cores_calendar
                       Where C_OFF = 0
                             And datediff(C_PAUSE_DATE, C_DATE) <= 0
                             And Datediff(C_UNPAUSE_DATE, C_DATE) > 0) As C_PAUSED_DAYS
                From view_processing_record
                Where PK_RECORD = $v_record_id";
        $r                 = $this->db->getRow($sql);
        $v_paused_days     = $r[C_PAUSED_DAYS];
        $v_old_return_date = $r[C_RETURN_DATE];

        //Tinh ra ngay tra ket qua moi
        $v_new_return_date = $this->next_working_day($v_paused_days, $v_old_return_date);
        $sql               = "Update t_r3_record 
                Set C_RETURN_DATE='$v_new_return_date' 
                Where PK_RECORD=$v_record_id";
        $this->db->Execute($sql);


        $this->popup_exec_done(1);
    }

    public function do_charging_record()
    {
        $v_record_id        = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : 0;
        $v_record_type_code = isset($_POST['hdn_record_type_code']) ? replace_bad_char($_POST['hdn_record_type_code']) : '';

        $v_fee  = isset($_POST['txt_fee']) ? replace_bad_char($_POST['txt_fee']) : '0';
        $v_cost = str_replace(',', '', get_post_var('txt_cost', 0));

        $v_fee_description = isset($_POST['txt_fee_description']) ? replace_bad_char($_POST['txt_fee_description']) : '';

        //Phi da tam thu
        $v_advance_cost = isset($_POST['hdn_advance_cost']) ? replace_bad_char($_POST['hdn_advance_cost']) : '0';

        //Insert Step
        $v_current_task = $this->db->getOne("Select C_NEXT_TASK_CODE 
            From view_processing_record 
            Where PK_RECORD=$v_record_id");
        if (strpos($v_current_task, _CONST_THU_PHI_ROLE) === false)
        {
            $this->popup_exec_done();
        }

        $v_step_seq = uniqid();

        if (!$this->_check_inhand_record($v_record_id))
        {
            $this->popup_exec_done();
        }

        //Next task
        $arr_next_task_info = $this->_qry_next_task_info($v_current_task);
        $v_next_task_code   = $arr_next_task_info['C_NEXT_TASK_CODE'];

        if (!isset($_POST['rad_next_user']))
        {
            $v_next_task_user_code      = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
            $v_next_task_user_name      = $arr_next_task_info['C_NEXT_USER_NAME'];
            $v_next_task_user_job_title = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];
        }
        else
        {
            $v_next_task_user_code = replace_bad_char($_POST['rad_next_user']);

            //next user info
            $arr_next_user_info         = $this->db->getRow("Select C_NAME, C_JOB_TITLE From t_cores_user Where C_LOGIN_NAME='$v_next_task_user_code'");
            $v_next_task_user_name      = $arr_next_user_info['C_NAME'];
            $v_next_task_user_job_title = $arr_next_user_info['C_JOB_TITLE'];
        }

        $xml_next_task = '<next_task';
        $xml_next_task .= ' code="' . $v_next_task_code . '"';
        $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
        $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
        $xml_next_task .= ' user_job_title="' . $v_next_task_user_job_title . '"';
        $xml_next_task .= ' />';
        if (!$v_next_task_code)
        {
            $this->popup_exec_done();
        }

        $step = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
        $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
        $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
        $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
        $step .= '<datetime>' . $this->getDate() . '</datetime>';
        $step .= '</step>';
        $this->_insert_record_processing_step($v_record_id, $step);
        $this->_update_next_task_info($v_record_id, $xml_next_task);

        //Ghi but toan thu phi
        $v_advance_cost = str_replace('.', '', $v_advance_cost);
        $v_advance_cost = str_replace(',', '', $v_advance_cost);
        $v_fee          = str_replace('.', '', $v_fee);
        $v_fee          = str_replace(',', '', $v_fee);
        $stmt           = 'Insert Into t_r3_record_fee(FK_RECORD, C_ADVANCE_COST, C_FINAL_FEE,C_FEE_DESCRIPTION, C_COST) Values(?,?,?,?,?)';
        $params         = array($v_record_id, $v_advance_cost, $v_fee, $v_fee_description, $v_cost);
        $this->db->Execute($stmt, $params);

        $this->popup_exec_done();
    }

    public function do_send_to_tax()
    {
        $v_item_id_list     = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';
        $v_record_type_code = isset($_POST['sel_record_type']) ? replace_bad_char($_POST['sel_record_type']) : '';

        $v_item_id_list != '' OR DIE();

        $arr_record = explode(',', $v_item_id_list);



        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_NOP_HO_SO_SANG_CHI_CUC_THUE_ROLE;

        //Ma chinh xac cua cong viec dang thuc hien
        $v_user_code    = Session::get('user_code');
        $a              = $this->db->getRow("Select C_TASK_CODE, C_NO_CHAIN From t_r3_user_task Where C_TASK_CODE Like '%$v_current_task' And C_USER_LOGIN_NAME='$v_user_code'");
        $v_current_task = $a['C_TASK_CODE'];
        $v_is_no_chain  = $a['C_NO_CHAIN']; //$v_current_task is a no_chain task ?

        foreach ($arr_record as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }

            //Next task (task chinh)
            $arr_next_task_info    = $this->_qry_next_task_info($v_current_task);
            $v_next_task_code      = $arr_next_task_info['C_NEXT_TASK_CODE'];
            $v_next_task_user_code = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
            $v_next_task_user_name = $arr_next_task_info['C_NEXT_USER_NAME'];
            $v_next_user_job_title = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];

            $xml_next_task = '<next_task ';
            $xml_next_task .= ' code="' . $v_next_task_code . '"';
            $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
            $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
            $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
            $xml_next_task .= ' />';
            if (!$v_next_task_code)
            {
                continue;
            }

            //Insert Step
            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task, $v_is_no_chain);
        }

        $this->exec_done($this->goback_url, array('sel_record_type' => $_POST['sel_record_type']));
    }

    /**
     * Nhan thong bao thue
     */
    public function do_receive_tax()
    {
        $v_item_id_list     = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';
        $v_record_type_code = isset($_POST['sel_record_type']) ? replace_bad_char($_POST['sel_record_type']) : '';

        $v_item_id_list != '' OR DIE();

        $arr_record = explode(',', $v_item_id_list);

        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_NHAN_THONG_BAO_CUA_CHI_CUC_THUE_ROLE;
        //Ma chinh xac cua cong viec dang thuc hien
        $v_user_code    = Session::get('user_code');
        $a              = $this->db->getRow("Select C_TASK_CODE, C_NO_CHAIN From t_r3_user_task Where C_TASK_CODE Like '%$v_current_task' And C_USER_LOGIN_NAME='$v_user_code'");
        $v_current_task = $a['C_TASK_CODE'];
        $v_is_no_chain  = $a['C_NO_CHAIN']; //$v_current_task is a no_chain task ?
        foreach ($arr_record as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }
            //Next task
            $arr_next_task_info    = $this->_qry_next_task_info($v_current_task);
            $v_next_task_code      = $arr_next_task_info['C_NEXT_TASK_CODE'];
            $v_next_task_user_code = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
            $v_next_task_user_name = $arr_next_task_info['C_NEXT_USER_NAME'];
            $v_next_user_job_title = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];

            $xml_next_task = '<next_task ';
            $xml_next_task .= ' code="' . $v_next_task_code . '"';
            $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
            $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
            $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
            $xml_next_task .= ' />';
            if (!$v_next_task_code)
            {
                continue;
            }

            //Insert Step
            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task, $v_is_no_chain);
        }

        $this->exec_done($this->goback_url, array('sel_record_type' => $_POST['sel_record_type']));
    }

    public function do_submit_tax()
    {
        $v_item_id_list     = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';
        $v_record_type_code = isset($_POST['sel_record_type']) ? replace_bad_char($_POST['sel_record_type']) : '';

        $v_item_id_list != '' OR DIE();

        $arr_record = explode(',', $v_item_id_list);

        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_CHUYEN_THONG_BAO_THUE_VE_BP_MOT_CUA_ROLE;
        foreach ($arr_record as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }
            //Next task
            $arr_next_task_info    = $this->_qry_next_task_info($v_current_task);
            $v_next_task_code      = $arr_next_task_info['C_NEXT_TASK_CODE'];
            $v_next_task_user_code = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
            $v_next_task_user_name = $arr_next_task_info['C_NEXT_USER_NAME'];
            $v_next_user_job_title = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];

            $xml_next_task = '<next_task ';
            $xml_next_task .= ' code="' . $v_next_task_code . '"';
            $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
            $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
            $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
            $xml_next_task .= ' />';
            if (!$v_next_task_code)
            {
                continue;
            }

            //Insert Step
            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task);
        }

        $this->exec_done($this->goback_url, array('sel_record_type' => $_POST['sel_record_type']));
    }

    public function do_return_tax_message()
    {
        $v_item_id_list     = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';
        $v_record_type_code = isset($_POST['sel_record_type']) ? replace_bad_char($_POST['sel_record_type']) : '';

        $v_item_id_list != '' OR DIE();

        $arr_record = explode(',', $v_item_id_list);

        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TRA_THONG_BAO_NOP_THUE_ROLE;
        //Ma chinh xac cua cong viec dang thuc hien
        $v_user_code    = Session::get('user_code');
        $a              = $this->db->getRow("Select C_TASK_CODE, C_NO_CHAIN From t_r3_user_task Where C_TASK_CODE Like '%$v_current_task' And C_USER_LOGIN_NAME='$v_user_code'");
        $v_current_task = $a['C_TASK_CODE'];
        $v_is_no_chain  = $a['C_NO_CHAIN']; //$v_current_task is a no_chain task ?
        foreach ($arr_record as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }
            //Next task
            $arr_next_task_info    = $this->_qry_next_task_info($v_current_task);
            $v_next_task_code      = $arr_next_task_info['C_NEXT_TASK_CODE'];
            $v_next_task_user_code = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
            $v_next_task_user_name = $arr_next_task_info['C_NEXT_USER_NAME'];
            $v_next_user_job_title = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];

            $xml_next_task = '<next_task ';
            $xml_next_task .= ' code="' . $v_next_task_code . '"';
            $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
            $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
            $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
            $xml_next_task .= ' />';
            if (!$v_next_task_code)
            {
                continue;
            }

            //Insert Step
            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task, $v_is_no_chain);
        }

        $this->exec_done($this->goback_url, array('sel_record_type' => $_POST['sel_record_type']));
    }

    /**
     * Nhận biên lai nộp thuế
     */
    public function do_receive_tax_receipt()
    {
        $v_item_id_list     = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';
        $v_record_type_code = isset($_POST['sel_record_type']) ? replace_bad_char($_POST['sel_record_type']) : '';

        $v_item_id_list != '' OR DIE();

        $arr_record = explode(',', $v_item_id_list);

        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_NHAN_BIEN_LAI_NOP_THUE_ROLE;
        //Ma chinh xac cua cong viec dang thuc hien
        $v_user_code    = Session::get('user_code');
        $a              = $this->db->getRow("Select C_TASK_CODE, C_NO_CHAIN From t_r3_user_task Where C_TASK_CODE Like '%$v_current_task' And C_USER_LOGIN_NAME='$v_user_code'");
        $v_current_task = $a['C_TASK_CODE'];
        $v_is_no_chain  = $a['C_NO_CHAIN']; //$v_current_task is a no_chain task ?
        foreach ($arr_record as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }

            //Next task
            $arr_next_task_info    = $this->_qry_next_task_info($v_current_task);
            $v_next_task_code      = $arr_next_task_info['C_NEXT_TASK_CODE'];
            $v_next_task_user_code = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
            $v_next_task_user_name = $arr_next_task_info['C_NEXT_USER_NAME'];
            $v_next_user_job_title = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];

            $xml_next_task = '<next_task ';
            $xml_next_task .= ' code="' . $v_next_task_code . '"';
            $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
            $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
            $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
            $xml_next_task .= ' />';
            if (!$v_next_task_code && !$v_is_no_chain)
            {
                continue;
            }

            //Insert Step
            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task, $v_is_no_chain);
        }
        $this->exec_done($this->goback_url, array('sel_record_type' => $_POST['sel_record_type']));
    }

    /**
     * Lấy danh sách hồ sơ mà NSD đã phân công
     */
    public function qry_all_alloted_record($rtc)
    {
        $v_task_code = $rtc . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE;
        $v_user_code = Session::get('user_code');

        //Xem theo trang
        page_calc($v_start, $v_end);

        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select R.*
                        ,U.C_NAME C_NEXT_USER_NAME
                        ,R.C_XML_PROCESSING.value('(//step[@code=''$v_task_code''][last()]/datetime)[1]', 'nvarchar(20)') as C_ALLOTED_DATE
                        ,' --' as C_DEADLINE_CAL
                        ,ROW_NUMBER() OVER (ORDER BY C_RECEIVE_DATE Desc) as RN
                    From [dbo].[view_processing_record] R Left Join t_cores_user U On R.C_NEXT_USER_CODE=U.C_LOGIN_NAME";
            $sql .= " Where R.C_LAST_TASK_CODE like '%$v_task_code' And R.C_LAST_USER_CODE='$v_user_code'";

            return $this->db->GetAll("Select a.* From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end Order By a.rn");
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            $condition_query = " R.C_LAST_TASK_CODE like '%$v_task_code' And R.C_LAST_USER_CODE='$v_user_code'";

            //Dem tong ban ghi
            $v_total_record = $this->db->getOne("Select Count(*) TOTAL_RECORD From view_processing_record R Where $condition_query");
            $sql            = "SELECT
                        @rownum:=@rownum + 1 AS RN
                        ,a.*
                        , Case When a.C_NEXT_USER_CODE='$v_user_code' Then 1 Else 0 End as C_OWNER
                        ,$v_total_record as TOTAL_RECORD
                        ,CASE
                            WHEN (DATEDIFF(NOW(), a.C_DOING_STEP_DEADLINE_DATE)>0) THEN
                                (SELECT -1 * (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())<=0
                                    AND DATEDIFF(WD.C_DATE, a.C_DOING_STEP_DEADLINE_DATE)>0 )
                            ELSE
                                (SELECT (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())>=0
                                    AND DATEDIFF(WD.C_DATE, a.C_DOING_STEP_DEADLINE_DATE)<0 )
                        END AS C_DOING_STEP_DAYS_REMAIN
                        ,CASE
                            WHEN (DATEDIFF(NOW(),a.C_RETURN_DATE)>0) THEN
                                (SELECT -1 * (COUNT(*))
                                FROM view_working_date WD
                                WHERE  DATEDIFF(WD.C_DATE, NOW())<=0
                                    AND DATEDIFF(WD.C_DATE, a.C_RETURN_DATE)>0 )
                            ELSE
                                (SELECT (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())>=0
                                    AND DATEDIFF(WD.C_DATE, a.C_RETURN_DATE)<0 )
                        END AS C_RETURN_DAYS_REMAIN
                    FROM (
                        SELECT
                            R.PK_RECORD
                            ,R.FK_RECORD_TYPE
                            ,R.C_RECORD_NO
                            , CAST(R.C_RECEIVE_DATE  AS CHAR(19)) AS C_RECEIVE_DATE
                            , CAST(R.C_RETURN_DATE  AS CHAR(19)) AS C_RETURN_DATE
                            ,R.C_RETURN_PHONE_NUMBER
                            ,R.C_XML_DATA
                            ,R.C_XML_PROCESSING
                            ,R.C_DELETED
                            ,R.C_CLEAR_DATE
                            ,R.C_XML_WORKFLOW
                            ,R.C_RETURN_EMAIL
                            ,R.C_REJECTED
                            ,R.C_REJECT_REASON
                            ,R.C_CITIZEN_NAME
                            ,R.C_ADVANCE_COST
                            ,R.C_CREATE_BY
                            ,R.C_NEXT_TASK_CODE
                            ,R.C_NEXT_USER_CODE
                            ,R.C_NEXT_CO_USER_CODE
                            ,R.C_LAST_TASK_CODE
                            ,R.C_LAST_USER_CODE
                            , CAST(R.C_DOING_STEP_BEGIN_DATE  AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
                            ,R.C_DOING_STEP_DEADLINE_DATE
                            ,R.C_BIZ_DAYS_EXCEED
                            ,UT.C_TASK_CODE
                            ,UT.C_STEP_TIME
                            ,(SELECT @rownum:=0)
                            ,R.C_PAUSE_DATE
                            ,R.C_UNPAUSE_DATE
                        FROM view_processing_record R LEFT JOIN t_r3_user_task UT ON (R.C_NEXT_TASK_CODE=UT.C_TASK_CODE AND R.C_NEXT_USER_CODE=UT.C_USER_LOGIN_NAME)
                        WHERE $condition_query
                        ORDER BY C_RECEIVE_DATE DESC
                        Limit $v_start, $v_limit
                    ) a";

            return $this->db->getAll($sql);
        }
        else
        {
            return NULL;
        }
    }

    public function do_add_comment()
    {
        //Check add_comment_token
        if (get_post_var('add_comment_token','tamviet!@#') !== Session::get('add_comment_token'))
        {
            require_once '403.php';
            exit;
        }
                
        $v_datetime_now = $this->get_datetime_now();
        if (DEBUG_MODE < 10)
        {
            $this->db->debug = 0;
        }

        $v_record_id = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : '0';
        $v_content   = isset($_POST['txt_content']) ? replace_bad_char($_POST['txt_content']) : '';
        $v_user_code = Session::get('user_code');

        $v_type = (Session::get('is_bod_member') == 1) ? 1 : 0;

        $stmt         = 'Insert Into t_r3_record_comment(FK_RECORD, C_USER_CODE, C_CONTENT, C_TYPE,  C_CREATE_DATE) Values(?,?,?,?,?)';
        $params       = array($v_record_id, $v_user_code, $v_content, $v_type, $v_datetime_now);
        $this->db->Execute($stmt, $params);
        $v_comment_id = $this->get_last_inserted_id('t_r3_record_comment', 'PK_COMMENT');

        //Tra ve danh sach y kien moi nhat
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = 'Select
                        RC.PK_RECORD_COMMENT as comment_id
                        ,RC.FK_RECORD
                        ,RC.C_USER_CODE         as user_code
                        ,RC.C_CONTENT           as content
                        ,Convert(varchar(10), RC.C_CREATE_DATE, 103) as [date]
                        ,U.C_NAME as [user_name]
                        ,U.C_JOB_TITLE as job_title
                        ,RC.C_TYPE [type]
                    From t_r3_record_comment RC left Join t_cores_user U On RC.C_USER_CODE=U.C_LOGIN_NAME
                    Where RC.FK_RECORD=? And PK_RECORD_COMMENT=?';
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = 'Select
                        RC.PK_RECORD_COMMENT as comment_id
                        ,RC.FK_RECORD
                        ,RC.C_USER_CODE         as user_code
                        ,RC.C_CONTENT           as content
                        ,' . $this->build_convert_date_query('RC.C_CREATE_DATE', 103) . 'as  `date`
                        ,U.C_NAME as `user_name`
                        ,U.C_JOB_TITLE as job_title
                        ,RC.C_TYPE `type`
                    From t_r3_record_comment RC left Join t_cores_user U On RC.C_USER_CODE=U.C_LOGIN_NAME
                    Where RC.FK_RECORD=? And PK_RECORD_COMMENT=?';
        }
        else
        {
            return NULL;
        }

        $params          = array($v_record_id, $v_comment_id);
        $ret             = $this->db->getAll($stmt, $params);
        $this->db->debug = DEBUG_MODE;
        
        //Done: Change the add_comment_token
        Session::set('add_comment_token', uniqid());
        return $ret;
    }

    public function do_delete_comment()
    {
        $v_comment_id = isset($_POST['comment_id']) ? replace_bad_char($_POST['comment_id']) : '0';
        $v_user_code  = isset($_POST['user_code']) ? replace_bad_char($_POST['user_code']) : '0';

        $stmt   = 'Delete From t_r3_record_comment Where PK_RECORD_COMMENT=? And C_USER_CODE=?';
        $params = array($v_comment_id, $v_user_code);

        $this->db->Execute($stmt, $params);
    }

    public function do_delete_doc()
    {
        $v_doc_id    = isset($_POST['doc_id']) ? replace_bad_char($_POST['doc_id']) : '0';
        $v_user_code = isset($_POST['user_code']) ? replace_bad_char($_POST['user_code']) : '0';

        //Xoa file
        $stmt   = 'Delete From t_r3_record_doc_file Where FK_DOC=? And FK_DOC In (Select PK_RECORD_DOC From t_r3_record_doc Where C_USER_CODE=?)';
        $params = array($v_doc_id, $v_user_code);
        $this->db->Execute($stmt, $params);

        $stmt   = 'Delete From t_r3_record_doc Where PK_RECORD_DOC=? And C_USER_CODE=?';
        $params = array($v_doc_id, $v_user_code);

        $this->db->Execute($stmt, $params);
    }

    public function do_add_doc()
    {
        //Check user token
        if ( ! check_user_token())
        {
            die('wrong token');
        }
        
        if (DEBUG_MODE < 10)
        {
            $this->db->debug = 0;
        }

        $v_user_code = Session::get('user_code');

        $v_record_id     = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : '0';
        $v_record_doc_no = isset($_POST['txt_record_doc_no']) ? replace_bad_char($_POST['txt_record_doc_no']) : '';
        $v_issuer        = isset($_POST['txt_issuer']) ? replace_bad_char($_POST['txt_issuer']) : '';
        $v_description   = isset($_POST['txt_description']) ? replace_bad_char($_POST['txt_description']) : '';
        $v_content       = isset($_POST['txt_content']) ? replace_bad_char($_POST['txt_content']) : '';

        $stmt            = 'INSERT INTO t_r3_record_doc
                        (FK_RECORD
                        ,C_USER_CODE
                        ,C_DOC_NO
                        ,C_ISSUER
                        ,C_DESCRIPTION
                        ,C_DOC_CONTENT
                        ,C_CREATE_DATE
                        )
                  VALUES
                        (?
                        ,?
                        ,?
                        ,?
                        ,?
                        ,?
                        ,?)';
        $params          = array($v_record_id, $v_user_code, $v_record_doc_no, $v_issuer, $v_description, $v_content, $this->get_datetime_now());
        $this->db->Execute($stmt, $params);
        $v_record_doc_id = $this->get_last_inserted_id('t_r3_record_doc');

        //Xem file dinh kem
        //Upload file
        $count = count($_FILES['uploader']['name']);
        for ($i = 0; $i < $count; $i++)
        {
            if ($_FILES['uploader']['error'][$i] == 0)
            {
                $v_file_name = $_FILES['uploader']['name'][$i];
                $v_tmp_name  = $_FILES['uploader']['tmp_name'][$i];

                $v_file_ext = array_pop(explode('.', $v_file_name));

                if (in_array($v_file_ext, explode('|', _CONST_RECORD_FILE_ACCEPT)))
                {
                    if (move_uploaded_file($v_tmp_name, SERVER_ROOT . "uploads" . DS . 'r3' . DS . $v_file_name))
                    {
                        $stmt   = 'Insert Into t_r3_record_doc_file(FK_DOC, C_FILE_NAME) Values(?,?)';
                        $params = array($v_record_doc_id, $v_file_name);
                        $this->db->Execute($stmt, $params);
                    }
                }
            }
        }

        //tra ve thong tin tai lieu moi them
        $stmt   = 'SELECT RD.PK_RECORD_DOC     as doc_id
                    ,RD.FK_RECORD            as record_id_code
                    ,RD.C_USER_CODE          as user_code
                    ,RD.C_DOC_NO             as doc_no
                    ,RD.C_ISSUER             as issuer
                    ,RD.C_DESCRIPTION        as description
                    ,RD.C_DOC_CONTENT        as doc_content
                    ,' . $this->build_convert_date_query('RD.C_CREATE_DATE', 103) . ' as create_date
                    ,U.C_NAME                  as user_name
                    ,' . $this->build_for_xml_raw_query('t_r3_record_doc_file', array('C_FILE_NAME'), ' Where FK_DOC=RD.PK_RECORD_DOC') . ' as xml_file_list
                FROM t_r3_record_doc RD Left Join t_cores_user U On RD.C_USER_CODE=U.C_LOGIN_NAME
                Where RD.PK_RECORD_DOC=?';
        $params = array($v_record_doc_id);
        $ret = $this->db->getAll($stmt, $params);
        
        $this->db->debug = DEBUG_MODE;
        return $ret;
    }

    /**
     * Lay danh sach tai lieu cua mot ho so
     * @param type $record_id
     */
    public function qry_all_record_doc($record_id)
    {

        $stmt   = 'SELECT RD.PK_RECORD_DOC
                    ,RD.FK_RECORD
                    ,RD.C_USER_CODE
                    ,RD.C_DOC_NO
                    ,RD.C_ISSUER
                    ,RD.C_DESCRIPTION
                    ,RD.C_DOC_CONTENT
                    ,' . $this->build_convert_date_query('RD.C_CREATE_DATE', 103) . ' as C_CREATE_DATE
                    ,U.C_NAME C_USER_NAME
                    ,' . $this->build_for_xml_raw_query('t_r3_record_doc_file', array('C_FILE_NAME'), ' Where FK_DOC=RD.PK_RECORD_DOC') . ' as C_RECORD_DOC_FILE_LIST
                FROM t_r3_record_doc RD Left Join t_cores_user U On RD.C_USER_CODE=U.C_LOGIN_NAME
                Where RD.FK_RECORD=?';
        $params = array($record_id);
        return $this->db->getAll($stmt, $params);
    }

    public function formal_record_step_days_to_date($record_id, $step_days_list)
    {
        $ret           = Array();
        $arr_step_time = explode(';', $step_days_list);

        $v_prev_end_date = NULL;
        $v_init_date     = $this->db->getOne("Select C_RECEIVE_DATE From view_record R Where PK_RECORD=$record_id");
        for ($i = 0; $i < sizeof($arr_step_time); $i++)
        {
            $v_step_time  = $arr_step_time[$i];
            $v_begin_date = ($i == 0) ? $v_init_date : $v_prev_end_date;
            $v_end_date   = $this->_step_deadline_calc($v_step_time, $v_begin_date);

            $ret[$i]['C_STEP_TIME']  = $v_step_time;
            $ret[$i]['C_BEGIN_DATE'] = $v_begin_date;
            $ret[$i]['C_END_DATE']   = $v_end_date;

            $v_prev_end_date = $v_end_date;
        }
        return $ret;
    }

    public function qry_all_record_for_lookup($p_record_type_code, $p_activity_filter)
    {
        $v_user_code = Session::get('user_code');
        //Xem theo trang
        page_calc($v_start, $v_end);

        $v_receive_date_from = isset($_POST['txt_receive_date_from']) ? jwDate::ddmmyyyy_to_yyyymmdd(replace_bad_char($_POST['txt_receive_date_from']), 0) : '';
        $v_receive_date_to   = isset($_POST['txt_receive_date_to']) ? jwDate::ddmmyyyy_to_yyyymmdd(replace_bad_char($_POST['txt_receive_date_to'])) : '';

        $v_return_date_from = isset($_POST['txt_return_date_from']) ? jwDate::ddmmyyyy_to_yyyymmdd(replace_bad_char($_POST['txt_return_date_from'])) : '';
        $v_return_date_to   = isset($_POST['txt_return_date_to']) ? jwDate::ddmmyyyy_to_yyyymmdd(replace_bad_char($_POST['txt_return_date_to'])) : '';

        $v_record_no = isset($_POST['txt_record_no']) ? replace_bad_char($_POST['txt_record_no']) : '';
        $v_free_text = isset($_POST['txt_free_text']) ? replace_bad_char($_POST['txt_free_text']) : '';

        $v_year = get_post_var('sel_year', 2013);
        if (get_post_var('hdn_search_mode') == 1)
        {
            $v_month     = get_post_var('sel_month');
            $start_month = 1;
            $end_month   = 12;
            if ($v_month)
            {
                $start_month = $end_month   = $v_month;
            }
            //tìm kiếm cơ bản
            $v_return_date_from  = '';
            $v_return_date_to    = '';
            $v_receive_date_from = "$v_year-$start_month-1";
            $v_receive_date_to   = "$v_year-$end_month-31";
        }

        //Danh sach ID loai thu tuc ma NSD da được phân công
        //Neu co quyen theo doi, giam sat toan bo HS
        if (check_permission('THEO_DOI_GIAM_SAT_TOAN_BO_HO_SO', $this->app_name))
        {
            $v_record_type_id_list   = '';
            $arr_record_type_id_list = array();
        }
        else
        {
            $sql = "Select Distinct PK_RECORD_TYPE
                    From t_r3_record_type RT
                        Right join ( Select Distinct C_RECORD_TYPE_CODE
                                     From t_r3_user_task
                                     Where C_USER_LOGIN_NAME = '$v_user_code'
                                   ) a
                        On RT.C_CODE = a.C_RECORD_TYPE_CODE
                    Where RT.PK_RECORD_TYPE Is Not Null";

            $arr_record_type_id_list = $this->db->getCol($sql);
            $v_record_type_id_list   = implode(',', $arr_record_type_id_list);
        }

        if ($this->is_mssql())
        {
            $stmt   = 'Exec [dbo].[sp_r3_record_lookup]
                        @p_user_code=?
                        ,@p_record_type_code = ?
                        ,@p_index_begin = ?
                        ,@p_index_end = ?
                        ,@p_activity_filter=?
                        ,@p_receive_date_from = ?
                        ,@p_receive_date_to = ?
                        ,@p_return_date_from = ?
                        ,@p_return_date_to = ?
                        ,@p_record_no=?';
            $params = array(
                Session::get('user_code')
                , $p_record_type_code
                , $v_start
                , $v_end
                , $p_activity_filter
                , $v_receive_date_from
                , $v_receive_date_to
                , $v_return_date_from
                , $v_return_date_to
                , $v_record_no
            );

            return $this->db->getAll($stmt, $params);
        }
        elseif ($this->is_mysql())
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            //Trang thai HS
            $v_from_and_where = $this->_build_from_and_where_query_by_record_activity($p_activity_filter);

            //Cac dieu kien loc
            $v_and_condition = '';


            if ($p_record_type_code != '' && $p_record_type_code != NULL)
            {
                $v_record_type_id = $this->db->GetOne("SELECT PK_RECORD_TYPE FROM t_r3_record_type WHERE C_CODE='$p_record_type_code'");

                $v_and_condition .= " And FK_RECORD_TYPE=$v_record_type_id";
            }
            if ($v_record_no != '' && $v_record_no != NULL)
            {
                $v_and_condition .= " And C_RECORD_NO like '%$v_record_no'";
            }
            if ($v_receive_date_from != '' && $v_receive_date_from != NULL)
            {
                $v_and_condition .= " And Datediff(C_RECEIVE_DATE,'$v_receive_date_from') >= 0";
            }
            if ($v_receive_date_to != '' && $v_receive_date_to != NULL)
            {
                $v_and_condition .= " And Datediff(C_RECEIVE_DATE,'$v_receive_date_to') <= 0";
            }
            if ($v_return_date_from != '' && $v_return_date_from != NULL)
            {
                $v_and_condition .= " And Datediff(C_RETURN_DATE,'$v_return_date_from') >=0";
            }
            if ($v_return_date_from != '' && $v_return_date_from != NULL)
            {
                $v_and_condition .= " And Datediff(C_RETURN_DATE,'$v_return_date_from') <=0";
            }

            if ($v_record_type_id_list != '')
            {
                $v_and_condition .= " And FK_RECORD_TYPE In ($v_record_type_id_list)";
            }
            /*
              if (sizeof($arr_record_type_id_list) > 0)
              {
              $v_and_condition .= ' And (';
              for ($i=0, $n=sizeof($arr_record_type_id_list); $i<$n; $i++)
              {
              if ($i>0)
              {
              $v_and_condition .= ' OR ';
              }
              $v_and_condition .= ' FK_RECORD_TYPE = ' . $arr_record_type_id_list[$i];
              }
              $v_and_condition .= ')';
              }
             */

            if ($v_free_text != '')
            {
                $v_and_condition .= ' And (';

                $v_and_condition .= " (ExtractValue(C_XML_DATA,'//item[./value[contains(.,\'$v_free_text\')]]/value') <> '')";

                //LienND update: 2013-05-29: Fix chu HOA, chu thuong
                $v_free_text_upper = mb_strtoupper($v_free_text, 'UTF-8');
                $v_and_condition .= " OR (ExtractValue(C_XML_DATA,'//item[./value[contains(.,\'$v_free_text_upper\')]]/value') <> '')";

                $v_free_text_lower = mb_strtolower($v_free_text, 'UTF-8');
                $v_and_condition .= " OR (ExtractValue(C_XML_DATA,'//item[./value[contains(.,\'$v_free_text_lower\')]]/value') <> '')";

                $v_and_condition .= ')';
            }


            //Dem tong so ban ghi tim thay theo trang thai va các điều kiện tra cúu
            $v_total_record = $this->db->getOne("Select Count(*) From $v_from_and_where $v_and_condition");

            $sql = "SELECT
                        @rownum:=@rownum + 1 AS RN
                        , CASE WHEN R.C_NEXT_USER_CODE='$v_user_code' THEN 1 ELSE 0 END AS C_OWNER
                        ,Case When (R.C_REJECTED = 1) Then 3 When (R.C_REJECTED <> 1 And (R.C_CLEAR_DATE Is Not Null)) Then 2 Else 1 End as C_ACTIVITY
                        , $v_total_record AS TOTAL_RECORD
                        , CASE WHEN (DATEDIFF(NOW(), R.C_DOING_STEP_DEADLINE_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)<0 ) END AS C_DOING_STEP_DAYS_REMAIN
                        , CASE WHEN (DATEDIFF(NOW(),R.C_RETURN_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)<0 ) END AS C_RETURN_DAYS_REMAIN
                        , R.PK_RECORD
                        , R.FK_RECORD_TYPE
                        , R.C_RECORD_NO
                        , CAST(R.C_RECEIVE_DATE AS CHAR(19)) AS C_RECEIVE_DATE
                        , CAST(R.C_RETURN_DATE AS CHAR(19)) AS C_RETURN_DATE
                        , R.C_RETURN_PHONE_NUMBER
                        , R.C_XML_DATA
                        , R.C_XML_PROCESSING
                        , R.C_DELETED
                        , R.C_CLEAR_DATE
                        , R.C_XML_WORKFLOW
                        , R.C_RETURN_EMAIL
                        , R.C_REJECTED
                        , R.C_REJECT_REASON
                        , R.C_CITIZEN_NAME
                        , R.C_ADVANCE_COST
                        , R.C_CREATE_BY
                        , R.C_NEXT_TASK_CODE
                        , R.C_NEXT_USER_CODE
                        , R.C_NEXT_CO_USER_CODE
                        , R.C_LAST_TASK_CODE
                        , R.C_LAST_USER_CODE
                        , CAST(R.C_DOING_STEP_BEGIN_DATE AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
                        , R.C_DOING_STEP_DEADLINE_DATE
                        , R.C_BIZ_DAYS_EXCEED
                        , a.C_TASK_CODE
                        , a.C_STEP_TIME
                        ,$p_activity_filter as TT
                        ,(Select C_CONTENT  FROM t_r3_record_comment
                                Where FK_RECORD=R.PK_RECORD
                                Order By C_CREATE_DATE DESC
                                Limit 1
                        ) C_LAST_RECORD_COMMENT
                        ,R.C_PAUSE_DATE
                        ,R.C_UNPAUSE_DATE
                    FROM
                    (
                        SELECT
                          RID.`PK_RECORD`
                        , UT.C_TASK_CODE
                        , UT.C_STEP_TIME
                        , (SELECT @rownum:=0)
                        FROM
                        (
                            SELECT
                              PK_RECORD
                            , C_NEXT_TASK_CODE
                            , C_NEXT_USER_CODE
                            FROM  $v_from_and_where $v_and_condition
                            ORDER BY C_RECEIVE_DATE DESC
                            LIMIT $v_start, $v_limit
                        ) RID LEFT JOIN t_r3_user_task UT ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
                    ) a LEFT JOIN t_r3_record R ON a.PK_RECORD=R.PK_RECORD
                    ";
            return $this->db->getAll($sql);
        }
    }

    public function count_record_by_activity($activity)
    {
        $v_count     = 0;
        $v_user_code = Session::get('user_code');

        if (DEBUG_MODE < 10)
        {
            $this->db->debug = 0;
        }

        //Danh sach ID loai thu tuc ma NSD da được phân công
        //Neu co quyen theo doi, giam sat toan bo HS
        if (check_permission('THEO_DOI_GIAM_SAT_TOAN_BO_HO_SO', $this->app_name))
        {
            $v_record_type_id_list = '';
        }
        else
        {
            $sql = "Select Distinct PK_RECORD_TYPE
                    From t_r3_record_type RT
                        Right join (Select Distinct C_RECORD_TYPE_CODE
                                    From t_r3_user_task
                                    Where C_USER_LOGIN_NAME = '$v_user_code') a
                        On RT.C_CODE = a.C_RECORD_TYPE_CODE
                    Where RT.PK_RECORD_TYPE Is Not Null";
            $arr_record_type_id_list = $this->db->getCol($sql);
            $v_record_type_id_list   = implode(',', $arr_record_type_id_list);
        }

        $v_and_condition = '';
        if (Session::get('la_can_bo_cap_xa'))
        {
            $v_and_condition .= " And FK_VILLAGE_ID=" . Session::get('village_id');
        }
        if ($v_record_type_id_list != '' && $v_record_type_id_list != ',')
        {
            $v_and_condition .= " And FK_RECORD_TYPE In ($v_record_type_id_list)";
        }

        $v_from_and_where = $this->_build_from_and_where_query_by_record_activity(intval($activity));

        $v_count         = 0;
        $sql             = "Select Count(*) From $v_from_and_where $v_and_condition";
        $v_count         = $this->db->getOne($sql);
        $this->db->debug = DEBUG_MODE;
        return $v_count;
    }

    public function qry_all_record_in_lai_phieu_tiep_nhan($record_type_code)
    {
        //Xem theo trang
        $v_page          = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
        $v_start         = $v_rows_per_page * ($v_page - 1) + 1;
        $v_end           = $v_start + $v_rows_per_page - 1;

        $stmt   = 'Exec [dbo].[sp_r3_record_get_all_for_in_lai_phieu_tiep_nhan] ?,?,?,?';
        $params = array(Session::get('user_code'), $record_type_code, $v_start, $v_end);

        return $this->db->getAll($stmt, $params);
    }

    public function qry_all_record_y_kien_lanh_dao($record_type_code)
    {
        //Xem theo trang
        page_calc($v_start, $v_end);

        $v_user_code = Session::get('user_code');

        $v_record_no_filter    = get_post_var('txt_record_no_filter');
        $v_citizen_name_filter = get_post_var('txt_citizen_name_filter');

        if ($this->is_mssql())
        {
            $stmt   = 'Exec [dbo].[sp_r3_record_get_all_for_y_kien_lanh_dao]
                        @p_user_code = ?
                        ,@p_record_type_code = ?
                        ,@p_index_begin = ?
                        ,@p_index_end = ?
                        ,@p_record_no = ?
                        ,@p_citizen_name = ?';
            $params = array(
                Session::get('user_code')
                , $record_type_code
                , $v_start
                , $v_end
                , $v_record_no_filter
                , $v_citizen_name_filter
            );
            return $this->db->getAll($stmt, $params);
        }
        elseif ($this->is_mysql())
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            $condition_query = '';
            if ($record_type_code != '')
            {
                $v_record_type_id = $this->db->getOne("Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE='$record_type_code'");
                $condition_query .= " And FK_RECORD_TYPE=$v_record_type_id";
            }

            if ($v_record_no_filter != '')
            {
                $condition_query .= " And C_RECORD_NO like '%$v_record_no_filter%'";
            }

            if ($v_citizen_name_filter != '')
            {
                $condition_query .= " And C_CITIZEN_NAME like '%$v_citizen_name_filter%'";
            }

            //LienND update: Với HỒ SƠ cap xa, liên thông, xã nào chỉ tra cứu HỒ SƠ của xã đó
            if (Session::get('la_can_bo_cap_xa'))
            {
                $condition_query .= " And FK_VILLAGE_ID = " . Session::get('village_id') . ' ';
                if (check_permission('THEO_DOI_GIAM_SAT_TOAN_BO_HO_SO', $this->app_name) == FALSE)
                {
                    $condition_query .= " And LENGTH(ExtractValue(C_XML_PROCESSING, '//step[contains(user_code,''$v_user_code'')]/user_code')) > 0";
                }
            }

            //Dem tong ban ghi
            $v_total_record = $this->db->getOne("Select Count(*) TOTAL_RECORD From view_processing_record R Where 1>0 $condition_query");
            $sql            = "Select
                        @rownum:=@rownum + 1 As RN
                        , CASE WHEN R.C_NEXT_USER_CODE='$v_user_code' THEN 1 ELSE 0 END AS C_OWNER
                        , $v_total_record AS TOTAL_RECORD
                        ,CASE
                            WHEN (DATEDIFF(NOW(), R.C_DOING_STEP_DEADLINE_DATE)>0) THEN
                                (SELECT -1 * (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())<=0
                                    AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)>0 )
                            ELSE
                                (SELECT (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())>=0
                                    AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)<0 )
                        END AS C_DOING_STEP_DAYS_REMAIN
                        ,CASE
                            WHEN (DATEDIFF(NOW(),R.C_RETURN_DATE)>0) THEN
                                (SELECT -1 * (COUNT(*))
                                FROM view_working_date WD
                                WHERE  DATEDIFF(WD.C_DATE, NOW())<=0
                                    AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)>0 )
                            ELSE
                                (SELECT (COUNT(*))
                                FROM view_working_date WD
                                WHERE DATEDIFF(WD.C_DATE, NOW())>=0
                                    AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)<0 )
                        END AS C_RETURN_DAYS_REMAIN
                        ,Case
                            When (C_REJECTED = 1) Then 3
                            When (C_REJECTED <> 1 And (C_CLEAR_DATE Is Not Null)) Then 2
                            Else 1 -- Dang xu ly
                        End  as C_ACTIVITY
                        , R.PK_RECORD
                        , R.FK_RECORD_TYPE
                        , R.C_RECORD_NO
                        , CAST(R.C_RECEIVE_DATE As CHAR(19)) AS C_RECEIVE_DATE
                        , CAST(R.C_RETURN_DATE As CHAR(19)) AS C_RETURN_DATE
                        , R.C_RETURN_PHONE_NUMBER
                        , R.C_XML_DATA
                        , R.C_XML_PROCESSING
                        , R.C_DELETED
                        , R.C_CLEAR_DATE
                        , R.C_XML_WORKFLOW
                        , R.C_RETURN_EMAIL
                        , R.C_REJECTED
                        , R.C_REJECT_REASON
                        , R.C_CITIZEN_NAME
                        , R.C_ADVANCE_COST
                        , R.C_CREATE_BY
                        , R.C_NEXT_TASK_CODE
                        , R.C_NEXT_USER_CODE
                        , R.C_NEXT_CO_USER_CODE
                        , R.C_LAST_TASK_CODE
                        , R.C_LAST_USER_CODE
                        , CAST(R.C_DOING_STEP_BEGIN_DATE As CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
                        , R.C_DOING_STEP_DEADLINE_DATE
                        , R.C_BIZ_DAYS_EXCEED
                        , a.C_TASK_CODE
                        , a.C_STEP_TIME
                        ,(Select C_CONTENT  FROM t_r3_record_comment
                                Where FK_RECORD=R.PK_RECORD
                                Order By C_CREATE_DATE DESC
                                Limit 1
                        ) C_LAST_RECORD_COMMENT
                        ,R.C_PAUSE_DATE
                        ,R.C_UNPAUSE_DATE
                    From
                    (
                        Select
                            RID.`PK_RECORD`
                          , UT.C_TASK_CODE
                          , UT.C_STEP_TIME
                          , (SELECT @rownum:=0)
                        From
                        (
                            Select
                                PK_RECORD
                              , C_NEXT_TASK_CODE
                              , C_NEXT_USER_CODE
                            From view_processing_record
                            Where (1> 0) $condition_query
                            Limit $v_start, $v_limit
                        ) RID Left join t_r3_user_task UT On (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE And RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
                    ) a Left join view_processing_record R On a.PK_RECORD=R.PK_RECORD
                    Order by R.C_RECEIVE_DATE DESC
                    ";
            return $this->db->getAll($sql);
        }
    }

    public function do_send_confirmation_request_record()
    {
        $v_go_to = get_post_var('hdn_go_to', 1);


        //Ma loai HS
        $v_record_type_code = isset($_POST['hdn_record_type_code']) ? replace_bad_char($_POST['hdn_record_type_code']) : '';

        ($v_record_type_code != '') OR DIE();

        $v_user_code = Session::get('user_code');

        //Danh sach HS chuyen di
        $v_record_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';

        //CB thu ly chinh
        $v_exec_user_code = isset($_POST['rad_receiver']) ? replace_bad_char($_POST['rad_receiver']) : '';
        $v_exec_user_name = isset($_POST['hdn_receiver_name']) ? replace_bad_char($_POST['hdn_receiver_name']) : '';

        //noi dung yeu cau
        $v_request_message_content = isset($_POST['txt_request_message_content']) ? replace_bad_char($_POST['txt_request_message_content']) : '';

        $v_co_exec_user_code_list = '';

        $arr_record_id = explode(',', $v_record_id_list);

        //Neu chuyen hs xuong xa
        if ($v_go_to == 1)
        {
            foreach ($arr_record_id as $v_record_id)
            {
                if (!$this->_check_inhand_record($v_record_id))
                {
                    continue;
                }
                $stmt           = 'Select C_NEXT_TASK_CODE From view_processing_record Where PK_RECORD=?';
                $v_current_task = $this->db->getOne($stmt, array($v_record_id));

                //Next task
                $arr_next_task_info       = $this->_qry_next_task_info($v_current_task);
                $v_next_task_code         = $arr_next_task_info['C_NEXT_TASK_CODE'];
                $v_next_task_user_code    = $v_exec_user_code;
                $v_next_task_user_name    = $v_exec_user_name;
                $v_next_task_co_user_code = ',' . $v_co_exec_user_code_list . ',';

                $xml_next_task = '<next_task ';
                $xml_next_task .= ' code="' . $v_next_task_code . '"';
                $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
                $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
                $xml_next_task .= ' user_job_title=""';
                $xml_next_task .= ' co_user="' . $v_next_task_co_user_code . '"';
                $xml_next_task .= ' promote="' . $v_request_message_content . '"';
                $xml_next_task .= ' />';

                if (!$v_next_task_code)
                {
                    continue;
                }

                $v_step_seq = uniqid();
                $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
                $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
                $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
                $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                $step .= '<datetime>' . $this->getDate() . '</datetime>';
                $step .= '<promote>' . $v_request_message_content . '</promote>';
                $step .= '<reason>' . $v_request_message_content . '</reason>';
                $step .= '</step>';
                $this->_insert_record_processing_step($v_record_id, $step);
                $this->_update_next_task_info($v_record_id, $xml_next_task);
            }
        }
        //Chuyen lai cho lanh dao phong
        elseif ($v_go_to == 2)
        {
            echo 'chuyen lai cho lanh dao phong';
            echo '<br>- Tìm ra người phân công';
            echo '<br>- Chuyển hồ sơ về trạng thái chờ phân công';

            $v_promote              = get_post_var('rad_promote');
            $v_back_message_content = get_post_var('txt_back_message_content');

            foreach ($arr_record_id as $v_record_id)
            {
                //Kiem tr NSD lam dung viec
                $stmt    = 'Select Count(*) From view_processing_record Where PK_RECORD=? And C_NEXT_USER_CODE=?';
                $v_count = $this->db->getOne($stmt, array($v_record_id, Session::get('user_code')));
                if ($v_count < 1)
                {
                    continue;
                }

                //Tim ra nguoi phan cong
                $v_xml_processing           = $this->_get_xml_processing($v_record_id);
                $dom_processing             = simplexml_load_string($v_xml_processing);
                //Thong tin phan cong
                $allot_info                 = xpath($dom_processing, "//step[contains(@code,'" . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE . "')][last()]", XPATH_DOM);
                //Tra lai nguoi phan cong
                $v_next_task_code           = $allot_info->attributes()->code;
                $v_next_task_user_code      = $allot_info->user_code;
                $v_next_task_user_name      = $allot_info->user_name;
                $v_next_task_user_job_title = $allot_info->user_job_title;
                $v_next_task_co_user_code   = '';
                $xml_next_task              = '<next_task ';
                $xml_next_task .= ' code="' . $v_next_task_code . '"';
                $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
                $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
                $xml_next_task .= ' user_job_title="' . $v_next_task_user_job_title . '"';
                $xml_next_task .= ' co_user="' . $v_next_task_co_user_code . '"';
                $xml_next_task .= ' promote="' . $v_back_message_content . '"';
                $xml_next_task .= ' />';
                if (!$v_next_task_code)
                {
                    continue;
                }
                //Ghi log
                $v_step_seq = uniqid();
                $step       = '<step seq="' . $v_step_seq . '" code="' . _CONST_THU_LY_ROLE . '">';
                $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
                $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
                $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
                $step .= '<datetime>' . $this->getDate() . '</datetime>';
                $step .= '<promote>' . $v_promote . '</promote>';
                $step .= '<reason>' . $v_back_message_content . '</reason>';
                $step .= '</step>';
                $this->_insert_record_processing_step($v_record_id, $step);
                $this->_update_next_task_info($v_record_id, $xml_next_task);
            }//end foreach
        }//end if go_to

        $this->popup_exec_done();
    }

    /**
     * Xa xac nhan ho so
     */
    public function do_send_confirmation_response_record()
    {
        //Ma loai HS
        $v_record_type_code = isset($_POST['hdn_record_type_code']) ? replace_bad_char($_POST['hdn_record_type_code']) : '';

        ($v_record_type_code != '') OR DIE();

        //Ket qua xac nhan
        $v_exec_value = isset($_POST['rad_approval']) ? replace_bad_char($_POST['rad_approval']) : '';
        ($v_exec_value != '') OR DIE();

        //Danh sach ma HS
        $v_record_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';

        //Ly do chua duyet HS
        $v_reason = isset($_POST['txt_reason']) ? replace_bad_char($_POST['txt_reason']) : '';

        $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE;

        $arr_record_id = explode(',', $v_record_id_list);

        foreach ($arr_record_id as $v_record_id)
        {
            $stmt    = 'Select Count(*) From view_processing_record Where PK_RECORD=? And C_NEXT_USER_CODE=?';
            $v_count = $this->db->getOne($stmt, array($v_record_id, Session::get('user_code')));
            if ($v_count < 1 || !$this->_check_inhand_record($v_record_id))
            {
                continue;
            }

            //$v_current_task ?????
            $stmt           = 'Select C_NEXT_TASK_CODE From view_processing_record Where PK_RECORD=?';
            $v_current_task = $this->db->getOne($stmt, array($v_record_id));

            //Tính toán về công việc tiếp theo
            //Chuyển HỒ SƠ về người (CB huyện) đã gửi yêu cầu xuống để tiếp tục thụ lý
            //Next task
            $arr_next_task_info = $this->_qry_next_task_info($v_current_task);
            $v_next_task_code   = $arr_next_task_info['C_NEXT_TASK_CODE'];

            $dom_processing             = simplexml_load_string($this->db->getOne('Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=?', array($v_record_id)));
            $next_user                  = xpath($dom_processing, '//step[last()]', XPATH_DOM);
            $v_next_task_user_code      = $next_user->user_code;
            $v_next_task_user_name      = $next_user->user_name;
            $v_next_task_user_job_title = $next_user->user_job_title;

            $xml_next_task = '<next_task';
            $xml_next_task .= ' code="' . $v_next_task_code . '"';
            $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
            $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
            $xml_next_task .= ' user_job_title="' . $v_next_task_user_job_title . '"';
            $xml_next_task .= ' promote="' . $v_exec_value . '"';
            $xml_next_task .= ' reason="' . $v_reason . '"';
            $xml_next_task .= ' />';
            if (!$v_next_task_code)
            {
                continue;
            }

            //ghi log qua trinh xu ly
            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '<promote>' . $v_exec_value . '</promote>';
            $step .= '<reason>' . $v_reason . '</reason>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task);
        }
        $this->popup_exec_done();
    }

    /**
     * Gui lai yeu cau xac nhan xuong xa
     */
    public function do_resend_confirmation_request_record()
    {
        //Ma loai HS
        $v_record_type_code = isset($_POST['hdn_record_type_code']) ? replace_bad_char($_POST['hdn_record_type_code']) : '';

        ($v_record_type_code != '') OR DIE();

        $v_user_code = Session::get('user_code');

        //Danh sach HS chuyen di
        $v_record_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';

        //CB thu ly chinh
        $v_exec_user_code = isset($_POST['rad_receiver']) ? replace_bad_char($_POST['rad_receiver']) : '';
        $v_exec_user_name = isset($_POST['hdn_receiver_name']) ? replace_bad_char($_POST['hdn_receiver_name']) : '';

        //noi dung yeu cau
        $v_request_message_content = isset($_POST['txt_request_message_content']) ? replace_bad_char($_POST['txt_request_message_content']) : '';

        $v_co_exec_user_code_list = '';

        $arr_record_id = explode(',', $v_record_id_list);
        foreach ($arr_record_id as $v_record_id)
        {
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }

            //Lay chinh xac ma cong viec
            $stmt           = 'Select distinct C_TASK_CODE From t_r3_user_task Where C_TASK_CODE Like ? And C_USER_LOGIN_NAME=?';
            $params         = array('%' . $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_CHUYEN_YEU_CAU_XAC_NHAN_XUONG_XA_ROLE, $v_user_code);
            $v_current_task = $this->db->getOne($stmt, $params);

            //Next task = XAC_NHAN
            $arr_next_task_info       = $this->_qry_next_task_info($v_current_task);
            $v_next_task_code         = $arr_next_task_info['C_NEXT_TASK_CODE'];
            $v_next_task_user_code    = $v_exec_user_code;
            $v_next_task_user_name    = $v_exec_user_name;
            $v_next_task_co_user_code = ',' . $v_co_exec_user_code_list . ',';

            $xml_next_task = '<next_task ';
            $xml_next_task .= ' code="' . $v_next_task_code . '"';
            $xml_next_task .= ' user="' . $v_next_task_user_code . '"';
            $xml_next_task .= ' user_name="' . $v_next_task_user_name . '"';
            $xml_next_task .= ' user_job_title=""';
            $xml_next_task .= ' co_user="' . $v_next_task_co_user_code . '"';
            $xml_next_task .= ' promote="' . $v_request_message_content . '"';
            $xml_next_task .= ' />';
            if (!$v_next_task_code)
            {
                continue;
            }

            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '<promote>' . $v_request_message_content . '</promote>';
            $step .= '<reason>' . $v_request_message_content . '</reason>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task);
        }

        $this->popup_exec_done();
    }

    public function qry_single_internet_record($p_record_id, $v_xml_workflow_file_name)
    {
        $v_record_id = get_post_var('hdn_item_id', replace_bad_char($p_record_id));
        if (!is_id_number($v_record_id))
        {
            $v_record_id = 0;
        }

        if ($v_record_id > 0)
        {
            if (DATABASE_TYPE == 'MSSQL')
            {
                $stmt = 'SELECT
                			R.[PK_RECORD]
    				        ,r.[FK_RECORD_TYPE]
    				        ,r.[C_RECORD_NO]
    				        , Convert(varchar(19), r.[C_RECEIVE_DATE], 120) as [C_RECEIVE_DATE]
    				        , Convert(varchar(19), r.[C_RETURN_DATE], 120) as [C_RETURN_DATE]
    				        ,r.[C_RETURN_PHONE_NUMBER]
    				        ,r.[C_XML_DATA]
    				        ,r.[C_XML_PROCESSING]
    				        ,r.[C_DELETED]
                			, Convert(varchar(19), r.[C_CLEAR_DATE], 120) as [C_CLEAR_DATE]
    				        ,r.[C_XML_WORKFLOW]
    				        ,r.[C_RETURN_EMAIL]
    				        ,r.[C_REJECTED]
    				        ,r.[C_REJECT_REASON]
    				        ,r.[C_CITIZEN_NAME]
    				        ,r.[C_ADVANCE_COST]
    				        ,r.[C_CREATE_BY]
    				        ,R.[C_NEXT_TASK_CODE]
    				        ,r.[C_NEXT_USER_CODE]
    				        ,r.[C_NEXT_CO_USER_CODE]
    				        ,r.[C_LAST_TASK_CODE]
    				        ,r.[C_LAST_USER_CODE]
    				        ,Convert(varchar(19), r.[C_DOING_STEP_BEGIN_DATE], 120) as [C_DOING_STEP_BEGIN_DATE]
    				        ,r.[C_DOING_STEP_DEADLINE_DATE]
    				        ,r.[C_BIZ_DAYS_EXCEED] 
    	                    ,RT.C_NAME as C_RECORD_TYPE_NAME
    	                    ,RT.C_CODE as C_RECORD_TYPE_CODE
                       	From t_r3_internet_record R Left Join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                        Where R.PK_RECORD=? And R.C_DELETED=0';
            }
            elseif (DATABASE_TYPE == 'MYSQL')
            {

                $stmt = 'Select
                              R.PK_RECORD
                            , R.FK_RECORD_TYPE
                            , R.C_RECORD_NO
                            , Cast(R.C_RECEIVE_DATE  AS CHAR(19)) AS C_RECEIVE_DATE
                            , NULL AS C_RETURN_DATE
                            , R.C_RETURN_PHONE_NUMBER
                            , R.C_XML_DATA
                            , NULL as C_XML_PROCESSING
                            , R.C_DELETED
                            , NULL as C_CLEAR_DATE
                            , NULL as C_XML_WORKFLOW
                            , R.C_RETURN_EMAIL
                            , R.C_CITIZEN_NAME
                            , R.C_NEXT_TASK_CODE
                            , R.C_NEXT_USER_CODE
                            , RT.C_NAME as C_RECORD_TYPE_NAME
                            , RT.C_CODE as C_RECORD_TYPE_CODE
                            , R.C_IS_REAL_RECORD
                            ,R.C_COMMENT
                        From t_r3_internet_record R Left join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                        Where R.PK_RECORD=? And R.C_DELETED=0';
            }

            $params    = array($v_record_id);
            $ret_array = $this->db->getRow($stmt, $params);

            //Tinh toan ngay tra ket qua
            if (file_exists($v_xml_workflow_file_name))
            {
                $dom          = simplexml_load_file($v_xml_workflow_file_name);
                $r            = xpath($dom, "/process/@totaltime[1]", XPATH_STRING);
                $v_total_time = str_replace(',', '.', $r); //Dau thap phan la dau cham "."

                $v_return_date = $this->_step_deadline_calc($v_total_time);

                $ret_array['C_RETURN_DATE'] = $v_return_date;
                $ret_array['C_TOTAL_TIME']  = $v_total_time;
            }

            return $ret_array;
        }

        return NULL;
    }

    public function qry_all_internet_record_file($item_id)
    {
        if (intval($item_id) < 1)
        {
            return NULL;
        }
        $stmt = 'Select PK_RECORD_FILE, C_FILE_NAME FROM t_r3_internet_record_file Where FK_RECORD=?';
        return $this->db->getAll($stmt, array($item_id));
    }

    public function update_internet_record()
    {
        $v_record_id = get_post_var('hdn_item_id');
        $v_xml_data  = get_post_var('XmlData', '<data/>', 0);

        //kiểm tra
        $arr_single_record = $this->db->GetRow("Select * 
            From t_r3_internet_record 
            Where PK_RECORD=?", array($v_record_id));

        $v_comment = $arr_single_record['C_IS_REAL_RECORD'] ? '' : get_post_var('txt_comment');

        $stmt = 'Update t_r3_internet_record Set C_XML_DATA=?, C_COMMENT=? ';
        if (!$arr_single_record['C_IS_REAL_RECORD'])
        {
            $v_xml_data = "<?xml version=\"1.0\" standalone=\"yes\"?>
                <data>
                    <item id=\"txtName\">
                        <value><![CDATA[{$arr_single_record['C_CITIZEN_NAME']}]]></value>
                    </item>
                </data>";
        }
        $params   = array($v_xml_data, $v_comment);
        $stmt .= " Where PK_RECORD=?";
        $params[] = $v_record_id;
        $this->db->Execute($stmt, $params);

        //Chấp nhận lưu trữ thừa DB, tăng tốc độ Query khi Tra cứu, báo cáo
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt   = "Update t_r3_internet_record Set
                        C_CITIZEN_NAME = C_XML_DATA.value('(//item[@id=''txtName'']/value/text())[1]','nvarchar(200)')
                    Where PK_RECORD=?";
            $params = array($v_record_id);
            $this->db->Execute($stmt, $params);
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt   = "Update t_r3_internet_record Set
                            C_CITIZEN_NAME = ExtractValue(C_XML_DATA, '//item[@id=''txtName'']/value[1]')
                    Where PK_RECORD=?";
            $params = array($v_record_id);
            $this->db->Execute($stmt, $params);
        }

        $arr_filter = get_filter_condition(array('sel_record_type'));
        $this->exec_done($this->goback_url, $arr_filter);
    }

    public function do_delete_internet_record()
    {
        $v_item_id_list = get_post_var('hdn_item_id_list');

        if ($v_item_id_list != '')
        {
            $sql = "Update t_r3_internet_record Set C_DELETED=1
                    Where PK_RECORD In ($v_item_id_list)";
            $this->db->Execute($sql);
        }

        $arr_filter = get_filter_condition(array('sel_record_type'));
        $this->exec_done($this->goback_url, $arr_filter);
    }

    public function do_accept_internet_record()
    {
        $v_internet_record_id     = get_post_var('hdn_item_id');
        $v_record_type_code       = get_post_var('record_type_code');
        $v_return_date            = get_post_var('hdn_return_date');
        $v_return_phone_number    = get_post_var('hdn_return_phone_number');
        $v_return_email           = get_post_var('hdn_return_email');
        $v_total_time             = get_post_var('hdn_total_time');
        $v_response_by            = get_post_var('rad_accept', 'email');
        $v_xml_workflow_file_name = get_post_var('hdn_xml_workflow_file_name');

        if (file_exists($v_xml_workflow_file_name))
        {
            $dom            = simplexml_load_file($v_xml_workflow_file_name);
            $v_current_task = xpath($dom, "//step[1]/task[1]/@code", XPATH_STRING);
        }
        else
        {
            $v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;
        }

        $v_user_code = $this->db->getOne("Select C_USER_LOGIN_NAME From t_r3_user_task Where C_TASK_CODE Like '%$v_current_task'");

        $sql = "Insert Into t_r3_record(
                        FK_RECORD_TYPE
                        ,C_RECORD_NO
                        ,C_RECEIVE_DATE
                        ,C_RETURN_DATE
                        ,C_RETURN_PHONE_NUMBER
                        ,C_XML_DATA
                        ,C_DELETED
                        ,C_RETURN_EMAIL
                        ,C_IS_INTERNET_RECORD
                    ) Values (
                        (Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE='$v_record_type_code')
                        ,(Select C_RECORD_NO From t_r3_internet_record Where PK_RECORD=$v_internet_record_id)
                        ," . $this->build_getdate_function() . "
                        ,'$v_return_date'
                        ,'$v_return_phone_number'
                        ,(Select C_XML_DATA From t_r3_internet_record Where PK_RECORD=$v_internet_record_id)
                        ,0
                        ,'$v_return_email'
                        ,'1'
                    )";
        $this->db->Execute($sql);

        $v_record_id       = $this->get_last_inserted_id('t_r3_record', 'PK_RECORD');
        //Next task
        //LienND update 22/10/2012
        //Neu task tiếp theo có nhiều người bàn giao, thì người bàn giao phải là người nhập HỒ SƠ này
        $stmt              = 'Select Count(*)
                From t_r3_user_task
                Where C_TASK_CODE=(Select distinct C_NEXT_TASK_CODE From t_r3_user_task Where C_TASK_CODE=?)';
        $params            = array($v_current_task);
        $v_count_next_user = $this->db->getOne($stmt, $params);

        if ($v_count_next_user < 2)
        {
            $arr_next_task_info = $this->_qry_next_task_info($v_current_task);
        }
        else
        {
            //Nếu có nhiều người bàn giao: Ai tiếp nhận, người đó bàn giao
            $sql                = "Select
                        C_TASK_CODE as C_NEXT_TASK_CODE
                        , C_NAME as C_NEXT_USER_NAME
                        , C_USER_LOGIN_NAME as C_NEXT_USER_LOGIN_NAME
                        , C_JOB_TITLE as C_NEXT_USER_JOB_TITLE
                    From t_cores_user U Right Join (
                            Select takeover.C_TASK_CODE, takeover.C_USER_LOGIN_NAME
                            From t_r3_user_task sender Left Join t_r3_user_task takeover On sender.C_NEXT_TASK_CODE=takeover.C_TASK_CODE
                            Where sender.C_TASK_CODE='$v_current_task'
                                And takeover.C_USER_LOGIN_NAME='$v_user_code'
                        ) a  On a.C_USER_LOGIN_NAME=U.C_LOGIN_NAME";
            $arr_next_task_info = $this->db->getRow($sql);

            //Cong viec tiep theo thuoc Step moi?
            $v_next_group_code = $this->db->getOne("Select C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE='$v_current_task'");
            $v_task_code       = $this->db->getOne("Select C_NEXT_TASK_CODE From t_r3_user_task Where C_TASK_CODE='$v_current_task'");

            $stmt                      = 'Select COUNT(*)
                    From t_r3_user_task
                    Where C_GROUP_CODE=?
                        And C_TASK_CODE=?';
            $params                    = array($v_next_group_code, $v_task_code);
            $v_count_next_task_in_step = $this->db->getOne($stmt, $params);

            $arr_next_task_info['IS_NEW_STEP'] = ($v_count_next_task_in_step == 0) ? TRUE : FALSE;
        }

        $v_next_task            = $arr_next_task_info['C_NEXT_TASK_CODE'];
        $v_next_user_login_name = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
        $v_next_user_name       = $arr_next_task_info['C_NEXT_USER_NAME'];
        $v_next_user_job_title  = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];

        //Init XML Processing
        $v_group_code = $this->db->getOne("Select C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE='$v_next_task'");
        $v_group_name = $this->db->getOne("Select C_NAME From t_cores_group Where C_CODE='$v_group_code'");
        $v_step_time  = $this->db->getOne("Select C_STEP_TIME From t_r3_user_task Where C_TASK_CODE='$v_next_task'");

        $next = '<next_task code="' . $v_next_task . '" user="' . $v_next_user_login_name
                . '" user_name="' . $v_next_user_name
                . '" user_job_title="' . $v_next_user_job_title
                . '" group_name="' . $v_group_name
                . '" step_time="' . $v_step_time
                . '" co_user=""
                />';

        $step = '<step seq="' . uniqid() . '" code="' . $v_current_task . '">';
        $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
        $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
        $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
        $step .= '<datetime>' . $this->getDate() . '</datetime>';
        $step .= '</step>';

        $xml_processing = '<?xml version="1.0" standalone="yes"?><data>' . $next . $step . '</data>';

        $stmt   = 'Update t_r3_record
                 Set C_XML_PROCESSING=?
                 Where PK_RECORD=?';
        $params = array($xml_processing, $v_record_id);
        $this->db->Execute($stmt, $params);

        //Xoa bang t_r3_internet_record
        $stmt   = 'Update t_r3_internet_record Set C_DELETED=1 Where PK_RECORD=?';
        $params = array($v_internet_record_id);
        $this->db->Execute($stmt, $params);

        $v_doing_step_deadline_date = $this->_step_deadline_calc($v_step_time);
        //Chấp nhận lưu trữ thừa DB, tăng tốc độ Query khi Tra cứu, báo cáo
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt   = "Update t_r3_record Set
                        C_CITIZEN_NAME = C_XML_DATA.value('(//item[@id=''txtName'']/value/text())[1]','nvarchar(200)')
                        ,C_ADVANCE_COST = IsNull(C_XML_DATA.value('(//item[@id=''txtCost'']/value/text())[1]','nvarchar(50)'),0)
                        ,C_CREATE_BY= C_XML_PROCESSING.value('(//step[1]/user_code/text())[1]', 'nvarchar(100)')
                        ,C_NEXT_TASK_CODE=C_XML_PROCESSING.value('(//next_task[1]/@code)[1]', 'nvarchar(500)')
                        ,C_NEXT_USER_CODE=C_XML_PROCESSING.value('(//next_task[1]/@user)[1]', 'nvarchar(100)')
                        ,C_NEXT_CO_USER_CODE=C_XML_PROCESSING.value('(//next_task[1]/@co_user)[1]', 'nvarchar(1000)')
                        ,C_LAST_TASK_CODE=C_XML_PROCESSING.value('(//step[last()]/@code)[1]', 'nvarchar(500)')
                        ,C_LAST_USER_CODE=C_XML_PROCESSING.value('(//step[last()]/user_code)[1]', 'nvarchar(500)')
                        ,C_DOING_STEP_DEADLINE_DATE='$v_doing_step_deadline_date'
                        ,C_DOING_STEP_BEGIN_DATE=getDate()
                    Where PK_RECORD=?";
            $params = array($v_record_id);
            $this->db->Execute($stmt, $params);
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt   = "Update t_r3_record Set
                            C_CITIZEN_NAME = ExtractValue(C_XML_DATA, '//item[@id=''txtName'']/value[1]')
                            ,C_ADVANCE_COST = Case ExtractValue(C_XML_DATA, '//item[@id=''txtCost'']/value[1]') When '' Then '0' Else ExtractValue(C_XML_DATA, '//item[@id=''txtCost'']/value[1]') End
                            ,C_CREATE_BY = ExtractValue(C_XML_PROCESSING, '//step[1]/user_code[1]')
                            ,C_NEXT_TASK_CODE = ExtractValue(C_XML_PROCESSING, '//next_task[1]/@code[1]')
                            ,C_NEXT_USER_CODE = ExtractValue(C_XML_PROCESSING, '//next_task[1]/@user[1]')
                            ,C_NEXT_CO_USER_CODE = ExtractValue(C_XML_PROCESSING, '//next_task[1]/@co_user[1]')
                            ,C_LAST_TASK_CODE = ExtractValue(C_XML_PROCESSING, '//step[last()]/@code[1]')
                            ,C_LAST_USER_CODE = ExtractValue(C_XML_PROCESSING, '//step[last()]/user_code[1]')
                            ,C_DOING_STEP_DEADLINE_DATE='$v_doing_step_deadline_date'
                            ,C_DOING_STEP_BEGIN_DATE=Now()
                    Where PK_RECORD=?";
            $params = array($v_record_id);
            $this->db->Execute($stmt, $params);
        }
        else
        {
            //Chưa hỗ trợ DB khác
        }

        //Upload file
        $stmt = "Insert Into t_r3_record_file(FK_RECORD, C_FILE_NAME)
                Select $v_record_id, C_FILE_NAME
                From t_r3_internet_record_file
                Where FK_RECORD=$v_internet_record_id";
        $this->db->Execute($stmt);
//        $arr_single_record = $this->qry_single_record($v_record_id);
//
//        if ($arr_single_record['C_RETURN_EMAIL'])
//        {
//            //Gui email cho cong dan
//            require_once dirname(__FILE__) . '/classes/announce.inc.php';
//            $mail = new announce_accept($v_return_email, $arr_single_record);
//            $mail->send();
//        }
        $this->popup_exec_done(null, SITE_ROOT . 'r3/record');
    }

//end func

    /**
     * Chuyen hs sang buoc tiep theo
     */
    public function do_go_forward_record($v_record_id_list)
    {
        $arr_record_id_list = explode(',', $v_record_id_list);
        for ($i = 0, $n = count($arr_record_id_list); $i < $n; $i++)
        {
            $v_record_id = $arr_record_id_list[$i];
            if (!$this->_check_inhand_record($v_record_id))
            {
                continue;
            }
            $v_current_task         = $this->db->getOne("Select C_NEXT_TASK_CODE 
				From view_processing_record 
				Where PK_RECORD=$v_record_id");
            $arr_next_task_info     = $this->_qry_next_task_info($v_current_task);
            $v_next_task_code       = $arr_next_task_info['C_NEXT_TASK_CODE'];
            $v_next_user_login_name = $arr_next_task_info['C_NEXT_USER_LOGIN_NAME'];
            $v_next_user_name       = $arr_next_task_info['C_NEXT_USER_NAME'];
            $v_next_user_job_title  = $arr_next_task_info['C_NEXT_USER_JOB_TITLE'];
            //Next
            $xml_next_task          = '<next_task ';
            $xml_next_task .= ' code="' . $v_next_task_code . '"';
            $xml_next_task .= ' user="' . $v_next_user_login_name . '"';
            $xml_next_task .= ' user_name="' . $v_next_user_name . '"';
            $xml_next_task .= ' user_job_title="' . $v_next_user_job_title . '"';
            //$xml_next_task .= ' reason="' . $v_reason . '"';
            $xml_next_task .= ' />';
            if (!$v_next_task_code)
            {
                continue;
            }

            //Step log
            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task);
        }
        $this->exec_done($this->goback_url);
    }

    /**
     * Lay danh sach phong ban nhan ban giao
     */
    public function qry_all_group_to_handover()
    {
        $v_user_code          = Session::get('user_code');
        $stmt                 = 'SELECT
                    b.C_GROUP_CODE
                    , G.C_NAME
                FROM (SELECT DISTINCT
                          U.C_GROUP_CODE
                      FROM t_r3_user_task U
                          RIGHT JOIN (SELECT
                                          C_NEXT_TASK_CODE
                                      FROM t_r3_user_task
                                      WHERE C_TASK_CODE LIKE ?
                                            AND C_USER_LOGIN_NAME = ?) a
                            ON U.C_TASK_CODE = a.C_NEXT_TASK_CODE) b
                    LEFT JOIN t_cores_group G
                      ON b.C_GROUP_CODE = G.C_CODE';
        $v_handover_task_code = '%' . _CONST_XML_RTT_DELIM . _CONST_BAN_GIAO_ROLE;
        $params               = array($v_handover_task_code, $v_user_code);

        return $this->db->getAssoc($stmt, $params);
    }

    /**
     * Lay danh sach hs ban giao (theo phong ban nhan ban giao)
     */
    function qry_all_record_to_handover($v_group_code, $v_task_code = '')
    {
        $v_user_code = Session::get('user_code');

        //Xem theo trang
        page_calc($v_start, $v_end);

        $v_start = $v_start - 1;
        $v_limit = $v_end - $v_start;

        if ($v_task_code == '')
        {
            $v_task_code = _CONST_XML_RTT_DELIM . _CONST_BAN_GIAO_ROLE;
        }

        $condition_query = " (C_NEXT_TASK_CODE like '%$v_task_code' AND (C_NEXT_USER_CODE='$v_user_code' Or C_NEXT_USER_CODE Like '%,$v_user_code,%' OR C_NEXT_CO_USER_CODE like '%,$v_user_code,%'))";

        //Dem tong ban ghi
        $sql            = "SELECT
                    COUNT(*)
                FROM (SELECT
                          RID.`PK_RECORD`
                          , UT.C_TASK_CODE
                          , UT.C_STEP_TIME
                          , (SELECT
                                 @rownum:=0)
                          , RID.C_NEXT_TASK_CODE
                          , C_NEXT_NEXT_CODE_CODE
                      FROM (SELECT
                                PK_RECORD
                                , C_NEXT_TASK_CODE
                                , C_NEXT_USER_CODE
                                , (SELECT
                                       C_NEXT_TASK_CODE
                                   FROM t_r3_user_task
                                   WHERE C_TASK_CODE = R.C_NEXT_TASK_CODE)    C_NEXT_NEXT_CODE_CODE
                            FROM view_processing_record R
                            WHERE $condition_query) RID
                          LEFT JOIN t_r3_user_task UT
                            ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE
                                AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)) a
                    LEFT JOIN t_r3_user_task UUTT
                      ON a.C_NEXT_NEXT_CODE_CODE = UUTT.C_TASK_CODE
                WHERE UUTT.C_GROUP_CODE = '$v_group_code'";
        $v_total_record = $this->db->getOne($sql);

        $sql = "
            SELECT
                  @rownum:=@rownum + 1 AS RN
                , CASE WHEN (R.C_NEXT_USER_CODE = '$v_user_code' OR R.C_NEXT_USER_CODE LIKE '%,$v_user_code,%' OR R.C_NEXT_NO_CHAIN_USER_CODE LIKE '%,$v_user_code,%') THEN 1 ELSE 0 END AS C_OWNER
                , 2 AS TOTAL_RECORD
                , CASE WHEN (DATEDIFF(NOW(), R.C_DOING_STEP_DEADLINE_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)<0 ) END AS C_DOING_STEP_DAYS_REMAIN
                , CASE WHEN (DATEDIFF(NOW(),R.C_RETURN_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)<0 ) END AS C_RETURN_DAYS_REMAIN
                , R.PK_RECORD
                , R.FK_RECORD_TYPE
                , R.C_RECORD_NO
                , CAST(R.C_RECEIVE_DATE AS CHAR(19)) AS C_RECEIVE_DATE
                , CAST(R.C_RETURN_DATE AS CHAR(19)) AS C_RETURN_DATE
                , R.C_RETURN_PHONE_NUMBER
                , R.C_XML_DATA
                , R.C_XML_PROCESSING
                , R.C_DELETED
                , R.C_CLEAR_DATE
                , R.C_XML_WORKFLOW
                , R.C_RETURN_EMAIL
                , R.C_REJECTED
                , R.C_REJECT_REASON
                , R.C_CITIZEN_NAME
                , R.C_ADVANCE_COST
                , R.C_CREATE_BY
                , R.C_NEXT_TASK_CODE
                , R.C_NEXT_USER_CODE
                , R.C_NEXT_CO_USER_CODE
                , R.C_LAST_TASK_CODE
                , R.C_LAST_USER_CODE
                , CAST(R.C_DOING_STEP_BEGIN_DATE AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
                , R.C_DOING_STEP_DEADLINE_DATE
                , R.C_BIZ_DAYS_EXCEED
                ,a.C_TASK_CODE
                , a.C_STEP_TIME
                , R.C_NEXT_NO_CHAIN_TASK_CODE
                , a.C_GROUP_CODE
                ,R.C_PAUSE_DATE
                ,R.C_UNPAUSE_DATE
            FROM
                (
                    SELECT
                        aa.*
                        , UUTT.C_GROUP_CODE
                    FROM (
                        SELECT
                          RID.`PK_RECORD`
                          , UT.C_TASK_CODE
                          , UT.C_STEP_TIME
                          , (SELECT @rownum:=0)
                          , RID.C_NEXT_TASK_CODE
                          , C_NEXT_NEXT_CODE_CODE
                      FROM (SELECT
                                PK_RECORD
                                , C_NEXT_TASK_CODE
                                , C_NEXT_USER_CODE
                                , (SELECT
                                       C_NEXT_TASK_CODE
                                   FROM t_r3_user_task
                                   WHERE C_TASK_CODE = R.C_NEXT_TASK_CODE Limit 1)    C_NEXT_NEXT_CODE_CODE
                            FROM view_processing_record R
                            WHERE $condition_query) RID
                          LEFT JOIN t_r3_user_task UT
                            ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE
                                AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)) aa
                    LEFT JOIN t_r3_user_task UUTT
                      ON aa.C_NEXT_NEXT_CODE_CODE = UUTT.C_TASK_CODE
                WHERE UUTT.C_GROUP_CODE = '$v_group_code'
                LIMIT $v_start, $v_limit                
                ) a LEFT JOIN view_processing_record R ON a.PK_RECORD=R.PK_RECORD                
                ORDER BY RN ASC";
        return $this->db->getAll($sql);
    }

    public function qry_ho_receive_group_name($v_record_id_list)
    {
        $v_record_id_list = replace_bad_char($v_record_id_list);
        $sql              = "Select C_NAME
                From t_cores_group
                Where C_CODE=(Select ut.C_GROUP_CODE
                                From t_r3_user_task ut
                                    Left Join t_r3_record_supplement rs
                                    On ut.C_TASK_CODE=rs.C_CREATE_FROM_TASK
                                Where rs.FK_RECORD in ($v_record_id_list)
                                Limit 1
                                )";
        return $this->db->GetOne($sql);
    }

    public function do_stop_cross_over_record()
    {
        $v_record_id = get_post_var('hdn_item_id', 0);
        $v_reason    = get_post_var('txt_reason');

        //Lay thong tin ho so
        $v_xml_processing = $this->_get_xml_processing($v_record_id);
        $dom_processing   = simplexml_load_string($v_xml_processing);

        //Tim ra nguoi tiep nhan?
        $dom_prev_task_info = xpath($dom_processing, "//step[contains(@code, '::BAN_GIAO')][last()]", XPATH_DOM);
        if (!$dom_prev_task_info)
        {
            die('Chức năng đang bảo trì:' . __FUNCTION__);
        }
        $v_next_task            = $dom_prev_task_info->attributes()->code;
        $v_next_user_login_name = $dom_prev_task_info->user_code;
        $v_next_user_name       = $dom_prev_task_info->user_name;
        $v_next_user_job_title  = $dom_prev_task_info->user_job_title;
        //Chuyen ho ve trang thai moi tiep nhan tai xa.
        $v_group_code           = $this->db->getOne("Select C_GROUP_CODE From t_r3_user_task Where C_TASK_CODE='$v_next_task'");
        $v_group_name           = $this->db->getOne("Select C_NAME From t_cores_group Where C_CODE='$v_group_code'");
        $v_step_time            = $this->db->getOne("Select C_STEP_TIME From t_r3_user_task Where C_TASK_CODE='$v_next_task'");

        if (!$this->_check_inhand_record($v_record_id))
        {
            continue;
        }
        $next = '<next_task code="' . $v_next_task . '" user="' . $v_next_user_login_name
                . '" user_name="' . $v_next_user_name
                . '" user_job_title="' . $v_next_user_job_title
                . '" group_name="' . $v_group_name
                . '" step_time="' . $v_step_time
                . '" promote="' . $v_reason
                . '" co_user=""
                />';

        $v_current_task = 'KHONG_NHAN_HO_SO';
        $step           = '<step seq="' . uniqid() . '" code="' . $v_current_task . '">';
        $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
        $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
        $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
        $step .= '<datetime>' . $this->getDate() . '</datetime>';
        $step .= '<promote>' . $v_reason . '</promote>';
        $step .= '<reason>' . $v_reason . '</reason>';
        $step .= '</step>';
        if (!strval($v_next_task))
        {
            continue;
        }
        $this->_insert_record_processing_step($v_record_id, $step);
        $this->_update_next_task_info($v_record_id, $next);

        $this->popup_exec_done();
    }

    public function do_supplement_request_record()
    {
        //Ma loai HS
        $v_record_type_code = isset($_POST['hdn_record_type_code']) ? replace_bad_char($_POST['hdn_record_type_code']) : '';

        ($v_record_type_code != '') OR DIE();

        //Danh sach ma HS
        $v_record_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';

        //Ly do chua duyet HS
        $v_reason = isset($_POST['txt_reason']) ? replace_bad_char($_POST['txt_reason']) : '';

        //$v_current_task = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_ROLE;
        $arr_record_id = explode(',', $v_record_id_list);

        $v_approval_value = _CONST_RECORD_APPROVAL_SUPPLEMENT;
        //người phụ trách bổ sung hồ sơ
        $v_user           = get_post_var('sel_user');

        foreach ($arr_record_id as $v_record_id)
        {
            $arr_single_user = $this->db->GetRow('Select * From t_cores_user Where C_LOGIN_NAME=?', array($v_user));
            if (!$this->_check_inhand_record($v_record_id) Or !$arr_single_user)
            {
                continue;
            }
            $v_current_task   = $this->db->GetOne('Select C_NEXT_TASK_CODE From t_r3_record
                Where PK_RECORD=?', array($v_record_id));
            //Yêu cầu bổ sung, về Một-Cửa để yêu cầu bổ sung
            //NEXT_TASK = 'BO SUNG'
            $v_xml_processing = $this->db->getOne("Select C_XML_PROCESSING From view_processing_record Where PK_RECORD=$v_record_id");
            $dom_processing   = simplexml_load_string($v_xml_processing);
            $v_code           = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;
            //$next_user_info   = xpath($dom_processing, "//step[1]", XPATH_DOM);

            $xml_next_task = '<next_task pause="true"';
            $xml_next_task .= ' code="' . $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE . '"';
            $xml_next_task .= ' user="' . $arr_single_user['C_LOGIN_NAME'] . '"';
            $xml_next_task .= ' user_name="' . $arr_single_user['C_NAME'] . '"';
            $xml_next_task .= ' user_job_title="' . $arr_single_user['C_JOB_TITLE'] . '"';
            $xml_next_task .= ' promote="' . $v_approval_value . '"';
            $xml_next_task .= ' reason="' . $v_reason . '"';
            $xml_next_task .= ' />';


            //Chuyển hồ sơ vào danh sách bổ sung
            $v_goto = 0; //Duyet tu dau
            $stmt   = 'Insert Into t_r3_record_supplement(FK_RECORD, C_GOTO,C_CREATE_FROM_TASK) Values(?,?,?)';
            $this->db->Execute($stmt, array($v_record_id, $v_goto, $v_current_task));

            //Step log
            $v_step_seq = uniqid();
            $step       = '<step seq="' . $v_step_seq . '" code="' . $v_current_task . '">';
            $step .= '<user_code>' . Session::get('user_code') . '</user_code>';
            $step .= '<user_name>' . Session::get('user_name') . '</user_name>';
            $step .= '<user_job_title>' . Session::get('user_job_title') . '</user_job_title>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '<promote>' . $v_approval_value . '</promote>';
            $step .= '<reason>' . $v_reason . '</reason>';
            $step .= '</step>';
            $this->_insert_record_processing_step($v_record_id, $step);
            $this->_update_next_task_info($v_record_id, $xml_next_task);
        }

        $this->popup_exec_done();
    }

}
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

class liveboard_Model extends Model
{

        /**
     * @var boolean 
     */
    private $nsd_la_can_bo_cap_xa;
    public $scope;

    function __construct()
    {
        parent::__construct();
    }
    //Danh sach thu tuc
    public function qry_all_record_type()
    {
        $conditions = " C_STATUS=1 ";
        if (strlen($this->scope))
        {
            $conditions .= " And C_SCOPE In({$this->scope})";
        }

        $stmt = "SELECT 
                    PK_RECORD_TYPE, C_CODE, C_NAME, C_SCOPE
                    
                FROM t_r3_record_type 
                WHERE $conditions
                ORDER BY C_SCOPE Desc, C_CODE";
        
        return $this->db->getAssoc($stmt);
    }
//    Mới tiếp nhận
    public function qry_count_today_receive_record($v_village_id = 0)
    {
        $conditions = " DATEDIFF(NOW(),C_RECEIVE_DATE)=0 ";
        
        $v_village_id = replace_bad_char($v_village_id);
        if ($v_village_id > 0)
        {
            $conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        
        $conditions .= $this->get_other_clause();
        $stmt       = "SELECT 
                        FK_RECORD_TYPE
                        , COUNT(*) C_COUNT
                FROM view_processing_record
                WHERE $conditions
                GROUP BY FK_RECORD_TYPE";
        
        return $this->db->getAssoc($stmt);
    }
    //Da ban giao
    public function qry_count_today_handover_record($v_village_id = 0)
    {
        $v_task_code  = _CONST_XML_RTT_DELIM . _CONST_BAN_GIAO_ROLE;
        $history_date = "CAST(ExtractValue(C_XML_PROCESSING, '//step[contains(@code,''$v_task_code'')][position()=1]/datetime') AS DATETIME)";
        $conditions   = " DATEDIFF(NOW(), $history_date)=0 ";
        
        $v_village_id = replace_bad_char($v_village_id);
        if ($v_village_id > 0)
        {
            $conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        
        
        $conditions .= $this->get_other_clause();

        $stmt = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_RECORD_TYPE";
        
        return $this->db->getAssoc($stmt);
    }

    //Dem hs dang xu ly
    public function qry_count_execing_record($v_village_id = 0)
    {
        $conditions = "
             C_BIZ_DAYS_EXCEED Is Null And (C_NEXT_TASK_CODE Is Not Null) And 
                    (   C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                        And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                    )
        ";
        $v_village_id = replace_bad_char($v_village_id);
        if ($v_village_id > 0)
        {
            $conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_RECORD_TYPE";

        return $this->db->getAssoc($stmt);
    }

    //Dung tien do
    public function qry_count_in_schedule_record($v_village_id = 0)
    {
        $conditions = "
             C_BIZ_DAYS_EXCEED Is Null
                        And (Datediff(Now(), C_DOING_STEP_DEADLINE_DATE) <= 0)
                        And (C_NEXT_TASK_CODE Is Not Null)
        ";
        $v_village_id = replace_bad_char($v_village_id);
        if ($v_village_id > 0)
        {
            $conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }

    //Cham tien do
    public function qry_count_over_deadline_record($v_village_id = 0)
    {
        $conditions = "
             C_BIZ_DAYS_EXCEED Is Null
                        And (Datediff(Now(), C_DOING_STEP_DEADLINE_DATE) > 0)
                        And (C_NEXT_TASK_CODE Is Not Null)
        ";
        $v_village_id = replace_bad_char($v_village_id);
        if ($v_village_id > 0)
        {
            $conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }

    //Qua han, den ngay tra KQ ma chua 
    public function qry_count_expried_record($v_village_id = 0)
    {
        $conditions = "
            C_BIZ_DAYS_EXCEED Is Null And (datediff(C_RETURN_DATE, Now()) < 0)
                    And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                    And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
        ";
        
        $v_village_id = replace_bad_char($v_village_id);
        if ($v_village_id > 0)
        {
            $conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        
        $conditions .= $this->get_other_clause();
        if (Session::get('la_can_bo_cap_xa'))
        {
            $scope = "SELECT c_scope FROM t_r3_record_type Where PK_RECORD_TYPE = FK_RECORD_TYPE";
            $conditions .= " AND ($scope) In (0,1) ";
            $conditions .= " AND fk_village_id = " . Session::get('village_id');
        }
        $stmt = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }

    //Phai bo sung
    public function qry_count_supplement_record($v_village_id = 0)
    {
        $conditions = "C_NEXT_TASK_CODE like '%" . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE . "'";
        
        $v_village_id = replace_bad_char($v_village_id);
        if ($v_village_id > 0)
        {
            $conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }

    //Cho tra
    public function qry_count_waiting_for_return_record($v_village_id = 0)
    {
        $conditions = "(   C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                        Or C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                     )";
        
        $v_village_id = replace_bad_char($v_village_id);
        if ($v_village_id > 0)
        {
            $conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }

    //Da tra ket qua
    public function qry_count_returned_record($v_village_id = 0)
    {
        $conditions = " C_CLEAR_DATE Is Not Null ";
        
        $v_village_id = replace_bad_char($v_village_id);
        if ($v_village_id > 0)
        {
            $conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_record
                Where $conditions
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }

    //Dang tam dung
    public function qry_count_pausing_record($v_village_id = 0)
    {
        $conditions = " C_CLEAR_DATE Is Null
                        And C_IS_PAUSING=1 ";
        
        $v_village_id = replace_bad_char($v_village_id);
        if ($v_village_id > 0)
        {
            $conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }

    private function get_other_clause($v_village_id = 0)
    {
        $conditions = '';
        
        $v_village_id = replace_bad_char($v_village_id);
        if ($v_village_id > 0)
        {
            $conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        
        if (strlen($this->scope))
        {
            $scope = "Select C_SCOPE From t_r3_record_type Where FK_RECORD_TYPE = PK_RECORD_TYPE ";
            $conditions .= " And ($scope) In ({$this->scope})";
        }
        return $conditions;
    }
    
    
    public function qry_all_village()
    {
        $sql = 'Select 
                    PK_OU
                    , C_NAME 
                From t_cores_ou 
                Where C_LEVEL=3
                Order by C_INTERNAL_ORDER';
        return $this->db->getAll($sql);
    }

    //################################ 
    
    //danh sach cac buoc da thu hien cho den hien tai
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
     * Lay danh sach ho so theo tiêu chi đã lựa chọn
     * @param int $v_type_record_id Ma loai thu tuc
     * @param int $v_village_id Ma don vi tiep nhan ho so
     */
    public function qry_all_record_by_role($v_type_record_id = '',$v_status,$v_village_id)
    {
            //Xem theo trang
            page_calc($v_start, $v_end);
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            //Kiểm tra trạng thái xem danh sách hồ sơ
             $v_village_id = replace_bad_char($v_village_id);
            switch ($v_status)
            {
                case 1:
                    // Xem tất cả
                    $v_conditions = ' 1>0 ';
                     $arr_all_type_record   = $this->qry_all_record_type();
                     $arr_count_record_type = $this->get_count_type_record($arr_all_type_record[$v_type_record_id]['PK_RECORD_TYPE'],$v_village_id);
                     $v_total_record = isset($arr_count_record_type) ? $arr_count_record_type: 0;
                     
                    break;
                case 2:
                    // Mới tiếp nhận
                    $v_conditions = "  C_CLEAR_DATE Is Null And DATEDIFF(NOW(),C_RECEIVE_DATE)=0 ";

                    $arr_today_receice = $this->qry_count_today_receive_record($v_village_id);
                    $v_total_record          = isset($arr_today_receice[$v_type_record_id]) ? $arr_today_receice[$v_type_record_id] : 0;
                    break;
                case 3:
                    // Da ban giao
                    $v_task_code  = _CONST_XML_RTT_DELIM . _CONST_BAN_GIAO_ROLE;
                    $history_date = "  C_CLEAR_DATE Is Null And C_CLEAR_DATE Is Null And CAST(ExtractValue(C_XML_PROCESSING, '//step[contains(@code,''$v_task_code'')][position()=1]/datetime') AS DATETIME)";
                    $v_conditions   = " DATEDIFF(NOW(), $history_date)=0 ";
     
                    $arr_today_handover      = $this->qry_count_today_handover_record($v_village_id);
                    $v_total_record          = isset($arr_today_handover[$v_type_record_id]) ? $arr_today_handover[$v_type_record_id] : 0;

                    break;
                case 4:
                    // Dang thu ly
                    $v_conditions = " C_CLEAR_DATE Is Null And
                                    C_BIZ_DAYS_EXCEED Is Null And (C_NEXT_TASK_CODE Is Not Null) And 
                                       (   C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                                           And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                                       )
                                    ";

                    $arr_execing            = $this->qry_count_execing_record($v_village_id);
                    $v_total_record          = isset($arr_execing[$v_type_record_id]) ? $arr_execing[$v_type_record_id] : 0;

                    break;
                case 5:
                    //    Dung tien do
                    $v_conditions = " C_CLEAR_DATE Is Null And
                                    C_BIZ_DAYS_EXCEED Is Null
                                               And (Datediff(Now(), C_DOING_STEP_DEADLINE_DATE) <= 0)
                                               And (C_NEXT_TASK_CODE Is Not Null)
                               ";
     
                    $arr_schedule            = $this->qry_count_in_schedule_record($v_village_id);
                    $v_total_record          = isset($arr_schedule[$v_type_record_id]) ? $arr_schedule[$v_type_record_id] : 0;
                    
                    
                    break;
                case 6:
                    //  Cham tien do
                     $v_conditions = " C_CLEAR_DATE Is Null And C_BIZ_DAYS_EXCEED Is Null
                                    And (Datediff(Now(), C_DOING_STEP_DEADLINE_DATE) > 0)
                                    And (C_NEXT_TASK_CODE Is Not Null)
                               ";
  
                    $arr_over_deadline             = $this->qry_count_over_deadline_record($v_village_id);
                    $v_total_record          = isset($arr_over_deadline[$v_type_record_id]) ? $arr_over_deadline[$v_type_record_id] : 0;
                    
                    break;
                case 7:
                    // qua han
                    $v_conditions = " C_CLEAR_DATE Is Null And
                        C_BIZ_DAYS_EXCEED Is Null And (datediff(C_RETURN_DATE, Now()) < 0)
                                And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                                And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                    ";

                    if (Session::get('la_can_bo_cap_xa'))
                    {
                        $scope = "SELECT c_scope FROM t_r3_record_type Where PK_RECORD_TYPE = FK_RECORD_TYPE";
                        $v_conditions .= " AND ($scope) In (0,1) ";
                        $v_conditions .= " AND fk_village_id = " . Session::get('village_id');
                    }
                    
                    $arr_expried             = $this->qry_count_expried_record($v_village_id);
                    $v_total_record          = isset($arr_expried[$v_type_record_id]) ? $arr_expried[$v_type_record_id] : 0;
                    
                    break;
                case 8:
                    // Phai bo xung
                    $v_conditions = "C_CLEAR_DATE Is Null 
                                    And  C_NEXT_TASK_CODE like '%" . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE . "'";
        
            
                    $arr_supplement          = $this->qry_count_supplement_record($v_village_id);
                    $v_total_record          = isset($arr_supplement[$v_type_record_id]) ? $arr_supplement[$v_type_record_id] : 0;

                    break;
                case 9:
                    //  Đang tạm dừng (Chờ bổ sung/thuế)
                    $v_conditions = " C_CLEAR_DATE Is Null 
                                      And `R`.`C_PAUSE_DATE` is not null and `R`.`C_UNPAUSE_DATE` is null ";

                    $arr_pausing             = $this->qry_count_pausing_record($v_village_id);
                    $v_total_record          = isset($arr_pausing[$v_type_record_id]) ? $arr_pausing[$v_type_record_id] : 0;

                    
                    break;
                case 10:
                    //  Cho tra ket qua
                    $v_conditions = "C_CLEAR_DATE Is Null and (   C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                        Or C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                     )";
                    
                    $arr_watiting_for_return = $this->qry_count_waiting_for_return_record($v_village_id);
                    $v_total_record          = isset($arr_watiting_for_return[$v_type_record_id]) ? $arr_watiting_for_return[$v_type_record_id] : 0;
                    break;
                case 11:
                     // Đã trả hồ sơ
                   $v_conditions = " C_CLEAR_DATE Is Not Null  ";

                   $arr_returned = $this->qry_count_returned_record($v_village_id);
                   $v_total_record = isset($arr_returned[$v_type_record_id]) ? $arr_returned[$v_type_record_id] : 0;
                   break;
                   
               default :
                   return;
                   break;
            }
               
                if ($v_village_id > 0)
                {
                    $v_conditions .= " and FK_VILLAGE_ID=$v_village_id ";
                }
                if($v_type_record_id != '')
                {
                    $v_conditions .= " and FK_RECORD_TYPE = $v_type_record_id ";
                }
            $v_conditions .= $this->get_other_clause();

                $stmt       = "Select
                                    @rownum:=@rownum + 1 As RN  
                                    , $v_total_record AS TOTAL_RECORD
                                     ,1 as C_OWNER    
                                     ,Case When (R.C_REJECTED = 1) Then 3 When (R.C_REJECTED <> 1 And (R.C_CLEAR_DATE Is Not Null)) Then 2 Else 1 End as C_ACTIVITY
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
                                    ,(Select C_CONTENT  FROM t_r3_record_comment
                                            Where FK_RECORD=R.PK_RECORD
                                            Order By C_CREATE_DATE DESC
                                            Limit 1
                                    ) C_LAST_RECORD_COMMENT
                                    ,R.C_PAUSE_DATE
                                    ,R.C_UNPAUSE_DATE
                                    ,(case when ((`R`.`C_PAUSE_DATE` is not null) and ISNULL(`R`.`C_UNPAUSE_DATE`)) then 1 else 0 end) AS `C_IS_PAUSING` 
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
                                        From view_record R

                                        Where  
                                                $v_conditions 
                                                limit $v_start,$v_end

                                    )RID Left join t_r3_user_task UT On (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE And RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
                                                ) a Left join view_record R On a.PK_RECORD=R.PK_RECORD
                                                Order by R.C_RECEIVE_DATE DESC";
                return $this->db->getAll($stmt);
    }
    
    /**
     * Đếm tổng số hồ sơ theo mã thủ tục 
     * @param int $v_code : mã thủ tục
     * @return int
     */
    private function get_count_type_record($v_type_record_id,$v_village_id = 0)
    {
        $v_conditions = '';
        if ($v_village_id > 0)
        {
            $v_conditions .= " And FK_VILLAGE_ID=$v_village_id ";
        }
        $v_conditions .= " and FK_RECORD_TYPE = $v_type_record_id ";
        $v_conditions .= $this->get_other_clause();
        
        $stmt = "select
                    COUNT(PK_RECORD) as C_COUNT
                from view_record
                where 1>0  $v_conditions ";
        return $this->db->GetOne($stmt);
    }
    
    

    /**
     * Lay thong tin chi tiet cua mot Ho so
     */
    public function qry_single_record($p_record_id = '')
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
  
    }
   
    public function get_datetime_now()
    {
        $ret = NULL;
        if (DEBUG_MODE < 10)
        {
            $this->db->debug = 0;
        }
        if (DATABASE_TYPE == 'MSSQL')
        {
            $ret = $this->db->getOne('SELECT CONVERT(VARCHAR(19), GETDATE(), 121)');
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $ret = $this->db->getOne('SELECT NOW()');
        }
        $this->db->debug = DEBUG_MODE;
        return $ret;
    }
    
    
    ###### Thống kê tổng hợp các xã
    // dem tong so ho so moi tiep nhan cua tung xa
    public function qry_count_today_receive_record_xa()
    {
        $conditions = " DATEDIFF(NOW(),C_RECEIVE_DATE)=0 ";
        $conditions .= $this->get_other_clause();
        $stmt       = "SELECT 
                        FK_VILLAGE_ID
                        , COUNT(*) C_COUNT
                FROM view_processing_record
                WHERE $conditions
                GROUP BY FK_VILLAGE_ID";
        return $this->db->getAssoc($stmt);
    }
    
    public function qry_count_today_handover_record_xa()
    {
        $v_task_code  = _CONST_XML_RTT_DELIM . _CONST_BAN_GIAO_ROLE;
        $history_date = "CAST(ExtractValue(C_XML_PROCESSING, '//step[contains(@code,''$v_task_code'')][position()=1]/datetime') AS DATETIME)";
        $conditions   = " DATEDIFF(NOW(), $history_date)=0 ";
        
        $conditions .= $this->get_other_clause();

        $stmt = "Select
                      FK_VILLAGE_ID
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_VILLAGE_ID";
        
        return $this->db->getAssoc($stmt);
    }
    //Dem hs dang xu ly cua tung xa
    public function qry_count_execing_record_xa()
    {
        $conditions = "
             C_BIZ_DAYS_EXCEED Is Null And (C_NEXT_TASK_CODE Is Not Null) And 
                    (   C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                        And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                    )
        ";
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_VILLAGE_ID
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_VILLAGE_ID";

        return $this->db->getAssoc($stmt);
    }

    //Dung tien do cua tung xa
    public function qry_count_in_schedule_record_xa()
    {
        $conditions = "
             C_BIZ_DAYS_EXCEED Is Null
                        And (Datediff(Now(), C_DOING_STEP_DEADLINE_DATE) <= 0)
                        And (C_NEXT_TASK_CODE Is Not Null)
        ";
        
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_VILLAGE_ID
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_VILLAGE_ID";
        return $this->db->getAssoc($stmt);
    }

    //Cham tien do cua tung xa
    public function qry_count_over_deadline_record_xa()
    {
        $conditions = "
             C_BIZ_DAYS_EXCEED Is Null
                        And (Datediff(Now(), C_DOING_STEP_DEADLINE_DATE) > 0)
                        And (C_NEXT_TASK_CODE Is Not Null)
        ";
        
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_VILLAGE_ID
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_VILLAGE_ID";
        return $this->db->getAssoc($stmt);
    }

    //Qua han, den ngay tra KQ ma chua  cua tung xa
    public function qry_count_expried_record_xa()
    {
        $conditions = "
            C_BIZ_DAYS_EXCEED Is Null And (datediff(C_RETURN_DATE, Now()) < 0)
                    And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                    And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
        ";
        
        $conditions .= $this->get_other_clause();
        if (Session::get('la_can_bo_cap_xa'))
        {
            $scope = "SELECT c_scope FROM t_r3_record_type Where PK_RECORD_TYPE = FK_RECORD_TYPE";
            $conditions .= " AND ($scope) In (0,1) ";
            $conditions .= " AND fk_village_id = " . Session::get('village_id');
        }
        $stmt = "Select
                      FK_VILLAGE_ID
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_VILLAGE_ID";
        return $this->db->getAssoc($stmt);
    }

    //Phai bo sung cua tung xa
    public function qry_count_supplement_record_xa()
    {
        $conditions = "C_NEXT_TASK_CODE like '%" . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE . "'";
        
        
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_VILLAGE_ID
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_VILLAGE_ID";
        return $this->db->getAssoc($stmt);
    }

    //Cho tra cua tung xa
    public function qry_count_waiting_for_return_record_Xa()
    {
        $conditions = "(   C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                        Or C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                     )";
        
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_VILLAGE_ID
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_VILLAGE_ID";
        return $this->db->getAssoc($stmt);
    }

    //Da tra ket qua cua tung xa
    public function qry_count_returned_record_xa()
    {
        $conditions = " C_CLEAR_DATE Is Not Null ";
       
        
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_VILLAGE_ID
                    , COUNT(*) C_COUNT
                From view_record
                Where $conditions
                Group by FK_VILLAGE_ID";
        return $this->db->getAssoc($stmt);
    }

    //get count Dang tam dung cua tung xa
    public function qry_count_pausing_record_xa()
    {
        $conditions = " C_CLEAR_DATE Is Null
                        And C_IS_PAUSING=1 ";
        $conditions .= $this->get_other_clause();
        $stmt       = "Select
                      FK_VILLAGE_ID
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where $conditions
                Group by FK_VILLAGE_ID";
        return $this->db->getAssoc($stmt);
    }
//    Kiem tra ton tai cua don vi tiep nhan theo id
    public function qry_count_village_id($v_village)
    {
        $v_village = replace_bad_char($v_village);
        $stmt = "SELECT
                    PK_OU,
                    C_NAME
                FROM t_cores_ou
                WHERE PK_OU = $v_village";
        return $this->db->GetOne($stmt);
    }
        
}
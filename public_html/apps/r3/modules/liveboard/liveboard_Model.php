<?php
/**
Copyright (C) 2012 Tam Viet Tech.

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

    public $scope;

    function __construct()
    {
        parent::__construct();
    }

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

}
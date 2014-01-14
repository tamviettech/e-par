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

defined('DS') or die;

class notice_Model extends Model
{

    /**
     * @var \ADOConnection
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Hồ sơ chờ xử lý
     * @param string $role
     * @return array
     */
    function qry_all_notice_record_type($role)
    {
        $role        = replace_bad_char($role);
        $v_user_code = Session::get('user_code');

        $v_real_role  = strtoupper($role);
        $v_village_id = Session::get('village_id');
        $params       = array();

        switch ($v_real_role)
        {
            case _CONST_TIEP_NHAN_ROLE:
                $role                 = '%' . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;
                //$v_task_code_handover = "ExtractValue(C_XML_PROCESSING, '//step[Contains(@code, \"" . _CONST_BAN_GIAO_ROLE . "\")][1]/@code')";

                $cond   = "(C_LAST_TASK_CODE like ?)
                         And FK_VILLAGE_ID=?
                         And (C_NEXT_USER_CODE=? Or C_NEXT_USER_CODE Like ?)";
                $params = array($role, $v_village_id, $v_user_code, "%,$v_user_code,%");
                
                break;

            case _CONST_PHAN_CONG_LAI_ROLE:
                $role   = '%' . _CONST_XML_RTT_DELIM . strtoupper(_CONST_PHAN_CONG_ROLE);
                $cond   = "R.C_LAST_TASK_CODE like ? And R.C_LAST_USER_CODE=?";
                $params = array($role, $v_user_code);
                break;

            case _CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE:
                
                $role = '%' . _CONST_XML_RTT_DELIM . strtoupper($role);
                $stmt  = "Select RT.C_CODE as record_type_code
                            , RT.C_NAME as record_type_name
                            , a.COUNT_RECORD as count_record
                        From t_r3_record_type RT Right Join (
                                Select COUNT(*) COUNT_RECORD, FK_RECORD_TYPE
                                From t_r3_internet_record R
                                Where (R.C_NEXT_TASK_CODE like ?)
                                    And (R.C_NEXT_USER_CODE=? Or R.C_NEXT_USER_CODE like '%,$v_user_code,%' Or R.C_NEXT_USER_CODE Is Null)
                                    And R.C_DELETED=0
                                Group By FK_RECORD_TYPE ) a
                            On RT.PK_RECORD_TYPE=a.FK_RECORD_TYPE
                        Order By RT.C_CODE";
                $params = array($role, $v_user_code);
                return $this->db->getAll($stmt, $params);
                break;

            case _CONST_KIEM_TRA_TRUOC_HO_SO_ROLE:
                $role   = '%' . _CONST_XML_RTT_DELIM . strtoupper($role);
                $cond   = "(R.C_NEXT_USER_CODE=? Or R.C_NEXT_USER_CODE like '%,$v_user_code,%' Or R.C_NEXT_USER_CODE Is Null)
                        And R.C_DELETED=0
                        And R.C_IS_REAL_RECORD<>1
                        And R.C_CLEAR_DATE Is Null";
                $params = array($v_user_code);
                break;

            case _CONST_IN_PHIEU_TIEP_NHAN_ROLE:
                $cond   = "R.C_CREATE_BY=?";
                $params = array($v_user_code);
                break;

            case _CONST_TRA_KET_QUA_ROLE:
                $role   = '%' . _CONST_XML_RTT_DELIM . strtoupper($v_real_role);
                $cond   = "R.C_NEXT_TASK_CODE Like ?
                        And R.FK_VILLAGE_ID=?
                        And (R.C_NEXT_USER_CODE=? 
                                Or C_NEXT_USER_CODE Like ? 
                                Or R.C_NEXT_USER_CODE Is Null 
                                Or C_NEXT_CO_USER_CODE like ?)";
                $params = array($role, $v_village_id, $v_user_code, "%,$v_user_code,%", "%,$v_user_code,%");
                break;

            case _CONST_RUT_ROLE:
                $cond = "R.C_DELETED = 0 And R.C_CLEAR_DATE Is Null And R.C_REJECTED = 0 And R.C_CREATE_BY='$v_user_code'";
                break;

            default:
                $role   = '%' . _CONST_XML_RTT_DELIM . strtoupper($role);
                $cond   = " (
                                    (R.C_NEXT_TASK_CODE like ? And (R.C_NEXT_USER_CODE=? Or C_NEXT_USER_CODE Like '%,$v_user_code,%' Or R.C_NEXT_USER_CODE Is Null Or C_NEXT_CO_USER_CODE like '%,$v_user_code,%'))
                                    OR (C_NEXT_NO_CHAIN_TASK_CODE Like ? And C_NEXT_NO_CHAIN_USER_CODE Like '%,$v_user_code,%') 
                                  )";
                $params = array($role, $v_user_code, $role);
        } //switch
        $stmt = "Select RT.C_CODE as record_type_code
                            , RT.C_NAME as record_type_name
                            , a.COUNT_RECORD as count_record
                    From t_r3_record_type RT Right Join (
                            Select COUNT(*) COUNT_RECORD, FK_RECORD_TYPE
                            From view_processing_record R
                            Where $cond
                            Group By FK_RECORD_TYPE ) a
                        On RT.PK_RECORD_TYPE=a.FK_RECORD_TYPE
                    Order By RT.C_CODE";
        return $this->db->GetAll($stmt, $params);
    }

    /**
     * Hồ sơ bị trả lại trong vòng 3 ngày
     * @param string $role
     * @return array
     */
    function qry_all_rollbacked($role)
    {
        $v_real_role   = strtoupper($role);
        $v_village_id  = Session::get('village_id');
        $v_user_code   = Session::get('user_code');
        
        if ($v_real_role == _CONST_TIEP_NHAN_ROLE)
        {
            $v_real_role = _CONST_BAN_GIAO_ROLE;
        }

        //echo '<hr>Thong bao hồ sơ bi tra lai';
        //thong bao HS bi tra lai
        /*
        $rollback_date = " extractvalue(C_XML_PROCESSING, \"//step[last()][contains(@code,'CHUYEN_LAI_BUOC_TRUOC') or contains(@code,'KHONG_NHAN_HO_SO') ]/datetime\")";
        $sub_query     = "
            Select PK_RECORD From t_r3_record
            Where C_DELETED = 0
                And FK_VILLAGE_ID = ?
                And (C_NEXT_USER_CODE = ? Or C_NEXT_USER_CODE Like ?) 
                And $rollback_date <> ''
                And DateDiff(NOW(), $rollback_date) <= 3   
                And C_NEXT_TASK_CODE Like ?
        ";
        $params        = array($v_village_id, $v_user_code, "%,$v_user_code,%"
            , '%' . _CONST_XML_RTT_DELIM . $v_real_role);

        $stmt = "
            Select 
                rt.C_CODE As C_TYPE_CODE, rt.C_NAME As C_TYPE_NAME
                , a.C_RECORD_NO, a.C_XML_PROCESSING, a.PK_RECORD, a.C_CITIZEN_NAME
            From t_r3_record a
            Inner Join ($sub_query) b   On a.PK_RECORD = b.PK_RECORD
            Inner Join t_r3_record_type rt  On rt.PK_RECORD_TYPE = a.FK_RECORD_TYPE
        ";
        return $this->db->GetAll($stmt, $params);
        */
        
        $stmt = " Select
                    RT.C_CODE As C_TYPE_CODE
                    , RT.C_NAME As C_TYPE_NAME
                    , R.C_RECORD_NO
                    , R.C_XML_PROCESSING
                    , R.PK_RECORD
                    , R.C_CITIZEN_NAME
                From view_processing_record R
                    Left Join t_r3_record_type RT  
                    On R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                Where R.FK_VILLAGE_ID = $v_village_id
                    And (R.C_NEXT_USER_CODE = '$v_user_code' Or R.C_NEXT_USER_CODE Like '%,$v_user_code,%') 
                    And (R.C_LAST_TASK_CODE = 'CHUYEN_LAI_BUOC_TRUOC' Or R.C_LAST_TASK_CODE = 'KHONG_NHAN_HO_SO')
                    And C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . $v_real_role . "'";
        return $this->db->GetAll($stmt);
    }
}
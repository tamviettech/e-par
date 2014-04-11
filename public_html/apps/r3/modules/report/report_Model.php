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

require_once __DIR__ . '/../record/record_Model.php';

class report_Model extends record_Model
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
    
    public function qry_all_record_type_with_spec_code()
    {
        $sql = 'Select PK_RECORD_TYPE, C_SPEC_CODE, C_CODE, C_NAME From t_r3_record_type Where C_STATUS>0 Order By C_ORDER';
        if (CONST_USE_ADODB_CACHE_FOR_REPORT)
        {
            return $this->db->CacheGetAll($sql);
        }

        return $this->db->GetAll($sql);
    }

    public function qry_all_report_data_6($period, $arr_all_spec, $group_code = '')
    {
        $v_village_id = Session::get('village_id');
        $arr_all_report_data = Array();
        $v_report_subtitle   = '[Kỳ báo cáo]';
        
        //du lieu 
        $year       = intval(get_request_var('year'));
        $month      = intval(get_request_var('month'));
        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd(get_request_var('begin_date',''));
        $v_end_date   = jwDate::ddmmyyyy_to_yyyymmdd(get_request_var('end_date',''));
        
        $condition = '';
        
//        //condition
//        if($group_code != '')
//        {
//            $condition = " And ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') = '$group_code'";
//        }
            
        
        switch ($period)
        {
            case 'year':
                //khai bao condition
                $mssql_condition = $condition;
                $mysql_condition = $condition;
                
                //Bao cap tiep nhan theo nam
                if($year != '' && $year != null)
                {
                    $the_date = $year . '-01-' . '01';
                    
                    $mssql_condition = $mssql_condition . " AND DATEDIFF(year, '$the_date', R.C_RECEIVE_DATE)=0";
                    $mysql_condition = $mysql_condition . " AND YEAR(R.C_RECEIVE_DATE) = $year";
                }
                

                $sql = '';
                $i   = 0;
                foreach ($arr_all_spec as $code => $name)
                {
                    $sql .= ($i > 0) ? ' Union All ' : '';
                    if ($this->is_mssql())
                    {
                        $sql .= "SELECT
                                    '$code' AS C_SPEC_CODE
                                    ,'$name' as C_SPEC_NAME
                                    ,(
                                      SELECT COUNT(*)
                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                                      WHERE (1>0) $mssql_condition AND RT.C_SPEC_CODE='$code'
                                     ) AS C_COUNT";
                    }
                    elseif ($this->is_mysql())
                    {
                        $sql .= "SELECT
                                    '$code' AS C_SPEC_CODE
                                    ,'$name' as C_SPEC_NAME
                                    ,(
                                      SELECT COUNT(*)
                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                                      WHERE (1>0) $mysql_condition AND RT.C_SPEC_CODE='$code' And R.FK_VILLAGE_ID = $v_village_id
                                     ) AS C_COUNT";
                    }

                    $i++;
                }
                
                $v_year = get_request_var('year');
                $v_report_subtitle = '';
                if($v_year != '')
                {
                    $v_report_subtitle = 'Năm ' . get_request_var('year');
                }
                
                //Nếu là năm quá khứ thì cache
                if (CONST_USE_ADODB_CACHE_FOR_REPORT && $year < Date('Y'))
                {
                    $arr_all_report_data = $this->db->cacheGetAll($sql);
                }
                else
                {
                    $arr_all_report_data = $this->db->getAll($sql);
                }

                break;

            case 'month':
                
                //khai bao condition
                $mssql_condition = $condition;
                $mysql_condition = $condition;
                
                //Bao cap tiep nhan theo nam
                if($year != '' && $year != null && $month != '' && $month != null)
                {
                    $the_date = $year . '-' . $month . '-' . '01';
                    
                    $mssql_condition = $mssql_condition . " AND DATEDIFF(month, '$the_date', R.C_RECEIVE_DATE)=0";
                    $mysql_condition = $mysql_condition . " AND YEAR(R.C_RECEIVE_DATE)=$year AND MONTH(R.C_RECEIVE_DATE)=$month";
                }

                $sql = '';
                $i   = 0;
                foreach ($arr_all_spec as $code => $name)
                {
                    $sql .= ($i > 0) ? ' Union All ' : '';
                    if ($this->is_mssql())
                    {
                        $sql .= "SELECT
                                    '$code' AS SPEC_CODE
                                    ,'$name' as C_SPEC_NAME
                                    ,(SELECT COUNT(*)
                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                                      WHERE (1>0) $mssql_condition AND RT.C_SPEC_CODE='$code'
                                     ) AS C_COUNT";
                    }
                    elseif ($this->is_mysql())
                    {
                        $sql .= "SELECT
                                    '$code' AS SPEC_CODE
                                    ,'$name' as C_SPEC_NAME
                                    ,(SELECT COUNT(*)
                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                                      WHERE (1>0) $mysql_condition AND RT.C_SPEC_CODE='$code' And R.FK_VILLAGE_ID = $v_village_id
                                     ) AS C_COUNT";
                    }
                    $i++;
                }
                $v_report_subtitle = 'Tháng ' . $month . ' năm ' . $year;

                //Nếu là tháng quá khứ thì dùng cache
                if (CONST_USE_ADODB_CACHE_FOR_REPORT && $month < Date('m') && $year <= Date('Y'))
                {
                    $arr_all_report_data = $this->db->CachegetAll($sql);
                }
                else
                {
                    $arr_all_report_data = $this->db->getAll($sql);
                }

                break;

            case 'week':
                //khai bao condition   
                $mssql_condition = $condition;
                $mysql_condition = $condition;
                
                //Bao cap tiep nhan theo nam
                if($v_begin_date != '' && $v_begin_date != null && $v_end_date != '' && $v_end_date != null)
                {
                    $the_date = $year . '-' . $month . '-' . '01';
                    
                    $mssql_condition = $mssql_condition . " AND DATEDIFF(day, '$v_begin_date', R.C_RECEIVE_DATE)>=0
                                                            AND DATEDIFF(day, '$v_end_date', R.C_RECEIVE_DATE)<=0";
                    
                    $mysql_condition = $mysql_condition . " AND DATEDIFF('$v_begin_date',R.C_RECEIVE_DATE)<=0
                                                            AND DATEDIFF('$v_end_date',R.C_RECEIVE_DATE)>=0";
                }
                
                
                $sql = '';
                $i   = 0;
                foreach ($arr_all_spec as $code => $name)
                {
                    $sql .= ($i > 0) ? ' Union All ' : '';
                    if ($this->is_mssql())
                    {
                        $sql .= "SELECT
                                    '$code' AS SPEC_CODE
                                    ,'$name' as C_SPEC_NAME
                                    ,(SELECT COUNT(*)
                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                                      WHERE (1>0) $mssql_condition AND RT.C_SPEC_CODE='$code'
                                     ) AS C_COUNT";
                    }
                    elseif ($this->is_mysql())
                    {
                        $sql .= "SELECT
                                    '$code' AS SPEC_CODE
                                    ,'$name' as C_SPEC_NAME
                                    ,(SELECT COUNT(*)
                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                                      WHERE (1>0) $mysql_condition AND RT.C_SPEC_CODE='$code' And R.FK_VILLAGE_ID = $v_village_id
                                     ) AS C_COUNT";
                    }
                    $i++;
                }

                $v_report_subtitle = 'Tuần từ ' . get_request_var('begin_date') . ' đến ' . get_request_var('end_date');

                //Dung Cache nếu là tuần quá khứ
                if ($v_end_date < 1)
                {
                    
                }
                $arr_all_report_data = $this->db->getAll($sql);
                break;

            case 'date':
            default:
                $date = jwDate::ddmmyyyy_to_yyyymmdd(get_request_var('date'));

                $sql = '';
                $i   = 0;
                foreach ($arr_all_spec as $code => $name)
                {
                    $sql .= ($i > 0) ? ' Union All ' : '';
                    if ($this->is_mssql())
                    {
                        $sql .= "SELECT
                                    '$code' AS SPEC_CODE
                                    ,'$name' as C_SPEC_NAME
                                    ,(SELECT COUNT(*)
                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                                      WHERE DATEDIFF(day, '$date', R.C_RECEIVE_DATE)=0
                                        AND RT.C_SPEC_CODE='$code'
                                     ) AS C_COUNT";
                    }
                    elseif ($this->is_mysql())
                    {
                        $sql .= "SELECT
                                    '$code' AS SPEC_CODE
                                    ,'$name' as C_SPEC_NAME
                                    ,(SELECT COUNT(*)
                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                                      WHERE DATEDIFF('$date',R.C_RECEIVE_DATE)=0
                                        AND RT.C_SPEC_CODE='$code' And R.FK_VILLAGE_ID = $v_village_id
                                     ) AS C_COUNT";
                    }

                    $i++;
                }
                $v_report_subtitle = 'Ngày ' . get_request_var('date');

                //Cache nếu là ngày quá khứ
                if ($date < 1)
                {
                    
                }
                $arr_all_report_data = $this->db->getAll($sql);

                break;
        }

        $ret['report_subtitle']     = $v_report_subtitle;
        $ret['arr_all_report_data'] = $arr_all_report_data;

        return $ret;
    }

    /**
     * Lấy danh sách phòng ban có tham gia vào việc thụ lý hồ sơ
     * @param Boolean $use_cache Co sung dung cache khong?
     */
    public function qry_all_exec_group($use_cache = FALSE)
    {
        $stmt   = 'Select Distinct
                    G.C_CODE
                    ,G.C_NAME
                From t_cores_group G Left join t_r3_user_task UT On UT.C_GROUP_CODE = G.C_CODE
                Where UT.C_TASK_CODE LIKE ?
                Order by G.C_NAME';
        $params = array('%' . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE);
        return ($use_cache) ? $this->db->cacheGetAssoc($stmt, $params) : $this->db->getAssoc($stmt, $params);
    }

    /**
     * Lay danh sach tat ca phong ban
     * @param type $use_cache
     */
    public function qry_all_group()
    {
        $vilage_id = replace_bad_char($_SESSION['village_id']);
        $condition = '';
        if((int) $vilage_id != 0) //neu la nguoi cap xa chi lay group ma ho thuoc ve
        {
            $condition = ' And OU.C_LEVEL = 3 And PK_OU = ' . $vilage_id;
        }
        $stmt = 'Select Distinct
                    G.C_CODE
                    ,G.C_NAME
                    ,OU.C_LEVEL 
                From t_cores_group G LEFT JOIN t_cores_ou OU
                ON G.FK_OU = OU.PK_OU Where (1>0) ' . $condition . ' Order by OU.C_LEVEL';
        $arr_all_group = $this->db->getAssoc($stmt);
        if((int) $vilage_id == 0)
        {
            $sql = 'SELECT C_NAME FROM t_cores_ou WHERE FK_OU = -1';
            $district_name = $this->db->getOne($sql);
            $arr_all_group = array_merge(array(0=>array('C_NAME'=>$district_name,'C_LEVEL'=>'1')),$arr_all_group);
        }
        return $arr_all_group;
    }
    /**
     * lay tat ca ou
     * @return type array
     */
    public function qry_all_ou()
    {
        $sql = "SELECT DISTINCT
                    CASE WHEN(FK_OU = -1) THEN -1  ELSE PK_OU END AS PK_OU,
                    C_NAME
                  FROM t_cores_ou";
        
        return $this->db->getAssoc($sql);
    }

//    public function qry_all_report_data_3($date)
//    {
//        //Danh sach phong ban chuyen mon
//        $arr_all_exec_group = $this->qry_all_exec_group(CONST_USE_ADODB_CACHE_FOR_REPORT);
//        $v_village_id       = Session::get('village_id');
//
//        //1.Tong so HS tiep nhan trong ngay theo tung phong ban
//        $sql = '';
//        foreach ($arr_all_exec_group as $code => $name)
//        {
//            $sql .= ($sql != '') ? ' Union All ' : '';
//            $sql .= "Select
//                        COUNT(*) C_COUNT
//                      , '$code' AS C_GROUP_CODE
//                      From view_record R
//                      Where ExtractValue(C_XML_PROCESSING, '//step[to_group_code=''$code'' and contains(datetime,''$date'')][last()]/@code[last()]') != ''";
//        }
//        $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
//        if (is_past_date($date) && CONST_USE_ADODB_CACHE_FOR_REPORT)
//        {
//            $arr_count_received_record_by_group = $this->db->CacheGetAll($sql);
//        }
//        else
//        {
//            $arr_count_received_record_by_group = $this->db->getAll($sql);
//        }
//
//        //2. Tong so HS đang xử lý trong ngay theo tung phong ban
//        $sql                                  = "Select
//                    COUNT(*) C_COUNT
//                  , ExtractValue(C_XML_PROCESSING, '//next_task/@group_code') C_GROUP_CODE
//                From view_processing_record
//                Where
//                  (   C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE . "'
//                      OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE . "'
//                      OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_ROLE . "'
//                      OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_YEU_CAU_THU_LY_LAI_ROLE . "'
//                      OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_BO_SUNG_ROLE . "'
//                  )
//                And FK_VILLAGE_ID = $v_village_id
//                Group by C_GROUP_CODE";
//        $arr_count_processing_record_by_group = $this->db->getAll($sql);
//
//        //3. Tong so HS đang cham tien do trong ngay theo tung phong ban
//        $sql                               = "Select
//                    COUNT(*) C_COUNT
//                  , ExtractValue(C_XML_PROCESSING, '//next_task/@group_code') C_GROUP_CODE
//                From view_processing_record
//
//                Where C_BIZ_DAYS_EXCEED IS NULL
//                And (DATEDIFF(C_DOING_STEP_DEADLINE_DATE, NOW()) < 0)
//                And (C_NEXT_TASK_CODE IS NOT NULL)
//                And FK_VILLAGE_ID = $v_village_id
//                Group by C_GROUP_CODE";
//        $arr_count_delayed_record_by_group = $this->db->getAll($sql);
//
//        //4. Tong hồ sơ đang quá hạn trong ngày theo tung phong ban
//        $sql                                = "Select
//                    COUNT(*) C_COUNT
//                  , ExtractValue(C_XML_PROCESSING, '//next_task/@group_code') C_GROUP_CODE
//                From view_processing_record
//
//                Where C_BIZ_DAYS_EXCEED Is Null
//                    And (datediff(C_RETURN_DATE, Now()) < 0)
//                    And FK_VILLAGE_ID = $v_village_id
//                Group by C_GROUP_CODE";
//        $arr_count_overtime_record_by_group = $this->db->getAll($sql);
//
//        //return
//        $ret['arr_count_received_record_by_group']   = $arr_count_received_record_by_group;
//        $ret['arr_count_processing_record_by_group'] = $arr_count_processing_record_by_group;
//        $ret['arr_count_delayed_record_by_group']    = $arr_count_delayed_record_by_group;
//        $ret['arr_count_overtime_record_by_group']   = $arr_count_overtime_record_by_group;
//
//        return $ret;
//    }

    /**
     * Cham tien do
     * @return type
     */
    public function qry_all_report_data_12()
    {
        $v_village_id = Session::get('village_id');
        $group_code   = get_request_var('group','');
        $spec_code    = get_request_var('spec_code','');
        $group_level  = get_request_var('group_level','');
            
        $group_condition = '';
        $spec_condition = '';
        
        //neu co ou
        if($v_village_id != 0)
        {
            $group_condition = " AND FK_VILLAGE_ID = $v_village_id";
        }
        else 
        {
            if($group_code != '')//ton tai group code
            {
                if((int)$group_level == 3)//neu yeu cau bao cao cap xa
                {
                    //lay id ou cua xa duoc chon
                    $sql = "SELECT
                                PK_OU
                              FROM t_cores_group G
                                LEFT JOIN t_cores_ou OU
                                  ON G.FK_OU = OU.PK_OU
                              WHERE C_CODE = '$group_code'";
                    $v_village_id = $this->db->getOne($sql);
                    
                    $group_condition = " AND FK_VILLAGE_ID = $v_village_id";
                }
                else if((int) $group_level == 1 && $group_code == '0') //can bo cap huyen chi xem bao cao tai huyen
                {
                    $group_condition = " AND FK_VILLAGE_ID = 0";
                }
                else 
                {
                    $group_condition = " And ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') = '$group_code'";
                }
            }
        }
        //neu co spec code
        if($spec_code != '' && $spec_code != null)
        {
            $spec_condition = " AND RT.C_SPEC_CODE = '$spec_code'";
        }
        
        $sql          = "SELECT
                    ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') AS C_DOING_GROUP_CODE
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
                    , 8 as TT
                FROM
                (
                    SELECT
                        RID.`PK_RECORD`
                      , UT.C_TASK_CODE
                      , UT.C_STEP_TIME
                    FROM
                    (
                        SELECT
                            PK_RECORD
                          , C_NEXT_TASK_CODE
                          , C_NEXT_USER_CODE
                          , FK_RECORD_TYPE
                        FROM t_r3_record
                        WHERE C_BIZ_DAYS_EXCEED IS NULL
                            AND C_DELETED <> 1 AND C_CLEAR_DATE IS NULL
                            AND (DATEDIFF(C_DOING_STEP_DEADLINE_DATE, NOW()) < 0)
                            AND (C_NEXT_TASK_CODE IS NOT NULL)
                            AND (C_PAUSE_DATE IS NULL OR (C_PAUSE_DATE IS NOT NULL AND C_UNPAUSE_DATE IS NOT NULL))
                            $group_condition
                    )  RID LEFT JOIN t_r3_user_task UT ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
                    LEFT JOIN t_r3_record_type RT 
                    ON RID.FK_RECORD_TYPE = RT.PK_RECORD_TYPE 
                    WHERE (1>0) $spec_condition
                ) a LEFT JOIN view_record R ON a.PK_RECORD=R.PK_RECORD
                ORDER BY C_DOING_GROUP_CODE, R.C_RECEIVE_DATE DESC
                ";
        return $this->db->getAll($sql);
    }

    /**
     * Bao cao thu tuc hanh chinh qua han
     */
    public function qry_all_report_data_13()
    {
        $v_village_id = Session::get('village_id');
        $group_code = get_request_var('group','');
        $spec_code = get_request_var('spec_code','');
        $group_level  = get_request_var('group_level','');
        
        $group_condition = '';
        $spec_condition = '';
        //neu co ou
        if($v_village_id != 0)
        {
            $group_condition = " AND FK_VILLAGE_ID = $v_village_id";
        }
        else 
        {
            if($group_code != '')//ton tai group code
            {
                if((int)$group_level == 3)//neu yeu cau bao cao cap xa
                {
                    //lay id ou cua xa duoc chon
                    $sql = "SELECT
                                PK_OU
                              FROM t_cores_group G
                                LEFT JOIN t_cores_ou OU
                                  ON G.FK_OU = OU.PK_OU
                              WHERE C_CODE = '$group_code'";
                    $v_village_id = $this->db->getOne($sql);
                    
                    $group_condition = " AND FK_VILLAGE_ID = $v_village_id";
                }
                else if((int) $group_level == 1 && $group_code == '0') //can bo cap huyen chi xem bao cao tai huyen
                {
                    $group_condition = " AND FK_VILLAGE_ID = 0";
                }
                else 
                {
                    $group_condition = " And ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') = '$group_code'";
                }
            }
        }
        
        //neu co spec code
        if($spec_code != '' && $spec_code != null)
        {
            $spec_condition = " AND RT.C_SPEC_CODE = '$spec_code'";
        }
        
        $sql          = "SELECT
                    ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') AS C_DOING_GROUP_CODE
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
                    , 8 as TT
                FROM
                (
                    SELECT
                        RID.`PK_RECORD`
                      , UT.C_TASK_CODE
                      , UT.C_STEP_TIME
                    FROM
                    (
                        SELECT
                            PK_RECORD
                          , C_NEXT_TASK_CODE
                          , C_NEXT_USER_CODE
                          , FK_RECORD_TYPE
                        From view_processing_record
                        Where C_IS_PAUSING = 0 
                            AND C_BIZ_DAYS_EXCEED Is Null
                            And (datediff(C_RETURN_DATE, Now()) < 0)
                            $group_condition
                    )  RID LEFT JOIN t_r3_user_task UT ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
                    LEFT JOIN t_r3_record_type RT 
                    ON RID.FK_RECORD_TYPE = RT.PK_RECORD_TYPE 
                    WHERE (1>0) $spec_condition
                ) a LEFT JOIN view_record R ON a.PK_RECORD=R.PK_RECORD
                ORDER BY C_DOING_GROUP_CODE, R.C_RECEIVE_DATE DESC
                ";
        return $this->db->getAll($sql);
    }

    /**
     * Bao cao thu tuc hanh chinh dang bo sung
     */
    public function qry_all_report_data_14()
    {
        $v_village_id = Session::get('village_id');
//        $group_code = get_request_var('group','');
        $spec_code = get_request_var('spec_code','');
        
        $spec_condition = '';
                //neu co spec code
        if($spec_code != '' && $spec_code != null)
        {
            $spec_condition = " AND RT.C_SPEC_CODE = '$spec_code'";
        }
        
        $sql          = "SELECT
                    ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') AS C_DOING_GROUP_CODE
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
                    , 8 as TT
                    , CAST(DATE_FORMAT(ExtractValue(C_XML_PROCESSING, '//step[contains(@code,''" . _CONST_XML_RTT_DELIM . _CONST_THONG_BAO_BO_SUNG_ROLE . "'')][last()]/datetime'),'%d-%m-%Y %H:%i:%s') AS CHAR ) as C_ANNOUNCE_DATE
                FROM
                (
                    SELECT
                        RID.`PK_RECORD`
                      , UT.C_TASK_CODE
                      , UT.C_STEP_TIME
                    FROM
                    (
                        SELECT
                            PK_RECORD
                          , C_NEXT_TASK_CODE
                          , C_NEXT_USER_CODE
                          , FK_RECORD_TYPE
                         From view_processing_record
                        Where C_NEXT_TASK_CODE like '%" . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE . "' And FK_VILLAGE_ID = $v_village_id 
                    )  RID LEFT JOIN t_r3_user_task UT ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
                    LEFT JOIN t_r3_record_type RT 
                    ON RID.FK_RECORD_TYPE = RT.PK_RECORD_TYPE 
                    WHERE (1>0) $spec_condition
                ) a LEFT JOIN view_record R ON a.PK_RECORD=R.PK_RECORD
                ORDER BY R.C_RECEIVE_DATE DESC
                ";
        return $this->db->getAll($sql);
    }

    /**
     * Bao cao thu tuc hanh chinh bi tu choi
     */
    public function qry_all_report_data_15($begin_date_yyyymmdd = '', $end_date_yyyymmdd = '',$group_code = '', $spec_code= '')
    {
        $v_village_id    = Session::get('village_id');
        $spec_condition  = '';
        $date_condition  = '';
        
        //neu co ou
//        if($group_code != '')
//        {
//            $group_condition = " And ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') = '$group_code'";
//        }
        //neu co spec code
        if($spec_code != '' && $spec_code != null)
        {
            $spec_condition = " AND RT.C_SPEC_CODE = '$spec_code'";
        }
        //ngay thang
        if($begin_date_yyyymmdd != '')
        {
            $date_condition = " And (DATEDIFF('$begin_date_yyyymmdd',C_REJECTED_DATE) <=0)";
        }
        
        if($end_date_yyyymmdd != '')
        {
            $date_condition .= " And (DATEDIFF('$end_date_yyyymmdd', C_REJECTED_DATE) >=0)";
        }
        $sql          = "SELECT
                    ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') AS C_DOING_GROUP_CODE
                    ,Case When (R.C_REJECTED = 1) Then 3 When (R.C_REJECTED <> 1 And (R.C_CLEAR_DATE Is Not Null)) Then 2 Else 1 End as C_ACTIVITY
                    , CASE WHEN (DATEDIFF(NOW(), R.C_DOING_STEP_DEADLINE_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)<0 ) END AS C_DOING_STEP_DAYS_REMAIN
                    , CASE WHEN (DATEDIFF(NOW(),R.C_RETURN_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)<0 ) END AS C_RETURN_DAYS_REMAIN
                    , R.PK_RECORD
                    , R.FK_RECORD_TYPE
                    , R.C_RECORD_NO
                    , CAST(R.C_RECEIVE_DATE AS CHAR(19)) AS C_RECEIVE_DATE
                    , CAST(DATE_FORMAT(R.C_RECEIVE_DATE,'%d-%m-%Y %H:%i:%s') AS CHAR) AS C_RECEIVE_DATE_DDMMYYYY
                    , CAST(R.C_RETURN_DATE AS CHAR(19)) AS C_RETURN_DATE
                    , CAST(DATE_FORMAT(R.C_RETURN_DATE,'%d-%m-%Y %H:%i:%s') AS CHAR) AS C_RETURN_DATE_DDMMYYYY
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
                    , 8 as TT
                    , CAST(DATE_FORMAT(C_REJECTED_DATE,'%d-%m-%Y %H:%i:%s') AS CHAR) AS C_REJECTED_DATE_DDMMYYYY
                FROM
                (
                    SELECT
                        RID.`PK_RECORD`
                      , UT.C_TASK_CODE
                      , UT.C_STEP_TIME
                      , C_REJECTED_DATE
                    FROM
                    (
                        SELECT
                            PK_RECORD
                          , C_NEXT_TASK_CODE
                          , C_NEXT_USER_CODE
                          , CAST(DATE_FORMAT(ExtractValue(C_XML_PROCESSING, '//step[@code=''REJECT''][last()]/datetime'),'%Y-%m-%d %H:%i:%s') AS CHAR) AS C_REJECTED_DATE
                          , FK_RECORD_TYPE
                        From view_record
                        Where C_REJECTED=1 And FK_VILAGE_ID = $v_village_id 
                    )  RID LEFT JOIN t_r3_user_task UT ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
                    LEFT JOIN t_r3_record_type RT 
                    ON RID.FK_RECORD_TYPE = RT.PK_RECORD_TYPE 
                    WHERE (1>0) $spec_condition
                ) a LEFT JOIN view_record R ON a.PK_RECORD=R.PK_RECORD
                Where (1>0) $date_condition
                ORDER BY R.C_RECEIVE_DATE DESC
                ";
        return $this->db->getAll($sql);
    }

    public function qry_all_report_data_16($v_spec_code, $v_record_type_id, $v_begin_date_yyyymmdd, $v_end_date_yyyymmdd,$group_code='',$group_level)
    {
        $v_village_id = Session::get('village_id');
         //neu co ou
        $group_condition = '';
        //neu co ou
        if($v_village_id != 0) //neu la nguoi o xa
        {
            $group_condition = " AND FK_VILLAGE_ID = $v_village_id";
        }
        else //neu la nguoi o huyen
        {
            
            if($group_code != '')//ton tai group code
            {
                if((int)$group_level == 3)//neu yeu cau bao cao cap xa
                {
                    //lay id ou cua xa duoc chon
                    $sql = "SELECT
                                PK_OU
                              FROM t_cores_group G
                                LEFT JOIN t_cores_ou OU
                                  ON G.FK_OU = OU.PK_OU
                              WHERE C_CODE = '$group_code'";
                    $v_village_id = $this->db->getOne($sql);
                    
                    $group_condition = " AND FK_VILLAGE_ID = $v_village_id";
                }
                else if((int) $group_level == 1 && $group_code == '0') //can bo cap huyen chi xem bao cao tai huyen
                {
                    $group_condition = " AND FK_VILLAGE_ID = 0";
                }
                else 
                {
                    $group_condition = " And ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') = '$group_code'";
                }
            }
        }
        
        $arr_report_filter = Array();
        $sql               = "Select
                    C_CITIZEN_NAME
                    ,FK_RECORD_TYPE
                    ,C_RECORD_NO
                    ,C_RECEIVE_DATE
                    ,C_RETURN_DATE
                    ,CAST(DATE_FORMAT(ExtractValue(C_XML_PROCESSING, '//step[@code=''REJECT''][last()]/datetime'),'%d-%m-%Y %H:%i:%s') AS CHAR(19)) AS C_REJECTED_DATE_DDMMYYYY
                    ,C_CLEAR_DATE
                    ,C_XML_DATA
              From view_record
              Where 1>0 
              $group_condition";
        if ($v_spec_code != '')
        {
            //Danh sach linh vuc
            $arr_all_spec = $this->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
            array_push($arr_report_filter, array('Lĩnh vực: ', $arr_all_spec[$v_spec_code]));

            if ($v_record_type_id > 0)
            {
                $sql .= " And FK_RECORD_TYPE=$v_record_type_id";

                //Danh sach loai thu tuc
                $arr_all_record_type = $this->db->cacheGetAssoc("Select PK_RECORD_TYPE, C_CODE, C_NAME From t_r3_record_type");
                array_push($arr_report_filter, array('Loại hồ sơ: ', $arr_all_record_type[$v_record_type_id]['C_CODE'] . ' - ' . $arr_all_record_type[$v_record_type_id]['C_NAME']));
            }
            else
            {
                if (CONST_USE_ADODB_CACHE_FOR_REPORT)
                {
                    $v_record_type_id_list = implode(',', $this->db->CacheGetCol("Select PK_RECORD_TYPE From t_r3_record_type RT Where C_SPEC_CODE='$v_spec_code'"));
                }
                else
                {
                    $v_record_type_id_list = implode(',', $this->db->getCol("Select PK_RECORD_TYPE From t_r3_record_type RT Where C_SPEC_CODE='$v_spec_code'"));
                }
                $sql .= " And FK_RECORD_TYPE In ($v_record_type_id_list)";
            }
        }

        if ($v_begin_date_yyyymmdd != '')
        {
            $sql .= " And (DATEDIFF('$v_begin_date_yyyymmdd',C_RECEIVE_DATE) <=0)";

            array_push($arr_report_filter, array('Tiếp nhận từ ngày: ', $v_begin_date_yyyymmdd));
        }
        if ($v_end_date_yyyymmdd != '')
        {
            $sql .= " And (DATEDIFF('$v_end_date_yyyymmdd', C_RECEIVE_DATE) >=0)";
            array_push($arr_report_filter, array('đến ngày: ', $v_end_date_yyyymmdd));
        }

        $ret['report_data']   = $this->db->getAll($sql);
        $ret['report_filter'] = $arr_report_filter;
        return $ret;
    }

    public function qry_all_report_data_7($v_begin_date_yyyymmdd, $v_end_date_yyyymmdd)
    {
        //Danh muc linh vuc
        $v_village_id = Session::get('village_id');
        $date_condition = '';
        //ngay thang
        if($v_begin_date_yyyymmdd != '')
        {
            $date_condition = " AND  DATEDIFF('$v_begin_date_yyyymmdd',a.C_CHARGE_DATE)<=0";
        }
        
        if($v_end_date_yyyymmdd != '')
        {
            $date_condition .= " AND DATEDIFF('$v_end_date_yyyymmdd',a.C_CHARGE_DATE)>=0";
        }
        
        $stmt            = "Select
                        a.C_SPEC_CODE
                      , COUNT(*) C_TOTAL_RECORD
                      , SUM(a.C_FINAL_FEE) AS C_TOTAL_FEE
                      , SUM(a.C_COST) As C_TOTAL_COST
                      , (SUM(a.C_FINAL_FEE) + SUM(a.C_COST)) AS C_SUM
                      ,a.C_CHARGE_DATE
                  From
                  (
                      Select
                            RF.FK_RECORD
                          , RF.C_FINAL_FEE
                          , RF.C_COST
                          , R.FK_RECORD_TYPE
                          , RT.C_SPEC_CODE
                          , ExtractValue(R.C_XML_PROCESSING, '//step[contains(@code,''" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'')][last()]/datetime[last()]') AS C_CHARGE_DATE
                      From t_r3_record_fee RF Left join view_record R On RF.FK_RECORD = R.PK_RECORD
                        Left join t_r3_record_type RT On R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                        Where (1>0) And R.FK_VILLAGE_ID = $v_village_id
                  ) a
                  Where a.C_SPEC_CODE IS NOT NULL $date_condition
                  Group by C_SPEC_CODE";
        $arr_report_data = $this->db->getAssoc($stmt);
        return $arr_report_data;
    }

    public function qry_all_report_data_7b($v_record_type,$v_spec, $v_begin_date_yyyymmdd, $v_end_date_yyyymmdd)
    {
        $v_village_id = Session::get('village_id');
        $date_condition = '';
        $record_type_condtion = '';
        //ngay thang
        if($v_begin_date_yyyymmdd != '')
        {
            $date_condition = " AND DATEDIFF('$v_begin_date_yyyymmdd',C_CHARGE_DATE)<=0";
        }
        
        if($v_end_date_yyyymmdd != '')
        {
            $date_condition .= " And DATEDIFF('$v_end_date_yyyymmdd',C_CHARGE_DATE)>=0 ";
        }
        
        if(is_numeric($v_record_type))
        {
            $record_type_condtion = " AND R.FK_RECORD_TYPE = $v_record_type";
        }
        
        //Lay danh sach Linh vuc
        $arr_all_spec = $this->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
        
        $sql          = '';
        foreach ($arr_all_spec as $code => $name)
        {
            if ($v_spec && ($v_spec != $code))
            {
                continue;
            }
            $sql .= ($sql != '') ? ' Union All ' : '';
            $sql .= "Select
                            C_SPEC_CODE
                          , C_SPEC_NAME
                          , C_RECORD_NO
                          , C_CITIZEN_NAME
                          , C_FINAL_FEE
                          , C_COST
                          , (C_FINAL_FEE+C_COST) As C_SUM
                          , C_FEE_DESCRIPTION
                          , DATE_FORMAT(C_CHARGE_DATE, '%d-%m-%Y %H:%i:%s') C_CHARGE_DATE
                    From (
                            Select
                                  RT.C_SPEC_CODE
                                , '$name' AS C_SPEC_NAME
                                , R.C_RECORD_NO
                                , R.C_CITIZEN_NAME
                                , RF.C_FINAL_FEE
                                , RF.C_COST
                                , RF.C_FEE_DESCRIPTION
                                , ExtractValue(R.C_XML_PROCESSING, '//step[contains(@code,''" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'')][last()]/datetime[last()]') AS C_CHARGE_DATE
                            From t_r3_record_fee RF Left join view_record R On RF.FK_RECORD = R.PK_RECORD
                                  Left join t_r3_record_type RT On R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                            Where RT.C_SPEC_CODE = '$code'
                                And R.FK_VILLAGE_ID = $v_village_id $record_type_condtion
                          ) $code
                    Where (1>0) $date_condition";
        }

        return $this->db->GetAll($sql);
    }

    public function qry_report999($year, $begin_month, $end_month)
    {
        $arr_all_spec = $this->list_get_all_by_listtype_code('DANH_MUC_LINH_VUC');
        
        $ret          = array();
        for ($i=0, $n=sizeof($arr_all_spec); $i<$n; $i++)
        {
            $spec_code = $arr_all_spec[$i]['C_CODE'];
            $spec_name = $arr_all_spec[$i]['C_NAME'];
            
            $month_sql = '';
            for ($j = $begin_month; $j <= $end_month; $j++)
            {
                $month_sql .= ($j > $begin_month) ? 'UNION ALL' : '';
                $month_sql .= " Select '$year-$j-01' as C_MONTH ";
            }
            
            $sql = "Select 
                        Month(m.C_MONTH) AS C_MONTH
                        ,d.*
                    From ($month_sql) as m
                        Left Join ( Select rhs.*
                                    From t_r3_record_history_stat rhs
                                        Right Join (Select MAX(C_HISTORY_DATE) as C_END_OF_MONTH_DATE
                                                    From t_r3_record_history_stat rhs
                                                    Where Year(C_HISTORY_DATE)=$year
                                                    Group By Year(C_HISTORY_DATE),Month(C_HISTORY_DATE)
                                                    ) a
                                        On datediff(rhs.C_HISTORY_DATE, a.C_END_OF_MONTH_DATE)=0
                                    Where C_SPEC_CODE = '$spec_code'
                                  ) as d
                        On Month(m.C_MONTH)=Month(d.C_HISTORY_DATE)
                    Where d.PK_HISTORY_STAT Is Not Null";
            
            $ret[$spec_name] = $this->db->GetAssoc($sql);
        }
        
        return $ret;
    }

    function qry_all_spec()
    {
        return $this->db->GetAssoc("
            Select ls.C_CODE, ls.C_NAME From t_cores_list ls
            Inner Join t_cores_listtype lt
                On ls.FK_LISTTYPE = lt.PK_LISTTYPE
            Where lt.C_CODE='DANH_MUC_LINH_VUC'
        ");
    }

    function qry_all_report_data_18($begin, $end,$spec_code)
    {
        $date_condition = '';
        $spec_condition = '';
        $v_village_id = Session::get('village_id');
         //ngay thang
        if($begin != '')
        {
            $date_condition = " And (DATEDIFF('$begin',C_CLEAR_DATE) <=0)";
        }
        
        if($end != '')
        {
            $date_condition .= " And (DATEDIFF('$end', C_CLEAR_DATE) >=0)";
        }
         //neu co spec code
        if($spec_code != '' && $spec_code != null)
        {
            $spec_condition = " AND rt.C_SPEC_CODE = '$spec_code'";
        }
        
        $fields          = "r.C_CITIZEN_NAME, r.C_RECORD_NO, r.C_XML_PROCESSING, r.C_RECEIVE_DATE, r.C_CLEAR_DATE, r.C_BIZ_DAYS_EXCEED";
        $fields .= " ,rt.C_CODE as C_TYPE_CODE ";
        $sql             = "Select $fields 
            From t_r3_record r
                Left Join t_r3_record_type rt On r.FK_RECORD_TYPE=rt.PK_RECORD_TYPE
            Where C_CLEAR_DATE Is Not Null
                And C_BIZ_DAYS_EXCEED < 0 $date_condition $spec_condition And r.FK_VILLAGE_ID = $v_village_id
                ";
        return $this->db->GetAll($sql);
    }
    /**
     * lay nam tu dong
     * @return type
     */
    public function get_year()
    {
        $sql = "SELECT
                    MAX(YEAR(C_RECEIVE_DATE)) AS C_MAX_YEAR,
                    MIN(YEAR(C_RECEIVE_DATE)) AS C_MIN_YEAR
                  FROM t_r3_record";
        return $this->db->getRow($sql);
    }
    
    public function qry_all_report_data_3($year, $begin_month, $end_month,$group_code = '',$group_level='')
    {
        $v_village_id = Session::get('village_id');
        //condition
        $condition = '';
        if($v_village_id != 0)//can bo cap xa
        {
            $condition = " AND FK_VILLAGE_ID = $v_village_id";
        }
        else //can bo cap huyen
        {
            if($group_code != '')//ton tai group code
            {
                if((int) $group_level == 3)//neu yeu cau bao cao cap xa
                {
                    //lay id ou cua xa duoc chon
                    $sql = "SELECT
                                PK_OU
                              FROM t_cores_group G
                                LEFT JOIN t_cores_ou OU
                                  ON G.FK_OU = OU.PK_OU
                              WHERE C_CODE = '$group_code'";
                    $v_village_id = $this->db->getOne($sql);
                    
                    $condition = " AND FK_VILLAGE_ID = $v_village_id";
                }
                else if((int) $group_level == 1 && $group_code == '0') //can bo cap huyen chi xem bao cao tai huyen
                {
                    $condition = " AND FK_VILLAGE_ID = 0";
                }
                else 
                {
                    $condition = " AND ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') = '$group_code'";
                }
            }
        }
            
        
        //lay tat ca ho so da tra - chua den han
        $stmt = "SELECT
                    CL.C_CODE,
                    COUNT(*)  AS COUNT_ALL_RECEIVE
                  FROM view_record VR
                    LEFT JOIN t_r3_record_type RT
                      ON VR.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                    LEFT JOIN t_cores_list CL
                      ON RT.C_SPEC_CODE = CL.C_CODE
                  WHERE YEAR(C_RECEIVE_DATE) = $year
                      AND MONTH(C_RECEIVE_DATE) >= $begin_month and MONTH(C_RECEIVE_DATE) <= $end_month
                      AND C_CLEAR_DATE is not null 
                      AND C_REJECTED <> 1
                      And ((DATEDIFF(C_CLEAR_DATE,C_RETURN_DATE) < 0 AND C_BIZ_DAYS_EXCEED IS NULL)
                               OR C_BIZ_DAYS_EXCEED > 0) $condition
                  GROUP BY RT.C_SPEC_CODE";
        $MODEL_DATA['arr_da_tra_chua_den_han'] = $this->db->GetAssoc($stmt);
        
        //lay tat ca ho so da tra - dung han
        $stmt = "SELECT
                    CL.C_CODE,
                    COUNT(*)  AS COUNT_ALL_RECEIVE
                  FROM view_record VR
                    LEFT JOIN t_r3_record_type RT
                      ON VR.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                    LEFT JOIN t_cores_list CL
                      ON RT.C_SPEC_CODE = CL.C_CODE
                  WHERE YEAR(C_RECEIVE_DATE) = $year
                      AND MONTH(C_RECEIVE_DATE) >= $begin_month and MONTH(C_RECEIVE_DATE) <= $end_month
                      AND C_CLEAR_DATE is not null 
                      AND C_REJECTED <> 1
                      AND ((DATEDIFF(C_CLEAR_DATE,C_RETURN_DATE) = 0 AND C_BIZ_DAYS_EXCEED IS NULL)
                            OR C_BIZ_DAYS_EXCEED = 0) $condition
                  GROUP BY RT.C_SPEC_CODE";
        $MODEL_DATA['arr_da_tra_dung_han'] = $this->db->GetAssoc($stmt);
        
        //lay tat ca ho so da tra - cham han
        $stmt = "SELECT
                    CL.C_CODE,
                    COUNT(*)  AS COUNT_ALL_RECEIVE
                  FROM view_record VR
                    LEFT JOIN t_r3_record_type RT
                      ON VR.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                    LEFT JOIN t_cores_list CL
                      ON RT.C_SPEC_CODE = CL.C_CODE
                  WHERE YEAR(C_RECEIVE_DATE) = $year
                      AND MONTH(C_RECEIVE_DATE) >= $begin_month and MONTH(C_RECEIVE_DATE) <= $end_month
                      AND C_CLEAR_DATE is not null 
                      AND C_REJECTED <> 1
                      And ((DATEDIFF(C_CLEAR_DATE,C_RETURN_DATE) > 0 AND C_BIZ_DAYS_EXCEED IS NULL) 
                             OR C_BIZ_DAYS_EXCEED < 0) $condition
                  GROUP BY RT.C_SPEC_CODE";
        $MODEL_DATA['arr_da_tra_cham_han'] = $this->db->GetAssoc($stmt);
        //lay tat ca ho so da tra - bi tu choi
        $stmt = "SELECT
                    CL.C_CODE,
                    COUNT(*)  AS COUNT_ALL_RECEIVE
                  FROM view_record VR
                    LEFT JOIN t_r3_record_type RT
                      ON VR.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                    LEFT JOIN t_cores_list CL
                      ON RT.C_SPEC_CODE = CL.C_CODE
                  WHERE YEAR(C_RECEIVE_DATE) = $year
                      AND MONTH(C_RECEIVE_DATE) >= $begin_month and MONTH(C_RECEIVE_DATE) <= $end_month
                      AND C_CLEAR_DATE is not null 
                      AND C_REJECTED = 1 $condition
                  GROUP BY RT.C_SPEC_CODE";
        $MODEL_DATA['arr_da_tra_bi_tu_choi'] = $this->db->GetAssoc($stmt);
        //lay tat ca ho so da tra - cong dan rut
        //dk: C_REJECTED = 2
        $stmt = "SELECT
                    CL.C_CODE,
                    COUNT(*)  AS COUNT_ALL_RECEIVE
                  FROM view_record VR
                    LEFT JOIN t_r3_record_type RT
                      ON VR.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                    LEFT JOIN t_cores_list CL
                      ON RT.C_SPEC_CODE = CL.C_CODE
                  WHERE YEAR(C_RECEIVE_DATE) = $year
                      AND MONTH(C_RECEIVE_DATE) >= $begin_month and MONTH(C_RECEIVE_DATE) <= $end_month
                      AND C_CLEAR_DATE is not null 
                      AND C_REJECTED = 2 $condition
                  GROUP BY RT.C_SPEC_CODE";
        $MODEL_DATA['arr_da_tra_cong_dan_rut'] = $this->db->GetAssoc($stmt);
        
        //dang xu ly - cham tien do
        $stmt = "SELECT
                    CL.C_CODE,
                    COUNT(*)  AS COUNT_ALL_RECEIVE
                  FROM view_processing_record PR
                    LEFT JOIN t_r3_record_type RT
                      ON PR.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                    LEFT JOIN t_cores_list CL
                      ON RT.C_SPEC_CODE = CL.C_CODE
                  WHERE YEAR(C_RECEIVE_DATE) = $year
                      AND MONTH(C_RECEIVE_DATE) >= $begin_month and MONTH(C_RECEIVE_DATE) <= $end_month
                      AND ((C_IS_PAUSING <> 1
                          and DATEDIFF(NOW(),C_RETURN_DATE) > 0
                          and ISNULL(C_BIZ_DAYS_EXCEED))
                          OR C_BIZ_DAYS_EXCEED < 0) $condition
                  GROUP BY RT.C_SPEC_CODE";
        $MODEL_DATA['arr_dang_xu_ly_cham'] = $this->db->GetAssoc($stmt);
        
        //dang xu ly - chua den han
        $stmt = "SELECT
                    CL.C_CODE,
                    COUNT(*)  AS COUNT_ALL_RECEIVE
                  FROM view_processing_record PR
                    LEFT JOIN t_r3_record_type RT
                      ON PR.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                    LEFT JOIN t_cores_list CL
                      ON RT.C_SPEC_CODE = CL.C_CODE
                  WHERE YEAR(C_RECEIVE_DATE) = $year
                      AND MONTH(C_RECEIVE_DATE) >= $begin_month and MONTH(C_RECEIVE_DATE) <= $end_month
                      AND (C_BIZ_DAYS_EXCEED >= 0
                          OR (C_IS_PAUSING = 1 AND C_NEXT_TASK_CODE NOT LIKE '%"._CONST_BO_SUNG_ROLE."%')
                          OR (DATEDIFF(NOW(),C_RETURN_DATE) <= 0
                              And ISNULL(C_BIZ_DAYS_EXCEED))) $condition
                  GROUP BY RT.C_SPEC_CODE";
        $MODEL_DATA['arr_dang_xu_ly_chua_den_han'] = $this->db->GetAssoc($stmt);
        
        //dang xu ly - cho bo sung
        $stmt = "SELECT
                    CL.C_CODE,
                    COUNT(*)  AS COUNT_ALL_RECEIVE
                  FROM view_processing_record PR
                    LEFT JOIN t_r3_record_type RT
                      ON PR.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                    LEFT JOIN t_cores_list CL
                      ON RT.C_SPEC_CODE = CL.C_CODE
                  WHERE YEAR(C_RECEIVE_DATE) = $year
                      AND MONTH(C_RECEIVE_DATE) >= $begin_month and MONTH(C_RECEIVE_DATE) <= $end_month
                      AND C_IS_PAUSING = 1 
                      AND C_NEXT_TASK_CODE LIKE '%"._CONST_BO_SUNG_ROLE."%' $condition
                  GROUP BY RT.C_SPEC_CODE";
        $MODEL_DATA['arr_dang_xu_ly_cho_bo_sung'] = $this->db->GetAssoc($stmt);
        
        //dang xu ly - nvtc
        $stmt = "SELECT
                    CL.C_CODE,
                    COUNT(*)  AS COUNT_ALL_RECEIVE
                  FROM view_record VR
                    LEFT JOIN t_r3_record_type RT
                      ON VR.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                    LEFT JOIN t_cores_list CL
                      ON RT.C_SPEC_CODE = CL.C_CODE
                  WHERE YEAR(C_RECEIVE_DATE) = $year
                      AND MONTH(C_RECEIVE_DATE) >= $begin_month and MONTH(C_RECEIVE_DATE) <= $end_month
                      AND (C_NEXT_TASK_CODE LIKE '"._CONST_NOP_HO_SO_SANG_CHI_CUC_THUE_ROLE."'
                            OR C_NEXT_TASK_CODE LIKE '"._CONST_NHAN_THONG_BAO_CUA_CHI_CUC_THUE_ROLE."'
                            OR C_NEXT_TASK_CODE LIKE '"._CONST_CHUYEN_THONG_BAO_THUE_VE_BP_MOT_CUA_ROLE."'
                            OR C_NEXT_TASK_CODE LIKE '"._CONST_TRA_THONG_BAO_NOP_THUE_ROLE."'
                            OR C_NEXT_TASK_CODE LIKE '"._CONST_NHAN_BIEN_LAI_NOP_THUE_ROLE."'
                            ) $condition
                  GROUP BY RT.C_SPEC_CODE";
        $MODEL_DATA['arr_dang_xu_ly_nvtc'] = $this->db->GetAssoc($stmt);
        
        return $MODEL_DATA;
    }

}
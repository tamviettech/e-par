<?php

require_once __DIR__ . '/../record/record_Model.php';

class synthesis_Model extends record_Model
{

    /**
     * @var \ADOConnection 
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }
    
    
    public function qry_all_record_for_lookup()
    {
        //kiem tra filter
        $v_record_no         = get_post_var('txt_record_no','');
        $v_citizen_name      = get_post_var('txt_citizen_name','');
        $v_receive_date_from = get_post_var('txt_receive_date_from','');
        $v_receive_date_to   = get_post_var('txt_receive_date_to','');
        
        if($v_record_no == '' && $v_citizen_name == '' && $v_receive_date_from == '' && $v_receive_date_to == '')
        {
            return array();
        }
        
        page_calc($v_start, $v_end);
        $v_start = $v_start - 1;
        $v_limit = $v_end - $v_start;
        
        //Cac dieu kien loc
        $v_and_condition = '';
        if ($v_record_no != '' && $v_record_no != NULL)
        {
            $v_and_condition .= " And C_RECORD_NO like '%$v_record_no%'";
        }
        if ($v_receive_date_from != '' && $v_receive_date_from != NULL)
        {
            $v_receive_date_from = jwDate::yyyymmdd_to_ddmmyyyy($v_receive_date_from);
            $v_and_condition .= " And Datediff(C_RECEIVE_DATE,'$v_receive_date_from') >= 0";
        }
        if ($v_receive_date_to != '' && $v_receive_date_to != NULL)
        {
            $v_receive_date_to = jwDate::yyyymmdd_to_ddmmyyyy($v_receive_date_to);
            $v_and_condition .= " And Datediff(C_RECEIVE_DATE,'$v_receive_date_to') <= 0";
        }
        
        if ($v_citizen_name != '' && $v_citizen_name != NULL)
        {
            $v_and_condition .= " And C_CITIZEN_NAME like '%$v_citizen_name%'";
        }
        
        //from where 
        $v_from_and_where = "t_r3_record R LEFT JOIN t_r3_user_task UT 
                                    ON (R.C_NEXT_TASK_CODE = UT.C_TASK_CODE AND R.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
                                Where 1>0 $v_and_condition 
                                ";
        //Dem tong so ban ghi tim thay theo trang thai va các điều kiện tra cúu
        $v_total_record = $this->db->getOne("Select Count(*) From $v_from_and_where");

        $sql = "SELECT
                    0 as C_OWNER
                    , Case When (R.C_REJECTED = 1) Then 3 When (R.C_REJECTED <> 1 And (R.C_CLEAR_DATE Is Not Null)) Then 2 Else 1 End as C_ACTIVITY
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
                    , UT.C_TASK_CODE
                    , UT.C_STEP_TIME
                    ,(Select C_CONTENT  FROM t_r3_record_comment
                            Where FK_RECORD=R.PK_RECORD
                            Order By C_CREATE_DATE DESC
                            Limit 1
                    ) C_LAST_RECORD_COMMENT
                    ,R.C_PAUSE_DATE
                    ,R.C_UNPAUSE_DATE
                FROM $v_from_and_where ORDER BY C_RECEIVE_DATE DESC limit $v_start,$v_limit
                ";
        return $this->db->getAll($sql);
    }
}
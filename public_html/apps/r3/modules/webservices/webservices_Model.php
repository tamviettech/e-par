<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class webservices_Model extends Model
{
    public $db;

    function __construct()
    {
        parent::__construct();
    }
    
    public function do_insert_exchange_record($record_type,$record_id,$xml_data,$xml_exchange_data)
    {
        $stmt = "INSERT INTO t_r3_exchange_record
                                (C_RECORD_TYPE_FROM,
                                 FK_RECORD_ID_FROM,
                                 C_XML_DATA,
                                 C_EXCHANGE_DATA
                                 )
                    VALUES (?,?,?,?)"; 
            
        $this->db->Execute($stmt,array($record_type,$record_id,$xml_data,$xml_exchange_data));
        if ($this->db->ErrorNo())//neu xay ra loi
        {
            return false;
        }
        return true;
    }
    
    public function receive_exchange_result($result,$record_id,$v_record_type_from)
    {
        $result = base64_decode($result);
        $task_code = $v_record_type_from . _CONST_XML_RTT_DELIM . _CONST_CHUYEN_HO_SO_LEN_SO_ROLE;
        $condition = "AND INSTR(extractValue(C_XML_PROCESSING,'//step[last()]/@code'),'$task_code') > 0";
        $xml_processing = $this->db->getOne("Select C_XML_PROCESSING From t_r3_record Where PK_RECORD = ? $condition",array($record_id));
        $dom = simplexml_load_string($xml_processing);
        if(@$dom)
        {
            $next_user_code = get_xml_value($dom,'//next_task//@user');
            $next_co_user_code = get_xml_value($dom,'//next_task//@co_user');
            $sql = "UPDATE t_r3_record
                    SET C_NEXT_CO_USER_CODE = '$next_co_user_code',
                      C_NEXT_USER_CODE = '$next_user_code',
                      C_XML_PROCESSING = UpdateXML(C_XML_PROCESSING,'//step[last()]/reason','<reason>$result</reason>')
                    WHERE PK_RECORD = $record_id";
            $this->db->Execute($sql);
            if ($this->db->ErrorNo())
            {
                return false;
            }
            return true;
        }
        return false;
    }
    
    public function get_exchange_record($record_id_from, $index)
    {
        $stmt = "SELECT
                    FK_RECORD_ID_TO
                  FROM t_r3_exchange_record
                  WHERE FK_RECORD_ID_FROM = ?";
        $arr_exchange_record = $this->db->getAll($stmt,array($record_id_from));
        return $arr_exchange_record[($index-1)]['FK_RECORD_ID_TO'];
    }
    
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

            $params = array($v_record_id);
            $ret_array = $this->db->getRow($stmt, $params);

            $ret_array['C_TOTAL_TIME'] = $this->days_between_two_date($ret_array['C_RECEIVE_DATE'], $ret_array['C_RETURN_DATE']);

            return $ret_array;
        }
        else //Them moi
        {
            //Tinh toan ngay tra ket qua
            if (file_exists($v_xml_workflow_file_name))
            {
                $dom = simplexml_load_file($v_xml_workflow_file_name);
                $r = xpath($dom, "/process/@totaltime[1]", XPATH_STRING);
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
                    $noon = ($v_hour_now <= intval(_CONST_MORNING_END_WORKING_TIME)) ? _CONST_MORNING_END_WORKING_TIME : _CONST_AFTERNOON_END_WORKING_TIME;
                    $v_total_time = intval($v_total_time);
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
    public function formal_record_step_days_to_date($record_id, $step_days_list)
    {
        $ret = Array();
        $arr_step_time = explode(';', $step_days_list);

        $v_prev_end_date = NULL;
        $v_init_date = $this->db->getOne("Select C_RECEIVE_DATE From view_record R Where PK_RECORD=$record_id");
        for ($i = 0; $i < sizeof($arr_step_time); $i++)
        {
            $v_step_time = $arr_step_time[$i];
            $v_begin_date = ($i == 0) ? $v_init_date : $v_prev_end_date;
            $v_end_date = $this->_step_deadline_calc($v_step_time, $v_begin_date);

            $ret[$i]['C_STEP_TIME'] = $v_step_time;
            $ret[$i]['C_BEGIN_DATE'] = $v_begin_date;
            $ret[$i]['C_END_DATE'] = $v_end_date;

            $v_prev_end_date = $v_end_date;
        }
        return $ret;
    }
    public function qry_exchange_unit($v_code='')
    {
        $condition = '';
        if($v_code != '')
        {
            $condition = "  AND C_CODE = '$v_code'";
        }
        $sql = "SELECT
                    C_CODE,
                    ExtractValue(C_XML_DATA,'//item[@id=\"txt_email_exchange\"]/value') AS C_EMAIL,
                    ExtractValue(C_XML_DATA,'//item[@id=\"txt_location\"]/value') AS C_LOCATION,
                    ExtractValue(C_XML_DATA,'//item[@id=\"txt_uri\"]/value') AS C_URI
                  FROM t_cores_list
                  WHERE FK_LISTTYPE = (SELECT
                                         PK_LISTTYPE
                                       FROM t_cores_listtype
                                       WHERE C_CODE = '"._CONST_DANH_MUC_DON_VI_LIEN_THONG."') $condition";
        if($condition != '')
        {
            return $this->db->GetRow($sql);
        }
        return $this->db->GetAssoc($sql);
    }
    
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
    public function qry_single_record_fee($record_id)
    {
        $stmt = "Select 
                    R.PK_RECORD
                    , IFNULL(RF.C_FINAL_FEE, 0) as C_FINAL_FEE
                    , ExtractValue(R.C_XML_DATA, '//item[@id=''txtCost'']/value[last()]') AS C_ADVANCE_COST
                From
                    t_r3_record R 
                    Left Join t_r3_record_fee RF 
                        On R.PK_RECORD = RF.FK_RECORD 
                Where R.PK_RECORD =  ?" ;
        $params = array($record_id);
        return $this->db->getRow($stmt, $params);
    }
    
    public function call_control_save_history_stat()
    {
        $sql = "CALL sp_control_save_history_stat";
        return $this->db->Execute($sql);
    }
    
    public function qry_history_stat_of_district()
    {
        $sql = 'Select 	
            PK_HISTORY_STAT,
            C_SPEC_CODE,
            C_HISTORY_DATE,
            C_COUNT_TONG_TIEP_NHAN,
            C_COUNT_TONG_TIEP_NHAN_TRONG_THANG, 
            C_COUNT_DANG_THU_LY, 
            C_COUNT_DANG_CHO_TRA_KET_QUA, 
            C_COUNT_DA_TRA_KET_QUA, 
            C_COUNT_DANG_THU_LY_DUNG_TIEN_DO, 
            C_COUNT_DANG_THU_LY_CHAM_TIEN_DO, 
            C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN, 
            C_COUNT_DA_TRA_KET_QUA_DUNG_HAN, 
            C_COUNT_DA_TRA_KET_QUA_QUA_HAN, 
            C_COUNT_CONG_DAN_RUT, 
            C_COUNT_TU_CHOI, 
            C_COUNT_BO_SUNG, 
            C_COUNT_THU_LY_QUA_HAN, 
            C_COUNT_THUE
        From t_r3_record_history_stat 
        Where C_HISTORY_DATE=(SELECT MAX(C_HISTORY_DATE) From t_r3_record_history_stat)
                AND FK_VILLAGE_ID = 0';
        $arr_all_report_form_2 = $this->db->getAll($sql);
        return $arr_all_report_form_2;
    }
    
    public function qry_history_stat_of_village()
    {
        $sql = 'Select 
                    OU.C_NAME,
                    HS.* 
                  From
                    t_r3_record_history_stat HS 
                    Left Join t_cores_ou OU 
                      On HS.FK_VILLAGE_ID = OU.PK_OU 
                  Where datediff(C_HISTORY_DATE, now())=0
                        AND HS.FK_VILLAGE_ID <> 0
                  order by HS.FK_VILLAGE_ID,
                    HS.C_SPEC_CODE';
        $arr_all_report_form_3 = $this->db->getAll($sql);
        return $arr_all_report_form_3;
    }
    
    public function receive_internet_record($dir)
    {
        $dom_record_info = simplexml_load_file($dir . DS . 'record_info.xml');
        if(empty($dom_record_info))
        {
            return false;
        }
        //insert t_r3_internet_record
        $v_record_type         = xpath($dom_record_info,'//C_RECORD_TYPE_CODE',XPATH_STRING);
        $v_record_no           = trim(xpath($dom_record_info,'//C_RECORD_NO',XPATH_STRING));
        $v_return_phone_number = xpath($dom_record_info,'//C_RETURN_PHONE_NUMBER',XPATH_STRING);
        $v_return_email        = xpath($dom_record_info,'//C_RETURN_EMAIL',XPATH_STRING);
        $v_citizen_name        = xpath($dom_record_info,'//C_CITIZEN_NAME',XPATH_STRING);
        $v_spec_code           = xpath($dom_record_info,'//C_SPEC_CODE',XPATH_STRING);
        $v_note                = xpath($dom_record_info,'//C_NOTE',XPATH_STRING);
        $v_citizen_address     = xpath($dom_record_info,'//C_CITIZEN_ADDRESS',XPATH_STRING);
        
        $v_xml_data = '<?xml version=""1.0"" standalone=""yes"" ?><data>';
        $v_xml_data .= '<item id="txtName"><value><![CDATA["' . $v_citizen_name . '"]]></value></item>';
        $v_xml_data .= '<item id="txtDiaChi"><value><![CDATA["' . $v_citizen_address . '"]]></value></item>';
        $v_xml_data .= '</data>';
        
        $v_task_code_like = $v_record_type . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;
        $sql = "Select C_USER_LOGIN_NAME From t_r3_user_task Where C_TASK_CODE Like '%" . $v_task_code_like . "'";
        $v_next_user_code = $this->db->getOne($sql);
        $v_next_task_code  = $v_record_type . _CONST_XML_RTT_DELIM . _CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE;
        
        $sql = "Insert Into t_r3_internet_record(";
        $sql .= " FK_RECORD_TYPE";
        $sql .= ",C_RECORD_NO";
        $sql .= ",C_RECEIVE_DATE";
        $sql .= ",C_RETURN_PHONE_NUMBER";
        $sql .= ",C_RETURN_EMAIL";
        $sql .= ",C_XML_DATA";
        $sql .= ",C_NEXT_TASK_CODE";
        $sql .= ",C_NEXT_USER_CODE";
        $sql .= ",C_IS_REAL_RECORD";
        $sql .= ",C_CITIZEN_NAME";
        $sql .= ",C_COMMENT";
        $sql .= ") Values (";
        $sql .= " (Select PK_RECORD_TYPE From t_r3_record_type Where C_CODE='$v_record_type')";
        $sql .= ",'$v_record_no'";
        $sql .= ",Now()";
        $sql .= ",'$v_return_phone_number'";
        $sql .= ",'$v_return_email'";
        $sql .= ",'$v_xml_data'";
        $sql .= ",'$v_next_task_code'";
        $sql .= ",'$v_next_user_code'";
        $sql .= ",1";
        $sql .= ",'$v_citizen_name'";
        $sql .= ",'$v_note'";
        $sql .= ")";
        $this->db->Execute($sql);
        $record_id = $this->db->Insert_ID('t_r3_internet_record');
        
        //insert t_r3_internet_record_file
        $arr_file = $dom_record_info->xpath('//ATTACHS/file');
        if(!empty($arr_file))
        {
            $sql = '';
            foreach($arr_file as $file_name)
            {
                if(file_exists($dir . DS . $file_name))
                {
                    $upload_internet_dir = SERVER_ROOT.'uploads'.DS.'r3'.DS.'internet'.DS.$file_name;
                    if(rename($dir . DS . $file_name, $upload_internet_dir ))//di chuyen file
                    {
                        if($sql == '')
                        {
                            $sql  = "Insert Into t_r3_internet_record_file(FK_RECORD, C_FILE_NAME) Values($record_id,'$file_name')";
                        }
                        else
                        {
                            $sql  .= ", ($record_id,'$file_name')";
                        }
                    }
                }
            }
            $this->db->Execute($sql);
            //xoa folder tao webservices
            foreach(glob($dir.DS."*.*") as $file)
            {
               if(file_exists($file))
               {
                   unlink($file);
               }
            }
            rmdir($dir);
        }
        return true;
    }
    
    public function r3_staff()
    {
        //lay nsd thuoc bo phan 1 cua
        $sql = "SELECT
                    U.*,
                    (SELECT CASE WHEN C_LEVEL = 3 THEN PK_OU ELSE 0 END FROM t_cores_ou WHERE PK_OU = U.FK_OU) AS FK_VILLAGE_ID
                  FROM (SELECT DISTINCT
                          C_USER_LOGIN_NAME
                        FROM t_r3_user_task
                        WHERE (C_TASK_CODE LIKE '%TIEP_NHAN'
                                OR C_TASK_CODE LIKE '%THU_PHI'
                                OR C_TASK_CODE LIKE '%TRA_KET_QUA')) UT
                    LEFT JOIN t_cores_user U
                      ON UT.C_USER_LOGIN_NAME = U.C_LOGIN_NAME 
                WHERE U.C_STATUS = 1";
        $arr_all_user = $this->db->getAll($sql);
        $xml = '<?xml version="1.0"?><root>';
        foreach($arr_all_user as $arr_user)
        {
            $user_code      = $arr_user['C_LOGIN_NAME'];
            $user_name      = $arr_user['C_NAME'];
            $user_job_title = $arr_user['C_JOB_TITLE'];
            $user_village_d = $arr_user['FK_VILLAGE_ID'];
            
            $user_email     = '';
            $user_birth_day = '';
            $user_education = '';
            $xml .= '<user>';
            $xml .= '<user_code>'.$user_code.'</user_code>';
            $xml .= '<user_name>'.$user_name.'</user_name>';
            $xml .= '<job_title>'.$user_job_title.'</job_title>';
            $xml .= '<village_id>'.$user_village_d.'</village_id>';
            $xml .= '<email>'.$user_email.'</email>';
            $xml .= '<birth_day>'.$user_birth_day.'</birth_day>';
            $xml .= '<education>'.$user_education.'</education>';
            $xml .= '</user>';
        }
        $xml .= '</root>';
        return $xml;
    }
}


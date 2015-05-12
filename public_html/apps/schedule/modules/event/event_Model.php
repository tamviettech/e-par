<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class event_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    public function qry_all_date_of_week($v_begin_of_week, $v_end_of_week)
    {
        if(DATABASE_TYPE == 'MSSQL')
        {
        	$stmt = 'Select CONVERT(nvarchar(10), c.C_DATE, 105) C_DATE_DDMMYYYY
                    ,CONVERT(nvarchar(10), c.C_DATE, 111) C_DATE_YYYYMMDD
                    ,c.C_OFF
                From T_CALENDAR C
                Where DATEDIFF(day, C_DATE, ?) <=0
                    And DATEDIFF(day, C_DATE, ?) >=0
                Order By c.C_DATE Asc';
        }
        elseif(DATABASE_TYPE == 'MYSQL')
        {
        	$stmt = 'Select DATE_FORMAT(C.C_DATE,\'%d-%m-%Y\') C_DATE_DDMMYYYY
                    ,DATE_FORMAT(C.C_DATE,\'%Y/%m/%d\') C_DATE_YYYYMMDD
                    ,C.C_OFF
                From t_cores_calendar C
                Where DATEDIFF(C.C_DATE, ?) >=0
                    And DATEDIFF(C.C_DATE, ?) <=0
                Order By C.C_DATE Asc';
        }
    	
        $params = array($v_begin_of_week, $v_end_of_week);
        return $this->db->getAll($stmt, $params);
    }

    public function qry_all_date_event($date_yyyymmdd, $noon)
    {
        $v_user_code = Session::get('user_code');
        $date_yyyymmdd = $this->replace_bad_char($date_yyyymmdd);

        /*
        $stmt = 'Select a.*
                 From (
                    Select e.*
                    From [dbo].[VIEW_EVENT] e
                    Where DATEDIFF(day, C_BEGIN_TIME, ?)=0 ';
        $stmt .= ($noon == 'am') ? ' And e.begin_hour < 12' : ' And e.begin_hour >= 12';
        $stmt .= ') a';


        //Event liên quan đến trực tiếp cá nhân, hoac nhom ma NSD la thanh vien
        $stmt .= " Where (
            (a.creator_user_code='$v_user_code')
            Or (a.event_user.exist('(//row[@user_code=''$v_user_code'' and @user_type=''user''])')=1)";
        /*
            foreach (Session::get('arr_group_code') as $v_group_code)
            {
                $stmt .= " OR (a.event_user.exist('(//row[@user_code=''$v_group_code'' and @user_type=''group''])')=1)";
            }
        $stmt .= ')';
        */
        if(DATABASE_TYPE == 'MSSQL')
        {
        	$stmt = 'Select e.*
                    From [dbo].[VIEW_EVENT] e
                    Where DATEDIFF(day, C_BEGIN_TIME, ?)=0 ';
        	$stmt .= ($noon == 'am') ? ' And e.begin_hour < 12' : ' And e.begin_hour >= 12';
        	
        	$stmt .= ' Order By e.C_BEGIN_TIME';
        }
        elseif(DATABASE_TYPE)
        {
        	$stmt = 'Select e.*
                    From view_event e
                    Where DATEDIFF(C_BEGIN_TIME, ?)=0 ';
        	$stmt .= ($noon == 'am') ? ' And e.begin_hour < 12' : ' And e.begin_hour >= 12';
        	
        	$stmt .= ' Order By e.C_BEGIN_TIME';
        }
        

        $this->db->debug = 0;
        return $this->db->getAll($stmt, array($date_yyyymmdd));
    }

    public function qry_single_event($event_id)
    {
        if ($event_id > 0)
        {
        	if(DATABASE_TYPE == 'MSSQL')
        	{
        		$stmt = 'Select e.*
                    From VIEW_EVENT e
                    Where e.event_id=?';
        	}
        	elseif(DATABASE_TYPE == 'MYSQL')
        	{
        		$stmt = 'Select e.*
                    From view_event e
                    Where e.event_id=?';
        	}
           

            return $this->db->getRow($stmt, array($event_id));
        }
        return array();
    }

    //Danh sách người (nhóm) chủ trì, thường trực
    public function qry_all_event_owner($event_id)
    {
        if ($event_id > 0)
        {	
        	if(DATABASE_TYPE == 'MSSQL')
        	{
            	$stmt = 'Select * From [dbo].[VIEW_EVENT_OWNER] Where FK_EVENT=?';
        	}
        	elseif(DATABASE_TYPE == 'MYSQL')
        	{
        		$stmt = 'Select * From view_event_owner Where FK_EVENT=?';
        	}
            $params = array($event_id);
            return $this->db->getAll($stmt, $params);
        }

        return array();
    }

    //Danh sách người (nhóm) tham dự, liên đới
    public function qry_all_event_attender($event_id)
    {
        if ($event_id > 0)
        {	
        	if(DATABASE_TYPE == 'MSSQL')
        	{
            	$stmt = 'Select * From [dbo].[VIEW_EVENT_ATTENDER] Where FK_EVENT=?';
        	}
        	elseif(DATABASE_TYPE == 'MYSQL')
        	{
        		$stmt = 'Select * From view_event_attender Where FK_EVENT=?';
        	}
            $params = array($event_id);
            return $this->db->getAll($stmt, $params);
        }

        return array();
    }

    public function update_event()
    {

        $v_event_id = isset($_POST['hdn_item_id']) ? $this->replace_bad_char($_POST['hdn_item_id']) : 0;
        $v_subject  = isset($_POST['txt_subject']) ? $this->replace_bad_char($_POST['txt_subject']) : '';
        $v_description  = isset($_POST['txt_description']) ? $this->replace_bad_char($_POST['txt_description']) : '';
        $v_location  = isset($_POST['txt_location']) ? $this->replace_bad_char($_POST['txt_location']) : '';

        $v_begin_date = isset($_POST['txt_begin_date']) ? $this->replace_bad_char($_POST['txt_begin_date']) : date('d-m-Y');
        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
        $v_begin_hour  = isset($_POST['sel_begin_hour']) ? $this->replace_bad_char($_POST['sel_begin_hour']) : '0';
        $v_begin_minute  = isset($_POST['sel_begin_minute']) ? $this->replace_bad_char($_POST['sel_begin_minute']) : '0';

        $v_begin_time = $v_begin_date . ' ' . $v_begin_hour . ':' . $v_begin_minute . ':00';

        $v_end_date = isset($_POST['txt_end_date']) ? $this->replace_bad_char($_POST['txt_end_date']) : date('d-m-Y');
        $v_end_date = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
        $v_end_hour  = isset($_POST['sel_end_hour']) ? $this->replace_bad_char($_POST['sel_end_hour']) : '0';
        $v_end_minute  = isset($_POST['sel_end_minute']) ? $this->replace_bad_char($_POST['sel_end_minute']) : '0';

        $v_end_time = $v_end_date . ' ' . $v_end_hour . ':' . $v_end_minute . ':00';

        $v_creator_user_code = Session::get('user_code');


        if ($v_event_id < 1)
        {
            //Add new
            if(DATABASE_TYPE == 'MSSQL')
            {
	            $stmt = 'Insert Into T_SCHEDULE_EVENT(
	                        C_SUBJECT
	                        ,C_DESCRIPTION
	                        ,C_LOCATION
	                        ,C_BEGIN_TIME
	                        ,C_END_TIME
	                        ,C_CREATOR_USER_CODE
	                    ) Values (
	                        N?
	                        ,N?
	                        ,N?
	                        ,?
	                        ,?
	                        ,?
	                    )';
            }
            elseif(DATABASE_TYPE == 'MYSQL')
            {
            	$stmt = 'Insert Into t_schedule_event(
	                        C_SUBJECT
	                        ,C_DESCRIPTION
	                        ,C_LOCATION
	                        ,C_BEGIN_TIME
	                        ,C_END_TIME
	                        ,C_CREATOR_USER_CODE
	                    ) Values (
	                        ?
	                        ,?
	                        ,?
	                        ,?
	                        ,?
	                        ,?
	                    )';
            }
            $params = array(
                    $v_subject
                    ,$v_description
                    ,$v_location
                    ,$v_begin_time
                    ,$v_end_time
                    ,$v_creator_user_code
            );
           // echo $stmt; Var_Dump($params); exit;
            $this->db->Execute($stmt, $params);
            
			if(DATABASE_TYPE == 'MSSQL')
			{
	            $v_event_id = $this->db->getOne("Select IDENT_CURRENT('T_SCHEDULE_EVENT')");
	
	            $v_old_begin_date = $this->db->getOne("Select replace(Convert(varchar(10),C_BEGIN_TIME, 111),'/','-') From T_SCHEDULE_EVENT Where PK_EVENT=?", array($v_event_id));
	            $v_old_begin_hour = $this->db->getOne('Select Datepart(HOUR, C_BEGIN_TIME) From T_SCHEDULE_EVENT Where PK_EVENT=?', array($v_event_id));
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$v_event_id = $this->db->Insert_ID('t_schedule_event');
				
				$v_old_begin_date = $this->db->getOne("Select DATE_FORMAT(C_BEGIN_TIME,'%y-%m-%d') From t_schedule_event Where PK_EVENT=?", array($v_event_id));
				$v_old_begin_hour = $this->db->getOne('Select HOUR(C_BEGIN_TIME) From t_schedule_event Where PK_EVENT=?', array($v_event_id));
			}
		}
        else
        {
        	if(DATABASE_TYPE == 'MSSQL')
        	{
	            $v_old_begin_date = $this->db->getOne("Select replace(Convert(varchar(10),C_BEGIN_TIME, 111),'/','-') From T_SCHEDULE_EVENT Where PK_EVENT=?", array($v_event_id));
	            $v_old_begin_hour = $this->db->getOne('Select Datepart(HOUR, C_BEGIN_TIME) From T_SCHEDULE_EVENT Where PK_EVENT=?', array($v_event_id));
	            $stmt = 'Update T_SCHEDULE_EVENT
	                    Set C_SUBJECT           = N?
	                        ,C_DESCRIPTION      = N?
	                        ,C_LOCATION         = N?
	                        ,C_BEGIN_TIME       = ?
	                        ,C_END_TIME         = ?
	                        ,C_CREATOR_USER_CODE = ?
	                    Where PK_EVENT=?';
        	}
        	elseif(DATABASE_TYPE == 'MYSQL')
        	{
        		$v_old_begin_date = $this->db->getOne("Select DATE_FORMAT(C_BEGIN_TIME,'%y-%m-%d') From t_schedule_event Where PK_EVENT=?", array($v_event_id));
	            $v_old_begin_hour = $this->db->getOne('Select HOUR(C_BEGIN_TIME) From t_schedule_event Where PK_EVENT=?', array($v_event_id));
	            $stmt = 'Update t_schedule_event
	                    Set C_SUBJECT           = ?
	                        ,C_DESCRIPTION      = ?
	                        ,C_LOCATION         = ?
	                        ,C_BEGIN_TIME       = ?
	                        ,C_END_TIME         = ?
	                        ,C_CREATOR_USER_CODE = ?
	                    Where PK_EVENT=?';
        	}
            $params = array(
                    $v_subject
                    ,$v_description
                    ,$v_location
                    ,$v_begin_time
                    ,$v_end_time
                    ,$v_creator_user_code
                    ,$v_event_id
            );
            $this->db->Execute($stmt, $params);
        }

        //1. NGUOI chu tri
        $v_owner_user_code_list = isset($_POST['hdn_owner_user_code_list']) ? $this->replace_bad_char($_POST['hdn_owner_user_code_list']) : '';
        //Xoa het du lieu cu
        $stmt = "Delete From t_schedule_event_user
                Where C_USER_TYPE='user'
                        And C_USER_ROLE='owner'
                        And FK_EVENT=?";
        $this->db->Execute($stmt, array($v_event_id));
        //Them du lieu moi
        if ($v_owner_user_code_list != '')
        {
            $arr_owner_user_code = explode(',', $v_owner_user_code_list);
            foreach ($arr_owner_user_code as $owner_user_code)
            {
                $stmt = "Insert Into t_schedule_event_user(
                            FK_EVENT
                            ,C_USER_CODE
                            ,C_USER_TYPE
                            ,C_USER_ROLE
                        ) Values(
                            ?
                            ,?
                            ,'user'
                            ,'owner'
                        )";
                $params = array($v_event_id, $owner_user_code);
                $this->db->Execute($stmt, $params);
            }
        }

        //2. NHOM chu tri
        $v_owner_group_code_list = isset($_POST['hdn_owner_group_code_list']) ? $this->replace_bad_char($_POST['hdn_owner_group_code_list']) : '';
        //Xoa het du lieu cu
        $stmt = "Delete From t_schedule_event_user
                Where C_USER_TYPE='group'
                        And C_USER_ROLE='owner'
                        And FK_EVENT=?";
        $this->db->Execute($stmt, array($v_event_id));

        //Them du lieu moi
        if ($v_owner_group_code_list != '')
        {
            $arr_owner_group_code = explode(',', $v_owner_group_code_list);
            foreach ($arr_owner_group_code as $owner_group_code)
            {
                $stmt = "Insert Into t_schedule_event_user(
                            FK_EVENT
                            ,C_USER_CODE
                            ,C_USER_TYPE
                            ,C_USER_ROLE
                        ) Values(
                            ?
                            ,?
                            ,'group'
                            ,'owner'
                        )";
                $params = array($v_event_id, $owner_group_code);
                $this->db->Execute($stmt, $params);
            }
        }

        //3. NGUOI tham du
        $v_attender_user_code_list = isset($_POST['hdn_attender_user_code_list']) ? $this->replace_bad_char($_POST['hdn_attender_user_code_list']) : '';
        //Xoa het du lieu cu
        $stmt = "Delete From t_schedule_event_user
                Where C_USER_TYPE='user'
                    And C_USER_ROLE='attender'
                    And FK_EVENT=?";
        $this->db->Execute($stmt, array($v_event_id));

        //Them du lieu moi
        if ($v_attender_user_code_list != '')
        {

            $arr_attender_user_code = explode(',', $v_attender_user_code_list);
            foreach ($arr_attender_user_code as $attender_user_code)
            {
                $stmt = "Insert Into t_schedule_event_user(
                            FK_EVENT
                            ,C_USER_CODE
                            ,C_USER_TYPE
                            ,C_USER_ROLE
                        ) Values(
                            ?
                            ,?
                            ,'user'
                            ,'attender'
                        )";
                $params = array($v_event_id, $attender_user_code);
                $this->db->Execute($stmt, $params);
            }
        }

        //4. NHOM tham du
        $v_attender_group_code_list = isset($_POST['hdn_attender_group_code_list']) ? $this->replace_bad_char($_POST['hdn_attender_group_code_list']) : '';
        //Xoa het du lieu cu
        $stmt = "Delete From t_schedule_event_user
                Where C_USER_TYPE='group'
                        And C_USER_ROLE='attender'
                        And FK_EVENT=?";
        $this->db->Execute($stmt, array($v_event_id));

        //Them du lieu moi
        if ($v_attender_group_code_list != '')
        {
            $arr_attender_group_code = explode(',', $v_attender_group_code_list);
            foreach ($arr_attender_group_code as $attender_group_code)
            {
                $stmt = "Insert Into t_schedule_event_user(
                            FK_EVENT
                            ,C_USER_CODE
                            ,C_USER_TYPE
                            ,C_USER_ROLE
                        ) Values(
                            ?
                            ,?
                            ,'group'
                            ,'attender'
                        )";
                $params = array($v_event_id, $attender_group_code);
                $this->db->Execute($stmt, $params);
            }
        }

        $noon = ($v_begin_hour < 12) ? 'am' : 'pm';
        $old_noon = ($v_old_begin_hour < 12) ? 'am' : 'pm';
   
        $ret_val ='Array("' . $noon . '","' . $v_begin_date . '","' . $old_noon . '","' . $v_old_begin_date . '")';
        $this->popup_exec_done($ret_val);
    }

    public function delete_event()
    {
        $v_event_id = isset($_POST['hdn_item_id']) ? $this->replace_bad_char($_POST['hdn_item_id']) : 0;
		if(DATABASE_TYPE == 'MSSQL')
		{
	        $stmt = "Select
	                    replace(convert(char(10), C_BEGIN_TIME, 111),'/','-') begin_date_yyyymmdd
	                    , begin_hour
	                From VIEW_EVENT
	                Where event_id=?";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$stmt = "Select
                    	DATE_FORMAT(C_BEGIN_TIME,'%y-%m-%d') begin_date_yyyymmdd
                    	, begin_hour
                From view_event
                Where event_id=?";
		}
        $params = array($v_event_id);
        $arr_date_info = $this->db->getRow($stmt, $params);

        //Xoa NSD lien quan den Event
        $stmt = 'Delete From t_schedule_event_user Where FK_EVENT=?';
        $params = array($v_event_id);
        $this->db->Execute($stmt,$params);

        //Xoa event
        $stmt = 'Delete From t_schedule_event Where PK_EVENT=?';
        $params = array($v_event_id);
        $this->db->Execute($stmt,$params);
        
        $this->popup_exec_done();
    }
}
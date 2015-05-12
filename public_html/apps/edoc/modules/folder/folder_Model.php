<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class folder_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Lay danh sach HS ma NSD co quyen truy cap
     * @return array Danh sach HS
     */
    public function qry_all_folder($p_filter = '')
    {
        $v_user_id = session::get('user_id');
        $v_ou_id = session::get('ou_id');
		if(DATABASE_TYPE == 'MSSQL')
		{
	        $stmt = "Select F.PK_FOLDER
	                    ,F.FK_USER
	                    ,F.C_CODE
	                    ,F.C_NAME
	                    ,F.C_DESCRIPTION
	                    ,Convert(VARCHAR(10), F.C_CREATE_DATE, 103) C_CREATE_DATE
	                    ,F.C_IS_CLOSED
	                    ,F.C_STATUS
	                    ,F.C_XML_DATA
	                    ,U.C_NAME as C_USER_NAME
	                    , (Select Count(*) From T_EDOC_FOLDER_DOC FD Where FD.FK_FOLDER=F.PK_FOLDER) as C_COUNT_DOC
	                From T_EDOC_FOLDER F Left join T_USER U on U.PK_USER=F.FK_USER
	                Where (F.FK_USER=?)
	                        Or (F.PK_FOLDER in (Select FK_FOLDER From T_EDOC_FOLDER_USER Where FK_USER=? And C_USER_TYPE='USER'))
	                        Or (F.PK_FOLDER in (Select FK_FOLDER From T_EDOC_FOLDER_USER Where FK_USER=? And C_USER_TYPE='OU'))";
		}
		elseif (DATABASE_TYPE == 'MYSQL')
		{
			$stmt = "Select F.PK_FOLDER
	                    ,F.FK_USER
	                    ,F.C_CODE
	                    ,F.C_NAME
	                    ,F.C_DESCRIPTION
	                    ,DATE_FORMAT(F.C_CREATE_DATE,'%d/%m/%Y') C_CREATE_DATE
	                    ,F.C_IS_CLOSED
	                    ,F.C_STATUS
	                    ,F.C_XML_DATA
	                    ,U.C_NAME as C_USER_NAME
	                    , (Select Count(*) From t_edoc_folder_doc FD Where FD.FK_FOLDER=F.PK_FOLDER) as C_COUNT_DOC
	                From t_edoc_folder F Left join t_cores_user U on U.PK_USER=F.FK_USER
	                Where (F.FK_USER=?)
	                        Or (F.PK_FOLDER in (Select FK_FOLDER From t_edoc_folder_user Where FK_USER=? And C_USER_TYPE='USER'))
	                        Or (F.PK_FOLDER in (Select FK_FOLDER From t_edoc_folder_user Where FK_USER=? And C_USER_TYPE='OU'))";
		}
        if ($p_filter != '')
        {
            $stmt .= " And (
                        (F.C_NAME like '%$p_filter%')
                        Or (F.C_CODE like '%$p_filter%')
                        Or (F.C_DESCRIPTION like '%$p_filter%')
                     )";
        }
        $stmt .= ' Order By F.C_CREATE_DATE Desc';
        $params = array(
                    $v_user_id
                    ,$v_user_id
                    ,$v_ou_id
            );
	
        return $this->db->getAll($stmt, $params);
    }

    public function qry_single_folder($p_folder_id)
    {
        if ($p_folder_id > 0)
        {
            $stmt = 'Select * From t_edoc_folder
                    Where PK_FOLDER=?';
            $params = array($p_folder_id);

            return $this->db->getRow($stmt, $params);
        }
        else
        {
            return array();
        }
    }

    /**
     * Lay danh sach van ban cua mot Ho so luu
     * @param Int $p_folder_id
     */
    public function qry_all_doc_by_folder($p_folder_id)
    {
		if(DATABASE_TYPE == 'MSSQL')
		{
	    	$stmt = "Select
	                    D.PK_DOC
	                    ,D.DOC_SO_KY_HIEU
	                    ,D.DOC_TRICH_YEU
	                    ,D.C_XML_DATA.value('(//item[@id=''doc_ngay_van_ban'']/value/text())[1]', 'varchar(50)') DOC_NGAY_VAN_BAN
	                    ,FD.FK_USER
	                    ,U.C_NAME C_USER_NAME
	                    ,Convert(Xml,(Select PK_DOC_FILE, FK_DOC, C_FILE_NAME From T_EDOC_DOC_FILE df Where df.FK_DOC=D.PK_DOC For xml raw),1) as DOC_ATTACH_FILE_LIST
	                From VIEW_DOC D Left Join T_EDOC_FOLDER_DOC FD On D.PK_DOC=FD.FK_DOC
	                    Left Join T_USER U on U.PK_USER=FD.FK_USER
	                Where FD.FK_FOLDER=?";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$stmt = "Select
	                    D.PK_DOC
	                    ,D.DOC_SO_KY_HIEU
	                    ,D.DOC_TRICH_YEU
	                    ,ExtractValue(D.C_XML_DATA,'//item[@id=''doc_ngay_van_ban'']/value') DOC_NGAY_VAN_BAN
	                    ,FD.FK_USER
	                    ,U.C_NAME C_USER_NAME
						,(SELECT GROUP_CONCAT('<row '
												,CONCAT(' PK_DOC_FILE=\"', df.PK_DOC_FILE, '\"')
												,CONCAT(' FK_DOC=\"', df.FK_DOC, '\"')
												,CONCAT(' C_FILE_NAME=\"', df.C_FILE_NAME, '\"')
												, ' />'
												SEPARATOR ''
											 )
						FROM t_edoc_doc_file df 
						WHERE df.FK_DOC=D.PK_DOC
						) AS DOC_ATTACH_FILE_LIST
	                From view_doc D Left Join t_edoc_folder_doc FD On D.PK_DOC=FD.FK_DOC
	                    Left Join t_cores_user U on U.PK_USER=FD.FK_USER
	                Where FD.FK_FOLDER=?";
		}
		
        $params = array($p_folder_id);

        return $this->db->getAll($stmt, $params);
    }

    public function update_folder()
    {
        //echo 'Line: ' . __LINE__ . '<br>File: ' . __FILE__;
        //var_dump::display($_POST);exit;

        //Cap nhat boi nguoi mo HS ?


        $v_folder_id = isset($_POST['hdn_item_id']) ? $this->replace_bad_char($_POST['hdn_item_id']) : 0;
        $v_code = isset($_POST['txt_code']) ? $this->replace_bad_char($_POST['txt_code']) : '';
        $v_name = isset($_POST['txt_name']) ? $this->replace_bad_char($_POST['txt_name']) : '';
        $v_description = isset($_POST['txt_description']) ? $this->replace_bad_char($_POST['txt_description']) : '';
        $v_xml_data = $_POST['XmlData'];

        $v_status       = isset($_POST['chk_status']) ? 1 : 0;
        $v_is_closed    = isset($_POST['chk_is_closed']) ? 1 : 0;

        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? $this->replace_bad_char($_POST['txt_filter']) : '';
        $v_page = isset($_POST['sel_goto_page']) ? $this->replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? $this->replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
        $arr_filter = array(
                            'txt_filter'   => $v_filter,
                            'sel_goto_page'   => $v_page,
                            'sel_rows_per_page'   => $v_rows_per_page
                        );

        //Kiem tra trung ma
        $sql = "Select Count(*)
                From t_edoc_folder
                Where C_CODE='$v_code' And PK_FOLDER <> $v_folder_id";
        if ($this->db->getOne($sql) > 0)
        {
            $this->exec_fail($this->goback_url, 'Mã hồ sơ đã tồn tại', $arr_filter);
            return;
        }

        //Kiem tra trung ten
        $sql = "Select Count(*)
                From t_edoc_folder
                Where C_NAME='$v_name' And PK_FOLDER <> $v_folder_id";
        if ($this->db->getOne($sql) > 0)
        {
            $this->exec_fail($this->goback_url, 'Tiêu đề hồ sơ đã tồn tại', $arr_filter);
            return;
        }

        $v_user_id = session::get('user_id');

        $v_update_by_owner = FALSE;
        if ($v_folder_id < 1)
        {
        	if(DATABASE_TYPE == 'MSSQL')
        	{
	            $sql = "Insert Into T_EDOC_FOLDER (
	                        FK_USER
	                        ,C_CODE
	                        ,C_NAME
	                        ,C_DESCRIPTION
	                        ,C_CREATE_DATE
	                        ,C_IS_CLOSED
	                        ,C_STATUS
	                        ,C_XML_DATA
	                     ) Values (
	                        $v_user_id,
	                        '$v_code',
	                        N'$v_name',
	                        N'$v_description',
	                        getDate(),
	                        $v_is_closed,
	                        $v_status,
	                        Convert(XML,N'$v_xml_data',1)
	                )";
	            
	            $this->db->Execute($sql);
	            $v_folder_id = $this->db->getOne("Select IDENT_CURRENT('T_EDOC_FOLDER')");
        	}
        	elseif(DATABASE_TYPE == 'MYSQL')
        	{
        		$sql = "Insert Into t_edoc_folder (
        		FK_USER
        		,C_CODE
        		,C_NAME
        		,C_DESCRIPTION
        		,C_CREATE_DATE
        		,C_IS_CLOSED
        		,C_STATUS
        		,C_XML_DATA
        		) Values (
        		$v_user_id,
        		'$v_code',
        		'$v_name',
        		'$v_description',
        		Now(),
        		$v_is_closed,
        		$v_status,
        		'$v_xml_data'
        		)";
        	
	            $this->db->Execute($sql);
	            $v_folder_id = $this->db->Insert_ID('t_edoc_folder');
        	}
            $v_update_by_owner = TRUE;
        }
        else
        {
            $v_update_by_owner = $this->db->getOne("Select Count(*) From t_edoc_folder Where PK_FOLDER=$v_folder_id And FK_USER=$v_user_id");
            if ($v_update_by_owner)
            {	
            	if(DATABASE_TYPE == 'MSSQL')
            	{
	                $sql = "Update T_EDOC_FOLDER Set
	                            C_CODE          = '$v_code'
	                            ,C_NAME         = N'$v_name'
	                            ,C_DESCRIPTION  = N'$v_description'
	                            ,C_IS_CLOSED    = $v_is_closed
	                            ,C_STATUS       = $v_status
	                            ,C_XML_DATA     = Convert(XML,N'$v_xml_data',1)
	                        Where PK_FOLDER=$v_folder_id";
            	}
            	elseif(DATABASE_TYPE == 'MYSQL')
            	{
            		$sql = "Update t_edoc_folder Set
						            		C_CODE          = '$v_code'
						            		,C_NAME         = '$v_name'
						            		,C_DESCRIPTION  = '$v_description'
						            		,C_IS_CLOSED    = $v_is_closed
						            		,C_STATUS       = $v_status
						            		,C_XML_DATA     = '$v_xml_data'
            				Where PK_FOLDER=$v_folder_id";
            	}
            
                $this->db->Execute($sql);
            }
        }

        //Cap nhat thong tin danh sach van ban cua HS
        $v_doc_id_list = isset($_POST['hdn_doc_id_list']) ? $this->replace_bad_char($_POST['hdn_doc_id_list']) : '';
        $this->db->Execute("Delete From t_edoc_folder_doc Where FK_FOLDER=$v_folder_id And FK_USER=$v_user_id");
        if ($v_doc_id_list != '')
        {
            $arr_doc_id = explode(',', $v_doc_id_list);
            foreach ($arr_doc_id as $v_doc_id)
            {	
            	if(DATABASE_TYPE == 'MSSQL')
            	{
            		$sql = "Insert Into T_EDOC_FOLDER_DOC(FK_FOLDER, FK_DOC, FK_USER, C_CREATE_DATE) Values($v_folder_id, $v_doc_id, $v_user_id, getDate())";
            	}
            	elseif(DATABASE_TYPE == 'MYSQL')
            	{
            		$sql = "Insert Into t_edoc_folder_doc(FK_FOLDER, FK_DOC, FK_USER, C_CREATE_DATE) Values($v_folder_id, $v_doc_id, $v_user_id, Now())";
            	}
                
                $this->db->Execute($sql);
            }
        }

        //Chi nguoi mo HS
        if ($v_update_by_owner)
        {
            //Cap nhat thong tin danh sach user
            $v_shared_user_id_list = isset($_POST['hdn_shared_user_id_list']) ? $this->replace_bad_char($_POST['hdn_shared_user_id_list']) : '';
            $this->db->Execute("Delete From t_edoc_folder_user Where FK_FOLDER=$v_folder_id And C_USER_TYPE='USER'");
            if ($v_shared_user_id_list != '')
            {
                $arr_shared_user_id = explode(',', $v_shared_user_id_list);
                foreach ($arr_shared_user_id as $v_shared_user_id)
                {
                    $sql = "Insert Into t_edoc_folder_user(FK_FOLDER, FK_USER, C_USER_TYPE) Values($v_folder_id, $v_shared_user_id, 'USER')";
                    $this->db->Execute($sql);
                }
            }

            //Cap nhat thong tin danh sach Phong ban
            $v_shared_ou_id_list = isset($_POST['hdn_shared_ou_id_list']) ? $this->replace_bad_char($_POST['hdn_shared_ou_id_list']) : '';
            $this->db->Execute("Delete From t_edoc_folder_user Where FK_FOLDER=$v_folder_id And C_USER_TYPE='OU'");
            if ($v_shared_ou_id_list != '')
            {
                $arr_shared_ou_id = explode(',', $v_shared_ou_id_list);
                foreach ($arr_shared_ou_id as $v_shared_ou_id)
                {
                    $sql = "Insert Into t_edoc_folder_user(FK_FOLDER, FK_USER, C_USER_TYPE) Values($v_folder_id, $v_shared_ou_id, 'OU')";
                    $this->db->Execute($sql);
                }
            }
        }

        //Done
        $this->exec_done($this->goback_url, $arr_filter);
    }

    public function delete_folder()
    {
        $v_folder_id_list = isset($_POST['hdn_item_id_list']) ? $this->replace_bad_char($_POST['hdn_item_id_list']) : 0;

        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? $this->replace_bad_char($_POST['txt_filter']) : '';
        $v_page = isset($_POST['sel_goto_page']) ? $this->replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? $this->replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
        $arr_filter = array(
                            'txt_filter'   => $v_filter,
                            'sel_goto_page'   => $v_page,
                            'sel_rows_per_page'   => $v_rows_per_page
                        );


        if ($v_folder_id_list != '')
        {
            $sql = "Delete From t_edoc_folder Where PK_FOLDER In ($v_folder_id_list)";
            $this->db->Execute($sql);
        }

        $this->exec_done($this->goback_url, $arr_filter);
    }

    //Lay danh sach NSD co lien quan (duoc chia se) voi HS Luu
    public function qry_all_shared_user($p_folder_id)
    {
        if(DATABASE_TYPE == 'MSSQL')
        {
	    	$stmt = "Select
	                    U.PK_USER
	                    ,U.C_NAME
	                From T_USER  as U Left Join T_EDOC_FOLDER_USER as FU on U.PK_USER=FU.FK_USER
	                Where FU.FK_FOLDER=? And FU.C_USER_TYPE='USER'
	                    And U.C_STATUS > 0";
        }
        elseif(DATABASE_TYPE == 'MYSQL')
        {
        	$stmt = "Select
	                    U.PK_USER
	                    ,U.C_NAME
	                From t_cores_user  as U Left Join t_edoc_folder_user as FU on U.PK_USER=FU.FK_USER
	                Where FU.FK_FOLDER=? And FU.C_USER_TYPE='USER'
	                    And U.C_STATUS > 0";
        }
        $params = array($p_folder_id);

        return $this->db->getAll($stmt, $params);
    }
    //Lay danh sach Phong ban co lien quan (duoc chia se) voi HS Luu
    public function qry_all_shared_ou($p_folder_id)
    {	if(DATABASE_TYPE == 'MSSQL')
 	   {
	        $stmt = "Select
	                    OU.PK_OU
	                    ,OU.C_NAME
	                From T_OU  as OU Left Join T_EDOC_FOLDER_USER as FU on OU.PK_OU=FU.FK_USER
	                Where FU.FK_FOLDER=? And FU.C_USER_TYPE='OU'";
	        $params = array($p_folder_id);	
 	   }
 	   elseif(DATABASE_TYPE == 'MYSQL')
 	   {
	 	   	$stmt = "Select
		                    OU.PK_OU
		                    ,OU.C_NAME
		                From t_cores_ou  as OU Left Join t_edoc_folder_user as FU on OU.PK_OU=FU.FK_USER
		                Where FU.FK_FOLDER=? And FU.C_USER_TYPE='OU'";
	 	   	$params = array($p_folder_id);
 	   }
		
        return $this->db->getAll($stmt, $params);
    }
}

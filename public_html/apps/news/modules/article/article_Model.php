<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class article_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    public function qry_all_article($p_filter)
    {
        $v_login_name = session::get('login_name');
        if(DATABASE_TYPE == 'MSSQL')
        {
	        $sql = 'Select A.*
	                From T_NEWS_ARTICLE A
	                Where (1>0) ';
	        //$sql .= " And ( (DateDiff(day,A.C_BEGIN_DATE, getDate()) >= 0 And A.C_STATUS >0)  Or A.C_CREATE_BY='$v_login_name')";
	        $sql .= " And ( (DateDiff(day,A.C_BEGIN_DATE, getDate()) >= 0 )  Or A.C_CREATE_BY='$v_login_name')";

        }
        elseif(DATABASE_TYPE == 'MYSQL')
        {
        	$sql = 'Select A.*
	                From t_news_article A
	                Where (1>0) ';
        	//$sql .= " And ( (DateDiff(day,A.C_BEGIN_DATE, getDate()) >= 0 And A.C_STATUS >0)  Or A.C_CREATE_BY='$v_login_name')";
        	$sql .= " And ( (DateDiff(A.C_BEGIN_DATE, NOW()) <= 0 )  Or A.C_CREATE_BY='$v_login_name')";
        }
        if ($p_filter != '')
        {
            $sql .= " And (A.C_TITLE like '%$p_filter%')";
        }

        $sql .= ' Order By A.C_BEGIN_DATE desc, A.C_CREATE_DATE desc';

        return $this->db->GetAll($sql);
    }

    public function qry_single_article($p_article_id)
    {
        if ($p_article_id > 0)
        {
            $stmt = 'Select A.* From t_news_article A Where PK_ARTICLE=?';

            return $this->db->getRow($stmt, array($p_article_id));
        }

        return array();
    }

    public function update_article()
    {
        $v_article_id = isset($_POST['hdn_item_id']) ? $this->replace_bad_char($_POST['hdn_item_id']) : 0;
        $v_title = isset($_POST['txt_title']) ? $this->replace_bad_char($_POST['txt_title']) : '';
        $v_short_content = isset($_POST['short_content']) ? ($_POST['short_content']) : '';
        $v_begin_date = isset($_POST['txt_begin_date']) ? $this->replace_bad_char($_POST['txt_begin_date']) : date('d/m/Y');
		
        $v_status         = isset($_POST['chk_status']) ? 1 :0;
        $v_link_to_attach = isset($_POST['chk_link_to_attach']) ? 1 : 0;
        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);

        if ($v_article_id < 1)
        {
            //Kiem tra quyen them moi
            if (!Controller::check_permission('THEM_MOI_TIN_BAI'))
            {
                Die('Access denied!');
            }
            if(DATABASE_TYPE == 'MSSQL')
            {
            	$stmt = 'Insert Into T_NEWS_ARTICLE(C_TITLE, C_BEGIN_DATE,C_SHORT_CONTENT, C_CREATE_BY, C_LINK_TO_ATTACH, C_STATUS, C_CREATE_DATE) Values(?, ?, N?, ?, ?, getDate())';
            }
            elseif(DATABASE_TYPE == 'MYSQL')
            {
            	$stmt = 'Insert Into t_news_article(C_TITLE, C_BEGIN_DATE,C_SHORT_CONTENT, C_CREATE_BY, C_LINK_TO_ATTACH,C_STATUS, C_CREATE_DATE) Values(?, ?, ?, ?, ?, ?,Now())';            	 
			
            }
            $params = array($v_title, $v_begin_date, $v_short_content , session::get('login_name'), $v_link_to_attach, $v_status);
            //echo $stmt; Var_Dump($params);exit;
            $this->db->Execute($stmt, $params);
			if(DATABASE_TYPE == 'MSSQL')
			{
            	$v_article_id = $this->db->getOne("Select IDENT_CURRENT('T_NEWS_ARTICLE')");
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$v_article_id = $this->db->Insert_ID('t_news_article');
			}
        }
        else
        {
            //Kiem tra quyen cap nhat tin bai
            $v_deny = FALSE;
            if (!Controller::check_permission('SUA_TIN_BAI'))
            {
                $v_deny = TRUE;
            }
            //Nguoi tao tin bai nay
            $v_creat_login_name = $this->db->getOne("Select C_CREATE_BY From t_news_article Where PK_ARTICLE=$v_article_id");
            if ($v_creat_login_name != session::get('login_name'))
            {
                $v_deny = TRUE;
            }

            if ($v_deny)
            {
                Die('Access denied!');
            }
			if(DATABASE_TYPE == 'MSSQL')
			{
	            $stmt = 'Update T_NEWS_ARTICLE Set
	                            C_TITLE = ?
	                            ,C_BEGIN_DATE = ?
	                            ,C_SHORT_CONTENT = N?
	                            ,C_UPDATE_BY=?
	                            ,C_UPDATE_DATE=getDate()
	                    		,C_STATUS        
	            				,C_LINK_TO_ATTACH=?	
	                    Where PK_ARTICLE=?';
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = 'Update t_news_article Set
	                            C_TITLE = ?
	                            ,C_BEGIN_DATE = ?
	                            ,C_SHORT_CONTENT = ?
	                            ,C_UPDATE_BY=?
	                            ,C_UPDATE_DATE=Now()
								,C_STATUS=?
	                            ,C_LINK_TO_ATTACH=?
	                    Where PK_ARTICLE=?';
			}
            $params = array($v_title, $v_begin_date, $v_short_content , session::get('login_name'),$v_status, $v_link_to_attach, $v_article_id);
            $this->db->Execute($stmt, $params);
        }

        //Cap nhat thong tin file dinh kem
        require_once(SERVER_ROOT . 'libs/upload.class.php');
        $v_upload_folder = CONST_SERVER_DOC_FILE_UPLOAD_DIR . 'news/';

        $upload = new zUpload();
        //File dinh kem
        $upload->set_limit_file_extension(array('pdf', 'zip', 'rar', 'doc', 'docx'));
        $arr_result = $upload->upload('file_attach', $v_upload_folder, 'real');
        if ($arr_result['error'] == 0)
        {
            foreach ($arr_result['des_file_name'] as $v_attach_file_name)
            {
                $stmt = 'Update t_news_article Set C_ATTACH_FILE_NAME=? Where PK_ARTICLE=?';
                $this->db->Execute($stmt, array($v_attach_file_name, $v_article_id));
            }
        }

        //Xoa file dinh kem
        $v_hdn_deleted_attach_file = isset($_POST['hdn_deleted_attach_file']) ? $this->replace_bad_char($_POST['hdn_deleted_attach_file']) : '';
        if ($v_hdn_deleted_attach_file != '')
        {
            $stmt = "Update t_news_article Set C_ATTACH_FILE_NAME=Null Where PK_ARTICLE=$v_article_id";
            $this->db->Execute($stmt);
        }

        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? $this->replace_bad_char($_POST['txt_filter']) : '';
        $v_page = isset($_POST['sel_goto_page']) ? $this->replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? $this->replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
        $arr_filter = array(
                            'txt_filter'   => $v_filter,
                            'sel_goto_page'   => $v_page,
                            'sel_rows_per_page'   => $v_rows_per_page
                        );
       $this->exec_done($this->goback_url, $arr_filter);
    }

    public function qry_all_attach_file($p_article_id)
    {
        $stmt = 'Select PK_FILE, C_FILE_NAME From t_news_article_attach_file Where FK_ARTICLE=?';
        return $this->db->GetAll($stmt, array($p_article_id));
    }

    public function delete_article()
    {
        $v_article_id_list = isset($_POST['hdn_item_id_list']) ? $this->replace_bad_char($_POST['hdn_item_id_list']) : 0;

        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? $this->replace_bad_char($_POST['txt_filter']) : '';
        $v_page = isset($_POST['sel_goto_page']) ? $this->replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? $this->replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
        $arr_filter = array(
                            'txt_filter'   => $v_filter,
                            'sel_goto_page'   => $v_page,
                            'sel_rows_per_page'   => $v_rows_per_page
                        );


        if ($v_article_id_list != '')
        {
            //Xoa file dinh kem
            $this->db->Execute("Delete From t_news_article_attach_file Where FK_ARTICLE in ($v_article_id_list)");
            //Xoa tin
            $this->db->Execute("Delete From t_news_article Where PK_ARTICLE In ($v_article_id_list)");
        }

        $this->exec_done($this->goback_url, $arr_filter);
    }
}
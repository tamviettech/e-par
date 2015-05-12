<?php

defined('DS') or die();

class weblink_Model extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function qry_all_weblink()
    {
        $website = Session::get('session_website_id');
        $website = isset($website) ? (int)$website : 0;
        
        $sql = "
            Select PK_WEBLINK, C_URL, C_NAME, C_BEGIN_DATE, C_STATUS, FK_TYPE
            ,DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y')as C_BEGIN_DATE
            From t_ps_weblink
            Where FK_WEBSITE = $website
            Order By C_ORDER
        ";
        return $this->db->getAll($sql);
    }

    function qry_single_weblink($id)
    {
        $website = Session::get('session_website_id');
        $website = isset($website) ? (int)$website : 0;
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select
                        W.PK_WEBLINK, W.C_FILE_NAME, W.C_URL, W.C_NAME
                        , W.FK_USER, W.C_STATUS, W.C_NEW_WINDOWN
                        , Convert(varchar, W.C_BEGIN_DATE, 103) As C_BEGIN_DATE
                        , Convert(varchar, W.C_END_DATE, 103) AS C_END_DATE
                        , Convert(varchar, W.C_INIT_DATE, 103) As C_INIT_DATE
                        , U.C_NAME as C_INIT_USER_NAME
                    From t_ps_weblink W
                    Left Join t_cores_user U
                    On W.FK_USER = U.PK_USER
                    Where FK_WEBSITE = $website
                    And PK_WEBLINK = $id
                ";
                    
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $sql = "Select
                        W.PK_WEBLINK, W.C_FILE_NAME, W.C_URL, W.C_NAME
                        , W.FK_USER, W.C_STATUS, W.C_NEW_WINDOWN
                        , DATE_FORMAT(W.C_BEGIN_DATE,'%d-%m-%Y') As C_BEGIN_DATE
                        , DATE_FORMAT(W.C_END_DATE,'%d-%m-%Y') AS C_END_DATE
                        , DATE_FORMAT(W.C_INIT_DATE,'%d-%m-%Y') As C_INIT_DATE
                        , U.C_NAME as C_INIT_USER_NAME
                        , W.FK_TYPE
                    From t_ps_weblink W
                    Left Join t_cores_user U
                    On W.FK_USER = U.PK_USER
                    Where FK_WEBSITE = $website
                    And PK_WEBLINK = $id
                ";
        }
        
        return $this->db->getRow($sql);
    }

    function update_weblink()
    {
        //get data
        $v_id           = (int) get_post_var('hdn_item_id');
        $v_name         = get_post_var('txt_name');
        $v_url          = get_post_var('txt_url');
        $v_begin_date   = get_post_var('txt_begin_date');
        $v_end_date     = get_post_var('txt_end_date');
        $v_init_date    = date('Y-m-d');
        
        $v_status       = isset($_POST['chk_status'])?1:0;
        
        $v_new_window   = isset($_POST['chk_new_window'])?1:0;
        
        $v_type_id      = get_post_var('rad_group','');
        
        $v_logo         = get_post_var('hdn_logo', null);
        $v_user_id      = Session::get('user_id');
        $v_website_id   = Session::get('session_website_id');
        $v_website_id   = isset($v_website_id) ? (int)$v_website_id : 0;
        //format
        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
        $v_end_date   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);

        
        //validate
        if (
                $v_name == ''
                or $v_url == ''
                or $v_begin_date == ''
                or $v_end_date == ''
        )
        {
            $this->exec_fail($this->goback_url, __('invalid request data'));
        }

        if ($v_id == 0) //insert
        {
            $sql = "
                Insert Into t_ps_weblink(
                    C_NAME, C_URL, C_INIT_DATE, C_BEGIN_DATE, C_END_DATE
                    , C_NEW_WINDOWN, FK_USER, C_FILE_NAME, C_STATUS, FK_WEBSITE,FK_TYPE
                )
                Values(
                    '$v_name', '$v_url', '$v_init_date', '$v_begin_date', '$v_end_date', $v_new_window
                    , $v_user_id, '$v_logo', $v_status, $v_website_id,$v_type_id
                )
            ";
        }
        else
        {
            $sql = "
                Update t_ps_weblink Set
                    C_NAME = '$v_name'
                    , C_URL = '$v_url'
                    , C_INIT_DATE = '$v_init_date'
                    , C_BEGIN_DATE = '$v_begin_date'
                    , C_END_DATE = '$v_end_date'
                    , C_NEW_WINDOWN = '$v_new_window'
                    , FK_USER = '$v_user_id'
                    , C_FILE_NAME = '$v_logo'
                    , C_STATUS = '$v_status'
                    , FK_WEBSITE = '$v_website_id'
                    , FK_TYPE = $v_type_id
                 Where PK_WEBLINK = $v_id
                 And FK_WEBSITE = $v_website_id
            ";
        }
        $this->db->Execute($sql);
        if ($this->db->errorNo() == 0)
        {
            $table = 't_ps_weblink';
            $pk_col = 'PK_WEBLINK';
            $order_col = 'C_ORDER';
            $other_clause = ' AND FK_WEBSITE = ' . $v_website_id;
            $this->build_order($table, $pk_col, $order_col, $other_clause);

            $this->exec_done($this->goback_url);
        }
        else
        {
            $this->exec_fail($this->goback_url, __('update error'));
        }
    }

    function delete_weblink()
    {
        $arr_delete = get_post_var('chk_item', array(), false);
        $website = Session::get('session_website_id');
        $website   = isset($website) ? (int)$website : 0;
        if (is_array($arr_delete) == false && isset($arr_delete[0]) == false)
        {
            die(_(('invalid request data')));
        }

        $arr_delete = replace_bad_char(implode(',', $arr_delete));
        $sql = "
            Delete From t_ps_weblink Where PK_WEBLINK In($arr_delete) And FK_WEBSITE = $website
        ";
        $this->db->Execute($sql);
    }
    
    
    public function qry_all_group_type()
    {
        $sql = "Select PK_LIST,C_NAME
                From t_cores_list
                Where FK_LISTTYPE = (Select
                                       PK_LISTTYPE
                                     From t_cores_listtype
                                     Where C_CODE = '".CONST_WEBLINK_GROUP."')";
        
        return $this->db->getAll($sql);
    }
}

?>

<?php
/**
 * @copyright	Copyright (C) 2012 Tam Viet Tech. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Ngo Duc Lien <liennd@gmail.com>
 * @author		Luong Thanh Binh <ltbinh@gmail.com>
 */

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class user_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }

    public function qry_ou_tree()
    {
        $sql = 'Select PK_OU, FK_OU, C_NAME, C_ORDER, C_INTERNAL_ORDER From t_cores_ou Order By C_INTERNAL_ORDER';
        $this->db->debug = 0;
        return $this->db->getAll($sql);
    }

    public function get_root_ou()
    {
        return $this->db->getOne('Select PK_OU From t_cores_ou Where (FK_OU < 0 Or FK_OU Is Null)');
    }

    /**
     * Lấy danh sách Đơn vị cấp dưới
     * @param Int $ou_id ID đơn vị
     */

    public function qry_all_sub_ou($ou_id)
    {
        $stmt = 'Select PK_OU, FK_OU, C_NAME, C_ORDER From t_cores_ou Where FK_OU=? Order By C_INTERNAL_ORDER';
        $params = array($ou_id);

        return $this->db->getAll($stmt, $params);
    }

    public function qry_all_user_by_ou($ou_id)
    {
        $stmt = 'Select * From t_cores_user Where FK_OU=? Order By C_ORDER';
        $params = array($ou_id);

        return $this->db->getAll($stmt, $params);
    }

    public function qry_all_group_by_ou($ou_id)
    {
        $stmt = 'Select * From t_cores_group Where FK_OU=? Order By C_NAME';
        $params = array($ou_id);
        return $this->db->getAll($stmt, $params);
    }

    public function qry_ou_path($ou_id)
    {
        if (!( preg_match( '/^\d*$/', trim($ou_id)) == 1 ))
        {
            $ou_id = $this->get_root_ou();
        }

        if (DATABASE_TYPE == 'MSSQL')
        {
            return $this->db->getAssoc("Select PK_OU, C_NAME From  dbo.f_qry_ou_path($ou_id) Order By C_INTERNAL_ORDER");
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $ret_array = array();

            $stmt = 'Select PK_OU, C_NAME, C_INTERNAL_ORDER, FK_OU From t_cores_ou Where PK_OU=?';
            $params = array($ou_id);
            $arr_ou_info = $this->db->getRow($stmt, $params);

            $v_parent_ou_id   = $arr_ou_info['FK_OU'];
            $v_internal_order = $arr_ou_info['C_INTERNAL_ORDER'];
            $v_ou_id          = $arr_ou_info['PK_OU'];
            $v_ou_name        = $arr_ou_info['C_NAME'];

            $ret_array[$v_ou_name] =  $v_ou_id;
            while (strlen($v_internal_order) > 3)
            {
                $stmt = 'Select PK_OU, C_NAME, C_INTERNAL_ORDER, FK_OU From t_cores_ou Where PK_OU=?';
                $params = array($v_parent_ou_id);
                $arr_ou_info = $this->db->getRow($stmt, $params);

                $v_parent_ou_id   = $arr_ou_info['FK_OU'];
                $v_internal_order = $arr_ou_info['C_INTERNAL_ORDER'];
                $v_ou_id          = $arr_ou_info['PK_OU'];
                $v_ou_name        = $arr_ou_info['C_NAME'];

                $ret_array[$v_ou_name] =  $v_ou_id;
            }
            return array_flip(array_reverse($ret_array));
        }
    }

    public function qry_single_ou($ou_id)
    {
        if ($ou_id > 0)
        {
            $stmt = 'Select * From t_cores_ou Where PK_OU=?';
            return $this->db->getRow($stmt, array($ou_id));
        }
        else
        {
            $v_next_order = $this->get_max('t_cores_ou', 'C_ORDER', ' FK_OU <> -1') + 1;
            return array('C_ORDER' => $v_next_order);
        }
    }

    public function update_ou()
    {
        $v_parent_ou_id        = get_post_var('hdn_parent_ou_id',0);
        $v_ou_id               = get_post_var('hdn_item_id',0);
        $v_name                = get_post_var('txt_name');
        $v_order               = get_post_var('txt_order');
        $v_xml_data            = get_post_var('XmlData','<data/>',0);

        //Kiem tra trung ten
        $stmt = 'Select Count(*) From t_cores_ou Where C_NAME=? And PK_OU <> ?';
        $params = array($v_name, $v_ou_id);
        $v_duplicate_name = $this->db->getOne($stmt, $params);

        if ($v_duplicate_name > 0)
        {
            $this->popup_exec_fail(__('Tên đơn vị đã tồn tại!'));
            return;
        }

        if ($v_ou_id < 1)
        {
            $stmt = 'Insert Into t_cores_ou(FK_OU, C_NAME,C_ORDER) Values(?, ?, ?)';
            $params = array( $v_parent_ou_id
                            ,$v_name
                            ,$v_order
            );
            $this->db->Execute($stmt, $params);

            $v_ou_id = $this->get_last_inserted_id('t_cores_ou','PK_OU');

            $v_current_order = -1;
        }
        else
        {
            $v_current_order = $this->db->getOne('Select C_ORDER From t_cores_ou Where PK_OU=?', array($v_ou_id));
            $stmt = 'Update t_cores_ou Set
                        C_NAME=N?
                        ,C_ORDER=?
                    Where PK_OU=?';
            $params = array(
                        $v_name
                        ,$v_order
                        ,$v_ou_id
            );

            $this->db->Execute($stmt, $params);
        }

        //reorder
        $this->ReOrder('t_cores_ou','PK_OU','C_ORDER', $v_ou_id, $v_order, $v_current_order, " FK_OU=$v_parent_ou_id AND PK_OU <> $v_parent_ou_id");

        //Rebuild internal order
        $this->build_interal_order('t_cores_ou', 'PK_OU','FK_OU',-1);

        $this->popup_exec_done();
    }

    public function delete_ou()
    {
        $v_ou_id = get_post_var('hdn_item_id',0);

        //Kiem tra co don vi con, hoac user, hoac nhom trong khong
        $stmt = 'Select SUM(a.C_COUNT) C_COUNT
                From (
                    Select COUNT(*) C_COUNT From t_cores_ou Where FK_OU=?
                    Union
                    Select COUNT(*) C_COUNT From t_cores_user Where FK_OU=?
                    Union
                    Select COUNT(*) C_COUNT From t_cores_group Where FK_OU=?
                    ) a ';
        $params = array($v_ou_id, $v_ou_id, $v_ou_id);
        $v_count = $this->db->getOne($stmt, $params);

        if ($v_count < 1)
        {
            $stmt = 'Delete From t_cores_ou Where PK_OU=?';
            $params = array($v_ou_id);
            $this->db->Execute($stmt, $params);
        }

        $this->exec_done($this->goback_url);
    }

    public function qry_single_user($user_id)
    {
        $v_parent_ou_id = get_request_var('parent_ou_id');

        if ($user_id > 0)
        {
            $stmt = 'Select * From t_cores_user Where PK_USER=?';
            return $this->db->getRow($stmt, $user_id);
        }
        else
        {
            $v_next_order = $this->get_max('t_cores_user', 'C_ORDER', "FK_OU=$v_parent_ou_id");
            return array('C_ORDER' => $v_next_order + 1);
        }
    }


    public function update_user()
    {
        $v_user_id          = get_post_var('hdn_item_id',0);
        $v_ou_id            = get_post_var('hdn_parent_ou_id',0);
        $v_name             = get_post_var('txt_name','');
        $v_password         = get_post_var('txt_password','');
        $v_order            = get_post_var('txt_order','0');
        $v_status           = isset($_POST['chk_status']) ? 1 : 0;
        $v_xml_data         = get_post_var('XmlData','<data/>',0);
        $v_job_title        = get_post_var('txt_job_title','');
        $v_login_name       = get_post_var('txt_login_name','');
        $v_login_name       = str_replace(',', '', $v_login_name);

        $v_group_id_list       = get_post_var('hdn_group_id_list','');

        //Kiem tra trung ten dang nhap
        $stmt = 'Select Count(*) From t_cores_user Where C_LOGIN_NAME=? And PK_USER <> ?';
        $params = array($v_login_name, $v_user_id);
        $v_duplicate_login_name = $this->db->getOne($stmt, $params);

        if ($v_duplicate_login_name)
        {
            $this->exec_fail($this->goback_url, 'Tên đăng nhập đã tồn tại!');
            return;
        }

        if ($v_user_id > 0)  //Update
        {
            $stmt = 'Update t_cores_user Set
                        C_NAME=?
                        ,C_ORDER=?
                        ,C_STATUS=?
                        ,FK_OU=?
                        ,C_XML_DATA=?
                        ,C_JOB_TITLE=?
                    Where PK_USER=?';
            $params = array(
                    $v_name
                    ,$v_order
                    ,$v_status
                    ,$v_ou_id
                    ,$v_xml_data
                    ,$v_job_title
                    ,$v_user_id
            );
            $this->db->Execute($stmt, $params);

            //Co thay doi mat khau khong
            if ($v_password != '')
            {
                $this->db->Execute("Update t_cores_user Set C_PASSWORD=md5('$v_password') Where PK_USER=$v_user_id");
            }
        }
        else  //Insert
        {
            $stmt = 'Insert Into t_cores_user(C_LOGIN_NAME, C_NAME, C_PASSWORD
                    , C_ORDER, C_STATUS, FK_OU, C_XML_DATA, C_JOB_TITLE) values (?,?,md5(?),?,?,?, ?, ?)';
            $params = array(
                    $v_login_name
                    ,$v_name
                    ,$v_password
                    ,$v_order
                    ,$v_status
                    ,$v_ou_id
                    ,$v_xml_data
                    ,$v_job_title
            );
            $this->db->Execute($stmt, $params);

            $v_user_id = $this->get_last_inserted_id('t_cores_user','PK_USER');
        }
        //Reorder
        $this->ReOrder('t_cores_user','PK_USER','C_ORDER', $v_user_id, $v_order, -1, "FK_OU=$v_ou_id");

        //Cap nhat thong tin nhom
        //Xoa het du lieu cu
        $stmt = 'Delete From t_cores_user_group Where FK_USER=?';
        $this->db->execute($stmt, array($v_user_id));
        //Cap nhat du lieu moi
        $arr_group_id_list = explode(',', $v_group_id_list);
        foreach ($arr_group_id_list as $v_group_id)
        {
            $stmt = 'Insert Into t_cores_user_group(FK_GROUP, FK_USER) Values(?, ?)';
            $params = array($v_group_id, $v_user_id );
            $this->db->Execute($stmt, $params);
        }

        //Cap nhat thong tin quyen tren ung dung
        $v_application_id   = get_post_var('sel_application',0);
        $v_grant_function   = get_post_var('hdn_grant_function','');
        //Xoa het thong tin cu
        $this->db->Execute('Delete From t_cores_user_function Where FK_USER=? And FK_APPLICATION=?', array($v_user_id, $v_application_id));
        $arr_grant_function = explode(',', $v_grant_function);
        foreach ($arr_grant_function as $v_function)
        {
            if ( $v_function != NULL && $v_function != '')
            {
                $stmt = 'Insert Into t_cores_user_function(FK_USER, FK_APPLICATION, C_FUNCTION_CODE) Values (?, ? , ?)';
                $params = array($v_user_id, $v_application_id, trim($v_function));
                $this->db->Execute($stmt, $params);
            }
        }

        $this->popup_exec_done(NULL);
    }

    public function delete_user()
    {
        $v_user_id = get_post_var('hdn_item_id',0);

        //Xoa NSD khoi nhom
        $stmt = 'Delete From t_cores_user_group Where FK_USER=?';
        $params = array($v_user_id);
        $this->db->Execute($stmt, $params);

        //Xoa quyen
        $stmt = 'Delete From t_cores_user_function Where FK_USER=?';
        $params = array($v_user_id);
        $this->db->Execute($stmt, $params);

        $stmt = 'Delete From t_cores_user Where PK_USER=?';
        $params = array($v_user_id);
        $this->db->Execute($stmt, $params);

        $this->exec_done($this->goback_url);
    }

    public function qry_all_application_option()
    {
        $sql = 'Select PK_APPLICATION, C_NAME
                From t_cores_application
                Where C_STATUS > 0
                Order By C_ORDER';
        $this->db->debug = 0;
        return $this->db->getAssoc($sql);
    }

    public function qry_single_group($group_id)
    {
        if ($group_id > 0)
        {
            $stmt = 'Select * From t_cores_group Where PK_GROUP=?';
            $params = array($group_id);

            return $this->db->getRow($stmt, $params);
        }

        return array();
    }

    public function qry_all_user_by_group($group_id)
    {
        $stmt = 'Select u.PK_USER
                        ,u.C_NAME
                        ,u.C_STATUS
                From t_cores_user u left join  t_cores_user_group g on u.PK_USER=g.FK_USER
                Where g.FK_GROUP=?';
        $params = array($group_id);

        return $this->db->getAll($stmt, $params);
    }

    public function update_group()
    {
        $v_group_id         = get_post_var('hdn_item_id');
        $v_ou_id            = get_post_var('hdn_parent_ou_id');
        $v_code             = get_post_var('txt_code');
        $v_name             = get_post_var('txt_name');
        $v_user_id_list     = get_post_var('hdn_user_id_list');

        //Kiem tra trung ma, trung ten
        $stmt = 'Select Count(*) From t_cores_group Where C_CODE=? And PK_GROUP <> ?';
        $params = array($v_code, $v_group_id);
        $v_duplicate_code = $this->db->getOne($stmt, $params);
        if ($v_duplicate_code)
        {
            $this->popup_exec_fail('Mã nhóm đã tồn tại!');
            return;
        }

        $stmt = 'Select Count(*) From t_cores_group Where C_NAME=? And PK_GROUP <> ?';
        $params = array($v_name, $v_group_id);
        $v_duplicate_code = $this->db->getOne($stmt, $params);
        if ($v_duplicate_code)
        {
            $this->popup_exec_fail('Tên nhóm đã tồn tại!');
            return;
        }

        if ($v_group_id < 1)
        {
            $stmt = 'Insert Into t_cores_group(FK_OU, C_CODE, C_NAME) Values(?, ?, ?)';
            $params = array($v_ou_id, $v_code, $v_name);

            $this->db->Execute($stmt, $params);

            $v_group_id = $this->get_last_inserted_id('t_cores_group','PK_GROUP');
        }
        else
        {
            $stmt = 'Update t_cores_group Set
                                FK_OU=?
                                ,C_CODE=?
                                ,C_NAME=?
                    Where PK_GROUP=?';

            $params = array($v_ou_id, $v_code, $v_name, $v_group_id);

            $this->db->Execute($stmt, $params);
        }

        //Cap nhat NSD trong nhom
        //Xoa het du lieu cu
        $stmt = 'Delete From t_cores_user_group Where FK_GROUP=?';
        $this->db->execute($stmt, array($v_group_id));
        //Cap nhat du lieu moi
        $arr_user_id_list = explode(',', $v_user_id_list);
        foreach ($arr_user_id_list as $v_user_id)
        {
            $stmt = 'Insert Into t_cores_user_group(FK_GROUP, FK_USER) Values(?, ?)';
            $params = array($v_group_id, $v_user_id );
            $this->db->Execute($stmt, $params);
        }

        //Cap nhat quyen cua nhom
        $v_application_id    = get_post_var('sel_application',0);
        $v_grant_function    = get_post_var('hdn_grant_function','');

        //Xoa het thong tin cac quyen cu
        $this->db->Execute('Delete From t_cores_group_function Where FK_GROUP=? And FK_APPLICATION=?', array($v_group_id, $v_application_id));

        //Them quyen moi
        $arr_grant_function = explode(',', $v_grant_function);
        foreach ($arr_grant_function as $v_function)
        {
            if ( $v_function != NULL && $v_function != '')
            {
                $stmt = 'Insert Into t_cores_group_function(FK_GROUP, FK_APPLICATION, C_FUNCTION_CODE) Values (?, ? , ?)';
                $params = array($v_group_id, $v_application_id, trim($v_function));
                $this->db->Execute($stmt, $params);
            }
        }

        $this->popup_exec_done();
    }

    public function delete_group()
    {
        $v_group_id = get_post_var('hdn_item_id',0);

        $v_is_build_in = $this->db->getOne('Select C_BUILT_IN From t_cores_group Where PK_GROUP=?', array($v_group_id));

        if ($v_is_build_in == 0)
        {
            //Xoa NSD trong nhom
            $stmt = 'Delete From t_cores_user_group Where FK_GROUP=?';
            $params = array($v_group_id);
            $this->db->Execute($stmt, $params);

            //Xoa quyen cua nhom
            $stmt = 'Delete From t_cores_group_function Where FK_GROUP=?';
            $params = array($v_group_id);
            $this->db->Execute($stmt, $params);

            //Xoa
            $stmt = 'Delete From t_cores_group Where PK_GROUP=? And (Select Count(*) From t_cores_user_group Where FK_GROUP=?) = 0';
            $params = array($v_group_id, $v_group_id);
            $this->db->Execute($stmt, $params);
        }

        $this->exec_done($this->goback_url);
    }

    public function qry_all_user_to_add($my_dept_only=0)
    {

        $stmt = 'Select PK_USER, C_LOGIN_NAME as C_CODE, C_NAME, C_STATUS,C_JOB_TITLE From t_cores_user Where C_STATUS > 0';
        if ($my_dept_only == 1)
        {
            $v_user_code = Session::get('user_code');
            $stmt .= "  And FK_OU=(Select FK_OU From t_cores_user Where C_LOGIN_NAME='$v_user_code')";
        }

        $v_group_code = isset($_REQUEST['group']) ? replace_bad_char($_REQUEST['group']) : '';
        if ($v_group_code != '')
        {
            $stmt .= "  And PK_USER In (Select FK_USER
                                        From t_cores_user_group UG Right Join t_cores_group G On UG.FK_GROUP=G.PK_GROUP
                                        Where G.C_CODE='$v_group_code')";
        }

        $stmt .= ' Order By C_NAME';

        return $this->db->getAll($stmt);
    }

    /**
     * Lấy danh sách NSD theo phòng ban
     */
    public function qry_all_user_by_ou_to_add()
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = 'Select
                        OU.PK_OU
                        , OU.C_NAME
                        ,(Select PK_USER, C_LOGIN_NAME, C_NAME, C_JOB_TITLE From t_cores_user Where FK_OU=OU.PK_OU And C_STATUS > 0 For XML Raw) C_XML_USER
                    From t_cores_ou OU Order By C_INTERNAL_ORDER';

            return $this->db->getAll($stmt);
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select
                        OU.PK_OU
                        , OU.C_NAME
                        ,(Select GROUP_CONCAT('<row'
                                                , Concat(' PK_USER=\"', PK_USER, '\"')
                                                , Concat(' C_LOGIN_NAME=\"', C_LOGIN_NAME, '\"')
                                                , Concat(' C_NAME=\"', C_NAME, '\"')
                                                , Concat(' C_JOB_TITLE=\"', C_JOB_TITLE, '\"')
                                                , ' /> '
                                                SEPARATOR ''
                                             )
                         from t_cores_user Where FK_OU=OU.PK_OU ) AS C_XML_USER
                    From t_cores_ou OU
                    Order By C_INTERNAL_ORDER";
            $arr_all_ou = $this->db->getAll($stmt);
            return $arr_all_ou;
            /*
            for ($i=0; $i<sizeof($arr_all_ou); $i++)
            {
                $stmt = 'Select
                            PK_USER
                            , C_LOGIN_NAME
                            , C_NAME
                            , C_JOB_TITLE
                        From t_cores_user
                        Where FK_OU=?
                            And C_STATUS > 0
                        Order By C_ORDER';
                $params = array($arr_all_ou[$i]['PK_OU']);
                $arr_all_user_by_ou = $this->db->getAll($stmt, $params);
                $v_xml_user = '';
                for($j=0;$j<sizeof($arr_all_user_by_ou);$j++)
                {
                    $v_xml_user .= '<row';
                    $v_xml_user .= ' PK_USER="' . $arr_all_user_by_ou[$j]['PK_USER'] . '"';
                    $v_xml_user .= ' C_LOGIN_NAME="' . $arr_all_user_by_ou[$j]['C_LOGIN_NAME'] . '"';
                    $v_xml_user .= ' C_NAME="' . $arr_all_user_by_ou[$j]['C_NAME'] . '"';
                    $v_xml_user .= ' C_JOB_TITLE="' . $arr_all_user_by_ou[$j]['C_JOB_TITLE'] . '"';
                    $v_xml_user .= '/>';
                }

                $arr_all_ou[$i]['C_XML_USER'] = $v_xml_user;
            } //end for $i
            return $arr_all_ou;
            */
        } //end if DATABASE_TYPE

        return array();
    }

    /**
     * Danh sach nhom ma 1 user la thanh vien
     * @param unknown $user_id
     */
    public function qry_all_group_by_user($user_id)
    {
        $stmt = 'Select g.PK_GROUP, g.C_NAME
                From t_cores_group g left join t_cores_user_group ug on g.PK_GROUP=ug.FK_GROUP
                WHere ug.FK_USER=?';
        $params = array($user_id);

        return $this->db->getAssoc($stmt, $params);
    }

    public function qry_all_group_to_add($my_dept_only=0)
    {
        $stmt = 'Select PK_GROUP, C_CODE, C_NAME From t_cores_group';
        if ($my_dept_only == 1)
        {
            $v_user_code = Session::get('user_code');
            $stmt .= " Where FK_OU=(Select FK_OU From t_cores_user Where C_LOGIN_NAME='$v_user_code')";
        }
        $stmt .= ' Order By C_NAME';

        return $this->db->getAll($stmt);
    }

    public function qry_all_user_to_grand($v_filter)
    {
        $stmt = 'Select PK_USER, C_NAME, C_STATUS From t_cores_user ';
        if ($v_filter != '')
        {
            $stmt .= " Where C_NAME like '%$v_filter%' ";
        }
        $stmt .= ' Order By C_NAME';
        return $this->db->getAll($stmt);
    }

    public function qry_all_group_to_grand($v_filter)
    {
        $stmt = 'Select PK_GROUP, C_NAME From t_cores_group ';
        if ($v_filter != '')
        {
            $stmt .= " Where C_NAME like '%$v_filter%' ";
        }
        $stmt .= ' Order By C_NAME';
        return $this->db->getAll($stmt);
    }

    /**
     * Lay danh sach da phan truc tiep cho mot user
     * @param unknown $user_id
     * @param unknown $appication_id
     */
    public function qry_single_user_permit_on_application($user_id, $appication_id)
    {
        $stmt = 'Select C_FUNCTION_CODE
                From t_cores_user_function
                Where FK_USER=? And FK_APPLICATION=?';
        $params = array($user_id, $appication_id);

        $this->db->debug=0;
        return $this->db->getCol($stmt, $params);
    }

    /**
     * Lay danh sach quyen da phan truc tiep cho mot Group
     * @param unknown $group_id
     * @param unknown $appication_id
     */
    public function qry_single_group_permit_on_application($group_id, $appication_id)
    {
        $stmt = 'Select C_FUNCTION_CODE
                From t_cores_group_function
                Where FK_GROUP=? And FK_APPLICATION=?';
        $params = array($group_id, $appication_id);

        $this->db->debug=0;
        return $this->db->getCol($stmt, $params);
    }

    public function update_user_permit()
    {
        $v_user_id          = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : 0;
        $v_application_id   = isset($_POST['sel_application']) ? replace_bad_char($_POST['sel_application']) : 0;

        $v_grant_function   = isset($_POST['hdn_grant_function']) ? replace_bad_char($_POST['hdn_grant_function']) : '';

        //Xoa het thong tin cu
        $this->db->Execute('Delete From t_cores_user_function Where FK_USER=? And FK_APPLICATION=?', array($v_user_id, $v_application_id));

        $arr_grant_function = explode(',', $v_grant_function);
        foreach ($arr_grant_function as $v_function)
        {
            if ( $v_function != NULL && $v_function != '')
            {
                $stmt = 'Insert Into t_cores_user_function(FK_USER, FK_APPLICATION, C_FUNCTION_CODE) Values (?, ? , ?)';
                $params = array($v_user_id, $v_application_id, trim($v_function));
                $this->db->Execute($stmt, $params);
            }
        }

        $this->popup_exec_done(FALSE);
    }

    public function update_group_permit()
    {
        $v_group_id          = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : 0;
        $v_application_id    = isset($_POST['sel_application']) ? replace_bad_char($_POST['sel_application']) : 0;

        $v_grant_function   = isset($_POST['hdn_grant_function']) ? replace_bad_char($_POST['hdn_grant_function']) : '';

        //Xoa het thong tin cac quyen cu
        $this->db->Execute('Delete From t_cores_group_function Where FK_GROUP=? And FK_APPLICATION=?', array($v_group_id, $v_application_id));

        $arr_grant_function = explode(',', $v_grant_function);
        foreach ($arr_grant_function as $v_function)
        {
            if ( $v_function != NULL && $v_function != '')
            {
                $stmt = 'Insert Into t_cores_group_function(FK_GROUP, FK_APPLICATION, C_FUNCTION_CODE) Values (?, ? , ?)';
                $params = array($v_group_id, $v_application_id, trim($v_function));
                $this->db->Execute($stmt, $params);
            }
        }

        $this->popup_exec_done(FALSE);
    }

    public function do_change_password()
    {
        $v_user_id = Session::get('user_id');
        $v_current_password = replace_bad_char($_POST['txt_current_password']);
        $v_new_password     = replace_bad_char($_POST['txt_new_password']);

        $stmt = 'Update t_cores_user Set C_PASSWORD=md5(?) Where PK_USER=? And C_PASSWORD=?';
        $params = array($v_new_password,$v_user_id, md5($v_current_password));

        $this->db->Execute($stmt, $params);
        if ($this->db->Affected_Rows() != 1) {
            echo '<script type="text/javascript">alert("Doi mat khau KHONG thanh cong");window.parent.hidePopWin();</script>';
            exit;
        }
        else
        {
            echo '<script type="text/javascript">alert("Doi mat khau thanh cong");window.parent.hidePopWin();</script>';
            exit;
        }
    }
    
    public function qry_all_ou()
    {
        $stmt = 'Select
                    PK_OU,
                    FK_OU,
                    C_NAME,
                    C_ORDER,
                    C_STATUS,
                    C_INTERNAL_ORDER,
                    C_XML_DATA
                 From t_cores_ou
                 Order By C_INTERNAL_ORDER';
        return $this->db->getAll($stmt);
    }

}
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

class login_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function do_login()
    {
        if (!isset($_POST['txt_login_name']))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_login_name = isset($_POST['txt_login_name']) ? $this->replace_bad_char($_POST['txt_login_name']) : null;
        $v_password   = isset($_POST['txt_password']) ? $this->replace_bad_char($_POST['txt_password']) : null;

        $v_password = md5($v_password);

        if ($v_password == NULL)
        {
            echo '<script>alert("Phai nhap [Mat khau]!"); document.location.replace("index.php");</script>\n';
            $this->exec_done($this->goback_url);
            exit();
        }

        //Login By AD
        $v_domain = isset($_POST['sel_domain']) ? $this->replace_bad_char($_POST['sel_domain']) : null;
        if ($v_domain == AD_DOMAIN_NAME)
        {
            $v_auth_by = 'AD';
            include SERVER_ROOT . 'libs/adldap/adLDAP.php';
            try
            {
                $adldap = new adLDAP();
            }
            catch (adLDAPException $e)
            {
                echo $e;
                exit();
            }

            //authenticate the user
            if ($adldap->authenticate($v_login_name, $v_password))
            {
                $stmt            = 'Select u.PK_USER
                            ,u.FK_OU
                            ,u.C_NAME as C_USER_NAME
                            ,u.C_LOGIN_NAME
                            ,u.C_XML_DATA
                            ,u.C_IS_ADMIN
                            ,ou.C_NAME as C_OU_NAME
                            ,ou.C_LEVEL as C_OU_LEVEL
                    From t_cores_user u Left Join t_cores_ou as ou On u.FK_OU=ou.PK_OU
                    Where u.C_LOGIN_NAME=?';
                $params          = array($v_login_name);
                $arr_single_user = $this->db->getRow($stmt, $params);
            }
            else
            {
                $arr_single_user = array();
            }
        }
        else
        {
            $v_auth_by       = 'WEB';
            $stmt            = 'Select u.PK_USER
                            ,u.FK_OU
                            ,u.C_NAME as C_USER_NAME
                            ,u.C_LOGIN_NAME
                            ,u.C_XML_DATA
                            ,u.C_IS_ADMIN
                            ,u.C_JOB_TITLE
                            ,ou.C_NAME as C_OU_NAME
                            ,ou.C_LEVEL as C_OU_LEVEL
                    From t_cores_user u Left Join t_cores_ou as ou On u.FK_OU=ou.PK_OU
                    Where u.C_LOGIN_NAME=? And u.C_PASSWORD=? And u.C_STATUS=1';
            $params          = array($v_login_name, $v_password);
            $arr_single_user = $this->db->getRow($stmt, $params);
        }

        if (sizeof($arr_single_user) > 0)
        {
            @session::init();
            $v_user_id = $arr_single_user['PK_USER'];

            session::set('login_name', $arr_single_user['C_LOGIN_NAME']);
            session::set('user_login_name', $arr_single_user['C_LOGIN_NAME']);
            session::set('user_name', $arr_single_user['C_USER_NAME']);
            session::set('user_code', $arr_single_user['C_LOGIN_NAME']);
            session::set('user_id', $arr_single_user['PK_USER']);
            session::set('ou_id', $arr_single_user['FK_OU']);
            session::set('ou_name', $arr_single_user['C_OU_NAME']);
            session::set('user_granted_xml', $arr_single_user['C_XML_DATA']);
            session::set('is_admin', $arr_single_user['C_IS_ADMIN']);
            session::set('auth_by', $v_auth_by);
            session::set('user_job_title', $arr_single_user['C_JOB_TITLE']);
            
            //User Token
            session::set('user_token', md5(uniqid()));

            //Danh sách nhóm mà NSD là thành viên
            $stmt           = 'Select G.C_CODE
                    From t_cores_group G Left Join t_cores_user_group UG On G.PK_GROUP=UG.FK_GROUP
                    Where UG.FK_USER=?';
            $params         = array($arr_single_user['PK_USER']);
            $arr_group_code = $this->db->getCol($stmt, $params);
            session::set('arr_group_code', $arr_group_code);

            //La thanh vien ban lanh dao?
            if (in_array(_CONST_BOD_GROUP_CODE, $arr_group_code))
            {
                session::set('is_bod_member', 1);
            }

            //Cap nhat thong tin lan dang nhap cua cua NSD
            if (DATABASE_TYPE == 'MSSQL')
            {
                $stmt = 'Update t_cores_user Set C_LAST_LOGIN_DATE=getDate() Where PK_USER=?';
            }
            elseif (DATABASE_TYPE == 'MYSQL')
            {
                $stmt = 'Update t_cores_user Set C_LAST_LOGIN_DATE=Now() Where PK_USER=?';
            }
            $this->db->Execute($stmt, array($arr_single_user['PK_USER']));

            //Danh sach quyen
            //Cau truc MA_UNG_DUNG::MA_CHUC_NANG
            if (DATABASE_TYPE == 'MSSQL')
            {
                $stmt = 'Select (Upper(a.C_CODE) + \'::\' + UF.C_FUNCTION_CODE) as C_FUNCTION_CODE
                        From t_cores_user_function UF Left Join t_cores_application a on UF.FK_APPLICATION=a.PK_APPLICATION
                        Where FK_USER=?

                        UNION

                        Select (Upper(a.C_CODE) + \'::\' + GF.C_FUNCTION_CODE) as C_FUNCTION_CODE
                        From t_cores_group_function GF Left Join t_cores_application A on GF.FK_APPLICATION=a.PK_APPLICATION
                        Where FK_GROUP in (Select FK_GROUP From t_cores_user_group Where FK_USER=?)';
            }
            elseif (DATABASE_TYPE == 'MYSQL')
            {
                $stmt = "Select Concat(Upper(a.C_CODE), '::', UF.C_FUNCTION_CODE) as C_FUNCTION_CODE
                        From t_cores_user_function UF Left Join t_cores_application a on UF.FK_APPLICATION=a.PK_APPLICATION
                        Where FK_USER=?

                        UNION

                        Select Concat(Upper(a.C_CODE), '::', GF.C_FUNCTION_CODE) as C_FUNCTION_CODE
                        From t_cores_group_function GF Left Join t_cores_application a on GF.FK_APPLICATION=a.PK_APPLICATION
                        Where FK_GROUP in (Select FK_GROUP From t_cores_user_group Where FK_USER=?)";
            }

            session::set('arr_function_code', $this->db->getCol($stmt, array($v_user_id, $v_user_id)));

            //NSD la can bo cap xa
            $sql                   = "Select
                        u.C_LOGIN_NAME
                            ,u.C_NAME
                    From t_cores_user u
                        Right Join (Select FK_USER
                                        From t_cores_user_group ug
                                            Left Join t_cores_group g
                                            ON ug.FK_GROUP=g.PK_GROUP
                                        Where g.C_CODE='CAN_BO_CAP_XA'
                                    ) as udc
                        On u.PK_USER=udc.FK_USER
					WHERE u.C_LOGIN_NAME IS NOT NULL
						AND u.C_NAME IS NOT NULL
						";
            $arr_all_can_bo_cap_xa = $this->db->GetAssoc($sql);
            session::set('la_can_bo_cap_xa', $arr_single_user['C_OU_LEVEL'] == 3);
            session::set('arr_all_can_bo_cap_xa', $arr_all_can_bo_cap_xa);
            Session::set('village_id', $arr_single_user['C_OU_LEVEL'] == 3 ? $arr_single_user['FK_OU'] : 0);
            
            //Lay danh sach toan bo cac xa
            if (session::get('la_can_bo_cap_xa') == TRUE)
            {
            	session::set('arr_all_village', Array());
            }
            else
            {
	            $sql = 'Select
						  PK_OU
						  , C_NAME
						From t_cores_ou OU
						Where C_LEVEL = 3
						Order By C_NAME';
	            $arr_all_village = $this->db->getAssoc($sql);
	            session::set('arr_all_village', $arr_all_village);
            }
            
            $this->exec_done(SITE_ROOT . build_url('r3/record/'));


            exit;
        }
        else
        {
            session::destroy();
            echo '<script>alert("Sai [Ten dang nhap] hoac [Mat khau]!");</script>';
            $this->exec_done($this->goback_url);
            exit();
        }
    }

}
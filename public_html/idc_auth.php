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
ini_set('date.timezone', 'Asia/Ho_Chi_Minh');

define('DS', DIRECTORY_SEPARATOR);
define('SERVER_ROOT', __DIR__ . DS);

require_once ('config.php');
require_once ('const.php');

if (CONST_IDC_INTERGRATED == FALSE)
{
    exit();
}
define('CONST_ID_SECRET_KEY', '123456');

//library
require_once (SERVER_ROOT . 'libs' . DS . 'PEAR' . DS . 'PEAR.php');
require_once (SERVER_ROOT . 'libs' . DS . 'PEAR' . DS . 'Savant3.php');
require_once (SERVER_ROOT . 'libs' . DS . 'adodb5' . DS . 'adodb.inc.php');
require_once (SERVER_ROOT . 'libs' . DS . 'jwdate.class.php');
require_once (SERVER_ROOT . 'libs' . DS . 'functions.php');
require_once (SERVER_ROOT . 'libs' . DS . 'session.php');
require_once (SERVER_ROOT . 'libs' . DS . 'lang.php');

//MVC
require_once (SERVER_ROOT . 'libs' . DS . 'model.php');

$idcAuth = New idcAuth();
$idcAuth->idc_auth();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class idcAuth
{
    public $model;
    function __construct()
    {
        $this->model = new Model();
        
        $this->idc_user     = get_post_var('idc_user'); //uid của người dùng trên hệ thống IDC
        $this->idc_otp      = get_post_var('idc_otp'); // Google Auth key của người dùng
        $this->idc_sid      = get_post_var('idc_sid'); // session id của người dùng trên Cloudgate. 
        $this->idc_auth_url = get_post_var('idc_auth_url'); //Địa chỉ (đường dẫn trên Cloudgate) để thực hiện việc kiểm tra session id của người dùng.
        
        if (($this->idc_user == '') OR ($this->idc_otp == '') OR ($this->idc_sid == '') OR ($this->idc_auth_url == ''))
        {
            die('0');
        }
    }
    
    public function idc_auth()
    {
        if (!$this->_check_opt_key())
        {
            die(0);
        }
        if (!$this->_check_session())
        {
            die(0);
        }
                
        //Let login
        $stmt  = 'Select u.PK_USER
                    ,u.FK_OU
                    ,u.C_NAME as C_USER_NAME
                    ,u.C_LOGIN_NAME
                    ,u.C_XML_DATA
                    ,u.C_IS_ADMIN
                    ,u.C_JOB_TITLE
                    ,ou.C_NAME as C_OU_NAME
                    ,ou.C_LEVEL as C_OU_LEVEL
            From t_cores_user u Left Join t_cores_ou as ou On u.FK_OU=ou.PK_OU
            Where C_IDC_USER_ID=? And u.C_STATUS=1';
        $params          = array($this->idc_user);
        $arr_single_user = $this->model->db->getRow($stmt, $params);
        
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
            $sql = "Select
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
            die(1);
        }//end if OK
    }
    
    private function _check_opt_key()
    {
        //Kiem tra cap idc_user && idc_otp la hop le
        return TRUE;
    }
    
    private function _check_session()
    {
        //Gui request nguoc tro lai IDC kiem tra session con ton tai
        return TRUE;
    }
} //end class idcAuth

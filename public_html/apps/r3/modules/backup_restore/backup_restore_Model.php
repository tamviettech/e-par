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

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

////step[1]/task[1]/@code
class backup_restore_Model extends Model
{
    /**
     * @var array
     */
    public $arr_can_bo_cap_xa;

    /**
     * @var \ADOConnection 
     */
    public $db;

    function __construct()
    {
        parent::__construct();
        (Session::get('is_admin') == 1 OR check_permission('QUAN_TRI_BACKUP_RESTORE')) Or die($this->access_denied());
    }

    
    // fc xóa file or folder 
    //khoi tao mang chứa lỗi
    public function dsp_delete_item($arr_items_name,$dir_default)
    {
        if(trim($dir_default) != '')
        {
        $arr_error      = array();
        for ($i = 0;$i <sizeof($arr_items_name);$i ++)
        {
            $v_item_name      = $arr_items_name[$i];
            $item_path_dir    = $dir_default . $v_item_name;
        $arr_error      = array();

            if(is_file($item_path_dir))
            {
                $delete_item = unlink($item_path_dir);
                 if (!$delete_item) {
                     $arr_error[] = 'Xảy lỗi không thể xóa được tập tin' . $v_item_name . 'Xin kiểm tra lại';
                 }
             }
             else
             {
                 $delete_item = unlink($filename);
                 if (!$delete_item) 
                 {
                     $arr_error[] = 'Xảy lỗi không thể xóa được thư mục' . $v_item_name . 'Xin kiểm tra lại';
                 }
             }
        }
        if(sizeof($arr_error) > 0)
        {
            $str_error = '';
            foreach ($arr_error as $error)
            {
                $str_error .= $error."\n";
            }
             $this->exec_fail($this->controller_url, $str_error);
             return;
        }
        
             $this->exec_fail($this->controller_url,'Bạn đã xóa thành công!');
             return;
        }
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
    
    public function get_datetime_now()
    {
        $ret = NULL;
        if (DEBUG_MODE < 10)
        {
            $this->db->debug = 0;
        }
        if (DATABASE_TYPE == 'MSSQL')
        {
            $ret = $this->db->getOne('SELECT CONVERT(VARCHAR(19), GETDATE(), 121)');
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $ret = $this->db->getOne('SELECT NOW()');
        }
        $this->db->debug = DEBUG_MODE;
        return $ret;
    }
    /**
     * restore 
     */
    public function restore($dir_file_restore)
    {
        if($dir_file_restore == '' or $dir_file_restore == NULL)
        {
            return ' Xảy ra lỗi, xin thử lại!';
            
        }
        $commend = "mysql --user=$this->_username --password=$this->_password  --host=$this->_host $this->_database_name < $dir_file_restore";
        system($commend, $result);
        if ($result == TRUE) 
        {
            return 'Đã xảy ra lỗi trong quá trình khôi phục';
            
        } else {
            return 'Bạn đã khôi phục dữ liệu thành công!';
            
        }
    }
    public function backup($dir_path_save)
    {
        if($dir_path_save == '' OR $dir_path_save == NULL)
        {
            echo 'Đã có lỗi xảy ra';
            return;
        }
        $commend = "mysqldump --user=$this->_username --password=$this->_password  --host=$this->_host $this->_database_name > $dir_path_save";
        system($commend, $result);
        if ($result != 0) {
            unlink($dir_path_save);
            return 'Xảy ra lỗi trong quá trình sao lưu!';
        }
        else 
        {
            return 'Bạn đã sao lưu dữ liệu thành công!';
        }
    }
}
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
    
    
    public function restore($dir_file_restore)
    {
        if($dir_file_restore == '' or $dir_file_restore == NULL)
        {
            return ' Xảy ra lỗi, xin thử lại!';
            
        }
        $command = "mysql --user=$this->_username --password=$this->_password  --host=$this->_host $this->_database_name < $dir_file_restore";
        system($command, $result);
        if ($result != 0) 
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
        $command = "mysql --user=$this->_username --password=$this->_password  --host=$this->_host $this->_database_name > $dir_path_save";
        system($command, $result);
        
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
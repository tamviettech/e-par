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

class backup_restore_Controller extends Controller
{
    protected $_set_filetype  = 'SQL';
    private   $_host          = CONST_DB_SERVER_ADDRESS ;
    private   $_database_name = CONST_DB_DATABASE_NAME;
    private   $_username      = CONST_DB_USER_NAME;
    private   $_password      = CONST_DB_USER_PASSWORD;
    private   $_direct_folder_default;
    protected $_dir_folder    = 'backup';
    //Roles

    protected $_arr_roles ;
    protected $_active_role;
    protected $_arr_user_role   = array();
    protected $_activity_filter = array(
        0  => 'Tất cả'
        , 1  => 'Mới tiếp nhận'
        , 2  => 'Chờ bổ sung'
        , 10 => 'Tạm dừng'
        , 3  => 'Bị từ chối'
        , 4  => 'Đang thụ lý'
        , 5  => 'Đang trình ký'
        , 6  => 'Chờ trả KQ'
        , 7  => 'Đã trả KQ'
        , 8  => 'Chậm tiến độ'
        , 9  => 'Quá hạn trả'
//        , 11 => 'Khôi phục HS'
    );

    /**
     *
     * @var \record_Model 
     */
    public $model;
    protected $_module_name = '';
    
    public function __construct()
    {
           //Kiem tra session
        session::init();
        $login_name = session::get('login_name');
        
        if ($login_name == NULL)
        {
            session::destroy();
            header('location:' . SITE_ROOT . 'login.php');
            exit;
        }
        (Session::get('is_admin') == 1 OR check_permission('QUAN_TRI_BACKUP_RESTORE')) Or die($this->access_denied());
        parent::__construct('r3', 'backup_restore');
        //tao array role tu r3_const
        $this->_arr_roles = json_decode(CONST_ALL_R3_ROLES,true);
        $this->view->show_left_side_bar = $this->view->template->show_left_side_bar = FALSE;
        
        //$this->view->template->arr_roles = $this->model->qry_all_user_role(Session::get('user_code'));// $this->_arr_roles;
        $this->view->controller_url               = $this->view->get_controller_url();
        $this->model->controller_url               = $this->view->get_controller_url();
        $this->view->template->controller_url     = $this->view->get_controller_url('record','r3');
        
        
        $this->view->template->activity_filter    = $this->_activity_filter;
        $this->view->role_text                    = $this->_arr_roles;
        $this->view->get_url_images               = SITE_ROOT.'apps/r3/modules/'.$this->module_name.'/images/';
        deny_bad_http_referer();
        
        $menu = Array();
        $arr_my_role = $this->model->qry_all_user_role(Session::get('user_code'));
        foreach ($this->_arr_roles as $key => $val)
        {
            if ($this->check_permission($key) && in_array($key, $arr_my_role))
            {
                $menu[$key]             = $val;
                $this->_arr_user_role[] = strtoupper($key);
            }
        }

        $arr_not_admin_roles = array(_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE
            , _CONST_KIEM_TRA_TRUOC_HO_SO_ROLE, _CONST_RUT_ROLE);
        $is_admin            = (bool) Session::get('is_admin');
        foreach ($arr_not_admin_roles as $role)
        {
            if ($this->check_permission($role) && !$is_admin)
            {
                $menu[$role]            = $this->_arr_roles[$role];
                $this->_arr_user_role[] = $role;
            }
        }

        $arr_more_roles = array(_CONST_Y_KIEN_LANH_DAO_ROLE, _CONST_TRA_CUU_ROLE
            , _CONST_TRA_CUU_LIEN_THONG_ROLE, _CONST_TRA_CUU_TAI_XA_ROLE, _CONST_BAO_CAO_ROLE);
        foreach ($arr_more_roles as $role)
        {
            if ($this->check_permission($role))
            {
                $menu[$role]            = $this->_arr_roles[$role];
                $this->_arr_user_role[] = $role;
            }
        }
        $this->view->template->arr_roles = $menu;
        $this->view->arr_roles           = $menu;
        
        //TRuyền trạng thái quyền đang sử dụng
        $this->view->template->active_role = '';
        
        //$this->view->template->role = 'R3';
        //Nếu Database nằm trên server khac: Ngày giờ hiện tại sẽ lấy từ DB
        $this->view->DATETIME_NOW           = $this->model->get_datetime_now();
        
        
        //##########################
        // Thiết lập đường dẫn mặc định cho các tập tin sao lưu và khôi phục
        $this->_direct_folder_default        = SERVER_ROOT .$this->_dir_folder.DS ;
        $this->model->_host                  = $this->_host;   
        $this->model->_database_name         = $this->_database_name;
        $this->model->_username              = $this->_username;
        $this->model->_password              = $this->_password;
        $this->model->_dir_folder            = $this->_dir_folder;
        //Kiemr tra định dạng mặc định của têp tin 
        if(!isset($this->_set_filetype))
        {
             $this->_set_filetype  = 'SQL';
        }
        $this->view->_set_filetype = $this->_set_filetype; 
    }
    
    public function main()
    {
        //phan trang
        page_calc($v_start, $v_end);

        $VIEW_DATA  = $arr_item =array();
        //Mảng chứa thông tin nếu có lỗi
        //check folder exist
        if(is_dir($this->_direct_folder_default) == FALSE)
        {
            mkdir($this->_direct_folder_default);
            return;
        }
        
        $path_dir = $this->_direct_folder_default.'*';
       
        if(!is_dir($this->_direct_folder_default))
        {
            return $arr_errors[] = 'Thư mục rỗng';
        }
        
        //Sắp xếp theo ngày mới create
        array_multisort(array_map('filemtime',($arr_all_item = glob($path_dir))),SORT_DESC,$arr_all_item);
        $total_file_is_backup  = 0;
        
        foreach ($arr_all_item as $item)
        {
            
            if(is_file($item) && file_exists($item))
            {
                $v_get_file_name = $this->_get_file_name($item);
                $v_get_file_type = $this->_get_file_type($v_get_file_name);
                
                if($v_get_file_type == TRUE )
                {
                    $total_file_is_backup +=1;
                    if($total_file_is_backup >= ($v_start) && $total_file_is_backup <= $v_end)
                    {
                        //La file dinh dang .sql
                        $arr_item['file_is_backup'][$v_get_file_name] = array( 
                                                            'name'          => $v_get_file_name,
                                                            'date_create'   => date("d/m/Y  H:i", filemtime($item)),
                                                            'type'          => 'Microsoft SQL Server Query File',
                                                            'size'          => $this->_get_file_size_unit(filesize($item)),
                                                            'dir'           => $item
                                                            );
                    }
                    
                }
//                else
//                {
//                    // La cac file khac dinh dang .sql
//                    //La file dinh dang .sql
//                    $arr_item['file'][$v_get_file_name] = array( 
//                                                        'name'     => $v_get_file_name,
//                                                        'date_create'   => date("d/m/Y H:i", filemtime($item)),
//                                                        'type'     => 'Microsoft SQL Server Query File',
//                                                        'size'     => $this->_get_file_size_unit($item),
//                                                        'dir'           => $item
//                                                        );
//                    $total_file_not_backup +=1;
//                }
            }
//            elseif(is_dir($item))
//            {
//                //Danh sanh khong la file
//                $v_get_file_name = dirname($item);
//                $arr_item['folder'][$v_get_file_name] = array(
//                                                        'name'          => $v_get_file_name ,
//                                                         'date_create'  => date("d/m/Y H:i", filemtime($item)),   
//                                                        'size'          => $this->_get_file_size_unit($item),
//                                                        'dir'           => $item
//                                                        );
//                $total_file_is_folder +=1;
//            }
        }
        $arr_item['TOTAL_RECORD']                     = array('TOTAL_RECORD'=>$total_file_is_backup);
//        $arr_item['file']['TOTAL_RECORD']           = $total_file_not_backup;
//        $arr_item['folder']['TOTAL_RECORD']         = $total_file_is_folder;
        
        $VIEW_DATA['all_dir_folder']  = json_encode($arr_item);
        $this->view->render('dsp_main',$VIEW_DATA);
    }
    /**
     * Xóa file 
     */
    public function dsp_delete_item()
    {
       $v_items_name   = get_request_var('hdn_item_id_list','');
       $arr_items_name = explode(',', $v_items_name);
       $dir_default = $this->_direct_folder_default;
       if($v_items_name != '' OR $v_items_name != NULL)
       {
            $this->model->dsp_delete_item($arr_items_name,$dir_default);
       }
       else
       {
           echo  'Không tìm thấy nội dung bạn cần xem!';
           echo '<br/><a href="' . $this->view->get_controller_url() . '">' . 'Bấm vào đây để trở về thư mục gốc' . '</a>';
           return;
       }
    }
    
    //Chỉnh sửa lại định dạng size của file . 
    private function _get_file_size_unit($file_size) {
        switch (true) {
            case ($file_size / 1024 < 1) :
                return intval($file_size) . " Bytes";
                break;
            case ($file_size / 1024 >= 1 && $file_size / (1024 * 1024) < 1) :
                return intval($file_size / 1024) . " KB";
                break;
            default:
                return intval($file_size / (1024 * 1024)) . " MB";
        }
    }
    /**
     * Lấy kiểu dữ liệu file (.sql)
     */
    private function _get_file_type($file_name)
    {
        if(trim($file_name))
        {
          $arr_item = explode('.', $file_name);
        }
        $v_get_file_type = end($arr_item);
        $v_get_file_type = isset($v_get_file_type) ? trim($v_get_file_type) : ''; 
        
        //convert danh sach dinh dang sang mang
        $arr_file_type   = explode(',', strtolower($this->_set_filetype)); 
        //Kiem tra file hơp lệ
        if(in_array((string)strtolower($v_get_file_type),$arr_file_type))
        {
            return TRUE;
        }
        else
            return FALSE;
    }

    //lấy tên file từ dir fiel
    private function _get_file_name($v_dir_path)
    {
        $v_dir_path = trim($v_dir_path);
        $arr_item   = explode(DS,$v_dir_path);
        return end($arr_item);
    }
    
    /**
     * Goi thoi gian he thong
     */
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
 * 
 * Sao luu du lieu
 */
    
    
    public function dsp_backup()
    {
       $file_name       = date("Y_m_d-H_i_s").'.sql';
       //Kiem tra trung ten
       $v_status_backup  = 1;
       $dir_path_save   = $this->_direct_folder_default.$file_name;
       if(is_file($dir_path_save) or is_dir($dir_path_save))
       {
           echo 'Tên bị trùng xin kiểm tra lại!';
           return;
       }
       echo $this->model->backup($dir_path_save);      
    }
    
    /**
     * 
     *   khoi phuc du lieu
     */
      public function dsp_restore()
      {
          $v_item_name = get_request_var('item_name','',true);
        if($v_item_name == '' OR $v_item_name == NULL)
        {
            echo 'Bạn chưa chọn tập tin cần khôi phục!';
            return FALSE;
        }
        
        //check file type
        $v_file_type  = end(explode('.', $v_item_name));
        $arr_filetype = explode(',', strtolower($this->_set_filetype));
        
        if(!in_array($v_file_type, $arr_filetype))
        {
             echo "Tập tin lựa chọn khôi phục cần đinh dạng *.sql.\n Xin kiểm tra lại!";
            return FALSE;
        }
        $dir_file_restore = $this->_direct_folder_default . $v_item_name;
        if(!file_exists($dir_file_restore))
        {
            echo 'Không tìm thấy tệp tin lựa chọn khôi phục trong thư mục!';
            return;
        }
        echo $this->model->restore($dir_file_restore);
      }
      /**
       * download
       */
      public function download()
      {
            $src  = get_request_var('src','');
            $v_file_name = get_request_var('name','');
            
            if(!trim($src) OR !trim($v_file_name))
            {
                echo 'Đã xảy ra lỗi!';
                return false;
            }
            $path_dir = $this->_direct_folder_default.$v_file_name;
            // Kiem tra ton tai file và dẫn từ trình duyệt gủi về là true
            if((string)md5($path_dir) != (string)$src OR !is_file($path_dir))
            {
                echo 'Đã xảy ra lỗi!';
                return FALSE;
            }
            
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Length: ". filesize("$path_dir").";");
            header("Content-Disposition: attachment; filename=$v_file_name");
            header("Content-Type: application/octet-stream; "); 
            header("Content-Transfer-Encoding: binary");

            readfile($path_dir);
      }
}
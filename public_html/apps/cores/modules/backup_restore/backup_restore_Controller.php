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
        parent::__construct('cores', 'backup_restore');
        $this->view->show_left_side_bar = $this->view->template->show_left_side_bar = FALSE;
       
        $this->view->controller_url     = $this->model->controller_url  = $this->view->get_controller_url();      
        $this->view->get_url_images               = SITE_ROOT.'apps/cores/modules/'.$this->module_name.'/images/';
        
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
            ini_set('display_errors',1);
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Length: ". filesize("$path_dir").";");
            header("Content-Disposition: attachment; filename=$v_file_name");
            header("Content-Type: application/octet-stream; "); 
            header("Content-Transfer-Encoding: binary");

            readfile($path_dir);
      }
}
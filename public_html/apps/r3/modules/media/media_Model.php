<?php

require_once __DIR__ . '/../record/record_Model.php';

class media_Model extends record_Model
{

    /**
     * @var \ADOConnection 
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * upload file len server
     */
    public function do_upload($folder_id,$share_user_id = '',$v_public = '')
    {
        $v_file_name = get_post_var('txt_name','');
        $file_upload = isset($_FILES['file_upload'])?$_FILES['file_upload']:'';
        //kiem tra dieu kien
        if($v_file_name == '' OR $file_upload == '')
        {
            $this->popup_exec_fail('không đủ điều kiện để thực hiện upload !!!');
                
        }
        //du lieu file upload
        $tmp_file = $file_upload['tmp_name'];
        $name_file = $file_upload['name'];
        $ext_file = strtolower(array_pop(explode('.', $name_file)));
        $v_cur_file_name = uniqid() . '.' .$ext_file;
        //kiem tra ext
        if(!in_array($ext_file, explode(',', trim(_CONST_MEDIA_FILE_ACCEPT))))
        {
            $this->popup_exec_fail('Đuôi mở rộng file upload ko đúng định dạng cho phép !!!');
        }
        
        //du lieu chung
        if($share_user_id != NULL OR $share_user_id != '' OR $share_user_id != '')
        {
            $v_user_id = $share_user_id;
        }
        else
        {
            $v_user_id = session::get('user_id');
        }
        
        $v_upload_date = date('Y-m-d');
        $folder_id = ($folder_id == '' OR $folder_id == NULL)? NULL:$folder_id;
        
        //upload file len server
        $v_dir_file = CONST_FILE_UPLOAD_PATH . date('Y') . DS . date('m') . DS . date('d');
        if(file_exists($v_dir_file) == FALSE)
        {
            mkdir($v_dir_file, 0777, true);
        }
        //di chuyen file va kiem tra file da duoc upload len chua
        if(move_uploaded_file($tmp_file, $v_dir_file. DS .$v_cur_file_name) == FALSE)
        {
            $this->popup_exec_fail('Xảy ra sự cố khi upload file!!!');
        }
        
        if(trim($v_public))
        {
            //cập nhật CSDL
            $stmt = "Insert Into t_r3_media
                                (C_NAME,
                                 C_EXT,
                                 C_TYPE,
                                 FK_USER,
                                 C_FILE_NAME,
                                 FK_PARENT,
                                 C_UPLOAD_DATE,
                                 C_PUBLIC)
                    values (?,?,?,?,?,?,?,?)";
            $this->db->Execute($stmt,array($v_file_name,$ext_file,0,$v_user_id,$v_cur_file_name,$folder_id,$v_upload_date,1));
        }
        else
        {
            //cập nhật CSDL
            $stmt = "Insert Into t_r3_media
                                (C_NAME,
                                 C_EXT,
                                 C_TYPE,
                                 FK_USER,
                                 C_FILE_NAME,
                                 FK_PARENT,
                                 C_UPLOAD_DATE)
                    values (?,?,?,?,?,?,?)";
            $this->db->Execute($stmt,array($v_file_name,$ext_file,0,$v_user_id,$v_cur_file_name,$folder_id,$v_upload_date));
        }
        
         
        
        //gan folder id = '' neu la null
        $folder_id = ($folder_id == NULL)?'':$folder_id;
        $arr_retval = array('hdn_folder_id'=>$folder_id);
        
        $retval = json_encode($arr_retval);
        $this->popup_exec_done($retval); 
    }
    
    /**
     * tao thu muc
     * @param type $parent_id: id thu muc cha
     * @param type $dirname: ten thu muc
     * @return string
     */
    public function create_folder($parent_id,$dirname,$v_media_public = '')
    {
        //kiem tra bien
        $v_media_public = replace_bad_char($v_media_public);
        $parent_id = ($parent_id != '' && $parent_id != NULL && is_numeric($parent_id))?$parent_id:NULL;
        $dirname   = ($dirname == '' OR $dirname == NULL)?'':$dirname;
        
        $v_user_id = session::get('user_id');
        $v_upload_date = date('Y-m-d');
                
        //kiem tra dieu kien
        if($dirname == '')
        {
            return 'false';
        }
        if(trim($v_media_public) == 1)
        {
            $stmt = "Insert into t_r3_media
                            (C_NAME,
                             C_TYPE,
                             FK_USER,
                             FK_PARENT,
                             C_UPLOAD_DATE,
                             C_PUBLIC)
                Values (?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?)";
            $this->db->Execute($stmt,array($dirname,1,$v_user_id,$parent_id,$v_upload_date,1));
        }
        else
        {
            $stmt = "Insert into t_r3_media
                            (C_NAME,
                             C_TYPE,
                             FK_USER,
                             FK_PARENT,
                             C_UPLOAD_DATE)
                Values (?,
                        ?,
                        ?,
                        ?,
                        ?)";
            $this->db->Execute($stmt,array($dirname,1,$v_user_id,$parent_id,$v_upload_date));
        }
        
        
        
        if($this->db->Affected_Rows() > 0)
        {
            $new_id = $this->db->Insert_ID('t_r3_media');
            return array('success'=>'true','new_id'=>$new_id);
        }
        else 
        {
            return array('success'=>'false');
        }
        
    }
    /**
     * lay tat ca thu muc
     * @param type $parent_id: dk thu muc cha
     * @return type array
     */
    public function qry_all_folder($parent_id = '')
    {
            $v_user_id = session::get('user_id');
            $stmt = "Select
                        PK_MEDIA,
                        C_NAME,
                        FK_PARENT,
                        C_PUBLIC
                      From t_r3_media
                      Where C_TYPE = 1 And FK_USER = ?
                      ORDER BY C_UPLOAD_DATE ";
            return $this->db->getAll($stmt,array($v_user_id));
        
    }
    /**
     * lay tat ca thu muc public
     * @param type $parent_id: dk thu muc cha
     * @return type array
     */
    public function qry_all_folder_public()
    {
            $stmt = "Select
                        PK_MEDIA,
                        C_NAME,
                        FK_PARENT,
                        C_PUBLIC
                      From t_r3_media
                      Where C_TYPE = 1 and  C_PUBLIC = 1
                      ORDER BY C_UPLOAD_DATE ";
            return $this->db->getAll($stmt);
        
    }
    
    
    /**
     * lay tat ca media cua folder duoc chon
     * @return type
     */
    public function qry_media_of_folder($folder_id = '',$is_share = '',$user_share = '',$v_public = '')
    {
        //kiem tra dieu kien
        if($folder_id != '' && is_numeric($folder_id) == FALSE)
        {
            return array();
        }
        
        $v_user_id = ($is_share != 1)?session::get('user_id'):$user_share;
        
        //tao dieu kien query
        $condition = '';
        if($folder_id == '')
        {
            $condition = " And FK_PARENT Is Null And FK_USER = $v_user_id";
        }
        else
        {
            $condition = " And FK_PARENT = $folder_id And FK_USER = $v_user_id";
        }
        if(trim($v_public) == 1)
        {
            if($folder_id == '')
            {
                $condition = " And FK_PARENT Is Null ";
            }
            else
            {
                $condition = " And FK_PARENT = $folder_id ";
            }
            $sql = "Select 
                        M.PK_MEDIA,
                        M.C_NAME,
                        M.C_EXT,
                        M.C_TYPE,
                        M.FK_USER,
                        M.C_FILE_NAME,
                        M.FK_PARENT,
                        M.C_PUBLIC,
                        (Select Count(FK_MEDIA) From t_r3_media_shared Where FK_MEDIA = M.PK_MEDIA) As C_SHARED,
                        DATE_FORMAT(M.C_UPLOAD_DATE,'%d-%m-%Y') as C_UPLOAD_DATE,
                        DATE_FORMAT(M.C_UPLOAD_DATE,'%Y') as C_YEAR,
                        DATE_FORMAT(M.C_UPLOAD_DATE,'%m') as C_MONTH,
                        DATE_FORMAT(M.C_UPLOAD_DATE,'%d') as C_DAY
                    From t_r3_media M 
                    Where 1>0  And C_PUBLIC = '1' $condition
                    ORDER BY M.C_TYPE DESC,M.C_UPLOAD_DATE";
        }
        else
        {
             $sql = "Select 
                        M.PK_MEDIA,
                        M.C_NAME,
                        M.C_EXT,
                        M.C_TYPE,
                        M.FK_USER,
                        M.C_FILE_NAME,
                        M.FK_PARENT,
                        M.C_PUBLIC,
                        (Select Count(FK_MEDIA) From t_r3_media_shared Where FK_MEDIA = M.PK_MEDIA) As C_SHARED,
                        DATE_FORMAT(M.C_UPLOAD_DATE,'%d-%m-%Y') as C_UPLOAD_DATE,
                        DATE_FORMAT(M.C_UPLOAD_DATE,'%Y') as C_YEAR,
                        DATE_FORMAT(M.C_UPLOAD_DATE,'%m') as C_MONTH,
                        DATE_FORMAT(M.C_UPLOAD_DATE,'%d') as C_DAY
                    From t_r3_media M
                    Where 1>0 $condition
                    ORDER BY M.C_TYPE DESC,M.C_UPLOAD_DATE";
        }
       
        
        $DATA_MODEL['arr_media_of_folder'] = $this->db->getAll($sql);
        //lay thu muc cha
        if($folder_id == '')
        {
            $DATA_MODEL['parent_folder_id'] = '-1';
        }
        else
        {
            if($v_public ==1)
            {
                 $sql = "Select FK_PARENT From t_r3_media Where PK_MEDIA = $folder_id $condition ";
            }
            else 
            {
                 $sql = "Select FK_PARENT From t_r3_media Where PK_MEDIA = $folder_id And FK_USER = $v_user_id ";
            }
           
            $DATA_MODEL['parent_folder_id'] = $this->db->getOne($sql);
        }
        return $DATA_MODEL;
    }
    /**
    * thuc hien xoa media theo list
    * @param type $list_id
    * @return type string check
    */
    public function do_delete_media($list_id,$user_id='',$v_media_public ='')
    {
        if($list_id == '' OR $list_id == NULL)
        {
            return array('message'=>'Bạn cần chọn thư mục hoặc file cần xóa !!!','deleted_id'=>'');
        }
        
        //khoi tao bien
        $message = '';
        $deleted_id = '';
        $v_user_id = ($user_id == '')?session::get('user_id'):$user_id;
        $v_condition = '';
        if(trim($v_media_public) == '')
        {
            $v_condition = " And FK_USER = $v_user_id ";
        }
        $sql = "Select 
                PK_MEDIA,
                C_NAME,
                C_EXT,
                C_TYPE,
                FK_USER,
                C_FILE_NAME,
                FK_PARENT,
                C_UPLOAD_DATE,
                DATE_FORMAT(C_UPLOAD_DATE,'%Y') as C_YEAR,
                DATE_FORMAT(C_UPLOAD_DATE,'%m') as C_MONTH,
                DATE_FORMAT(C_UPLOAD_DATE,'%d') as C_DAY
            From t_r3_media Where PK_MEDIA in ($list_id) $v_condition ";
        $arr_all = $this->db->getAll($sql);
        
        //thuc hien xoa
        foreach($arr_all as $arr_info)
        {
            //lay thong tin xu ly
            $v_media_id   = $arr_info['PK_MEDIA'];
            $v_media_type = $arr_info['C_TYPE'];
            $v_file_name  = $arr_info['C_FILE_NAME'];
            $v_year       = $arr_info['C_YEAR'];
            $v_month      = $arr_info['C_MONTH'];
            $v_day        = $arr_info['C_DAY'];
            $v_name       = $arr_info['C_NAME'];
            
            //kiem tra neu la file => xoa
            if($v_media_type == 0)
            {
                //xoa share
                $stmt = "Delete From t_r3_media_shared Where FK_MEDIA = ?";
                $this->db->Execute($stmt,array($v_media_id));
                //xoa media
                $stmt = "Delete From t_r3_media Where PK_MEDIA = ? $v_condition";
                $this->db->Execute($stmt,array($v_media_id));
                //xoa file that
                if($this->db->Affected_Rows()>0)
                {
                    $real_path = CONST_FILE_UPLOAD_PATH . $v_year . DS . $v_month . DS . $v_day . DS . $v_file_name;
                    unlink($real_path);
                }
                //tao thong bao loi
                else
                {
                    $message .= "FILE: $v_name - không thể xóa do gặp sự cố \n";
                }
            }
            //neu la folder
            else 
            {
                //kiem tra xem co media con ko
                $sql = "Select count(*) From t_r3_media Where FK_PARENT = $v_media_id $v_condition";
                $v_check = $this->db->GetOne($sql);
                
                //neu ko co media con => xoa
                if($v_check < 1)
                {
                    //xoa share
                    $stmt = "Delete From t_r3_media_shared Where FK_MEDIA = ?";
                    $this->db->Execute($stmt,array($v_media_id));
                    //xoa media
                    $stmt = "Delete From t_r3_media Where PK_MEDIA = ?  $v_condition";
                    $this->db->Execute($stmt,array($v_media_id));
                    
                    if($deleted_id == '')
                    {
                        $deleted_id .= $v_media_id;
                    }
                    else
                    {
                        $deleted_id .= ',' . $v_media_id;
                    }
                }
                else
                {
                    $message .= "FOLDER: $v_name - Vẫn còn chứa file hoặc thư mục con \n";
                }
            }
        }
        
        return array('message'=>$message,'deleted_id'=>$deleted_id);
    }
    /**
     * sua ten 
     * @param type $v_id
     * @param type $v_newname
     * @return string
     */
    public function do_rename_media($v_id,$v_newname,$v_public ='')
    {
        $message = '';
        $v_user_id = session::get('user_id');
        //kiem tra bien
        if(is_numeric($v_id) == FALSE || $v_newname == '' || $v_newname == NULL)
        {
            $message = 'Bạn phai nhập tên muốn sửa đổi !!!';
            return message;
        }
        if($v_public ==1)
        {
            $stmt = "Update t_r3_media
                Set C_NAME = ?
                Where PK_MEDIA = ?
                    and C_PUBLIC =1 ";
        }
        else
        {
            $stmt = "Update t_r3_media
                Set C_NAME = ?
                Where PK_MEDIA = ?
                    And FK_USER = ?";
        }
        
            
            
        $this->db->Execute($stmt,array($v_newname,$v_id,$v_user_id));
        
        if($this->db->Affected_Rows() > 0)
        {
            $message = '1';
        }
        else 
        {
            $message = 'Xảy ra sự cố ko thể thay đổi tên!!!';
        }
        return $message;
    }
    
    
     /**
     * Lấy danh sách NSD theo phòng ban
     */
    public function qry_all_user_by_ou_to_add()
    {
        $v_user_id = session::get('user_id');
        
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = 'Select
                        OU.PK_OU
                        , OU.C_NAME
                        ,(Select PK_USER, C_LOGIN_NAME, C_NAME, C_JOB_TITLE From t_cores_user Where FK_OU=OU.PK_OU And PK_USER <> $v_user_id And C_STATUS > 0 For XML Raw) C_XML_USER
                    From t_cores_ou OU Order By C_INTERNAL_ORDER';

            return $this->db->getAll($stmt);
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt       = "Select
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
                         From t_cores_user Where FK_OU=OU.PK_OU And PK_USER <> $v_user_id And C_STATUS > 0) AS C_XML_USER
                    From t_cores_ou OU
                    Order By C_INTERNAL_ORDER";
            $arr_all_ou = $this->db->getAll($stmt);
            return $arr_all_ou;
        } //end if DATABASE_TYPE

        return array();
    }
    
    public function do_share_media()
    {
        $v_user_id      = session::get('user_id');
        $v_media_id     = get_post_var('hdn_media_id','');
        
        $v_user_list_id = get_post_var('hdn_user_id_list','');
        $arr_user = ($v_user_list_id == '')?array():explode(',',$v_user_list_id);
        
        $message = '';
        //kiem tra dieu kien
        if(is_numeric($v_media_id) == TRUE)
        {
            //lay id cua nguoi chu so huu
            $sql = "Select FK_USER FRom t_r3_media Where PK_MEDIA = $v_media_id";
            $v_cur_user_id = $this->db->GetOne($sql);
            if($v_cur_user_id != $v_user_id)
            {
                $message = 'Bạn không có quyền để chia sẻ !!!';
            }
            //insert
            else
            {
                //xoa toan bo nguoi duoc chia se voi file hien hanh
                $sql = "Delete From t_r3_media_shared Where FK_MEDIA = $v_media_id";
                $this->db->Execute($sql);
                
                //tao sql insert
                $sql = "";

                foreach($arr_user as $user)
                {
                    //tranh truong hop chia se cho chinh ban than
                    if($v_user_id == $user)
                    {
                        continue;
                    }
                    //cap quyen cho thu muc chia se
                    $v_chk_grant_name = 'chk_grant_' . $user;
                    
                    $v_grant = 'NULL';
                    if(isset($_POST[$v_chk_grant_name]))
                    {
                        $v_grant = "'" . implode(',', $_POST[$v_chk_grant_name]) . "'";
                    }
                    
                    if($sql == '')
                    {
                        $sql .= "Insert into t_r3_media_shared(FK_MEDIA,FK_USER,C_GRANT) Values ($v_media_id,$user,$v_grant) ";
                    }
                    else
                    {
                        $sql .= ",($v_media_id,$user,$v_grant) ";
                    }
                }

                $this->db->Execute($sql);
                //kiem tra da insert chua
                if($this->db->Affected_Rows() < 1)
                {
                    //neu xoa het NSD => bo chia se file
                    if(count($arr_user) < 1)
                    {
                        $message = 'Hủy chia sẻ thành công!!!';
                    }
                    //loi xay ra tai database
                    else
                    {
                        $message = 'Xảy ra sự cố không thể chia sẻ file, bạn hãy thử lại lần sau !!!';
                    }
                }
            }
        }
        else
        {
            $message = 'Bạn cần chọn 1 thư mục hay 1 file để chia sẻ !!!';
        }
        $array_ret = array('message'=>$message);
        $retval  = json_encode($array_ret);
        $this->popup_exec_done($retval);
    }
    
    
//    public function qry_all_shared($folder_id = '')
//    {
//        //du lieu
//        $v_user_id = session::get('user_id');
//        $v_condition = '';
//        if($folder_id != '')
//        {
//            //lay tat ca file va thu muc con cua folder
//            $sql = "Select PK_MEDIA From t_r3_media Where FK_USER = $v_user_id And FK_PARENT = $folder_id";
//            $media_list = implode(',', $this->db->getCol($sql));
//            
//            $v_condition .= " And FK_MEDIA in ($media_list) ";
//        }
//        
//        //lay tat ca file va thu muc da shared
//        $sql = "Select DISTINCT FK_MEDIA From t_r3_media_shared Where (1>0)";
//        return $this->db->GetCol($sql);
//    }
    /**
     * lay tat ca user duoc chia se theo file
     * @param type $v_media_id
     * @return type
     */
    public function qry_all_user_shared($v_media_id)
    {
        //du lieu
        $v_user_id = session::get('user_id');
        $condition = '';
        
        //kiem tra media co thuoc quyen su huu cua NSD k
        $sql = "Select FK_USER From t_r3_media Where PK_MEDIA = $v_media_id";
        $v_cur_user_id = $this->db->getOne($sql);
        if($v_user_id != $v_cur_user_id)
        {
            die('Bạn không có quyển chi sẻ thư mục này !!!');
        }
        $condition = " And FK_MEDIA = $v_media_id";
      
        
        //lay danh sach user duoc chia se
        $sql = "Select DISTINCT
                    MS.FK_USER,U.C_NAME,MS.C_GRANT 
                  From t_r3_media_shared MS left join t_cores_user U
                  ON MS.FK_USER = U.PK_USER
                  Where (1>0) $condition And U.C_STATUS = 1";
        
        return $this->db->getAll($sql);
    }
    
    /**
     * lay tat ca thong tin cua file da phuong tien
     * @param type $v_media_id
     * @return type
     */
    public function qry_info_of_media($v_media_id)
    {
        $sql = "Select * From t_r3_media Where PK_MEDIA = $v_media_id";
        
        return $this->db->getRow($sql);
    }
    
    /**
     * lay tat ca NSD chi se file voi ban
     * @return type
     */
    public function qry_all_user_share_to_you()
    {
        $v_user_id = session::get('user_id');
        
        $stmt = "Select
                    DISTINCT
                    U.PK_USER,
                    U.C_NAME
                  From t_r3_media M
                    Left Join t_cores_user U
                      On M.FK_USER = U.PK_USER
                  Where PK_MEDIA in(Select DISTINCT
                                      FK_MEDIA
                                    From t_r3_media_shared
                                    Where FK_USER = ?)
                      And U.C_STATUS = 1  ORDER BY U.C_NAME";
        
        return $this->db->GetAll($stmt,array($v_user_id));
    }
    /**
     * lay tat ca file da duoc chia se
     * @param type $v_user_share
     * @return type
     */
    public function qry_all_media_share_to_you($v_user_share)
    {
        //kiem tra dieu kien
        if(is_numeric($v_user_share) == FALSE)
        {
            return array();
        }
        
        $v_user_id = session::get('user_id');
        $stmt = "Select
                    M.PK_MEDIA,
                    M.C_NAME,
                    M.C_EXT,
                    M.C_TYPE,
                    M.FK_USER,
                    M.C_FILE_NAME,
                    M.FK_PARENT,
                    (Select Count(FK_MEDIA) From t_r3_media_shared Where FK_MEDIA = M.PK_MEDIA) As C_SHARED,
                    DATE_FORMAT(M.C_UPLOAD_DATE,'%d-%m-%Y') as C_UPLOAD_DATE,
                    DATE_FORMAT(M.C_UPLOAD_DATE,'%Y') as C_YEAR,
                    DATE_FORMAT(M.C_UPLOAD_DATE,'%m') as C_MONTH,
                    DATE_FORMAT(M.C_UPLOAD_DATE,'%d') as C_DAY,
                    MS.C_GRANT
                  From t_r3_media M
                    left join t_r3_media_shared MS
                      ON M.PK_MEDIA = MS.FK_MEDIA
                  Where M.FK_USER = ?
                      And MS.FK_USER = ?";
       return $this->db->getAll($stmt,array($v_user_share,$v_user_id));
    }
}
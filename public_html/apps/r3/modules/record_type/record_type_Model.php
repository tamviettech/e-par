<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class record_type_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function qry_all_record_type($arr_filter)
    {
        //Phan trang
        page_calc($v_start, $v_end);

        $condition_query = '';
        $v_filter        = $arr_filter['txt_filter'];

        if ($v_filter != '')
        {
            $condition_query = " And (RT.C_CODE like '%$v_filter%' Or RT.C_NAME like '%$v_filter%')";
        }

        //Dem tong ban ghi
        $sql_count_record = "Select Count(*) From t_r3_record_type RT Where (1 > 0) $condition_query";

        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select RT.*
                        ,($sql_count_record) as TOTAL_RECORD
                        ,ROW_NUMBER() OVER (ORDER BY RT.C_CODE) as RN
                        ,Case C_SCOPE
                            When 0 Then 'Thủ tục cấp xã'
                            When 1 Then 'Thủ tục Liên thông Xã ->Huyện'
                            When 2 Then 'Thủ tục Liên thông Huyện->Xã'
                            When 3 Then 'Thủ tục Cấp huyện'
                            Else 'Chưa xác định'
                        End As C_SCOPE_TEXT
                    From t_r3_record_type RT
                    Where (1>0) $condition_query";
            return $this->db->GetAll("Select * From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end Order By a.rn");
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            $sql = "Select RT.*
                        ,Case C_SCOPE
                            When 0 Then 'Thủ tục cấp xã'
                            When 1 Then 'Thủ tục Liên thông Xã ->Huyện'
                            When 2 Then 'Thủ tục Liên thông Huyện->Xã'
                            When 3 Then 'Thủ tục Cấp huyện'
                            Else 'Chưa xác định'
                        End As C_SCOPE_TEXT
                        , ($sql_count_record) TOTAL_RECORD
                    FROM t_r3_record_type RT
                    Where (1 > 0) $condition_query
                    Order By C_CODE
                    Limit $v_start, $v_limit";

            return $this->db->getAll($sql);
        }
        elseif (DATABASE_TYPE == 'ORACLE')
        {
            return array();
        }
    }

    function qry_all_report_books($record_type_id)
    {
        $record_type_id = (int) $record_type_id;
        $sql            = "
            Select ls.*, link.c_book_code As C_IS_CHECKED From t_cores_listtype lt
            Inner Join t_cores_list ls
            On ls.fk_listtype = lt.pk_listtype
            Left Join t_r3_book_record_type link
            ON (BINARY ls.c_code = BINARY link.c_book_code And link.fk_record_type = ?)
            Where lt.C_CODE = ?
            ";
        return $this->db->GetAll($sql, array($record_type_id, 'DM_SO_THEO_DOI_HO_SO'));
    }

    public function delete_record_type($arr_filter = array())
    {
        $v_record_type_id_list = isset($_POST['hdn_item_id_list']) ? $this->replace_bad_char($_POST['hdn_item_id_list']) : 0;

        if ($v_record_type_id_list != '')
        {
            $sql = "Delete From t_r3_record_type
                    Where PK_RECORD_TYPE In ($v_record_type_id_list)
                        And PK_RECORD_TYPE Not In (Select FK_RECORD_TYPE From t_r3_record)";

            $this->db->Execute($sql);
        }

        $this->exec_done($this->goback_url, $arr_filter);
    }

    public function qry_single_record_type($p_record_type_id)
    {
        if ($p_record_type_id < 1)
        {
            return array('C_ORDER' => $this->get_max('t_r3_record_type', 'C_ORDER') + 1);
        }

        $stmt   = 'Select
                        PK_RECORD_TYPE
                      ,C_CODE
                      ,C_NAME
                      ,C_XML_FORM_STRUCT_FILE_NAME
                      ,C_XML_WORKFLOW_FILE_NAME
                      ,C_XML_HO_TEMPLATE_FILE_NAME
                      ,C_XML_DATA
                      ,C_ORDER
                      ,C_STATUS
                      ,C_SCOPE
                      ,C_SEND_OVER_INTERNET
                      ,C_SPEC_CODE
                      ,C_ALLOW_VERIFY_RECORD
                 From t_r3_record_type RT Where PK_RECORD_TYPE=?';
        $params = array($p_record_type_id);
        return $this->db->GetRow($stmt, $params);
    }
    
    public function get_all_template_file($v_record_type_id)
    {
        $arr_single_record_type = $this->qry_single_record_type($v_record_type_id);
        
        $xml_data     = isset($arr_single_record_type['C_XML_DATA']) ? $arr_single_record_type['C_XML_DATA'] : '';
        if(trim($xml_data) == '' OR $xml_data == NULL)
        {
            return;
        }
        $dom          = simplexml_load_string($xml_data);
        
        $v_xpath      = '//data/media/file/text()';
        $r            = $dom->xpath($v_xpath);
        $arr_all_file = array();
        foreach ($r as $item)
        {
            $item = (string)$item ;

            if(trim($item) != '' && $item != NULL)
            {   
                $v_path_file = CONST_TYPE_FILE_UPLOAD . 'template_files_types' .DS . $item;
                                
                if(is_file($v_path_file))
                {
                    $arr_string = explode('_', $item,2);
                    $key_file   = isset($arr_string[0]) ? $arr_string[0] : '';
                    $arr_all_file[$item]['file_name'] =  isset($arr_string[1]) ? $arr_string[1] : '';
                    $arr_all_file[$item]['path']      =  $v_path_file;
                    $arr_all_file[$item]['type']      = filetype($v_path_file);
                }
            }            
        }
        return $arr_all_file;
    }
    
    
    
    public function update_record_type($arr_filter)
    {
         
        $v_record_type_id   = get_post_var('hdn_item_id', 0);
        $v_code             = get_post_var('txt_code', '');
        $v_name             = get_post_var('txt_name', '');       
        $v_xml_data         = get_post_var('XmlData', '<data></data>', FALSE);
        $v_scope            = get_post_var('rad_scope', 3);
        $v_order            = get_post_var('txt_order', 1);
        $v_spec_code        = get_post_var('sel_spec_code', 'XX');
        $arr_file           = isset($_FILES['uploader']['name']) ? $_FILES['uploader']['name'] : array();
        $arr_report_books   = get_post_var('chk_report_book', array(), false);
        $v_list_file_key    = get_post_var('hdn_delete_file_list_id',''); 
        $v_status             = isset($_POST['chk_status']) ? 1 : 0;
        $v_save_and_addnew    = isset($_POST['chk_save_and_addnew']) ? 1 : 0;
        $v_send_over_internet = isset($_POST['chk_send_over_internet']) ? '1' : '0';
        
        $v_allow_verify       = isset($_POST['chk_allow_verify_record']) ? '1' : '0';
        $v_xml_data           = isset($_POST['XmlData']) ? $_POST['XmlData'] : '<data/>';
        
        //Kiem tra trung ma
        $sql = "Select Count(*)
                From t_r3_record_type
                Where C_CODE='$v_code' And PK_RECORD_TYPE <> $v_record_type_id";
        if ($this->db->getOne($sql) > 0)
        {
            $this->exec_fail($this->goback_url, 'Mã loại hồ sơ đã tồn tại', $arr_filter);
            return;
        }

        //Kiem tra trung ten
        $sql = "Select Count(*)
                From t_r3_record_type
                Where C_NAME='$v_name' And PK_RECORD_TYPE <> $v_record_type_id";
        if ($this->db->getOne($sql) > 0)
        {
            $this->exec_fail($this->goback_url, 'Tên loại hồ sơ đã tồn tại', $arr_filter);
            return;
        }
        
        if ($v_record_type_id < 1) //Insert
        {
            // add dom media vao xml_data khi them moi
            $doc = new SimpleXMLElement($v_xml_data);
            $media = $doc->addChild('media');
            $v_xml_data = $doc->asXML();
            
            $stmt   = 'Insert Into t_r3_record_type (C_CODE, C_NAME, C_XML_DATA,C_STATUS,C_SCOPE,C_ORDER, C_SEND_OVER_INTERNET,C_SPEC_CODE,C_ALLOW_VERIFY_RECORD) Values (?,?,?,?,?,?,?,?,?)';
            $params = array($v_code, $v_name, $v_xml_data, $v_status, $v_scope, $v_order, $v_send_over_internet, $v_spec_code, $v_allow_verify);
            $this->db->Execute($stmt, $params);

            $v_record_type_id = $this->get_last_inserted_id('t_r3_record_type', 'PK_RECORD_TYPE');
            
        }
        else //Update
        {
            
            //Lay xml hien tai
            $xml_data_current = $this->db->GetOne("select C_XML_DATA from t_r3_record_type where PK_RECORD_TYPE = ? ",array($v_record_type_id));

            //Lay danh sach file name dang luu tru
            if(trim($xml_data_current) == '')
            {
                // add dom media vao xml_data khi them moi
                $xml_data_current = '<data><media></media></data>';
            }
            $dom                   = simplexml_load_string($xml_data_current);
            $v_xpath_file          = '//data/media/file';
            $arr_results      = $dom->xpath($v_xpath_file);
            
            $doc = new SimpleXMLElement($v_xml_data);
            $media = $doc->addChild('media');
            foreach ($arr_results as $item)
            {
                $media->addChild('file',$item);
            }
            $v_xml_data = $doc->asXML();
            
            // lay xml luu tru ma linh vuc
            $v_xpath_item          = '//item/value';
            $obj_listype_code_spcode   = $dom->xpath($v_xpath_item);
            $v_listype_code_spcode = isset($obj_listype_code_spcode[0]) ? (string)$obj_listype_code_spcode[0] : '';
            
            if(trim($v_listype_code_spcode) != '' && $v_listype_code_spcode != null)
            {
                $xml = new SimpleXMLExtended($v_xml_data);
                $xml_crr = $xml->addChild('item');
                $xml_crr->addAttribute('id','sel_spec_code');            
                $xml_crr->addChild('value')->addCData($v_listype_code_spcode);            
                $v_xml_data = $xml->asXML();
            }

            $stmt   = 'Update t_r3_record_type Set
                        C_CODE=?
                        ,C_NAME=?
                        ,C_XML_DATA=?
                        ,C_STATUS=?
                        ,C_SCOPE=?
                        ,C_ORDER=?
                        ,C_SEND_OVER_INTERNET=?
                        ,C_SPEC_CODE=?
                        ,C_ALLOW_VERIFY_RECORD=?
                    Where PK_RECORD_TYPE=?';
            $params = array(
                $v_code,
                $v_name,
                $v_xml_data,
                $v_status,
                $v_scope,
                $v_order,
                $v_send_over_internet,
                $v_spec_code,
                $v_allow_verify,
                $v_record_type_id
            );

            $this->db->Execute($stmt, $params);
        }

        //$this->ReOrder('t_r3_record_type', 'PK_RECORD_TYPE', 'C_ORDER', $v_record_type_id, $v_order);
        $this->update_report_book_report_type($v_record_type_id, $arr_report_books);
        
        //Them file dinh kem
        if(count($arr_file) > 0)
        {
            $this->update_template_file_type($v_record_type_id,$arr_file);
        }
        //xoa file dinh kem da chon xoa
        if(trim($v_list_file_key) != '')
        {
            $this->delete_file_tempate_type($v_record_type_id,$v_list_file_key);
        }
        
        //LienND update: 2014-06-14: Danh sach cong viec thu ly bat buoc
        $arr_exec_must_do_code = get_post_var('chk_must_do');
        //Xoa het cau hinh cu
        $stmt = 'Delete From t_r3_record_type_exec_must_do Where FK_RECORD_TYPE=?';
        $params = array($v_record_type_id);
        $this->db->Execute($stmt, $params);
        
        //Insert lai cau hinh moi
        foreach ($arr_exec_must_do_code as $v_must_do_code)
        {
            $stmt = 'Insert Into t_r3_record_type_exec_must_do(FK_RECORD_TYPE, C_EXEC_MUST_DO_CODE) Values(?,?)';
            $params = array($v_record_type_id, $v_must_do_code);
            $this->db->Execute($stmt, $params);
        }
        
        //Luu dieu kien loc
        $arr_filter = get_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
        
        //Done
        if ($v_save_and_addnew > 0)
        {
            $this->exec_done($this->goforward_url, $arr_filter);
        }
        else
        {
           $this->exec_done($this->goback_url, $arr_filter);
        }
    }
    
    function update_report_book_report_type($v_record_type_id, $arr_new_books)
    {
        if (!is_array($arr_new_books))
        {
            $arr_new_books = array();
        }

        $this->db->Execute('DELETE FROM t_r3_book_record_type 
            WHERE fk_record_type = ?', array($v_record_type_id));
        $insert_list = '';
        reset($arr_new_books);
        $insert_list .= "'" . replace_bad_char(current($arr_new_books)) . "'";
        while (next($arr_new_books))
        {
            $insert_list .= ",'" . replace_bad_char(current($arr_new_books)) . "'";
        }

        $sql_insert = "
            INSERT INTO t_r3_book_record_type(c_book_code, fk_record_type)
            SELECT ls.c_code, ? AS fk_record_type
            FROM t_cores_list ls
            INNER JOIN t_cores_listtype lt
            ON ls.fk_listtype = lt.pk_listtype
            WHERE ls.c_code IN($insert_list)
            AND lt.c_code = 'DM_SO_THEO_DOI_HO_SO'";
        $this->db->Execute($sql_insert, array($v_record_type_id));
    }
    
    function update_template_file_type($v_record_type_id,$arr_file)
    {
        //Lay danh sach cac file da tai len
       $sql = "SELECT ExtractValue(C_XML_DATA,'count(/data/media)') as C_COUNT_MEDIA, C_XML_DATA
                    FROM t_r3_record_type 
                    WHERE PK_RECORD_TYPE = ?";
        $resluts = $this->db->GetRow($sql,$v_record_type_id);
        $v_xml_data       = $resluts['C_XML_DATA'];        
        
        $dom              = simplexml_load_string($v_xml_data);
        
        $arr_new_file     = array();
        if((int)$resluts['C_COUNT_MEDIA'] >0 )
        {
            $v_xpath        = '//data/media/file/text()';
            $arr_results    = $dom->xpath($v_xpath);
            
            foreach ($arr_results as $item)
            {
                $arr_new_file[] = '<file>'.(string)$item.'</file>';
            }
        }
       
        // add file moi
        $v_count_file = count($arr_file);
        if($v_count_file > 0)
        {
            for($i = 0;$i < $v_count_file; $i ++)
            {
                 if ($_FILES['uploader']['error'][$i] == 0)
                 {
                    $v_file_name     = $this->vn_str_filter($_FILES['uploader']['name'][$i]);
                    $v_tmp_name      = $_FILES['uploader']['tmp_name'][$i];
                    
                    $v_file_ext      = array_pop(explode('.', $v_file_name));
                    $v_user_id       = session::get('user_id');
                    $v_cur_file_name = uniqid().'_' . $v_file_name;
                    $v_upload_date = date('Y-m-d');
                    
                    if (in_array($v_file_ext, explode(',', _CONST_TYPE_FILE_ACCEPT)))
                    {                     
                        //check folder root
                        $v_dir_file = CONST_TYPE_FILE_UPLOAD . 'template_files_types' ;
                        if(file_exists($v_dir_file) == FALSE)
                        {
                            mkdir($v_dir_file, 0777, true);
                        }
                        if (!move_uploaded_file($v_tmp_name, CONST_TYPE_FILE_UPLOAD . 'template_files_types' . DS . $v_cur_file_name))
                        {
                            $this->popup_exec_fail('Xảy ra sự cố khi upload file!');
                        }
                        else
                        {
                            $arr_new_file[] = '<file>'.$v_cur_file_name.'</file>';
                        }
                    }
                }
            }
            
            $tring_xml_file  = (string)implode('', $arr_new_file);
            $sql = "
                UPDATE t_r3_record_type 
                        SET	
                         C_XML_DATA = UpdateXML(C_XML_DATA,'//data/media','<media>$tring_xml_file</media>') 	
                        WHERE
                        PK_RECORD_TYPE = ? ";
            $this->db->Execute($sql,array($v_record_type_id));
        }
    }
    
    
    
    
    public function delete_file_tempate_type($v_record_type_id,$v_list_file_key)
    {
        $arr_file_id  = explode('|',$v_list_file_key);
        $v_xml_data = '';
        
        if(count($arr_file_id) >0 )
        {
            for($i = 0; $i <count($arr_file_id); $i++)
            {
                $v_file_id = trim($arr_file_id[$i]);
                 $sql = "
                        UPDATE t_r3_record_type 
                                SET	
                                 C_XML_DATA = UpdateXML(C_XML_DATA,'//data/media/file[node()=\"$v_file_id\"]',' ') 	
                                WHERE
                                PK_RECORD_TYPE = ? ";
                $this->db->Execute($sql,array($v_record_type_id));                
                //Xoa thong tun file
                $v_path_file = CONST_TYPE_FILE_UPLOAD . 'template_files_types' .DS . $arr_file_id[$i];
                if(is_file($v_path_file))
                {
                    unlink($v_path_file);
                }

            }
        }
    }
 //Loai bo dau va khi thu xau khi upload file
function vn_str_filter ($str){

    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
    $str = preg_replace("/(Đ)/", 'D', $str);
    $str = preg_replace("/( )/", '-', $str);
    $str = preg_replace("/,/", '-', $str);
    $str = preg_replace('/(?|\'|"|&|#)/', '', $str);
    //$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
    return $str;
   }
}
//add Cdata to xml
class SimpleXMLExtended extends SimpleXMLElement // http://coffeerings.posterous.com/php-simplexml-and-cdata
{
  public function addCData($cdata_text)
  {
    $node= dom_import_simplexml($this); 
    $no = $node->ownerDocument; 
    $node->appendChild($no->createCDATASection($cdata_text)); 
  } 
}
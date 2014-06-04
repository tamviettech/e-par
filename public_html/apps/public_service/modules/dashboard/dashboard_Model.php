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
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class dashboard_Model extends Model {

    function __construct() {
        parent::__construct();
    }
    
    public function do_change_session_menu_select($value)
    {
        session::set('menu_select',$value);
        $this->exec_done($this->goback_url);
    }
    
    public function set_session($v_website_id,$v_lang_id)
    {
        if($v_website_id == 0)
        {
            $arr_all_website=$this->gp_qry_all_website_by_user($v_lang_id);
            foreach ($arr_all_website as $key => $value)
            {
                $v_website_id = $key;
                break;
            }
            session::set('session_website_id',$v_website_id);
        }
        else
        {
            session::set('session_website_id',$v_website_id);
        }
        session::set('session_lang_id',$v_lang_id);
        $this->exec_done($this->goback_url);
    }
    
    public function do_change_password()
    {
        $v_user_id          = Session::get('user_id');
        $v_current_password = $this->replace_bad_char($_POST['txt_current_password']);
        $v_new_password     = $this->replace_bad_char($_POST['txt_new_password']);
        
        $v_current_password = encrypt_password($v_current_password); 
        $v_new_password     = encrypt_password($v_new_password); 
        
        if($v_new_password !='')
        {
            $stmt = 'Update T_CORES_USER Set C_PASSWORD=? Where PK_USER=? And C_PASSWORD=?';
            $params = array($v_new_password,$v_user_id, $v_current_password);

            $this->db->Execute($stmt, $params);
        }
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
    
    //create mysql
//    public function create_mysql($table_name)
//    {
//        $table_name = strtolower($table_name);
//        
//        $sql="SELECT *
//            FROM information_schema.columns
//            WHERE table_name='$table_name'";
//        $arr_all_field = $this->db->getAll($sql);
//        
////        echo __FILE__;
////        var_dump::display($arr_all_field);
////        echo 'on line: ' . __LINE__;
//        
//        $mysql = "DROP TABLE IF EXISTS $table_name;
//                 CREATE TABLE $table_name (";
//        foreach ($arr_all_field as $arr_field) {
//            $v_col_name   = $arr_field['COLUMN_NAME'];
//            
//            $v_is_null    = $arr_field['IS_NULLABLE'];
//            if($v_is_null == 'NO')
//            {
//                $v_is_null = 'not null';
//            }
//            else 
//            {
//                $v_is_null = '';
//            }
//            
//            $v_data_type  = $arr_field['DATA_TYPE'];
//            
//            if($v_data_type == 'xml')
//            {
//                $v_data_type = "text";
//            }
//                
//            $v_max_length = $arr_field['CHARACTER_MAXIMUM_LENGTH'];
//            if($v_max_length == NULL || $v_max_length == '-1')
//            {
//                $v_max_length = '';
//            }
//            else
//            {
//                $v_max_length="($v_max_length)";
//            }
//            if($mysql == ("DROP TABLE IF EXISTS $table_name;
//                 CREATE TABLE $table_name ("))
//            {
//                $mysql .= "\n \t $v_col_name $v_data_type".$v_max_length." $v_is_null PRIMARY KEY";
//            }
//            else
//            {
//                $mysql .= ",\n \t $v_col_name $v_data_type".$v_max_length." $v_is_null ";
//            }
//        }
//        $mysql .= ")ENGINE=INNODB;";
//                    
//        
//        echo __FILE__;
//        var_dump::display($mysql);
//        echo 'on line: ' . __LINE__;
//        
//    }
//    //chuyen doi du lieu mssql sang mysql
//    public function mssql_to_mysql($arr_table)
//    {       
////        set_time_limit(0);
////        foreach ($arr_table as $table)
////        {
////            $table = strtolower($table);
////            $sql="SELECT *
////                    FROM information_schema.columns
////                    WHERE table_name='$table'";
////            $arr_all_field = $this->db->getAll($sql);
////            
////            $v_pk_column = $arr_all_field['0']['COLUMN_NAME'];
//////            echo __FILE__;
//////            var_dump::display($arr_all_field);
//////            echo 'on line: ' . __LINE__;
////             $sql = "ALTER TABLE $table
////                    MODIFY COLUMN $v_pk_column INT NOT NULL AUTO_INCREMENT  ";
////             $this->db->Execute($sql);
////        }
//        
//        
//        
//        
////        echo __FILE__;
////        var_dump::display($arr_table);
////        echo 'on line: ' . __LINE__;exit();
//                
//        $this->mysql_db->debug = '0';
//        ini_set('display_errors',1);
//    	set_time_limit(0);
//        ini_set('memory_limit', '500M');
//        
//        foreach ($arr_table as $table)
//        {
//            //mssql
//            $sql = "select * from $table";
//            $arr_all_data = $this->db->getAll($sql);
//            
//            $v_mysql_table = strtolower($table);
//            //mysql
//            $sql = "DELETE FROM $v_mysql_table";
//            $this->mysql_db->Execute($sql);
//            
//            foreach ($arr_all_data as $arr_data)
//            {
//                $str_all_field = '';
//                $str_all_value = '';
//                foreach ($arr_data as $key => $value) 
//                {
//                    //lay tat ca cac cot
//                    if($str_all_field == '')
//                    {
//                        $str_all_field .=  $key;
//                    }
//                    else 
//                    {
//                        $str_all_field .=  ',' . $key;
//                    }
//                    //lay tat ca gia tri
//                    if($str_all_value == '')
//                    {
//                        $str_all_value .=  "'$value'";
//                    }
//                    else 
//                    {
//                        $str_all_value .=  ',' . "'$value'";
//                    }
//                }
//                $sql_insert = "insert into $v_mysql_table($str_all_field) values($str_all_value)";
//                $this->mysql_db->Execute($sql_insert);
//            }
//        }
//    }
    
//    public function update_extra_data()
//    {
//        set_time_limit(0);
//        ini_set('memory_limit', '500M');
//        
//        $sql = "SELECT PK_CATEGORY,FK_WEBSITE FROM t_web_category";
//        $arr_all_category = $this->db->getAll($sql);
//        
//        foreach($arr_all_category as $arr_category)
//        {
//            $v_category_id = $arr_category['PK_CATEGORY'];
//            $v_website_id = $arr_category['FK_WEBSITE'];
//            
//            //lay array tat ca tin bai thuoc chuyen muc
//            $sql = "SELECT DISTINCT FK_ARTICLE FROM t_web_category_article WHERE FK_CATEGORY = ?";
//            $arr_all_article = $this->db->getCol($sql,array($v_category_id));
//            //bien arr thanh str
//            $v_str_article = implode(',', $arr_all_article);
//            //update C_DEFAULT_CATEGORY va C_DEFAULT_WEBSITE
//            $sql = "UPDATE t_web_article
//                    SET C_DEFAULT_CATEGORY = ?, C_DEFAULT_WEBSITE = ?
//                    WHERE PK_ARTICLE IN ($v_str_article)";
//            
//            $this->db->Execute($sql,array($v_category_id,$v_website_id));
//        }
//                
//    }
    
    public function article_entity_endcode()
    {
        set_time_limit(0);
        ini_set('memory_limit', '700M');
        
        for($i=78000;$i<=82864;$i++)
        {
            $v_str_limit = "limit $i,1";
            
            //lay du lieu
            $sql = "SELECT PK_ARTICLE,C_CONTENT,C_SUMMARY FROM t_web_article $v_str_limit";
            $arr_article = $this->db->getRow($sql);
            $v_article_id = $arr_article['PK_ARTICLE'];
            $v_content    = htmlspecialchars($arr_article['C_CONTENT']);
            $v_summary    = htmlspecialchars($arr_article['C_SUMMARY']);
            
            //update 
            $sql = "UPDATE t_web_article
                    SET C_CONTENT = ? , C_SUMMARY= ?
                    WHERE PK_ARTICLE = ?";
            $arr_param = array($v_content,$v_summary,$v_article_id);
            $this->db->Execute($sql,$arr_param);
        }
    }
       public function abc()
       {
           $sql= "select * from Units";
           $arr_unit = $this->mssql_db->getAll($sql);
           $sql = "delete  from t_cores_ou  where PK_OU <> 1";
           $this->db->Execute($sql);
           foreach ($arr_unit as $arr_data)
           {
               $v_unit_name = utf8_decode($arr_data['UnitName']);
               
               echo __FILE__;
               var_dump::display($v_unit_name);
               echo 'on line: ' . __LINE__;
//               echo __FILE__;
//               var_dump::display($v_unit_name);
//               echo 'on line: ' . __LINE__;
//               $v_order = 8;
//               $sql = "insert into t_cores_ou 
//                        (
//                        FK_OU,C_NAME,C_ORDER,C_STATUS,C_INTERNAL_ORDER
//                        )
//                        VALUES 
//                        (
//                        1,'$v_unit_name',$v_order,1,0
//                        )";
//               $this->db->Execute($sql);
               
           }   
            
       }
    
}

?>
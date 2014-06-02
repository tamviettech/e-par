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
         $this->exec_fail($this->controller_url);
    }
    
    
    public function restore($dir_file_restore)
    {
        if($dir_file_restore == '' or $dir_file_restore == NULL)
        {
            return ' Xảy ra lỗi, xin thử lại!';
            
        }
        $command = SERVER_ROOT.DS.'bin'.DS."mysql --user=$this->_username --password=$this->_password  --host=$this->_host $this->_database_name < $dir_file_restore";
        
        system($command, $result);
        if ($result != 0) 
        {
            echo $dir_file_restore;
            return 'Đã xảy ra lỗi trong quá trình khôi phục';
            
        } else {
            return 'Bạn đã khôi phục dữ liệu thành công!';
            
        }
    }
    public function do_backup($dir_path_save,$type)
    {
        if($dir_path_save == '' OR $dir_path_save == NULL)
        {
            echo 'Đã có lỗi xảy ra';
            return;
        }
        
        //xem loai back up
        if((int)$type == 1)
        {
            return $this->create_back_up_by_mysqld($dir_path_save);
            
        }
        elseif((int)$type == 0)
        {
            return $this->create_back_up_by_text($dir_path_save);
        }
    }
    
    /**
     * tao backup boi mysqld
     * @return string
     */
    public function create_back_up_by_mysqld($dir_path_save)
    {
        $command = SERVER_ROOT.DS.'bin'.DS."mysqldump --user=$this->_username --password=$this->_password  --host=$this->_host $this->_database_name> $dir_path_save";
        system($command, $result);
        
        if ($result != 0) 
        {
            unlink($dir_path_save);
            return 'Xảy ra lỗi trong quá trình sao lưu!';
        }
        else 
        {
            return 'Bạn đã sao lưu dữ liệu thành công!';
        }
    }
    
    /**
     * tao backup -> put string to file
     * @return type
     */
    public function create_back_up_by_text($dir_path_save)
    {
        ini_set('memory_limit','768M');
        set_time_limit(0);
        
        //tao create table
        $file = fopen($dir_path_save, 'a') or die("can't open file");
        $this->create_sql_table($file);
        fclose($file);//dong file
        unset($file);
        
        //tao insert data
        $arr_all_table = $this->db->getAll('SHOW FULL TABLES');
        $arr_all_table = array(0=>array('Tables_in_lang-giang'=>'t_r3_record','Table_type'=>'asd'),
//                                1=>array('Tables_in_lang-giang'=>'t_r3_record_type','Table_type'=>'asd'),
                                1=>array('Tables_in_lang-giang'=>'t_cores_group','Table_type'=>'asd'),
//                                1=>array('Tables_in_lang-giang'=>'t_r3_record_history','Table_type'=>'asd')
                                );
        
        //insert data into table
        foreach($arr_all_table as $arr_table)
        {
            $file = fopen($dir_path_save, 'a') or die("can't open file");
            
            $table_name = $arr_table['Tables_in_lang-giang'];
            $table_type = $arr_table['Table_type'];
            if(strtolower($table_type) == 'view')
            {
                continue;
            }
            //set fetch mode number
            $this->db->SetFetchMode(ADODB_FETCH_NUM);
            $sql = "select count(*) From $table_name";
            $record_count_no = (int) $this->db->getOne($sql);
            
            if($record_count_no > 0)
            {
                if($record_count_no > 100) //du lieu vua va qua lon
                {
                    $loop_limit = ceil($record_count_no/100);
                    for($loop_no = 0;$loop_no < $loop_limit;$loop_no++)
                    {
                        $offset = $loop_no * 100;
                        $limit  = 100;
                        
                        $sql = "select * From $table_name limit $offset,$limit";
                        $arr_all_data = $this->db->getAll($sql);
                        //tao sql insert
                        $this->create_sql_insert($arr_all_data,$table_name,$file);
                    }
                    unset($arr_all_data);//xoa khoi ram
                }
                else //du lieu nho
                {
                    $sql = "select * From $table_name";
                    $arr_all_data = $this->db->getAll($sql);
                    
                    //tao sql insert
                    $this->create_sql_insert($arr_all_data,$table_name,$file);
                }
            }//end if $record_count_no > 0
            echo __FILE__;
            var_dump::display(memory_get_usage());
            echo 'online:' . __LINE__;
            fclose($file);//dong file
            unset($file);
        }//end foreach array all table
        
        return 'Bạn đã sao lưu giữ liệu thành công !!!';
    }
    /**
     * tao cau lenh create table
     */
    public function create_sql_table($file)
    {
        $sql_text = '';
        //get all table
        $arr_all_table = $this->db->getAll('SHOW FULL TABLES');
        //create table
        foreach($arr_all_table as $arr_table)
        {
            $table_name = $arr_table['Tables_in_lang-giang'];
            $table_type = $arr_table['Table_type'];
            
            $sql_text .= "DROP TABLE IF EXISTS $table_name;\n";
            if(strtolower($table_type) != "view")
            {
                $arr_create_table = $this->db->getRow("SHOW CREATE TABLE $table_name");
                $sql_text .= $arr_create_table['Create Table'] . "; \n";
            }
            else
            {
                $arr_all_column = $this->db->getAll("SHOW COLUMNS FROM $table_name");
                $sql_text .= "CREATE TABLE IF NOT EXISTS $table_name ( \n";
                $create_column = '';
                foreach($arr_all_column as $arr_column) 
                {
                    $field = $arr_column['Field'];
                    $type = $arr_column['Type'];
                    
                    if($create_column == '')
                    {
                        $create_column .= "`$field` $type \n";
                    }
                    else
                    {
                        $create_column .= ", `$field` $type \n";
                    }
                }
                $sql_text .= $create_column . "); \n";
            }
        }
        
        //create view
        $arr_all_view = $this->db->getAll("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
        foreach ($arr_all_view as $arr_view )
        {
            $view_name = $arr_view['Tables_in_lang-giang'];
            //sql drop 
            $sql_text .= "DROP TABLE IF EXISTS $view_name;\n";
            $sql_text .= "DROP VIEW IF EXISTS $view_name;\n";
            //sql create
            $arr_create_table = $this->db->getRow("SHOW CREATE VIEW $view_name");
            $sql_text .= $arr_create_table['Create View'].";\n";
            
        }
        //put create table and view in file
        fwrite($file, $sql_text);
        unset($sql_text,$file);
    }
    /**
     * tao cau lenh insert database
     * @param type $arr_all_data
     * @param type $table_name
     */
    public function create_sql_insert($arr_all_data,$table_name,$file)
    {
        $sql_text = "INSERT INTO $table_name VALUES ";
        
        for($i=0;$i<count($arr_all_data);$i++)
        {
            $arr_data = $arr_all_data[$i];
            
            $sql_data = "(";
            foreach($arr_data as $data)
            {
                //replace \r\n vaf " -> neu la string
                $data = $this->replace_string_sql($data);
                
                if($sql_data == "(")
                {
                    $sql_data .= "'". $data . "'";
                }
                else
                {
                    //neu du lieu la null
                    if($data == NULL)
                    {
                        $sql_data .= ',' . "NULL";
                    }
                    else
                    {
                        $sql_data .= ',' . "'". $data ."'";
                    }
                }
            }
            if($i < (count($arr_all_data) - 1))
            {
                $sql_data .= "),";
            }
            else
            {
                $sql_data .= "); \n";
            }
            
            $sql_text = $sql_text . $sql_data;
        }
        fwrite($file, $sql_text);
        unset($sql_text,$sql_data,$arr_all_data);
    }
    
    /**
     * replace \r\n va " neu la string
     * @param type $var
     * @return type
     */
    public function replace_string_sql($var)
    {
        if(gettype($var) == 'string')
        {
            $var = mysql_real_escape_string($var);
            $var = utf8_decode($var);
        }
        return $var;
    }
}
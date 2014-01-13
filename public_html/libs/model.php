<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php

class Model
{
    /** @var \ADOConnection */
    public $db;
    public $goback_url;
    public $goforward_url;

    public function __construct()
    {
        switch (DATABASE_TYPE)
        {
            case 'ORACLE':
                //Oracle Setting
                putenv("NLS_LANG=AMERICAN_AMERICA.AL32UTF8");
                $this->db = NewADOConnection(CONST_ORACLE_DSN) or die('Cannot connect to Oracle Database Server!');
                break;

            case 'MYSQL':
                $this->db = ADONewConnection(CONST_MYSQL_DSN) or die('Cannot connect to MySQL Database Server!');
                mysql_set_charset('utf8');
                break;

            case 'MSSQL':
            default:
                $this->db = ADONewConnection('ado_mssql');
                $this->db->Connect(CONST_MSSQL_DSN) or die('Cannot connect to MSSQL Database Server!');
                break;
        }

        global $ADODB_CACHE_DIR;
        $ADODB_CACHE_DIR = SERVER_ROOT . 'cache/ADODB_cache/';

        //$this->db->cacheDir = './cache/';
        $this->db->cacheSecs = 3600 * 24 * 7 * 4; //4 tuan
        $this->db->SetFetchMode(ADODB_FETCH_BOTH);
        $this->db->debug     = DEBUG_MODE;
        //$this->db->debug = 0;
    }

    public function is_mssql()
    {
        return DATABASE_TYPE == 'MSSQL';
    }

    public function is_mysql()
    {
        return DATABASE_TYPE == 'MYSQL';
    }

    public function is_oracle()
    {
        return DATABASE_TYPE == 'ORACLE';
    }

    public static function create_single_xml_node($name, $value, $cdata = FALSE)
    {
        $node = '<' . $name . '>';
        $node .= ($cdata) ? '<![CDATA[' . $value . ']]>' : $value;
        $node .= '</' . $name . '>';

        return $node;
    }

    public static function replace_bad_char($str)
    {
        $str = stripslashes($str);
        $str = str_replace("&", '&amp;', $str);
        $str = str_replace('<', '&lt;', $str);
        $str = str_replace('>', '&gt;', $str);
        $str = str_replace('"', '&#34;', $str);
        $str = str_replace("'", '&#39;', $str);

        return $str;
    }

//end func replace_bad_char

    public static function get_post_var($html_object_name, $is_replace_bad_char = TRUE)
    {
        $var = isset($_POST[$html_object_name]) ? $_POST[$html_object_name] : NULL;

        if ($is_replace_bad_char)
        {
            return Model::replace_bad_char($var);
        }

        return $var;
    }

    //Quay ve man hinh truoc sau khi thuc hien thao tac voi CSDL
    public static function exec_done($url, $filter_array = array())
    {
        $html = '<html><head></head><body>';
        $html .= '<form name="frmMain" action="' . $url . '" method="POST">';

        foreach ($filter_array as $key => $val)
        {
            $html .= View::hidden($key, $val);
        }

        $html .= '</form>';
        $html .= '<script type="text/javascript">document.frmMain.submit();</script>';
        $html .= '</body></html>';

        echo $html;
        exit;
    }

    public static function popup_exec_done($retVal = NULL, $url = '')
    {
        echo '<script type="text/javascript">';
        if ($retVal != NULL && $retVal != FALSE)
        {
            echo "var returnVal = '$retVal';";
            echo 'window.parent.hidePopWin(true);';
        }
        else
        {
            echo 'window.parent.hidePopWin();';
            if ($url)
            {
                echo "window.parent.document.frmMain.action='$url';";
                
            }
            echo 'window.parent.document.frmMain.submit();';
        }

        echo '</script>';
        exit;
    }

    public function popup_exec_fail($message = 'Cáº­p nháº­t dá»¯ liá»‡u tháº¥t báº¡i!')
    {
        echo '<script type="text/javascript">';
        echo 'alert("' . replace_bad_char($message) . '");';
        echo 'window.parent.hidePopWin();';
        echo '</script>';
        exit;
    }

    //Quay ve man hinh truoc sau khi thuc hien thao tac voi CSDL
    public static function exec_fail($url, $message, $filter_array = array())
    {
        $html = '<html><head></head><body>';
        $html .= '<form name="frmMain" action="' . $url . '" method="POST">';

        foreach ($filter_array as $key => $val)
        {
            $html .= View::hidden($key, $val);
        }

        $html .= '</form>';
        $html .= '<script type="text/javascript">alert("' . $message . '");document.frmMain.submit();</script>';
        $html .= '</body></html>';

        echo $html;
        exit;
    }

    //Common Database Exec
    public function get_max($table_name, $field_name, $other_clause = '')
    {
        $sql = "SELECT MAX($field_name) as a FROM $table_name WHERE 1>0";
        if ($other_clause !== '')
        {
            $sql .= ' AND ' . $other_clause;
        }

        return $this->db->getOne($sql);
    }

//end func GetMaxValue

    /**
     * Thuc hien sap xep lai thu tu hen thi
     *
     * @param string $table_name	Ten bang
     * @param string $pk_field		Ten cot dong vai tro PK
     * @param string $order_field	Ten Cot Order
     * @param string $pk_value		Gia tri cua PK
     * @param int $assign_order		Gia tri Order moi
     * @param int $current_order	Gia tri Order hien tai
     * @param string $other_clause	Dieu kien khac
     * @author  Ngo Duc Lien <liennd@gmail.com>
     */
    function ReOrder($table_name, $pk_field, $order_field, $pk_value, $assign_order, $current_order = -1, $other_clause = '')
    {
        if (empty($other_clause))
            $other_clause = '';

        //if (intval($assign_order) == intval($current_order)) return;
        if (intval($current_order) > 0)
        {
            if ($assign_order > $current_order)
            {
                $str_sql = "update $table_name "
                        . "\n set $order_field = $order_field - 1"
                        . "\n where $order_field > $current_order and $order_field <= $assign_order and $pk_field <> $pk_value";
            }
            else
            {
                $str_sql = "update $table_name "
                        . "\n set $order_field = $order_field + 1"
                        . "\n where $order_field >= $assign_order and $order_field < $current_order and $pk_field <> $pk_value";
            }
        }
        else
        {
            $str_sql = " update $table_name "
                    . "\n set $order_field = $order_field + 1"
                    . "\n where $order_field >= $assign_order and $pk_field <> $pk_value";
        }
        if (strlen($other_clause) > 0)
        {
            $str_sql.="\n and $other_clause";
        }

        $this->db->Execute($str_sql);
        if ($this->db->ErrorNo() != 0)
        {
            return $this->db->ErrorMsg();
        }

        //Gan dung vi tri hien thi
        $this->db->Execute("update $table_name "
                . "\n set $order_field = $assign_order"
                . "\n where $pk_field = $pk_value");

        /* THU TU HIEN THI KHONG NHAT THIET PHAI LIEN MACH */
        //Abs Order
        $str_query = "select $pk_field from $table_name ";
        if (strlen($other_clause) > 0)
        {
            $str_query.="\n where $other_clause";
        }
        $str_query.="\n order by $order_field";

        $this->db->SetFetchMode(ADODB_FETCH_NUM);
        $arr_ID   = $this->db->GetAll($str_query);
        $count_ID = count($arr_ID);
        if ($count_ID > 0)
        {
            for ($i = 0; $i < $count_ID; $i++)
            {
                $ID      = $arr_ID[$i][0];
                $j       = $i + 1;
                $str_sql = "Update $table_name"
                        . "\n set $order_field=$j"
                        . "\n where $pk_field=$ID";
                $this->db->Execute($str_sql);
                if ($this->db->ErrorNo() != 0)
                {
                    return $this->db->ErrorMsg();
                }
            }
        }
        $this->db->SetFetchMode(ADODB_FETCH_BOTH);
    }

//end Func ReOrder()

    public function swap_order($p_table_name, $p_pk_column_name, $p_order_columm_name, $p_pk_column_value1, $p_pk_column_value2, $p_other_clause = '')
    {
        if (strlen($p_other_clause) > 0)
        {
            $v_other_clause = ' AND (' . $p_other_clause . ')';
        }
        else
        {
            $v_other_clause = '';
        }

        $this->db->SetFetchMode(ADODB_FETCH_NUM);
        //Lay order hien tai cua doi tuong 1
        $str_sql  = "Select $p_order_columm_name as C_ORDER From $p_table_name Where $p_pk_column_name=$p_pk_column_value1";
        $v_order1 = $this->db->GetOne($str_sql);

        //Lay Order cua doi tuong 2
        $str_sql  = "Select $p_order_columm_name as C_ORDER From $p_table_name Where $p_pk_column_name=$p_pk_column_value2";
        $v_order2 = $this->db->GetOne($str_sql);

        $str_sql = "Update $p_table_name Set $p_order_columm_name=$v_order2 Where $p_pk_column_name=$p_pk_column_value1";
        $this->db->Execute($str_sql);

        $str_sql = "Update $p_table_name Set $p_order_columm_name=$v_order1 Where $p_pk_column_name=$p_pk_column_value2";
        $this->db->Execute($str_sql);

        $ret_array = array(
            $p_pk_column_value1 => $v_order2,
            $p_pk_column_value2 => $v_order1,
        );
        $this->db->SetFetchMode(ADODB_FETCH_BOTH);
        return $ret_array;
    }

//end func swap_order()

    /**
     * Luu du lieu nhi phan vao CSDL trong cot co kieu BLOB
     *
     * @param string $table_name			Ten bang
     * @param string $pk_column_name		Ten cot PK
     * @param string $file_name_column		Ten cot chua ten file
     * @param string $file_content_column	Ten cot chua noi dung file
     * @param string $pk_value				Gia tri PK
     * @param string $full_path_to_file		Duong dan day du toi file
     * @return Gia tri ID vua duoc cap nhat neu thanh cong, nguoc lai false
     * @see UpdateBlobFile
     */
    public function save_file_to_db($table_name, $file_name_column, $file_content_column, $full_path_to_file, $where = '')
    {

        if (!is_file($full_path_to_file))
            return false;

        //Lay ten file
        $arr_path_info = pathinfo($full_path_to_file);
        $str_file_name = $this->db->qstr($arr_path_info['basename']);

        //Luu ten file
        $sql = "Update $table_name set $file_name_column = $str_file_name";
        $sql .= ($where == '') ? '' : " where $where";
        $this->db->Execute($sql);

        //Luu noi dung file
        $this->db->UpdateBlobFile($table_name, $file_content_column, $full_path_to_file, $where);
    }

//end func save_file_to_db

    public function create_file_from_db($table_name, $file_content_column, $full_path_to_file, $where = '', $over_write = false)
    {
        $str_sql = "Select $file_content_column as FILE_CONTENT from $table_name";
        $str_sql .= (strlen($where) > 0) ? "\n Where $where" : '';

        if (($over_write === true) or (!file_exists($full_path_to_file)))
        {
            $this->db->setFetchMode(ADODB_FETCH_NUM);
            $file_content = $this->db->getOne($str_sql);
            $handle       = @fopen($full_path_to_file, "wb");
            @fwrite($handle, $file_content);
            @fclose($handle);
            $this->db->setFetchMode(ADODB_FETCH_BOTH);
        }
        return false;
    }

//end func create_file_from_db

    public function list_get_all_by_listtype_code($listtype_code, $arr_xml_tag = null)
    {
        if (DATABASE_TYPE == 'ORACLE')
        {
            $sql = 'SELECT t.c_code, t.c_name ';
            for ($i = 0; $i < sizeof($arr_xml_tag); $i++)
            {
                $id = '"' . $arr_xml_tag[$i] . '"';
                $sql .= ", EXTRACTVALUE(t.c_xml_data, '/data/item[@id=$id]/value') $id";
            }
            $sql .= ' FROM t_list t ';
            $sql .= " WHERE (t.fk_listtype=(Select PK_LISTTYPE FROM t_listtype WHERE c_code='$listtype_code')) ";
            $sql .= ' AND (t.c_status > 0)';
        }
        elseif (DATABASE_TYPE == 'MSSSQL')
        {
            $sql = 'SELECT L.C_CODE, L.C_NAME ';
            for ($i = 0; $i < sizeof($arr_xml_tag); $i++)
            {
                $id = $arr_xml_tag[$i];
                $sql .= ",isnull(item.value('(item[@id=''$id'']/value/text())[1]','Nvarchar(Max)'),'') as $id";
            }
            $sql .= " From T_LIST as L CROSS APPLY C_XML_DATA.nodes('/data') t(item)";
            $sql .= " WHERE (L.fk_listtype=(Select PK_LISTTYPE FROM t_listtype WHERE c_code='$listtype_code')) ";
            $sql .= ' AND (L.c_status > 0)';
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $sql = 'SELECT t.C_CODE, t.C_NAME ';
            for ($i = 0; $i < sizeof($arr_xml_tag); $i++)
            {
                $id = '"' . $arr_xml_tag[$i] . '"';
                $sql .= ", EXTRACTVALUE(t.c_xml_data, '/data/item[@id=$id]/value') $id";
            }
            $sql .= ' FROM t_cores_list t ';
            $sql .= " WHERE (t.FK_LISTTYPE=(Select PK_LISTTYPE FROM t_cores_listtype WHERE C_CODE='$listtype_code')) ";
            $sql .= ' AND (t.C_STATUS > 0)';
        }

        return $this->db->getAll($sql);
    }

//end func list_get_all_by_listtype_code

    public function assoc_list_get_all_by_listtype_code($listtype_code, $use_cache = FALSE)
    {
        $stmt   = "Select
                    L.C_CODE
                    , L.C_NAME
                From t_cores_list As L
                Where L.C_STATUS > 0
                    And L.FK_LISTTYPE=(Select PK_LISTTYPE From t_cores_listtype Where C_CODE=? And C_STATUS>0)
                    Order By C_ORDER";
        $params = array($listtype_code);

        if ($use_cache)
        {
            return $this->db->cacheGetAssoc($stmt, $params);
        }
        return $this->db->getAssoc($stmt, $params);
    }

    public function get_new_seq_val($table_seq_name)
    {
        $table_seq_name = $this->replace_bad_char($table_seq_name);

        $sql = "Insert Into $table_seq_name(C_DATE_CREATED) Values(getDate())";
        $this->db->Execute($sql);

        if (DATABASE_TYPE == 'MSSQL')
        {
            return $this->db->getOne("SELECT IDENT_CURRENT('$table_seq_name')");
        }
        else
        {
            return $this->db->Insert_ID($table_seq_name);
        }
    }

    public function getDate()
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            return $this->db->getOne("Select convert(varchar,getDate(), 120) as d");
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            return $this->db->getOne("Select Now() as d");
        }

        return NULL;
    }

    /**
     *
     * @param Int $nDay
     * @return date string in yyymmdd format
     */
    public function date_which_diff_day($nDay)
    {
        $stmt = 'Select Convert(varchar(10),a.C_DATE,103) C_DATE
                From (
                        Select C_DATE
                            ,ROW_NUMBER() OVER (ORDER BY C_DATE Asc) as RN
                        From t_cores_calendar
                        Where C_OFF=0 And DATEDIFF(day, GETDATE(), C_DATE) >= 0
                    ) a
                Where a.RN = ?';
        // $this->db->debug=0;
        return $this->db->getOne($stmt, array($nDay + 1));
    }

    public function date_which_diff_day_yyyymmdd($nDay)
    {
        if ($this->is_mssql())
        {
            $stmt = "Select Replace(Convert(varchar(10),a.C_DATE,111), '/','-')  C_DATE
                    From (
                        Select
                            C_DATE
                            , ROW_NUMBER() OVER (Order by C_DATE Asc) as RN
                        From t_cores_calendar
                        Where C_OFF=0
                            And DATEDIFF(day, GETDATE(), C_DATE) >= 0
                    ) a
                    Where a.RN = ?";
        }
        elseif ($this->is_mysql())
        {
            $stmt = "SELECT
                        DATE_FORMAT(a.C_DATE,'%Y-%m-%d')    C_DATE
                    FROM (SELECT
                              C_DATE
                          FROM t_cores_calendar
                          WHERE C_OFF = 0
                                AND DATEDIFF(NOW(), C_DATE) <= 0
                          ORDER BY C_DATE ASC
                          LIMIT ? ) a
                    ORDER BY C_DATE DESC";
        }
        else
        {
            return NULL;
        }

        return $this->db->getOne($stmt, array($nDay + 1));
    }

    /**
     * Tim ngay lam viec tiep theo
     * @param number $count
     * @param string $from_date_yyyymmddhhmmss
     */
    public function next_working_day($count = 1, $from_date_yyyymmddhhmmss = NULL)
    {
        $count = intval($count);
        if ($from_date_yyyymmddhhmmss == NULL)
        {
            $from_date_yyyymmddhhmmss = $this->get_date_yyyymmdd_now();
        }

        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "Select Replace(Convert(varchar(10),a.C_DATE,111), '/','-')  C_DATE
                        From (
                            Select
                                C_DATE
                                , ROW_NUMBER() OVER (Order by C_DATE Asc) as RN
                            From t_cores_calendar
                            Where C_OFF=0
                                And DATEDIFF(day, ?, C_DATE) >= 0
                        ) a
                        Where a.RN = ?";
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "SELECT DATE_FORMAT(a.C_DATE,'%Y-%m-%d') C_DATE
                    FROM (
                        SELECT C_DATE
                        FROM t_cores_calendar
                        WHERE C_OFF=0
                            AND DATEDIFF(?, C_DATE) <= 0
                        ORDER BY C_DATE ASC
                        LIMIT ?
                    ) a
                    ORDER BY C_DATE DESC";
        }

        return $this->db->getOne($stmt, array($from_date_yyyymmddhhmmss, $count + 1));
    }

    /**
     *  Ä�áº¿m sá»‘ ngÃ y lÃ m viá»‡c giá»¯a 2 má»‘c ngÃ y
     * @param $from_date_in_yyyymmdd NgÃ y báº¯t Ä‘áº§u, theo Ä‘á»‹nh dáº¡ng yyyy-mm-dd
     * @param $to_date_in_yyyymmdd NgÃ y káº¿t thÃºc, theo Ä‘á»‹nh dáº¡ng yyyy-mm-dd
     *
     * @return Int Sá»‘ ngÃ y lÃ m viá»‡c giá»¯a hai ngÃ y.
     */
    public function days_between_two_date($from_date_in_yyyymmdd, $to_date_in_yyyymmdd)
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = 'Select Count(*)
                    From t_cores_calendar
                    Where C_OFF=0
                        And datediff(day, ?, C_DATE)>=0
                        And Datediff(day, ?, C_DATE)<0';
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {

            if (days_diff($from_date_in_yyyymmdd, $to_date_in_yyyymmdd) < 0)
            {
                $stmt = 'Select -1 * Count(*)
                    From t_cores_calendar
                    Where C_OFF=0
                        And datediff(?, C_DATE)>0
                        And Datediff(?, C_DATE)<=0';
            }
            else
            {
                $stmt = 'Select Count(*)
                    From t_cores_calendar
                    Where C_OFF=0
                        And datediff(?, C_DATE)<=0
                        And Datediff(?, C_DATE)>0';
            }
        }
        else
        {
            return NULL;
        }

        $params = array($from_date_in_yyyymmdd, $to_date_in_yyyymmdd);

        $this->db->debug = 0;
        return $this->db->getOne($stmt, $params);
    }

    /**
     * Kiem tra hom nay co phai ngay lam viec khong?
     */
    public function check_today_working_day()
    {
        $stmt = 'Select Count(*)
                From t_cores_calendar
                Where C_OFF=0
                    And Datediff(day, getDate(), C_DATE)=0';
        return $this->db->getOne($stmt);
    }

    public function build_interal_order($table_name = 't_cores_ou', $pk_column_name = 'PK_OU', $parent_column_name = 'FK_OU', $pk_value = -1, $order_column_name = 'C_ORDER', $internal_order_column_name = 'C_INTERNAL_ORDER')
    {
        $this->db->SetFetchMode(ADODB_FETCH_BOTH);

        //Stack
        $arr_stack = array();

        $id  = $pk_value;
        //Kiem tra ID co ton tai khong
        $sql = "Select Count(*) From $table_name Where $pk_column_name=$id";
        if ($this->db->getOne($sql) < 1)
        {
            $sql = "Select $pk_column_name From $table_name Where ($parent_column_name Is Null Or $parent_column_name < 1)";
            $id  = $this->db->getOne($sql);
        }

        //Cáº­p nháº­t Internal Order cá»§a node
        $v_order = $this->db->getOne("Select $order_column_name From $table_name Where $pk_column_name=$id");
        $v_order = str_repeat('0', 3 - strlen($v_order)) . $v_order;

        $v_parent_internal_order = $this->db->getOne("Select $internal_order_column_name From $table_name Where $pk_column_name=(Select $parent_column_name From $table_name Where $pk_column_name=$id)");
        $v_new_internal_order    = $v_parent_internal_order . $v_order;
        $sql                     = "Update $table_name Set $internal_order_column_name='$v_new_internal_order' Where $pk_column_name=$id";
        $this->db->Execute($sql);

        //Cáº­p nháº­t Internal Order cá»§a táº¥t cáº£ cÃ¡c node con
        $sql       = "Select
                    $pk_column_name
                    ,$internal_order_column_name
                    ,$order_column_name
                From $table_name Where $parent_column_name=(Select $parent_column_name From $table_name Where $pk_column_name=$id)
                Order by $order_column_name";
        $arr_stack = $this->db->getAll($sql);
        $i         = 1;
        while (sizeof($arr_stack) > 0 && $i < 10000)
        {
            //Pop stack
            $arr_single_row = array_pop($arr_stack);

            $v_ou_id          = $arr_single_row[$pk_column_name];
            $v_internal_order = $arr_single_row[$internal_order_column_name];
            $v_order          = $arr_single_row[$order_column_name];

            //Update all children internal order
            if (DATABASE_TYPE == 'MSSQL')
            {
                $sql = "Update $table_name
                        Set $internal_order_column_name = '$v_internal_order' + Case
                                                                                When $order_column_name < 10 Then '00' + Convert(varchar(1),$order_column_name)
                                                                                When $order_column_name >= 10 And $order_column_name < 100 Then '0' + Convert(varchar(2),$order_column_name)
                                                                                Else Convert(varchar(3),$order_column_name)
                                                                            End

                        WHERE $parent_column_name=$v_ou_id";
            }
            elseif (DATABASE_TYPE == 'MYSQL')
            {
                $sql = "Update $table_name
                        Set $internal_order_column_name = Concat ('$v_internal_order', Case
                                                                                When $order_column_name < 10 Then Concat('00', $order_column_name)
                                                                                When $order_column_name >= 10 And $order_column_name < 100 Then Concat('0', $order_column_name)
                                                                                Else $order_column_name
                                                                            End
                                                        )
                        WHERE $parent_column_name=$v_ou_id";
            }
            $this->db->Execute($sql);

            //Push stack
            $stmt           = "Select
                        $pk_column_name
                        ,$internal_order_column_name
                        ,$order_column_name
                    From $table_name Where $parent_column_name=$v_ou_id
                    Order by $order_column_name";
            $arr_all_sub_ou = $this->db->getAll($stmt);
            foreach ($arr_all_sub_ou as $ou)
            {
                array_push($arr_stack, $ou);
            }
            $i++;
        }//end while
    }

    public function get_last_inserted_id($table_name, $id_column_name = '')
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            return $this->db->getOne("SELECT IDENT_CURRENT('$table_name')");
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            return $this->db->Insert_ID($table_name, $id_column_name);
        }

        return NULL;
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

    public function get_date_yyyymmdd_now()
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            return $this->db->getOne('SELECT CONVERT(VARCHAR(10), GETDATE(), 121)');
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            return $this->db->getOne("Select DATE_FORMAT(NOW(),'%Y-%m-%d')");
        }

        return NULL;
    }

    public function get_hour_now()
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            return $this->db->getOne('Select datepart(hour,getDate())');
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            return $this->db->getOne("SELECT DATE_FORMAT(NOW(),'%H')");
        }

        return NULL;
    }

    public function build_for_xml_raw_query($table_name, $arr_column, $clause = '')
    {
        if ($this->is_mssql())
        {
            $sql = '';
            foreach ($arr_column as $column)
            {
                $sql .= ($sql == '') ? "Convert(Xml, (Select $column " : ",$column";
            }
            $sql .= " From $table_name";

            if ($clause != '')
            {
                $sql .= $clause;
            }
            $sql .= ' For xml raw), 1)';
        }
        elseif ($this->is_mysql())
        {
            $sql = "(Select GROUP_CONCAT('<row '";
            foreach ($arr_column as $column)
            {
                $sql .= ", Concat(' $column=\"', $column , '\"')";
            }
            $sql .= ", ' />' SEPARATOR '')";
            $sql .= " From $table_name ";
            if ($clause != '')
            {
                $sql .= $clause;
            }
            $sql .= ')';
        }

        return $sql;
    }

//end func

    public function build_convert_date_query($column_name, $date_code)
    {
        if ($this->is_mssql())
        {
            $sql = " Convert(Varchar, $column_name, $date_code)";
        }
        elseif ($this->is_mysql())
        {
            switch ($date_code)
            {
                case 101:
                    //MM/DD/YYYY
                    $sql = " CAST(DATE_FORMAT($column_name,'%m/%d/%Y') AS CHAR )";
                    break;

                case 103:
                    //DD/MM/YYYY
                    $sql = " CAST(DATE_FORMAT($column_name,'%d/%m/%Y') AS CHAR )";
                    break;

                case 131:
                    //DD/MM/YYYY HH:MI:SS
                    $sql = " CAST(DATE_FORMAT($column_name,'%d/%m/%Y %H:%i:%s') AS CHAR )";
                    break;

                case 111:
                    //[YYYY/MM/DD]
                    $sql = " CAST(DATE_FORMAT($column_name,'%Y-%m-%d') AS CHAR )";
                    break;

                default:
                    $sql = " CAST(DATE_FORMAT($column_name,'%Y-%m-%d %H:%i:%s') AS CHAR )";
                    break;
            }
        }
        return $sql;
    }

//end func

    public function build_getdate_function()
    {
        if (($this->is_mssql()))
        {
            $str = 'getDate()';
        }
        elseif ($this->is_mysql())
        {
            $str = 'Now()';
        }
        else
        {
            $str = NULL;
        }

        return $str;
    }

}
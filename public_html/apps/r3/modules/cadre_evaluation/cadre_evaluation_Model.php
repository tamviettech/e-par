<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

    class cadre_evaluation_Model extends Model 
    {
        
        function __construct() 
        {
            parent::__construct();
        }
        // Lay tat ca tieu chi theo tung can bo danh gia
        function qry_all_criterial($v_staff_id)
        {
            $v_staff_id = replace_bad_char($v_staff_id);
            $sql = 'SELECT
                            l.PK_LIST,
                            l.C_CODE,
                            l.C_NAME,  	
                            ExtractValue(C_XML_DATA, "/data/item/value") AS IMAGE_LINK,
                            (
                                  SELECT a.C_VOTE 
                                          FROM t_r3_assessment a
                                          WHERE FK_CRITERIAL  = l.PK_LIST 
                                                  AND a.FK_USER = ?
                                                  AND a.C_DAY   = DAY(CURRENT_DATE())
                                                  AND a.C_MONTH = MONTH(CURRENT_DATE())
                                                  AND a.C_YEAR  = YEAR(CURRENT_DATE())
                                  ) AS C_VOTE	
                          FROM t_cores_list l
                          WHERE C_STATUS = 1
                              AND FK_LISTTYPE = (SELECT
                                                   PK_LISTTYPE
                                                 FROM t_cores_listtype
                                                 WHERE C_CODE = "DM_TIEU_CHI_DANH_GIA")
                          ORDER BY l.C_ORDER ASC
';
            $arr_criterial = $this->db->GetAll($sql,array($v_staff_id));
            if(count($arr_criterial) > 0) {
                return $arr_criterial;
            }
            return false;
        }
        
        // Cap nhat vote
        function update_vote() 
        {
            if($_POST) 
            {
                if(isset($_POST['user_id']) && isset($_POST['today']) && isset($_POST['fk_criterial'])) 
                {
                    $v_key_cookie_check = 'vote_user_'.intval($_POST['user_id']);
                    if(!isset($_COOKIE[$v_key_cookie_check]) OR $_COOKIE[$v_key_cookie_check] === NULL)
                    {
                        $user_id        = $_POST['user_id'];
                        $today          = $_POST['today'];
                        $v_fk_creterial = $_POST['fk_criterial'];
                        $arr_today_date = explode('-', $today);
                        // Kiem tra ton tai danh gia trong ngay
                        $stmt = "SELECT PK_ASSESSMENT
                                , C_VOTE
                                FROM t_r3_assessment
                                WHERE FK_CRITERIAL = ?
                                  AND C_DAY = DAY(NOW())
                                  AND C_MONTH = MONTH(NOW())
                                  AND C_YEAR = YEAR(NOW())
                                  AND FK_USER = ?
                                ";
                        $res = $this->db->GetRow($stmt, array($v_fk_creterial, $user_id));
                        if(count($res) > 0) 
                        {
                            $new_vote = $res['C_VOTE'] + 1;
                            $stmt = "UPDATE t_r3_assessment
                                    SET C_VOTE = ?
                                    WHERE PK_ASSESSMENT = ?
                                    ";
                            $res = $this->db->Execute($stmt, array($new_vote, $res['PK_ASSESSMENT']));

                            if($this->db->ErrorNo() == 0)
                            {
                                setcookie($v_key_cookie_check, 'check_vote', time() + 30);
//                                echo json_encode(1);
                                $qry_all_vote = "SELECT a.C_VOTE 
                                                FROM t_r3_assessment a
                                                WHERE FK_CRITERIAL  = ?
                                                        AND a.FK_USER = ?
                                                        AND a.C_DAY   = DAY(CURRENT_DATE())
                                                        AND a.C_MONTH = MONTH(CURRENT_DATE())
                                                        AND a.C_YEAR  = YEAR(CURRENT_DATE())";
                                $params = array($v_fk_creterial,$user_id);
                                echo json_encode($this->db->GetOne($qry_all_vote,$params));
                            }
                        }
                        else
                        {
                            $stmt = "INSERT INTO t_r3_assessment(FK_USER, FK_CRITERIAL, C_VOTE, C_DAY, C_MONTH, C_YEAR)
                                        VALUES(?,?,?,DAY(NOW()),MONTH(NOW()),YEAR(NOW()))
                                    ";
                            $res = $this->db->Execute($stmt, array($user_id, $v_fk_creterial, 1));
                            if($res > 0)
                            {
                                setcookie($v_key_cookie_check, 'check_vote', time() + 30);
                                  echo json_encode(1);
                                  exit();
                            }
                        }
                        
                    }
                    else
                    {
                        unset($_COOKIE[$v_key_cookie_check]);
                        echo decoct(0);
                        exit();
                    }
                }
            }    
        }
        
        // Lay danh sach can bo duoc danh gia
        function qry_all_staff()
        {            
            $condition_query = '';
            //Mã vùng lựa chọn nhân viên đánh giá
            $arr_filter = json_decode(CONST_FILETER_CADRE_EVALUATON_VILLAGE);
            if (sizeof($arr_filter)>0)
            {
                $condition = '';
                for($i = 0; $i <count($arr_filter); $i ++)
                {
                     $v_village_name = trim($arr_filter[$i]);
                    if($condition == '')
                    {
                        $condition  .= " OU.C_NAME = '$v_village_name' "; 
                    }
                    else
                    {
                         $condition  .= " OR OU.C_NAME = '$v_village_name' "; 
                    }
                   
                }   
                $condition_query = " AND ( $condition )";
            }
            
              // Tim kiem theo name
            $v_filter_name = get_post_var('txt_filter','');
            if(trim($v_filter_name))
            {
                $condition_query .= " AND U.C_NAME like '%$v_filter_name%'";
            }
            
            //Dem tong ban ghi
            $sql_count_record = "SELECT
                      COUNT(DISTINCT(U.PK_USER))
                    FROM t_cores_user U
                      INNER JOIN t_r3_user_task T LEFT JOIN t_cores_ou OU
                        ON U.C_LOGIN_NAME = T.C_USER_LOGIN_NAME AND  U.FK_OU = OU.PK_OU 
                        WHERE (T.C_TASK_CODE LIKE '%TIEP_NHAN'
                    	OR T.C_TASK_CODE LIKE '%TRA_KET_QUA'
                    	OR T.C_TASK_CODE LIKE '%THU_PHI')
                    	AND U.C_STATUS = 1
                    	$condition_query        
                    ";
            $stmt = 
                    "SELECT
                      DISTINCT(U.PK_USER)
                      ,U.C_NAME
                      ,U.C_LOGIN_NAME
                      ,($sql_count_record) TOTAL_RECORD
                    FROM t_cores_user U
                      INNER JOIN t_r3_user_task T LEFT JOIN t_cores_ou OU
                        ON U.C_LOGIN_NAME = T.C_USER_LOGIN_NAME AND  U.FK_OU = OU.PK_OU
                        WHERE (T.C_TASK_CODE LIKE '%TIEP_NHAN'
                    	OR T.C_TASK_CODE LIKE '%TRA_KET_QUA'
                    	OR T.C_TASK_CODE LIKE '%THU_PHI')
                    	AND U.C_STATUS = 1
                    	$condition_query
                    	ORDER BY U.PK_USER ASC
                    	         
                    ";
            $arr_user = $this->db->GetAll($stmt);
            
            if(count($arr_user) > 0) 
            {
                return $arr_user;
            }
            return array();
        }
        
        // Lay danh gia tung can bo
        function qry_single_result($user_id)
        {
            $day = get_post_var('day', '');
            $month = get_post_var('month', '');
            $year = get_post_var('year', '');
            $cond = '';
            if(!empty($day))
            {
                $cond .= " AND C_DAY = $day";
            }
            if(!empty($month))
            {
                $cond .= " AND C_MONTH = $month";
            }
            if(!empty($year))
            {
                $cond .= " AND C_YEAR = $year";
            }
            $stmt = "SELECT L.C_NAME,
                    SUM(A.C_VOTE) C_VOTE
                    FROM t_cores_list L
                    INNER JOIN t_r3_assessment A ON L.PK_LIST = A.FK_CRITERIAL
                    INNER JOIN t_cores_listtype T ON L.FK_LISTTYPE = T.PK_LISTTYPE
                    WHERE A.FK_USER = ? $cond
                      AND T.C_CODE = 'DM_TIEU_CHI_DANH_GIA'
                      GROUP BY A.FK_CRITERIAL
                      ORDER BY L.PK_LIST ASC
                    ";
            $arr_result = array();
            $arr_result = $this->db->GetAll($stmt, array($user_id));
            
            $stmt = "SELECT SUM(C_VOTE) TOTAL FROM t_r3_assessment WHERE FK_USER = ? $cond";
            $arr_total = $this->db->GetRow($stmt, array($user_id));
            $arr_result[] = $arr_total;
            if(count($arr_result > 0)) 
            {
                return $arr_result;
            }
            return false;
        }
        
        // Lay nhung nam danh gia
        function qry_year()
        {
            $sql = "SELECT DISTINCT(C_YEAR) FROM t_r3_assessment";
            return $this->db->GetAll($sql);
        }
        
        function check_access()
        {
            $v_user_id = session::get('user_id');
            $v_access_user_id = $this->qry_user_in_function();
            if($v_access_user_id != '')
            {
                $arr_access_user_id = explode(',', $v_access_user_id);
                if(in_array($v_user_id, $arr_access_user_id))
                {
                    return true;
                }
                return false;
            }
            return false;
        }
        /**
         * Lay ten cua nhan vien theo ma id
         * @param int $v_staff_id ID nhan vien
         * @return string Ten cua nhan vien
         */
        public function  qry_all_report($v_begin_date ='',$v_end_date = '')
        {
            $v_conditon = '';
            if(trim($v_begin_date) !='')
            {
                $arr_begin_date = explode('-', $v_begin_date);
                $v_begin_day      = $arr_begin_date[0];
                $v_begin_month    = $arr_begin_date[1];
                $v_begin_year     = $arr_begin_date[2];
                $v_conditon .= " And C_DAY >= $v_begin_day And C_MONTH >= $v_begin_month And C_YEAR >= $v_begin_year ";
            }
            if(trim($v_end_date) !='')
            {
                $arr_end_date = explode('-', $v_end_date);
                $v_end_day      = $arr_end_date[0];
                $v_end_month    = $arr_end_date[1];
                $v_end_year     = $arr_end_date[2];
                $v_conditon .= " And C_DAY <= $v_end_day And C_MONTH <= $v_end_month And C_YEAR <= $v_end_year ";
            }
           $arr_list = $this->get_all_criteria();
           
           $arr_all_staff = $this->qry_all_staff();
         
            $stmt = "SELECT  a.FK_USER ";
            $arr_list_id =array();
            foreach ($arr_list as $key => $val)
            {
                $v_list_id      = $val['PK_LIST'];
                $arr_list_id[]    = $v_list_id;
                $v_list_code    = $val['C_CODE'];
               $stmt .= ",(SELECT SUM(C_VOTE)  FROM t_r3_assessment  WHERE a.FK_USER = FK_USER AND  FK_CRITERIAL = '$v_list_id' $v_conditon GROUP BY FK_USER,FK_CRITERIAL) AS C_VOTE_$v_list_code";
            }   
            $v_list_id = implode(', ', $arr_list_id);
            $stmt .= ",(SELECT SUM(C_VOTE)  FROM t_r3_assessment  WHERE a.FK_USER = FK_USER $v_conditon AND  FK_CRITERIAL IN ( $v_list_id )) AS C_TOTAL_VOTE 
            FROM t_r3_assessment a 
            Where (1=1) $v_conditon
            GROUP BY FK_USER";
                
            $arr_results = $this->db->GetAssoc($stmt);
           
            if($this->db->ErrorNo() == 0)
            {
                return $arr_results ;
            }
            return array();
        }
        /**
         * Lấy danh sách tất cả tiêu chí đánh gia
         * @return  array() True mang chứa dánh sách tiêu chí 
         */
        
        function get_all_criteria()
        {
              $sql = "SELECT
                            C_CODE,
                            PK_LIST,
                            C_NAME
                          FROM t_cores_list
                          WHERE FK_LISTTYPE = (SELECT
                                                 PK_LISTTYPE
                                               FROM t_cores_listtype
                                               WHERE C_CODE = ? and C_STATUS = 1)
                              AND C_STATUS = 1
                          ORDER BY C_ORDER asc ";
            return $this->db->GetAssoc($sql,array(_CONST_DANH_MUC_TIEU_CHI_DANH_GIA));
        }
        /**
         * Lay ten cua nhan vien theo ma id
         * @param int $v_staff_id ID nhan vien
         * @return string Ten cua nhan vien
         */
        public function qry_staff_get_id($v_staff_id)
        {
            $sql = "SELECT 
                        C_NAME,
                        C_LOGIN_NAME
                    FROM t_cores_user 
                    WHERE PK_USER =?";
            return $this->db->GetAll($sql,array($v_staff_id));
        }
    }
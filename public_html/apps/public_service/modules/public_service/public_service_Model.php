<?php 
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
/**
 * Description of public_service_Model
 *
 * @author Tam Viet
 */
class public_service_Model extends Model
{
    function __construct()
    {
        parent::__construct();
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
     * Lây danh sach tin lien quan. Mếu tin bài liên quan ít hơn quy định hiển thị .Thì mặc định lấy những tin bài khác tiếp theo
     * @param int $category_id  : Ma chuyen muc
     * @param int  $article_id  : Ma tin bai
     */
    public function qry_all_connection_article($category_id,$article_id)
    {
        $category_id = replace_bad_char($category_id);
        $article_id  = replace_bad_char($article_id);
        
        $query       = "" ;
        if(COUNT_LIMIT_ARTICLE_CONNECT >0)
        {
            $query       = " LIMIT ".COUNT_LIMIT_ARTICLE_CONNECT ;
        }
        
        $stmt    = "SELECT
                        DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y %H:%i:%s') AS C_BEGIN_DATE,
                        PK_ARTICLE,
                        C_TITLE,
                        C_SLUG                AS C_SLUG_ARTICLE,
                        C_SUB_TITLE,
                        C_PEN_NAME,
                        C_KEYWORDS,
                        C_TAGS,
                        C_CACHED_RATING,
                        C_CACHED_RATING_COUNT,
                        C_SUMMARY,
                        C_TAGS,
                        C_FILE_NAME,
                        C_CONTENT,
                        $category_id as C_CAT_ID,
                        (SELECT
                           C_SLUG
                         FROM t_ps_category
                         WHERE PK_CATEGORY = ?) AS C_SLUG_CAT,
                        (SELECT
                           C_NAME
                         FROM t_ps_category
                         WHERE PK_CATEGORY = ?) AS C_CATEGORY_NAME
                      FROM t_ps_article a
                        INNER JOIN t_ps_category_article ca
                          ON a.PK_ARTICLE = ca.FK_ARTICLE
                      WHERE PK_ARTICLE <> ?
                          AND FK_CATEGORY = ?
                          AND FK_CATEGORY IN(SELECT
                                               PK_CATEGORY
                                             FROM t_ps_category where C_STATUS = 1)
                          AND C_STATUS = 1
                          AND C_BEGIN_DATE <= NOW()
                          AND C_END_DATE >= NOW()
                          and PK_ARTICLE < ?
                          ORDER BY PK_ARTICLE DESC
                         $query ";
        $article = $this->db->getAll($stmt, array($category_id, $category_id, $article_id, $category_id,$article_id));
      
        if($this->db->ErrorNo() == 0)
        {
            return $article;
        }
        return array();   
    }
    function get_article_khac($arr_article_connect,$v_article_current)
    {   
            $list_id_article = $v_article_current;
            for($i=0;$i<count($arr_article_connect); $i++)
            {
                $list_id_article .= ','.$arr_article_connect[$i]['PK_ARTICLE'];
            }
           
            $query       = " LIMIT ".(COUNT_LIMIT_ARTICLE_CONNECT - sizeof($arr_article_connect));
            $sql        = "  SELECT
                            DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y %H:%i:%s') AS C_BEGIN_DATE,
                            PK_ARTICLE,
                            C_TITLE,
                            C_SLUG                AS C_SLUG_ARTICLE,
                            C_SUB_TITLE,
                            C_PEN_NAME,
                            C_KEYWORDS,
                            C_TAGS,
                            C_CACHED_RATING,
                            C_CACHED_RATING_COUNT,
                            C_SUMMARY,
                            C_TAGS,
                            C_FILE_NAME,
                            C_CONTENT,
                            a.C_DEFAULT_CATEGORY   AS C_CAT_ID,
                            (SELECT
                               C_SLUG
                             FROM t_ps_category
                             WHERE PK_CATEGORY = a.C_DEFAULT_CATEGORY ) AS C_SLUG_CAT,
                            (SELECT
                               C_NAME
                             FROM t_ps_category
                             WHERE PK_CATEGORY = a.C_DEFAULT_CATEGORY) AS C_CATEGORY_NAME
                          FROM t_ps_article a
                            INNER JOIN t_ps_category_article ca
                              ON a.PK_ARTICLE = ca.FK_ARTICLE
                          WHERE PK_ARTICLE NOT IN ($list_id_article)
                              AND FK_CATEGORY = a.C_DEFAULT_CATEGORY
                              AND FK_CATEGORY IN(SELECT
                                                   PK_CATEGORY
                                                 FROM t_ps_category
                                                 WHERE C_STATUS = 1)
                              AND C_STATUS = 1
                              AND C_BEGIN_DATE <= NOW()
                              AND C_END_DATE >= NOW()
                              
                          ORDER BY PK_ARTICLE DESC
                         $query ";
            $article = $this->db->getAll($sql);
            if($this->db->ErrorNo() == 0)
            {
                return $article;
            }        
        return array();   
    }
    
    
    /**
     * Lay danh sach chuyen muc
     */
    public function qry_all_category()
    {
        $stmt = "select * 
                from t_ps_category
                where C_STATUS =1";
       $results =  $this->db->GetAll($stmt);
       if($this->db->ErrorNo() == 0)
       {
           return $results;
       }
       return array();
    }
    /**
     * Lay chi chi tiet tin bai
     * @param int $category_id
     * @param int $article_id
     * @return array
     */
    public function qry_single_article($category_id, $article_id)
    {
        $category_id = replace_bad_char($category_id);
        $article_id  = replace_bad_char($article_id);
        
        
        $stmt    = "SELECT
                        DATE_FORMAT(C_BEGIN_DATE,'%d-%m-%Y %H:%i:%s') AS C_BEGIN_DATE,
                        C_TITLE,
                        C_SLUG                AS C_SLUG_ARTICLE,
                        C_SUB_TITLE,
                        C_PEN_NAME,
                        C_KEYWORDS,
                        C_TAGS,
                        C_CACHED_RATING,
                        C_CACHED_RATING_COUNT,
                        C_SUMMARY,
                        C_TAGS,
                        C_FILE_NAME,
                        C_CONTENT,
                        (SELECT
                           C_SLUG
                         FROM t_ps_category
                         WHERE PK_CATEGORY = ?) AS C_SLUG_CAT,
                        (SELECT
                           C_NAME
                         FROM t_ps_category
                         WHERE PK_CATEGORY = ?) AS C_CATEGORY_NAME,
                         (SELECT GROUP_CONCAT(C_FILE_NAME)
                            FROM t_ps_article_attachment
                            WHERE FK_ARTICLE = $article_id) as C_FILE_NAME
                      FROM t_ps_article a
                        INNER JOIN t_ps_category_article ca
                          ON a.PK_ARTICLE = ca.FK_ARTICLE
                      WHERE PK_ARTICLE = ?
                          AND FK_CATEGORY = ?
                          AND FK_CATEGORY IN(SELECT
                                               PK_CATEGORY
                                             FROM t_ps_category)
                          AND C_STATUS = 1
                          AND (SELECT
                                 C_STATUS
                               FROM t_ps_category
                               WHERE PK_CATEGORY = ?) = 1
                          AND C_BEGIN_DATE <= NOW()
                          AND C_END_DATE >= NOW()
                          
                            ";
        $article = $this->db->getRow($stmt, array($category_id, $category_id, $article_id, $category_id, $category_id));
        return $article;
    }
    
    /**
    * Lấy Thông tin  chi tiết thủ tục theo mã thủ tục
    */
   public function qry_single_record_type($v_id)
   {
       $sql = "select
                   C_SPEC_CODE
                 from t_r3_record_type rt
                 where PK_RECORD_TYPE = $v_id
               ";
       $v_code = $this->db->GetOne($sql);

       $stmt = "select
                   (select
                             PK_LIST
                       from t_cores_list
                       where C_CODE =?)as PK_LIST,
                   (select
                          C_NAME
                      from t_cores_list
                      where C_CODE =?) AS C_NAME_THU_TUC,
                   C_NAME,
                   C_XML_DATA,
                   PK_RECORD_TYPE
                   ,C_SEND_OVER_INTERNET
               from t_r3_record_type rt
               where
                   PK_RECORD_TYPE = ?
                   and C_STATUS = 1
                   ";
      return  $this->db->GetRow($stmt,array($v_code,$v_code,$v_id));
   }
     /**
     * lay nam tiep nhan ho so 
     * @return type
     */
    public function get_year()
    {
        $sql = "Select
                    MAX(Y.C_YEAR) As C_MAX_YEAR,
                    MIN(Y.C_YEAR) As C_MIN_YEAR
                  From (select
                          YEAR(C_RECEIVE_DATE) as C_YEAR
                        from view_record) Y";
        return $this->db->getRow($sql);
    }
    
    public function qry_record_progress($v_year = '')
    {
        if($v_year == '')
        {
            $v_year = DATE('Y');
        }
        $DATA_MODEL = array();
        
        //lay tong so record
        $sql = "select COUNT(*) from view_record Where Year(C_RECEIVE_DATE) = '$v_year'";
        $DATA_MODEL['C_TOTAL_RECORD'] = $this->db->getOne($sql);
        
        //dang xu ly - cham tien do
        $sql = "Select
                    COUNT(*)
                  From view_processing_record
                  where ((C_IS_PAUSING <> 1
                          and DATEDIFF(NOW(),C_RETURN_DATE) > 0
                          and ISNULL(C_BIZ_DAYS_EXCEED))
                          OR C_BIZ_DAYS_EXCEED < 0)
                      And Year(C_RECEIVE_DATE) = '$v_year'";
        $DATA_MODEL['C_COUNT_CHUA_TRA_CHAM_TIEN_DO'] = $this->db->getOne($sql);
        
        //dang xu ly - chua den han
        $sql = "Select
                    COUNT(*)
                  From view_processing_record
                  Where (C_BIZ_DAYS_EXCEED >= 0
                          OR C_IS_PAUSING = 1
                          OR (DATEDIFF(NOW(),C_RETURN_DATE) <= 0
                              And ISNULL(C_BIZ_DAYS_EXCEED)))
                      And Year(C_RECEIVE_DATE) = '$v_year'";
        $DATA_MODEL['C_COUNT_CHUA_TRA_CHUA_DEN_HAN'] = $this->db->getOne($sql);
        
        //da tra  - cham han
        $sql = "select
                    COUNT(*)
                  from view_record
                  Where C_CLEAR_DATE is not null
                      and Year(C_RECEIVE_DATE) = '$v_year'
                      And ((DATEDIFF(C_CLEAR_DATE,C_RETURN_DATE) > 0 AND C_BIZ_DAYS_EXCEED IS NULL) 
                             OR C_BIZ_DAYS_EXCEED < 0)";
        $DATA_MODEL['C_COUNT_DA_TRA_CHAM_HAN'] = $this->db->getOne($sql);
        //da tra  - dung han
        $sql = "select
                    COUNT(*)
                  from view_record
                  Where C_CLEAR_DATE is not null
                      and Year(C_RECEIVE_DATE) = '$v_year'
                      And ((DATEDIFF(C_CLEAR_DATE,C_RETURN_DATE) = 0 AND C_BIZ_DAYS_EXCEED IS NULL)
                            OR C_BIZ_DAYS_EXCEED = 0)";
        $DATA_MODEL['C_COUNT_DA_TRA_DUNG_HAN'] = $this->db->getOne($sql);
        //da tra  - som han
        $sql = "select
                    COUNT(*)
                  from view_record
                  Where C_CLEAR_DATE is not null
                      and Year(C_RECEIVE_DATE) = '$v_year'
                      And ((DATEDIFF(C_CLEAR_DATE,C_RETURN_DATE) < 0 AND C_BIZ_DAYS_EXCEED IS NULL)
                               OR C_BIZ_DAYS_EXCEED > 0)"; 
        $DATA_MODEL['C_COUNT_DA_TRA_SOM_HAN'] = $this->db->getOne($sql);
        
        return $DATA_MODEL;
    }
    
    /**
     * lay tat ca ho so tiep nhan va da tra ket qua theo nam
     * @param type $v_year
     * @return array
     */
    public function qry_record_receive_respond($v_year = '')
    {
        if($v_year == '')
        {
            $v_year = DATE('Y');
        }
        $DATA_MODEL = array();
        //ho so tiep nhan
        $sql = "Select
                    C_MONTH,
                    COUNT(PK_RECORD) as C_COUNT_TIEP_NHAN
                  From (Select
                          PK_RECORD,
                          MONTH(C_RECEIVE_DATE) as C_MONTH
                        from view_record
                        where YEAR(C_RECEIVE_DATE) = '$v_year') TEMP
                  GROUP by TEMP.C_MONTH";
        $DATA_MODEL['TIEP_NHAN'] = $this->db->GetAssoc($sql);
        
        //ho so da tra ket qua
        $sql = "Select
                    C_MONTH,
                    COUNT(PK_RECORD) as C_COUNT_DA_TRA
                  From (Select
                          PK_RECORD,
                          MONTH(C_RECEIVE_DATE) as C_MONTH
                        from view_record
                        where YEAR(C_RECEIVE_DATE) = '$v_year'
                            And C_CLEAR_DATE is not null) TEMP
                  GROUP by TEMP.C_MONTH";
        $DATA_MODEL['DA_TRA'] = $this->db->GetAssoc($sql);
        
        return $DATA_MODEL;
    }
    
    /**
     *  Lấy danh sách tất cả lĩnh vực
     * @return array
     */
    public function qry_all_record_listtype()
    {
        $stmt = "SELECT  PK_LIST,     
                         C_NAME
                        
                FROM t_cores_list
                WHERE FK_LISTTYPE = (SELECT
                                       PK_LISTTYPE
                                     FROM t_cores_listtype
                                     WHERE C_CODE = 'DANH_MUC_LINH_VUC'
                                        AND C_STATUS =1
                                     )
                     AND C_STATUS =1
                     ORDER BY C_ORDER ASC ";
        return $this->db->GetAssoc($stmt);
    }
    
    /**
     * Lay ho so theo dieu dien loc
     */
    
    public function qry_record_filter($p_record_type_code)
    {
        //Xem theo trang
        page_calc($v_start, $v_end);

        $v_record_no   = isset($_POST['txt_record_no']) ? replace_bad_char($_POST['txt_record_no']) : '';

       if ($this->is_mysql())
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            //Cac dieu kien loc
            $v_and_condition = '';

            if ($p_record_type_code != '' && $p_record_type_code != NULL)
            {
                $v_record_type_id = $this->db->GetOne("SELECT PK_RECORD_TYPE FROM t_r3_record_type WHERE C_CODE='$p_record_type_code'");

                $v_and_condition .= " And FK_RECORD_TYPE=$v_record_type_id";
            }
            if ($v_record_no != '' && $v_record_no != NULL)
            {
                $v_and_condition .= " And C_RECORD_NO = '$v_record_no'";
            }
            else   return array();
            
            $v_total_record = $this->db->getOne("Select Count(PK_RECORD) From view_record where (1=1) $v_and_condition");

            $sql = "SELECT
                        @rownum:=@rownum + 1 AS RN
                        , 1 AS C_OWNER
                        ,Case When (R.C_REJECTED <> 0) Then 3 When ((R.C_REJECTED = 0 OR R.C_REJECTED IS NULL) And (R.C_CLEAR_DATE Is Not Null)) Then 2 Else 1 End as C_ACTIVITY
                        , $v_total_record AS TOTAL_RECORD
                        , CASE WHEN (DATEDIFF(NOW(), R.C_DOING_STEP_DEADLINE_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)<0 ) END AS C_DOING_STEP_DAYS_REMAIN
                        , CASE WHEN (DATEDIFF(NOW(),R.C_RETURN_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)<0 ) END AS C_RETURN_DAYS_REMAIN
                        , R.PK_RECORD
                        , R.FK_RECORD_TYPE
                        , R.C_RECORD_NO
                        , CAST(R.C_RECEIVE_DATE AS CHAR(19)) AS C_RECEIVE_DATE
                        , CAST(R.C_RETURN_DATE AS CHAR(19)) AS C_RETURN_DATE
                        , R.C_RETURN_PHONE_NUMBER
                        , R.C_XML_DATA
                        , R.C_XML_PROCESSING
                        , R.C_DELETED
                        , R.C_CLEAR_DATE
                        , R.C_XML_WORKFLOW
                        , R.C_RETURN_EMAIL
                        , R.C_REJECTED
                        , R.C_REJECT_REASON
                        , R.C_CITIZEN_NAME
                        , R.C_ADVANCE_COST
                        , R.C_CREATE_BY
                        , R.C_NEXT_TASK_CODE
                        , R.C_NEXT_USER_CODE
                        , R.C_NEXT_CO_USER_CODE
                        , R.C_LAST_TASK_CODE
                        , R.C_LAST_USER_CODE
                        , CAST(R.C_DOING_STEP_BEGIN_DATE AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
                        , R.C_DOING_STEP_DEADLINE_DATE
                        , R.C_BIZ_DAYS_EXCEED
                        , a.C_TASK_CODE
                        , a.C_STEP_TIME
                        ,(Select C_CONTENT  FROM t_r3_record_comment
                                Where FK_RECORD=R.PK_RECORD
                                Order By C_CREATE_DATE DESC
                                Limit 1
                        ) C_LAST_RECORD_COMMENT
                        ,R.C_PAUSE_DATE
                        ,R.C_UNPAUSE_DATE
                    FROM
                    (
                        SELECT
                          RID.`PK_RECORD`
                        , UT.C_TASK_CODE
                        , UT.C_STEP_TIME
                        , (SELECT @rownum:=0)
                        FROM
                        (
                            SELECT
                              PK_RECORD
                            , C_NEXT_TASK_CODE
                            , C_NEXT_USER_CODE
                            FROM  view_record 
                            where (1=1)
                            $v_and_condition
                            ORDER BY C_RECEIVE_DATE DESC
                            LIMIT $v_start, $v_limit
                        ) RID LEFT JOIN t_r3_user_task UT ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
                    ) a LEFT JOIN t_r3_record R ON a.PK_RECORD=R.PK_RECORD
                    ";
            return $this->db->getAll($sql);
        }
        
     
    }
    /**
     * Lay danh sach thu tuc(dung cho page search)
     */
    public function qry_all_record_type($v_list_id = 0)
    {
        $v_list_id      = isset($v_list_id) ? replace_bad_char($v_list_id) : 0;        
        $v_condition = '';
        if($v_list_id > 0)
        {
            $v_condition .=" AND C_SPEC_CODE = (SELECT
                                                   C_CODE
                                                 FROM t_cores_list
                                                 WHERE FK_LISTTYPE = (SELECT
                                                                        PK_LISTTYPE
                                                                      FROM t_cores_listtype
                                                                      WHERE C_CODE = 'DANH_MUC_LINH_VUC'
                                                                          AND C_STATUS = 1)
                                                    AND PK_LIST = '$v_list_id'
                                                    AND C_STATUS = 1
                                                 )";
        }
        
            $sql = " SELECT RT.C_CODE,
                            CONCAT(RT.C_CODE,' - ',RT.C_NAME) as C_NAME
                            FROM t_r3_record_type RT
                            WHERE (1=1)
                                    $v_condition
                                    AND RT.C_STATUS = 1
                                    ORDER BY RT.C_ORDER ASC
                                    ";
        
            $results =  $this->db->GetAssoc($sql);         
            if($this->db->ErrorNo() == 0)
            {
                return $results;
            }
        return array();
    }
    
    
    /**
     * Lay danh sach thu tuc "Dung cho huong dan thu tuc"
     */
    public function qry_all_record_type_guidance()
    {
        //Id linh vuc
        $v_list_id           = get_post_var('sel_record_list',0);       
        //code thu tuc
        $v_record_type_code  = get_post_var('txt_record_type_code','');        
        //Cap do cua thu tuc
        $v_record_leve       = get_post_var('sel_record_leve',0);
        
        page_calc($v_start, $v_end);
        $v_start = $v_start - 1; //offset
        $v_limit = $v_end - $v_start;
       
        $conditions = '';
        $v_list_name = '';
        if($v_list_id > 0)
        {
            $conditions .=  " and rt.C_SPEC_CODE = (SELECT
                                                C_CODE
                                              FROM t_cores_list
                                              WHERE PK_LIST = '$v_list_id'
                                                  AND C_STATUS = 1) ";
            
            $sql = "SELECT
                                        C_NAME
                                FROM t_cores_list
                                WHERE PK_LIST = ?";
            $v_list_name = $this->db->GetOne($sql,$v_list_id);
        }
        if(trim($v_record_type_code) != '' )
        {
            $conditions .= " and rt.C_CODE = '$v_record_type_code' ";
        }

    //   check la ho so nop truc tuyen chưa làm  
        if((int)$v_record_leve > 1)
        {
           $conditions .= "and rt.C_SEND_OVER_INTERNET = '1'";
        }
        else if((int)$v_record_leve == 1 )
        {
            $conditions .= "and rt.C_SEND_OVER_INTERNET = '0'";
        }
    
        $stmt = "SELECT
                        '$v_list_name' as C_NAME_LINH_VUC,
                        PK_RECORD_TYPE,
                        C_CODE,
                        C_NAME,
                        CASE 
                            -- WHEN C_SCOPE  = 3 THEN 'Thủ tục cấp huyện'
                            WHEN C_SCOPE  = 3 THEN 'Thủ tục cấp Sở'
                            WHEN C_SCOPE  = 4 THEN 'Thủ tục liên thông'
                            WHEN C_SCOPE   = 1 THEN 'Thủ tục liên thông xã huyện'
                            WHEN C_SCOPE   = 2 THEN 'Thủ tục liên thông huyện xã'
                            ELSE 'Thủ tục cấp xã'
                            END AS C_SCOPE,
                            C_SEND_OVER_INTERNET,
                        (SELECT
                           COUNT(PK_RECORD_TYPE)
                            FROM t_r3_record_type rt   
                            WHERE C_STATUS = 1 
                            $conditions) AS TOTAL_RECORD,
                                C_XML_DATA
                      FROM t_r3_record_type rt 
                      WHERE  C_STATUS = 1 
                      $conditions
                      ORDER BY C_ORDER ASC 
                      limit $v_start,$v_limit";
    
        $results =  $this->db->GetAll($stmt);
        if($this->db->ErrorNo() == 0)
        {
            for($i = 0; $i< count($results);$i ++)
            {
                $xml_data          = isset($results[$i]['C_XML_DATA']) ? $results[$i]['C_XML_DATA'] : '';
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
                            $arr_all_file[$item]['name']      = isset($arr_string[1]) ? $arr_string[1] : '';
                            $key_file   = isset($arr_string[0]) ? $arr_string[0] : '';
                            $arr_all_file[$item]['file_name'] =  $item;
                            $arr_all_file[$item]['path']      =  $v_path_file;
                            $arr_all_file[$item]['type']      = filetype($v_path_file);
                        }
                    }            
                }
                $results[$i]['arr_all_file'] = $arr_all_file;
            }
            return $results;
        }
        return array();
    }   
    public function formal_record_step_days_to_date($record_id, $step_days_list)
    {
        $ret           = Array();
        $arr_step_time = explode(';', $step_days_list);

        $v_prev_end_date = NULL;
        $v_init_date     = $this->db->getOne("Select C_RECEIVE_DATE From view_record R Where PK_RECORD=$record_id");
        for ($i = 0; $i < sizeof($arr_step_time); $i++)
        {
            $v_step_time  = $arr_step_time[$i];
            $v_begin_date = ($i == 0) ? $v_init_date : $v_prev_end_date;
            $v_end_date   = $this->_step_deadline_calc($v_step_time, $v_begin_date);

            $ret[$i]['C_STEP_TIME']  = $v_step_time;
            $ret[$i]['C_BEGIN_DATE'] = $v_begin_date;
            $ret[$i]['C_END_DATE']   = $v_end_date;

            $v_prev_end_date = $v_end_date;
        }
        return $ret;
    }
    
    /**
     * Tinh ngay ket thuc cua step
     * @param Int $days So ngay cua step
     * @param datetime $begin_datetime Bat dau tinh tu ngay
     */
    private function _step_deadline_calc($days, $begin_datetime = NULL)
    {
        if ($begin_datetime == NULL)
        {
            $begin_datetime = $this->get_datetime_now();
        }
        $v_begin_hour = Date('H', strtotime($begin_datetime));

        //Tinh ket qua theo so ngay cua thu tuc
        if ($days == 0) //Thu tuc 0 ngay
        {
            if ($v_begin_hour < 12) //Nhận sáng trả sáng
            {
                $v_end_date = substr($begin_datetime, 0, 10) . chr(32) . _CONST_MORNING_END_WORKING_TIME;
            }
            else //Nhận chiều trả chiều
            {
                $v_end_date = substr($begin_datetime, 0, 10) . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
            }
        }
        elseif ($days == 0.5) //Thủ tục 0.5 ngày
        {
            if ($v_begin_hour < 12) //Nhận sáng trả chiều
            {
                $v_end_date = substr($begin_datetime, 0, 10) . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
            }
            else //Nhận chiều trả sáng ngày làm việc tiếp theo
            {
                $v_end_date = $this->next_working_day(1, $begin_datetime) . chr(32) . _CONST_MORNING_END_WORKING_TIME;
            }
        }
        else //Cộng số ngày làm việc, Nhận sáng trả sáng, nhận chiều trả chiều
        {
            $v_end_date = $this->next_working_day($days, $begin_datetime);
            if ($v_begin_hour < 12) //Nhận sáng trả sáng
            {
                $v_end_date .= chr(32) . _CONST_MORNING_END_WORKING_TIME;
            }
            else //Nhận chiều trả chiều
            {
                $v_end_date .= chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
            }
        }

        return $v_end_date;
    }
    
    /**
     * Lấy danh sách file đính kèm của 1 hồ sơ
     * @param int $item_id: ID cua HS
     */
    public function qry_all_record_file($item_id)
    {
        if (intval($item_id) < 1)
        {
            return NULL;
        }
        $stmt = "SELECT
                    RF.PK_RECORD_FILE,
                    RF.C_FILE_NAME,
                    RF.FK_MEDIA,
                    DATE_FORMAT(M.C_UPLOAD_DATE,'%Y') AS C_YEAR,
                    DATE_FORMAT(M.C_UPLOAD_DATE,'%m') AS C_MONTH,
                    DATE_FORMAT(M.C_UPLOAD_DATE,'%d') AS C_DAY,
                    M.C_FILE_NAME AS C_MEDIA_FILE_NAME,
                    M.C_NAME,
                    M.C_EXT
                  FROM t_r3_record_file RF
                    LEFT JOIN t_r3_media M
                      ON RF.FK_MEDIA = M.PK_MEDIA
                  WHERE RF.FK_RECORD = ?";
        return $this->db->getAll($stmt, array($item_id));
    }

    /**
     * Lay danh sach tai lieu cua mot ho so
     * @param type $record_id
     */
    public function qry_all_record_doc($record_id)
    {

        $stmt   = 'SELECT RD.PK_RECORD_DOC
                    ,RD.FK_RECORD
                    ,RD.C_USER_CODE
                    ,RD.C_DOC_NO
                    ,RD.C_ISSUER
                    ,RD.C_DESCRIPTION
                    ,RD.C_DOC_CONTENT
                    ,' . $this->build_convert_date_query('RD.C_CREATE_DATE', 103) . ' as C_CREATE_DATE
                    ,U.C_NAME C_USER_NAME
                    ,' . $this->build_for_xml_raw_query('t_r3_record_doc_file', array('C_FILE_NAME'), ' Where FK_DOC=RD.PK_RECORD_DOC') . ' as C_RECORD_DOC_FILE_LIST
                FROM t_r3_record_doc RD Left Join t_cores_user U On RD.C_USER_CODE=U.C_LOGIN_NAME
                Where RD.FK_RECORD=?';
        $params = array($record_id);
        return $this->db->getAll($stmt, $params);
    }
    
    /**
     * Lay thong tin chi tiet cua mot Ho so
     */
    public function qry_single_record($p_record_id = '', $v_record_type_code = '', $v_xml_workflow_file_name = '')
    {
        $v_record_id = $p_record_id ? (int) $p_record_id : (int) get_post_var('hdn_item_id');

        if ($v_record_id > 0)
        {
            if (DATABASE_TYPE == 'MSSQL')
            {
                $stmt = 'SELECT
                			R.[PK_RECORD]
    				        ,r.[FK_RECORD_TYPE]
    				        ,r.[C_RECORD_NO]
    				        , Convert(varchar(19), r.[C_RECEIVE_DATE], 120) as [C_RECEIVE_DATE]
    				        , Convert(varchar(19), r.[C_RETURN_DATE], 120) as [C_RETURN_DATE]
    				        ,r.[C_RETURN_PHONE_NUMBER]
    				        ,r.[C_XML_DATA]
    				        ,r.[C_XML_PROCESSING]
    				        ,r.[C_DELETED]
                			, Convert(varchar(19), r.[C_CLEAR_DATE], 120) as [C_CLEAR_DATE]
    				        ,r.[C_XML_WORKFLOW]
    				        ,r.[C_RETURN_EMAIL]
    				        ,r.[C_REJECTED]
    				        ,r.[C_REJECT_REASON]
    				        ,r.[C_CITIZEN_NAME]
    				        ,r.[C_ADVANCE_COST]
    				        ,r.[C_CREATE_BY]
    				        ,R.[C_NEXT_TASK_CODE]
    				        ,r.[C_NEXT_USER_CODE]
    				        ,r.[C_NEXT_CO_USER_CODE]
    				        ,r.[C_LAST_TASK_CODE]
    				        ,r.[C_LAST_USER_CODE]
    				        ,Convert(varchar(19), r.[C_DOING_STEP_BEGIN_DATE], 120) as [C_DOING_STEP_BEGIN_DATE]
    				        ,r.[C_DOING_STEP_DEADLINE_DATE]
    				        ,r.[C_BIZ_DAYS_EXCEED]
    	                    ,RT.C_NAME as C_RECORD_TYPE_NAME
    	                    ,RT.C_CODE as C_RECORD_TYPE_CODE
                       	From [dbo].[view_record] R Left Join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                        Where R.PK_RECORD=?';
            }
            elseif (DATABASE_TYPE == 'MYSQL')
            {

                $stmt = 'Select
                              R.PK_RECORD
                            , R.FK_RECORD_TYPE
                            , R.C_RECORD_NO
                            , Cast(R.C_RECEIVE_DATE  AS CHAR(19)) AS C_RECEIVE_DATE
                            , Cast(R.C_RETURN_DATE  AS CHAR(19)) AS C_RETURN_DATE
                            , R.C_RETURN_PHONE_NUMBER
                            , R.C_XML_DATA
                            , R.C_XML_PROCESSING
                            , R.C_DELETED
                            , Cast(R.C_CLEAR_DATE  AS CHAR(19)) AS C_CLEAR_DATE
                            , R.C_XML_WORKFLOW
                            , R.C_RETURN_EMAIL
                            , R.C_REJECTED
                            , R.C_REJECT_REASON
                            , R.C_CITIZEN_NAME
                            , R.C_ADVANCE_COST
                            , R.C_CREATE_BY
                            , R.C_NEXT_TASK_CODE
                            , R.C_NEXT_USER_CODE
                            , R.C_NEXT_CO_USER_CODE
                            , R.C_LAST_TASK_CODE
                            , R.C_LAST_USER_CODE
                            , Cast(R.C_DOING_STEP_BEGIN_DATE  AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
                            , R.C_DOING_STEP_DEADLINE_DATE
                            , R.C_BIZ_DAYS_EXCEED
                            , RT.C_NAME as C_RECORD_TYPE_NAME
                            , RT.C_CODE as C_RECORD_TYPE_CODE
                            , R.XML_RECORD_RESULT
                        From t_r3_record R Left join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                        Where R.PK_RECORD=?';
            }

            $params    = array($v_record_id);
            $ret_array = $this->db->getRow($stmt, $params);

            $ret_array['C_TOTAL_TIME'] = $this->days_between_two_date($ret_array['C_RECEIVE_DATE'], $ret_array['C_RETURN_DATE']);

            return $ret_array;
        }
        else //Them moi
        {
            //Tinh toan ngay tra ket qua
            if (file_exists($v_xml_workflow_file_name))
            {
                $dom          = simplexml_load_file($v_xml_workflow_file_name);
                $r            = xpath($dom, "/process/@totaltime[1]", XPATH_STRING);
                $v_total_time = str_replace(',', '.', $r); //Dau thap phan la dau cham "."

                $v_return_date = $this->_step_deadline_calc($v_total_time);

                $v_hour_now = $this->get_hour_now();
                //Thu tuc 0 ngay
                if ($v_total_time < 0)
                {
                    $v_return_date = '';
                }
                elseif ($v_total_time == '0')
                {
                    //Nhan sang tra sang
                    if ($v_hour_now <= intval(_CONST_MORNING_END_WORKING_TIME))
                    {
                        $v_return_date = $this->get_date_yyyymmdd_now() . chr(32) . _CONST_MORNING_END_WORKING_TIME;
                    }
                    else //Nhan chieu tra chieu
                    {
                        $v_return_date = $this->get_date_yyyymmdd_now() . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
                    }
                }
                elseif ($v_total_time == '0.5') //Thu tuc 0.5 ngay
                {
                    //Nhan sang tra chieu
                    if ($v_hour_now <= intval(_CONST_MORNING_END_WORKING_TIME))
                    {
                        $v_return_date = $v_return_date = $this->get_date_yyyymmdd_now() . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
                    }
                    else //Nhan chieu tra sang hom sau
                    {
                        $v_return_date = $this->date_which_diff_day_yyyymmdd(1) . chr(32) . _CONST_MORNING_END_WORKING_TIME;
                    }
                }
                //Thủ tục 1 ngày: QUA ĐÊM
                elseif ($v_total_time == 1)
                {
                    //Nhận sáng: trả sáng ngày làm việc tiếp theo
                    if ($v_hour_now <= intval(_CONST_MORNING_END_WORKING_TIME))
                    {
                        $v_return_date = $this->date_which_diff_day_yyyymmdd(1) . chr(32) . _CONST_MORNING_END_WORKING_TIME;
                    }
                    else //Nhận chiều: Trả buổi chiều, ngày làm việc tiếp theo
                    {
                        $v_return_date = $this->date_which_diff_day_yyyymmdd(1) . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
                    }
                }
                //2 ngày trở lên: Cộng số ngày làm việc, Nhận sáng trả sáng, nhận chiều trả chiều
                else
                {
                    $noon          = ($v_hour_now <= intval(_CONST_MORNING_END_WORKING_TIME)) ? _CONST_MORNING_END_WORKING_TIME : _CONST_AFTERNOON_END_WORKING_TIME;
                    $v_total_time  = intval($v_total_time);
                    $v_return_date = $this->date_which_diff_day_yyyymmdd($v_total_time) . chr(32) . $noon;
                }

                $ret_array = array(
                    'C_RETURN_DATE' => $v_return_date,
                    'C_TOTAL_TIME'  => $v_total_time
                );
            }
            else
            {
                $ret_array = NULL;
            }

            return $ret_array;
        }
    }

    /**
     * Lay danh sach thu tuc cho phep nop ho so truc tuyen
     * @return array Mang chua danh sach thu tuc
     */
    public function qry_all_record_type_send_internet()
    {
        
        $stmt = "SELECT   L.PK_LIST
                            ,L.C_CODE
                            ,L.C_NAME
                            ,CONCAT('<root>',(SELECT GROUP_CONCAT('<item'
                                                            , CONCAT(' PK_RECORD_TYPE=\"',PK_RECORD_TYPE,'\" ')
                                                            , CONCAT(' C_CODE=\"',C_CODE,'\" ')
                                                            , CONCAT(' C_SCOPE=\"',CASE
                                                                                       -- WHEN C_SCOPE = 3 THEN 'Thủ tục cấp Huyện'
                                                                                       WHEN C_SCOPE = 3 THEN 'Thủ tục cấp Sở'
                                                                                       WHEN C_SCOPE = 4 THEN 'Thủ tục liên thông'
                                                                                       WHEN C_SCOPE = 1 THEN 'Thủ tục liên thông xã huyện'
                                                                                       WHEN C_SCOPE = 2 THEN 'Thủ tục liên thông huyện xã'
                                                                                       ELSE 'Thủ tục cấp xã'
                                                                                   END ,'\" >')
                                                            , CONCAT('<![CDATA[',C_NAME,']]>')
                                             ,' </item> '	
                                            SEPARATOR ' ')
                                    FROM t_r3_record_type
                                    WHERE C_SPEC_CODE = L.C_CODE
                                            AND C_SEND_OVER_INTERNET >1
                                    ORDER BY C_ORDER ASC),'</root>') as C_XML_DATA
                            ,(SELECT COUNT(PK_RECORD_TYPE)
                                    FROM t_r3_record_type
                                    WHERE C_SPEC_CODE = L.C_CODE
                                    AND C_SEND_OVER_INTERNET >1) AS C_TOTAL_RECORD
                            FROM t_cores_list L
                            WHERE FK_LISTTYPE = (SELECT PK_LISTTYPE 
                                                            FROM t_cores_listtype 
                                                            WHERE C_CODE = '"._CONST_DANH_MUC_LINH_VUC."'
                                                                    AND C_STATUS  =1)

                                    AND L.C_STATUS = 1
                            ORDER BY L.C_ORDER ASC";
        $results =  $this->db->GetAll($stmt);
        if($this->db->ErrorNo() == 0)
        {
            return $results;
        }
        return array();
    }
    public function qry_all_web_link()
    {
        $sql = "SELECT
                    C_NAME,
                    C_URL,
                    C_FILE_NAME
                  FROM t_ps_weblink
                  WHERE C_STATUS = 1
                  ORDER BY C_ORDER";

        $results =  $this->db->GetAll($sql);
        if($this->db->ErrorNo() == 0)
        {
            return $results;
        }
        return array();
    }
    
    /*
     * Lay danh sach tin noi bat cua trang chu sticky
     * 
     * @return array
     */
    public function gp_qry_all_sticky()
    {
        
        $stmt = "Select
                    sa.FK_CATEGORY
                    , (Select C_SLUG From t_ps_category Where PK_CATEGORY = sa.FK_CATEGORY) as C_SLUG_CATEGORY
                    , A.PK_ARTICLE   as FK_ARTICLE
                    , A.C_TITLE
                    , A.C_SUMMARY
                    , A.C_SLUG       as C_SLUG_ARTICLE
                    , sa.C_ORDER
                    , A. C_FILE_NAME
                    , DATE_FORMAT(A.C_BEGIN_DATE,'%Y-%m-%d %H:%i:%s') as C_BEGIN_DATE
                    , A.C_HAS_VIDEO
                    , A.C_HAS_PHOTO
                    , A.C_CONTENT
                From t_ps_article A
                  Right Join (Select
                                FK_ARTICLE
                                , FK_CATEGORY
                                , C_ORDER
                              From t_ps_sticky S
                              Where C_DEFAULT = 1
                                  And (Select C_STATUS From t_ps_category Where PK_CATEGORY = FK_CATEGORY) = 1
                                  ) sa
                    On A.PK_ARTICLE = sa.FK_ARTICLE
                Where A.C_STATUS = 1
                     AND DATEDIFF(NOW(),C_BEGIN_DATE) >=0
                     AND DATEDIFF(C_END_DATE, NOW()) >= 0
                Order by sa.C_ORDER";
        return $this->db->getAll($stmt);
        
    }
    
    //Lay thong tin cau hoi
    public function qry_single_poll()
    {
            $sql = "Select   PK_POLL
                            ,C_NAME
                            ,DATEDIFF(NOW(),C_BEGIN_DATE) as CK_BEGIN_DATE
                            ,DATEDIFF(C_END_DATE, NOW()) as CK_END_DATE
                    From t_ps_poll
                    Where C_STATUS = 1 ";
        return $this->db->getRow($sql);
    }
    function qry_all_poll_detail($poll_id)
    {
        $sql = " Select PK_POLL_DETAIL, C_ANSWER, C_VOTE From t_ps_poll_detail
            Where FK_POLL = $poll_id";
        return $this->db->getAll($sql);
    }

    
    function handle_widget_poll($poll_id, $choice)
    {
            $sql = "
                    Update t_ps_poll_detail
                    Set C_VOTE = C_VOTE + 1
                    Where FK_POLL = $poll_id
                    And PK_POLL_DETAIL = $choice
                     ";
            $this->db->Execute($sql);
        
        if ($this->db->errorNo() == 0)
        {
            Cookie::set('WIDGET_POLL_' . $poll_id, 1, time() + 3600 * 24 * 30 * 12 * 10);
        }
    }
}
  
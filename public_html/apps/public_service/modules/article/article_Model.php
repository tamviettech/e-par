<?php

defined('DS') or die('no direct access');

class article_Model extends Model
{
    function __construct()
    {
        parent::__construct();
    }

    function qry_all_article($a_other_clause = '', $c_other_clause = '', $ca_other_clause = '')
    {
        $v_website_id = Session::get('session_website_id');
        $v_website_id = ((int)($v_website_id) >0) ? $v_website_id : 0;
        
        $condition_website_id = "C_DEFAULT_WEBSITE = $v_website_id " ;
         
        //get data
        $v_begin_date   = get_post_var('txt_begin_date');
        $v_end_date     = get_post_var('txt_end_date');
        $v_status       = intval(get_post_var('sel_status', -1));
        
        $v_category_id  = intval(get_request_var('hdn_category'));
        $v_init_user_id = intval(get_post_var('sel_init_user'));
        $v_keywords     = auto_slug(get_request_var('txt_keyword'));
        
//        $txt_begin_date     = str_replace('-', ' and ', $v_keywords);

        $v_title = trim(get_post_var('txt_title'));
        $user_id = Session::get('user_id');
        
        page_calc($v_start, $v_end);
        $v_start = $v_start -1;
        $v_limit = $v_end - $v_start;
        
        //Xay dung dieu kien loc
        $v_filter_condition = '';
        //Loc theo ngay bat dau
        if ($v_begin_date != '')
        {
            //prepare datetime
            $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date, 0);
            $v_filter_condition .= " And (C_BEGIN_DATE >= '$v_begin_date')";
        }
        
        //Loc theo ngay ket thuc
        if ($v_end_date != '')
        {
            $v_end_date = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date, 0);
            $v_filter_condition .= " And (C_END_DATE <= '$v_end_date')";
        }
        
        //Loc theo trang thai
        if ($v_status >= 0 && $v_status <= 3)
        {
            $a_other_clause .= " And C_STATUS = $v_status";
            $v_filter_condition .= " And (C_STATUS = $v_status)";
        }
        
        //Loc theo nguoi nhap tin bai
        if ($v_init_user_id > 0)
        {
            $a_other_clause .= " And FK_INIT_USER = $v_init_user_id";
            $v_filter_condition .= " And FK_INIT_USER = $v_init_user_id";
        }
        
        //full text search
        if ($v_keywords)
        {
                $a_other_clause .= " AND ( MATCH(C_TITLE) AGAINST('$v_keywords' IN BOOLEAN MODE)
                                        )";
                
                $v_filter_condition .= " AND ( MATCH(C_TITLE) AGAINST('$v_keywords' IN BOOLEAN MODE)
                                        )";
        }
        
        //tieu de
        if ($v_title != '')
        {
            if(DATABASE_TYPE == "MYSQL")
            {
                $a_other_clause .= " And MATCH(C_TITLE) AGAINST ('$v_title')";
                $v_filter_condition .= " And MATCH(C_TITLE) AGAINST ('$v_title')";
            }
        }

        $sql_index_article    = "
            Select PK_ARTICLE, C_BEGIN_DATE, FK_INIT_USER
            From t_ps_article Where 1=1 $a_other_clause
        ";
        
        $sql_category         = "
                Select PK_CATEGORY, PK_CATEGORY As FK_CATEGORY, C_SLUG, C_STATUS, FK_WEBSITE
                From t_ps_category
                Where 1=1
                $c_other_clause";
        $sql_category_article = "
             Select Max(FK_CATEGORY) As FK_CATEGORY, FK_ARTICLE From t_ps_category_article
             Where 1 = 1
             $ca_other_clause
             Group By FK_ARTICLE
            ";
        
        //check chuyen muc
        if ($v_category_id > 0)
        {
            $c_other_clause .= " And PK_CATEGORY = $v_category_id";
            $ca_other_clause .= " And FK_CATEGORY = $v_category_id";
        }
       if(DATABASE_TYPE == 'MYSQL')
        {
            //Dem tong so tin bai thoa man dieu kien loc
            $v_query_count_all_article = "Select 
                                        Count(*) 
                                    From t_ps_article A USE INDEX(C_DEFAULT_WEBSITE)
                                    Where A.$condition_website_id $v_filter_condition ";
            
            //dem so tin bai neu chon category
            if ($v_category_id > 0)
            {
                //count all article 
                $v_query_count_all_article = "SELECT
                                                COUNT(*)
                                              FROM t_ps_category_article CA USE INDEX(FK_ARTICLE)
                                                LEFT JOIN t_ps_article A 
                                                  ON CA.FK_ARTICLE = A.PK_ARTICLE
                                              WHERE A.$condition_website_id
                                                  AND CA.FK_CATEGORY = $v_category_id $v_filter_condition";
            }
            //lay total record
            $v_count_all_article = $this->db->getOne($v_query_count_all_article);
            
            
            //query limit article 
            if($v_category_id > 0)
            {
                //dem so ban ghi neu so ban ghi nho ko su dung force index
                $v_force_index = '';
                if($v_count_all_article >= 1000)
                {
                    $v_force_index = " FORCE INDEX(C_BEGIN_DATE) ";
                }
                
                //query limit article with category id
                $v_query_limit_article = "(Select 
                                                A.PK_ARTICLE                    
                                              From t_ps_article A 
                                               $v_force_index
                                                Left Join t_ps_category_article CA
                                                On A.PK_ARTICLE=CA.FK_ARTICLE
                                              Where A.$condition_website_id
                                                And CA.FK_CATEGORY=$v_category_id $v_filter_condition 
                                            Order By C_BEGIN_DATE Desc 
                                            Limit ? ,?
                                           ) ";
            }
            else
            {
                //Lay danh sach ID thoa man cac dieu kien loc, phan theo trang
                $v_query_limit_article = "( Select 
                                                PK_ARTICLE
                                            From t_ps_article A FORCE INDEX(C_BEGIN_DATE)
                                            Where $condition_website_id $v_filter_condition 
                                            Order By C_BEGIN_DATE Desc 
                                            Limit ? ,?
                                          ) ";
            }
            
            
            //chk status su dung cho dsp_all_article_svc
            if($v_category_id > 0)
            {
                $sql_chk_cat_status = ",(Select C_STATUS From t_ps_category Where PK_CATEGORY  = $v_category_id) As C_CAT_STATUS ";
                $sql_cat_id         = ",$v_category_id As PK_CATEGORY";
            }
            else
            {
                $sql_chk_cat_status = ",(Select C_STATUS From t_ps_category Where PK_CATEGORY  = FA.C_DEFAULT_CATEGORY) As C_CAT_STATUS ";
                $sql_cat_id         = ",(Select PK_CATEGORY From t_ps_category Where PK_CATEGORY = FA.C_DEFAULT_CATEGORY) As PK_CATEGORY";
            }
            
            $sql = "Select 
                            FA.PK_ARTICLE
                            ,FA.C_TITLE
                            ,FA.C_MESSAGE
                            ,FA.C_SLUG
                            ,FA.C_BEGIN_DATE as C_REAL_DATE
                            ,DATE_FORMAT(FA.C_BEGIN_DATE,'%d-%m-%Y') as C_BEGIN_DATE
                            ,FA.C_STATUS
                            ,DATEDIFF(NOW(),FA.C_BEGIN_DATE) as CK_BEGIN_DATE
                            ,DATEDIFF(FA.C_END_DATE, NOW()) as CK_END_DATE
                            $sql_chk_cat_status
                            $sql_cat_id
                            ,ExtractValue(C_XML_VERSION, '//version[@status=3][last()]/user_name') As C_SENSORER
                            ,(Select C_NAME From t_cores_user U Where U.PK_USER=FA.FK_INIT_USER) As C_INIT_USER_NAME
                            ,$v_count_all_article as TOTAL_RECORD
                        From t_ps_article FA
                            Right Join ($v_query_limit_article) as lim_a
                            On FA.PK_ARTICLE = lim_a.PK_ARTICLE";
        }
        $param = array($v_start,$v_limit) ;
        
        return $this->db->getAll($sql, $param);
        
    }

    function qry_all_user()
    {
        $sql = 'Select PK_USER, C_NAME From t_cores_user';
        return $this->db->getAll($sql);
    }

    function fix_db_content()
    {
        Session::init();
        set_time_limit(0);
        $sql                        = "Select A.C_TITLE, A.C_SUMMARY, A.C_CONTENT, A.PK_ARTICLE
            ,row_number() over(order by pk_article) as rn
            From t_ps_article A";
        $sql                        = " Select TEMP.* From ($sql) TEMP where TEMP.RN Between ? and ? order by temp.rn";
        $_POST['sel_rows_per_page'] = 1000;
        for ($_POST['sel_goto_page'] = 1; $_POST['sel_goto_page'] < 50; $_POST['sel_goto_page']++)
        {
            page_calc($v_start, $v_end);
            $param = array($v_start, $v_end);
            $arr_articles = $this->db->getAll($sql, $param);
            foreach ($arr_articles as $row)
            {
                $v_title   = $this->restore_utf8_char($row['C_TITLE']);
                $v_id      = $row['PK_ARTICLE'];
                Session::set('update_process', (int) Session::get('update_process') + 1);
                $v_summary = $this->restore_utf8_char($row['C_SUMMARY']);
                $v_content = $this->restore_utf8_char($row['C_CONTENT']);

                $sql_update   = "Update t_ps_article Set C_TITLE = ?, C_CONTENT = ?, C_SUMMARY = ? 
                    Where PK_ARTICLE = ?";
                $param_update = array($v_title, $v_content, $v_summary, $v_id);
                $this->db->Execute($sql_update, $param_update);
            }
        }
    }

    /**
     * 
     * @param string $str chuỗi html bị encode htmlentities (hỏng tiếng việt html)
     * @return string $str chuỗi html đã khôi phục
     */
    function restore_utf8_char($str)
    {
        $char_list = "à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ";
        $char_list .= "|è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ";
        $char_list .= "|ì|í|ị|ỉ|ĩ";
        $char_list .= "|ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ";
        $char_list .= "|ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ";
        $char_list .= "|ỳ|ý|ỵ|ỷ|ỹ";
        $char_list .= "|đ|Đ";
        $char_list .= "|\"|“|”";
        $arr_lower = explode('|', mb_strtolower($char_list, 'UTF-8'));
        $arr_upper = explode('|', mb_strtoupper($char_list, 'UTF-8'));
        $search    = array();
        $replace = array();

        foreach ($arr_lower as $val)
        {
            //$search[]  = str_replace('&', '&amp;', htmlentities($val, ENT_QUOTES, "UTF-8"));
            $search[]  = htmlentities($val, ENT_QUOTES, "UTF-8");
            $replace[] = $val;
        }
        foreach ($arr_upper as $val)
        {
            //$search[]  = str_replace('&', '&amp;', htmlentities($val, ENT_QUOTES, "UTF-8"));
            $search[]  = htmlentities($val, ENT_QUOTES, "UTF-8");
            $replace[] = $val;
        }

        $search[]  = "&ETH;";
        $replace[] = "Đ";

        return str_replace($search, $replace, $str);
    }

    function title_exists($id, $title)
    {
        $sql    = "Select count(*) From t_ps_article Where C_TITLE = ? And PK_ARTICLE <> ?";
        $params = array($title, $id);
        return $this->db->getOne($sql, $params) > 0 ? true : false;
    }

    function qry_category_name($id)
    {
        $sql     = 'Select C_NAME From t_ps_category Where PK_CATEGORY = ?';
        $param[] = $id;

        return $this->db->getOne($sql, $param);
    }

    function qry_single_article($id, $a_other_clause = '', $c_other_clause = '')
    {
        $sql_sub_article = "Select PK_ARTICLE From t_ps_article Where PK_ARTICLE = $id $a_other_clause";
        $sql_category    = "Select PK_CATEGORY, C_SLUG, C_NAME From t_ps_category Where 1=1 $c_other_clause";
        $sql             = "
            Select 
                A.PK_ARTICLE
                , A.C_TITLE
                , A.C_MESSAGE
                , A.FK_INIT_USER
                , A.C_HAS_VIDEO
                , A.C_HAS_PHOTO
                , A.C_IS_COPY
                , A.C_IS_IMG_NEWS
                , A.FK_EMPLOYEE
                , DATE_FORMAT(A.C_BEGIN_DATE,'%Y-%m-%d %h:%i:%s') AS C_BEGIN_DATE
                , DATE_FORMAT(A.C_END_DATE,'%Y-%m-%d %h:%i:%s') AS C_END_DATE
                , A.C_STATUS
                , A.C_SUB_TITLE
                , A.C_SUMMARY
                , A.C_CONTENT
                , A.C_SLUG
                , A.C_KEYWORDS
                , A.C_TAGS
                , A.C_VIEWS
                , A.C_XML_VERSION
                , A.C_PEN_NAME
                , U.C_NAME as C_INIT_USER_NAME
                , A.C_FILE_NAME
            From ($sql_sub_article) SUB_A
            Inner Join t_ps_article A
            On SUB_A.PK_ARTICLE = A.PK_ARTICLE
            Inner Join t_cores_user U
            On  A.FK_INIT_USER = U.PK_USER
            Inner Join t_ps_category_article CA
            On CA.FK_ARTICLE = A.PK_ARTICLE
            Inner Join ($sql_category) C
            On C.PK_CATEGORY = CA.FK_CATEGORY
            ";
        return $this->db->getRow($sql);
    }

    function qry_category_article($id,$no_website = '')
    {
        $sql = '
            Select CA.FK_CATEGORY
            From t_ps_category_article CA
            Inner Join t_ps_category C
            On CA.FK_CATEGORY = C.PK_CATEGORY
            Where FK_ARTICLE = ?
        ';
        $param[] = $id;

        return $this->db->getCol($sql,$param);
    }

    function qry_all_category($other_clause = '')
    {
        //hien category trong o tim kiem, neu la phong vien/bien tap vien chi hien category duoc phan cong
        $sql = '
            Select PK_CATEGORY, C_NAME, C_INTERNAL_ORDER, C_STATUS, C_SLUG
            From t_ps_category
            Where 1 = 1
            ';
        $sql .= ' ' . $other_clause;
        $sql .= ' Order by C_INTERNAL_ORDER';
        return $this->db->getAll($sql);
    }

    function update_general_info()
    {
    	if (DEBUG_MODE < 10)
    	{
    		$this->db->debug = 0;
    	}
        //mac dinh
        $arr_action = array('init', 'update');
        //lay du lieu
        $v_id         = intval(get_request_var('hdn_item_id', 0));
        $v_user       = intval(Session::get('user_id'));
        $v_pen_name   = get_request_var('txt_pen_name');
        $v_title      = get_request_var('txt_title');
        $v_sub_title  = get_request_var('txt_sub_title');
        $v_slug       = auto_slug(get_request_var('txt_slug'));
        
        $v_summary    = $this->prepare_tinyMCE(get_request_var('txt_summary', '', 0)); // xu ly rieng
//        $v_summary    = htmlspecialchars($v_summary);
        
        $v_content    = $this->prepare_tinyMCE(get_request_var('txt_content', '', 0)); // xu ly rieng
//        $v_content    = htmlspecialchars($v_content);
        
        $v_tags       = get_request_var('txt_tags');
        $v_keywords   = get_request_var('txt_keyword');
        $v_thumbnail  = str_replace('\\', '/', get_request_var('hdn_thumbnail', '0'));
        $arr_category = isset($_POST['chk_category']) ? $_POST['chk_category'] : array();
        $arr_attachment = get_request_var('hdn_attachment', array(), false);
        $v_begin_date = get_request_var('txt_begin_date', date('Y/m/d H:i:s'));
        $v_end_date   = '2100/1/1';
        $v_has_video  = (int) get_post_var('chk_has_video',0);
        $v_has_photo    = (int) get_post_var('chk_has_photo',0);
        
        $v_is_img_news  = (int) isset($_POST['chk_is_img_news'])?1:0;
        
        $v_redirect_url  = trim(get_post_var('txt_redirect_url',''));
        
        $v_employee_id = get_post_var('sel_employee','0');
        $v_is_copy     =  isset($_POST['chk_is_copy'])?'1':'0';

        //validate
        if (
                !$v_title OR
                !$v_slug OR
                !$v_summary OR
                !$v_content OR
                !$v_user OR
                (count($arr_category) == 0)
        )
        {
            return array(
                'msg'    => __('invalid request data'),
                'status' => 'error'
            );
        }

        //insert
        $param = array();
        if ($v_id == 0)
        {
            $v_website_id   = Session::get('session_website_id');
            $v_website_id   = ((int)($v_website_id) >0) ? $v_website_id : '0';
            
            $sql = "
            Insert Into t_ps_article
            (
                C_TITLE,
                C_SUB_TITLE,
                C_SLUG,
                C_TAGS,
                C_KEYWORDS,
                FK_INIT_USER,
                C_STATUS,
                C_VIEWS,
                C_SUMMARY,
                C_CONTENT,
                C_PEN_NAME,
                C_BEGIN_DATE,
                C_END_DATE,
                C_FILE_NAME,
                C_CACHED_RATING,
                C_CACHED_RATING_COUNT,
                C_HAS_VIDEO,
                C_HAS_PHOTO,
                C_REDIRECT_URL,
                C_DEFAULT_CATEGORY,
                C_DEFAULT_WEBSITE,
                FK_EMPLOYEE,
                C_IS_COPY,
                C_IS_IMG_NEWS
            )
            Values
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
            	?,
                ?,
                ?,
                ?,
                ?,
                ?
           )
            ";

            $param = array(
                $v_title,
                $v_sub_title,
                $v_slug,
                $v_tags,
                $v_keywords,
                $v_user,
                0,
                0,
                $v_summary,
                $v_content,
                $v_pen_name,
                $v_begin_date,
                $v_end_date,
                $v_thumbnail,
                0,
                0,
                $v_has_video,
                $v_has_photo,
            	$v_redirect_url,
                $arr_category[0],
                $v_website_id,
                $v_employee_id,
                $v_is_copy,
                $v_is_img_news,
            );

            $this->db->Execute($sql, $param);
            if(DATABASE_TYPE == 'MYSQL')
            {
                $v_id = intval($this->db->getOne("SELECT MAX(PK_ARTICLE) FROM t_ps_article"));
            }
            //kiem tra xem co loi k 
            if ($this->db->errorNo())
            {
                $arr_msg = array(
                    'msg'    => __('update fail') . ':' . $this->db->errorMsg(),
                    'status' => 'error'
                );
            }
            //khong loi cap nhat category article
            else
            {
                $this->update_category_article($v_id, $arr_category);
                $arr_msg = array(
                    'msg'    => strval(__('update success')),
                    'status' => 'success',
                    'id'     => "$v_id"
                );
            }
        }
        else //update
        {
            //Check là tin sticky thi khong cho sua doi chuyen  muc
            $arr_cat = implode(',', $arr_category);
            $sql = "SELECT
                        COUNT(PK_STICKY)
                      FROM t_ps_sticky
                      WHERE FK_ARTICLE = ?
                          AND FK_CATEGORY in ($arr_cat)";
            $count_article_is_sticky = $this->db->GetOne($sql,array($v_id));
            if((int)$count_article_is_sticky > 0)
            {
                return array(
                            'msg'    => 'Tin bài đang được đánh dấu làm tin nội bật. Để thay đổi chuyên mục của tin bài này bạn cần gỡ bỏ trạng thái nôi bật!',
                            'status' => 'error',
                            'id'     => $v_id
                    );
            }
            unset($sql);
            
            $granted_category   = implode(', ', Session::get('granted_category'));
            $user_id            = Session::get('user_id');
            $v_website_id       = Session::get('session_website_id');
            $v_website_id = ((int)($v_website_id) >0) ? $v_website_id : '0';
            $str_pen_name     = $v_pen_name ? ",C_PEN_NAME = '$v_pen_name'" : '';
            $sql              = "
                Update t_ps_article Set
                    C_TITLE = ?,
                    C_SUB_TITLE = ?,
                    C_SLUG = ?,
                    C_TAGS = ?,
                    C_KEYWORDS = ?,
                    C_SUMMARY = ?,
                    C_CONTENT = ?,
                    C_FILE_NAME = ?,
                    C_HAS_VIDEO = ?,
                    C_HAS_PHOTO = ?,
                    C_REDIRECT_URL=?,
                    C_DEFAULT_CATEGORY = ?,
                    C_DEFAULT_WEBSITE = ? ,
                    FK_EMPLOYEE = ?,
                    C_IS_COPY = ? ,
                    C_IS_IMG_NEWS = ?
                    $str_pen_name
                Where PK_ARTICLE = ?
            ";

            $param = array(
                $v_title,
                $v_sub_title,
                $v_slug,
                $v_tags,
                $v_keywords,
                $v_summary,
                $v_content,
                $v_thumbnail,
                $v_has_video,
                $v_has_photo,
            	$v_redirect_url,
                $arr_category[0],
                $v_website_id,
                $v_employee_id,
                $v_is_copy,
                $v_is_img_news,
                $v_id
            );

            $this->db->Execute($sql, $param);
            
            //kiem tra xem co loi
            if ($this->db->errorNo())
            {
                $arr_msg = array(
                    'msg'    => __('update fail') . ':' . $this->db->errorMsg(),
                    'status' => 'error'
                );
            }
            //cap nhat category article
            else
            {
                $this->update_category_article($v_id, $arr_category);
                $msg     = strval(__('update success'));
                $arr_msg = array(
                    'msg'    => $msg,
                    'status' => 'success',
                    'id'     => "$v_id"
                );
//                
//                //LienND update 2013-10-30
//                //Cache tin bai moi nhat
//                if (get_system_config_value(CFGKEY_CACHE) == 'true')
//                {
//                    $v_website_id = session::get('session_website_id');
//                    $v_website_id = ((int)($v_website_id) >0) ? $v_website_id : 'NULL';
//                    $cache = new GP_Cache();
//                    $v_success = $cache->create_latest_article_cache($v_website_id);
//                }
            }
        } //het update
        
        //cap nhat file dinh kem
        $this->update_attachment($v_id, $arr_attachment);

        //cap nhat version
        $this->update_xml_version($v_id, array(
            'content' => $v_content,
            'summary' => $v_summary,
        ));

        //cap nhat tags
        $this->save_tags($v_tags);

      

        $this->db->debug = DEBUG_MODE;
        return $arr_msg;
    }

//  function update_article()

    function update_category_article($article_id, $arr_category)
    {
        //lay category dang co
        $arr_current_cat = $this->qry_category_article($article_id);
        $arr_insert      = array();
        $arr_delete = array();

        //lay category_article can delete
        foreach ($arr_current_cat as $item)
        {
            if (!in_array($item, $arr_category))
                $arr_delete[] = intval($item);
        }

        //lay category_article can insert
        foreach ($arr_category as $item)
        {
            if (!in_array($item, $arr_current_cat))
                $arr_insert[] = intval($item);
        }

        //delete category_article
        $arr_delete = implode(',', $arr_delete);
        $arr_delete = replace_bad_char($arr_delete);
        if (!empty($arr_delete))
        {
            $sql = "Delete From t_ps_category_article
                Where FK_ARTICLE = $article_id And FK_CATEGORY In($arr_delete)";
            $this->db->Execute($sql);
        }

        //insert category_article
        if (!empty($arr_insert))
        {
            $arr_granted = Session::get('granted_category');
            $arr_granted = implode(',', $arr_granted);
            $arr_insert  = replace_bad_char(implode(', ', $arr_insert));
            $v_website_id     = Session::get('session_website_id');
            $v_website_id = ((int)($v_website_id) >0) ? $v_website_id : 0;
            
            $v_condition = " FK_WEBSITE = $v_website_id";
            
            $sql         = " Select PK_CATEGORY as ARTICLE 
                    From t_ps_category
                    Where $v_condition
                    And PK_CATEGORY In($arr_insert)
                    And PK_CATEGORY In($arr_granted)";
            $arr_category_id = $this->db->getCol($sql);
            foreach ($arr_category_id as $v_category_id)
            {
                $sql         = "Insert Into t_ps_category_article (FK_CATEGORY, FK_ARTICLE) VALUES($v_category_id,$article_id) ";
                $this->db->Execute($sql);
            }
        }
    }

// function update_category_article()

    function update_edited_article()
    {
        $msg = array(
            'msg'                => '',
            'status'             => '',
            'id'                 => ''
        );
        //get data
        $v_id                = intval(get_post_var('hdn_item_id', 0));
        $v_status            = intval(get_post_var('sel_status'));
        $v_begin_date        = get_post_var('txt_begin_date');
        $v_begin_time        = get_post_var('txt_begin_time');
        $v_end_date          = get_post_var('txt_end_date');
        $v_end_time          = get_post_var('txt_end_time');
        $v_article_msg       = get_post_var('txt_msg');
        $v_user              = Session::get('user_id');
        $v_pen_name          = get_post_var('txt_pen_name');
        $arr_sticky_category = get_post_var('chk_category', array(), false);
      
        //Sưa và thêm mới được quyen tren moi dc sua ngay
        if (check_permission('SUA_TIN_BAI', $this->app_name) OR  check_permission('THEM_MOI_TIN_BAI', $this->app_name))
        {
            $v_begin_date = DateTime::createFromFormat('d-m-Y H:i', $v_begin_date . ' ' . $v_begin_time);
            $v_end_date   = DateTime::createFromFormat('d-m-Y H:i', $v_end_date . ' ' . $v_end_time);

            if (!$v_begin_date or !$v_end_date)
            {
                $msg = array(
                    'msg'    => __('invalid request data'),
                    'status' => 'error',
                    'id'     => $v_id
                );

//                return $msg;
            }
            $v_begin_date = $v_begin_date->format('Y-m-d H:i');
            $v_end_date   = $v_end_date->format('Y-m-d H:i');
        }
        else
        {
            $v_begin_date = $v_begin_time = $v_end_date   = $v_end_time   = '';
        }
        //verify
        if ($v_id <= 0)
        {
            return array(
                'msg'    => __('invalid request data'),
                'status' => 'error',
                'id'     => $v_id
            );
        }
        
        //end verify permission
        //update
        $sql     = 'Update t_ps_article Set ';
        $sql .= ' C_STATUS = ?';
        $param[] = $v_status;

        $sql .= ', C_MESSAGE = ?';
        $param[] = $v_article_msg;

        if ($v_begin_date && $v_end_date)
        {
            $sql .= ', C_BEGIN_DATE = ?';
            $param[] = $v_begin_date;

            $sql .= ', C_END_DATE = ?';
            $param[] = $v_end_date;
        }
        $sql .= ', C_PEN_NAME = ?';
        $param[] = (string) $v_pen_name;

        $granted_category = implode(', ', Session::get('granted_category'));
        $sql .= ' Where PK_ARTICLE = ?';
        $param[]          = $v_id;

        $this->db->Execute($sql, $param);
        $this->update_article_sticky($v_id, $arr_sticky_category);

        if ($this->db->errorNo())
        {
            return array(
                'msg'    => __('update fail') . ': ' . $this->db->errorMsg(),
                'status' => 'error',
                'id'     => $v_id
            );
        }
        else //update ko loi
        {
            //cap nhat version
            $this->update_xml_version($v_id, array(
                'status' => $v_status
            ));
            return array(
                'msg'    => __('update success'),
                'status' => 'success',
                'id'     => $v_id
            );
            
            //LienND update 2013-10-30
            //Cache tin bai moi nhat
//            if (get_system_config_value(CFGKEY_CACHE) == 'true')
//            {
//                $v_website_id = session::get('session_website_id');
//                
//                $cache = new GP_Cache();
//                $v_success = $cache->create_latest_article_cache($v_website_id);
//            }
        }
    }

// end function update_edited_article

    function update_article_sticky($article_id, $arr_sticky_category)
    {
        $arr_old_sticky = $this->qry_sticky_category($article_id);
        $arr_add        = array_diff($arr_sticky_category, $arr_old_sticky);
        $arr_remove     = array_diff($arr_old_sticky, $arr_sticky_category);
        $v_website_id     = Session::get('session_website_id');
        $v_website_id = ((int)($v_website_id) >0) ? $v_website_id : 0;
        //insert
        $n = count($arr_add);
        for ($i = 0; $i < $n; $i++)
        {
            $category_id = (int) $arr_add[$i];
            $sql         = 'Insert Into t_ps_sticky(FK_CATEGORY, FK_ARTICLE, C_DEFAULT, FK_WEBSITE)
                VALUES(?,?,?,?)';
            $params      = array($category_id, $article_id, 0, $v_website_id);
            $this->db->execute($sql, $params);
            Model::build_order('t_ps_sticky'
                    , 'PK_STICKY'
                    , 'C_ORDER'
                    , "WHERE FK_WEBSITE=$v_website_id AND C_DEFAULT=0 AND FK_CATEGORY=$category_id");
        }

        //delete
        if (!empty($arr_remove))
        {
            $arr_remove = replace_bad_char(implode(',', $arr_remove));
            $sql        = "Delete From t_ps_sticky 
                Where C_DEFAULT = 0
                AND FK_WEBSITE = ?
                AND FK_ARTICLE = ?
                AND FK_CATEGORY IN($arr_remove)";
            $params     = array($v_website_id, $article_id);
            $this->db->execute($sql, $params);
        }
    }

    function qry_sticky_category($article_id)
    {
        $v_website_id       = Session::get('session_website_id');
        
        $v_website_id = ((int)($v_website_id) >0) ? $v_website_id : 0;
        $condition  = " and FK_WEBSITE = $v_website_id ";
        
        $sql    = "Select FK_CATEGORY From t_ps_sticky
            Where FK_ARTICLE = ?
                    $condition
            AND C_DEFAULT = 0";
        $params = array($article_id);
        return $this->db->getCol($sql, $params);
    }

    function qry_user_name($user_id)
    {
        $user_id = intval($user_id);
        $sql     = 'Select C_NAME From t_cores_user Where PK_USER = ' . $user_id;
        return $this->db->getOne($sql);
    }

    function restore_version($article_id, $version_id)
    {
        $article_id = isset($article_id)?replace_bad_char($article_id):'0';
        $version_id = isset($version_id)?replace_bad_char($version_id):'-1';
        
        if ($article_id != '0' && $version_id != '-1')
        {
            //verify
            $granted_category = implode(',', Session::get('granted_category'));
            $user_id          = Session::get('user_id');
            $v_website_id     = Session::get('session_website_id');
            $v_website_id     = ((int)($v_website_id) >0) ? $v_website_id : 0;
            
            $v_all_version = $this->db->getOne("
                Select A.C_XML_VERSION From t_ps_article A
                Where A.PK_ARTICLE = $article_id
            ");

            if (empty($v_all_version)) //xem co ton tai tin bai khong
            {
                die(__('this object is nolonger available!'));
            }
            else
            {
                $xml = simplexml_load_string($v_all_version, 'SimpleXMLElement', LIBXML_NOCDATA);
                $xml = $xml->xpath('//version');
                if (empty($xml[$version_id])) //kiem tra version ton tai
                {
                    die(__('this object is nolonger available!'));
                }
                else
                {
                    $single_version = $xml[$version_id];
                    $v_content      = $single_version->content;
                    $v_summary      = $single_version->summary;
                    $sql            = "Update t_ps_article Set C_SUMMARY = ?, C_CONTENT = ? Where PK_ARTICLE = ?";
                    $this->db->Execute($sql, array($v_summary, $v_content, $article_id));

                    //cap nhat version
                    $this->update_xml_version($article_id, array(
                        'summary' => $v_summary,
                        'content' => $v_content
                    ));
                }
            }
        }
        else
        {
            die(__('invalid request data'));
        }
    }

    //end function restore
    /**
     * 
     * @param int $article_id
     * @param array $arr_version gom phan tu: [summary, content, status]
     */
    function update_xml_version($article_id, $arr_version)
    {
        //get data
        $arr_version['summary'] = isset($arr_version['summary']) ? $arr_version['summary'] : '';
        $arr_version['content'] = isset($arr_version['content']) ? $arr_version['content'] : '';

        $arr_version['user']      = Session::get('user_id');
        $arr_version['user_name'] = $this->qry_user_name($arr_version['user']);
        //Lay xml version hien tai
        $v_single_article         = $this->db->getRow("Select C_STATUS, C_XML_VERSION From t_ps_article Where PK_ARTICLE=$article_id");

        if (empty($v_single_article['C_XML_VERSION']))
        {
            $arr_version['status'] = 0;
            $arr_version['action'] = 'init';
            $v_xml_version         = '<?xml version="1.0" standalone="yes"?><root></root>';
        }
        else
        {
            $arr_version['status'] = isset($arr_version['status']) ? $arr_version['status'] : $v_single_article['C_STATUS'];
            $arr_version['action'] = 'update';
            $v_xml_version         = $v_single_article['C_XML_VERSION'];
        }
        $v_xml_version         = $v_xml_version;
        $v_lastest_version     = '<version>';
        $v_lastest_version .= '<action>' . $arr_version['action'] . '</action>';
        $v_lastest_version .= '<date>' . date('Y/m/d H:i') . '</date>';
        $v_lastest_version .= '<user>' . $arr_version['user'] . '</user>';
        $v_lastest_version .= '<user_name>' . $arr_version['user_name'] . '</user_name>';
        $v_lastest_version .= '<status>' . $arr_version['status'] . '</status>';
        $v_lastest_version .= '<summary><![CDATA[' . $arr_version['summary'] . ']]></summary>';
        $v_lastest_version .= '<content><![CDATA[' . $arr_version['content'] . ']]></content>';
        $v_lastest_version .= '</version>';
        $v_xml_version         = str_replace('</root>', $v_lastest_version . '</root>', $v_xml_version);

        $granted_category   = implode(',', Session::get('granted_category'));
        $user_id            = Session::get('user_id');
        $v_website_id       = Session::get('session_website_id');
        $v_website_id = ((int)($v_website_id) >0) ? $v_website_id : 0;
        
        if(DATABASE_TYPE == 'MSSQL')
        {
            $sql  = "Update t_ps_article 
                        Set C_XML_VERSION = '$v_xml_version'
                        From t_ps_article A
                        Inner Join t_ps_category_article CA
                        On A.PK_ARTICLE = CA.FK_ARTICLE
                        Inner Join t_ps_category C
                        On C.PK_CATEGORY = CA.FK_CATEGORY
                        Where A.PK_ARTICLE = $article_id
                        And C.PK_CATEGORY In($granted_category)
                        And (A.C_STATUS > 0 Or (A.C_STATUS = 0 And A.FK_INIT_USER = $user_id))
                    ";
            $this->db->Execute($sql);
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $sql = "SELECT count(*)  FROM t_ps_article A
                            INNER JOIN t_ps_category_article CA
                            ON A.PK_ARTICLE = CA.FK_ARTICLE
                            INNER JOIN t_ps_category C
                            ON C.PK_CATEGORY = CA.FK_CATEGORY
                            WHERE A.PK_ARTICLE = $article_id
                            AND C.PK_CATEGORY IN($granted_category)
                            AND (A.C_STATUS > 0 OR (A.C_STATUS = 0 AND A.FK_INIT_USER = $user_id))";
            $check = $this->db->getOne($sql);
            if($check > 0)
            {
                $sql  = "Update t_ps_article 
                        Set C_XML_VERSION = '$v_xml_version' 
                    WHERE PK_ARTICLE = $article_id";
                $this->db->Execute($sql);
            }
        }
        
    }

    /**
     * Lấy danh sách chuyên mục
     */
    function qry_all_grant_category()
    {
        $stmt = "select PK_CATEGORY from t_ps_category";
        return $this->db->GetCol($stmt);
    }

    function update_attachment($article_id, $arr_attachment)
    {
        $article_id = intval($article_id);
        
        $sql        = 'Delete From t_ps_article_attachment Where FK_ARTICLE = ' . $article_id . ';';
        $this->db->Execute($sql);
        
        $param      = array();
        
        $sql = '';
        if (!empty($arr_attachment))
        {
            $sql .= 'Insert Into t_ps_article_attachment(FK_ARTICLE, C_FILE_NAME) Values';
            $first_att = str_replace('\\', '/', array_shift($arr_attachment));
            $sql .= "($article_id, ?)";
            $param[]   = $first_att;
            if (!empty($arr_attachment))
            {
                foreach ($arr_attachment as $val)
                {
                    $val     = str_replace('\\', '/', $val);
                    $sql .= ",($article_id, ?)";
                    $param[] = $val;
                }
            }
            $this->db->Execute($sql, $param);
        }
    }

    function qry_all_attachment($id)
    {
        $id  = intval($id);
        $sql = 'Select C_FILE_NAME
                From t_ps_article_attachment
                Where FK_ARTICLE =' . $id;
        return $this->db->getAll($sql);
    }

    function delete_article($a_other_clause = '', $c_other_clause = '')
    {
 
        $arr_delete = get_post_var('chk', array(), false);

        if (empty($arr_delete))
        {
            die(__('invalid request data'));
        }
        $arr_delete = implode(',', $arr_delete);
        $arr_delete = replace_bad_char($arr_delete);

        $arr_fk = array(
            't_ps_article_attachment' => 'FK_ARTICLE'
            , 't_ps_article_comment'    => 'FK_ARTICLE'
            , 't_ps_article_rating'     => 'FK_ARTICLE'
        );

        $sql = '';
        foreach ($arr_fk as $table => $col)
        {
            $sql .= " Delete From $table where $col In($arr_delete);";
        }
        $this->db->Execute($sql);

        $a_other_clause .= " And PK_ARTICLE In($arr_delete)";
        $sql = "
                Delete From t_ps_article
                Where PK_ARTICLE In(
                    Select A.PK_ARTICLE From (Select PK_ARTICLE From t_ps_article where 1=1 $a_other_clause) A
                           Inner Join t_ps_category_article CA
                            On A.PK_ARTICLE = CA.FK_ARTICLE
                            Inner Join (Select PK_CATEGORY from t_ps_category Where 1=1 $c_other_clause) C
                            On CA.FK_CATEGORY = C.PK_CATEGORY
                )
        ";

        $this->db->Execute($sql);
    }

    function qry_my_pen_name()
    {
        $user = Session::get('user_id');
        $sql  = "
            Select C_PEN_NAME from 
            (
                    Select distinct ltrim(rtrim(C_PEN_NAME)) as C_PEN_NAME 
                    From t_ps_article 
                    Where FK_INIT_USER = $user
            ) as temp
            where C_PEN_NAME <> ''
            order by C_PEN_NAME
        ";
        return $this->db->getCol($sql);
    }

    function qry_all_tags()
    {
        $key = OPT_TAGS;
        $sql = "Select C_OPTION_VALUE From t_cores_option Where C_OPTION_KEY ='$key'";
        return $this->db->getOne($sql);
    }

    function count_all_article($other_clause = '')
    {
        $v_website_id = Session::get('session_website_id');
        
        $v_website_id = ((int)($v_website_id) >0) ? $v_website_id : 0;
        $consition = " C_DEFAULT_WEBSITE = $v_website_id  ";
    
        $sql = "Select 
                    Count(*) 
               From t_ps_article A FORCE INDEX(C_DEFAULT_WEBSITE)
               Where $consition  $other_clause";
        return $this->db->getOne($sql);
    }

    private function save_tags($str_tags)
    {
        $key          = OPT_TAGS;
        $arr_old_tags = unserialize($this->qry_all_tags());
        if (!$arr_old_tags)
        {
            $arr_old_tags = array();
        }
        if ($str_tags != '')
        {
            $arr_tags = explode(',', trim(preg_replace('/( +)/', ' ', $str_tags), ' '));
            foreach ($arr_tags as $k => $val)
            {
                $arr_tags[$k] = trim($val, ' ');
            }
        }
        else
        {
            $arr_tags = array();
        }
        foreach ($arr_old_tags as $tag)
        {
            if (in_array($tag, $arr_tags) == false)
            {
                $arr_tags[] = $tag;
            }
        }
        sort($arr_tags);

        $db_data = serialize($arr_tags);
        
        if(DATABASE_TYPE == 'MYSQL')
        {
            $sql = "Select COUNT(PK_OPTION) From t_cores_option Where C_OPTION_KEY = '$key'";
            $check = $this->db->getOne($sql);
            
            if($check > 0)
            {
                $sql = "Update t_cores_option Set C_OPTION_VALUE = '$db_data' Where C_OPTION_KEY = '$key'";
                $this->db->Execute($sql);
            }
            else
            {
                $sql = "Insert Into t_cores_option(C_OPTION_KEY, C_OPTION_VALUE) Values('$key', '$db_data')";
                $this->db->Execute($sql);
            }
        }
        
    }
   
}
    
?>

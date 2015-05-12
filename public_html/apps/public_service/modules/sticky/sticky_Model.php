<?php

defined('DS') or die('no direct access');

class sticky_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    function qry_all_sticky($other_clause = '')
    {
        $v_website_id = Session::get('session_website_id');
        $v_website_id = ((int) $v_website_id >0) ? (int) $v_website_id : 0;
        
       if(DATABASE_TYPE == 'MYSQL')
        {
            $sql = 'Select distinct S.PK_STICKY, S.C_ORDER
                        , A.C_TITLE, A.C_STATUS
                        , DATEDIFF(NOW(),A.C_BEGIN_DATE) As CK_BEGIN_DATE
                        , DATEDIFF(A.C_END_DATE, NOW()) As CK_END_DATE
                        , C.C_STATUS As C_CAT_STATUS
                    From t_ps_sticky S
                    Inner Join t_ps_article A
                    On S.FK_ARTICLE = A.PK_ARTICLE
                    Inner Join t_ps_category_article CA
                    On CA.FK_ARTICLE = A.PK_ARTICLE
                    Inner Join t_ps_category C
                    On C.PK_CATEGORY = CA.FK_CATEGORY';
            $sql .= ' Where 1=1 And S.FK_WEBSITE = ' . $v_website_id;
            $sql .= ' ' . $other_clause;
            $sql .= ' Order by S.C_ORDER';
        }
        return $this->db->getAll($sql);
    }

    function insert_sticky()
    {
        $v_default   = intval(get_post_var('default', 0));
        $arr_article = get_post_var('article', array(), false);
        $v_website = Session::get('session_website_id');
        $v_website = ((int)$v_website > 0) ? (int) $v_website : 0;
        //validate
        if ($v_default < 0 or $v_default > 2)
        {
            die(__('invalid request data'));
        }
        if (empty($arr_article))
        {
            die(__('invalid request data'));
        }
        
        $sql = '';
        $n     = count($arr_article);
        $param = array();
        for ($i = 0; $i < $n; $i++)
        {
            if($v_default != 2)
            {
                $stmt = "SELECT COUNT(PK_STICKY) FROM t_ps_sticky WHERE FK_CATEGORY = ? AND FK_ARTICLE = ? AND C_DEFAULT = ? AND FK_WEBSITE = ?";
                $arr_param = array($arr_article[$i]['article_category_id'],$arr_article[$i]['article_id'],$v_default,$v_website);
            }
            //truong hop tin dang chu y trong ngay
            else if($v_default == 2)
            {
                $stmt = "SELECT COUNT(PK_STICKY) FROM t_ps_sticky WHERE FK_CATEGORY = ? AND FK_ARTICLE = ? AND C_TYPE = ? AND FK_WEBSITE = ?";
                $arr_param = array($arr_article[$i]['article_category_id'],$arr_article[$i]['article_id'],$v_default,$v_website);
            }
            //kiem tra xem co trung chua
            if($this->db->getOne($stmt,$arr_param)< 1)
            {
                //ghep noi insert nhieu ban ghi cung 1 luc
                if($sql == '')
                {
                    if($v_default != 2)
                    {
                        $sql   .= 'Insert Into t_ps_sticky (FK_ARTICLE, FK_CATEGORY, FK_WEBSITE, C_DEFAULT) Values (?, ?, ?, ?)';
                    }
                    else if($v_default == 2)
                    {
                        $sql   .= 'Insert Into t_ps_sticky (FK_ARTICLE, FK_CATEGORY, FK_WEBSITE, C_TYPE) Values (?, ?, ?, ?)';
                    }
                }
                else
                {
                    $sql .= ",(?, ?, ?, ?)";
                }
                
                $param[] = $arr_article[$i]['article_id'];
                $param[] = $arr_article[$i]['article_category_id'];
                $param[] = $v_website;
                $param[] = $v_default;
            }
        }
       
        $this->db->Execute($sql, $param);
        $this->build_order('t_ps_sticky', 'PK_STICKY', 'C_ORDER', " AND FK_WEBSITE=$v_website And C_DEFAULT=$v_default");
    }

    function delete_sticky()
    {
        $arr_delete = get_post_var('chk_item', array(), false);
        if (empty($arr_delete))
        {
            die('invalid request data');
        }
        $arr_delete = implode(',', $arr_delete);
        $arr_delete = replace_bad_char($arr_delete);
        $sql        = "Delete From t_ps_sticky Where PK_STICKY In($arr_delete)";
        $this->db->Execute($sql);
    }

    function swap_sticky_order()
    {
        $item1 = intval(get_post_var('item1'));
        $item2 = intval(get_post_var('item2'));
        $this->swap_order('t_ps_sticky', 'PK_STICKY', 'C_ORDER', $item1, $item2);
    }

    function qry_all_category($other_clause)
    {
        $sql = 'Select PK_CATEGORY, C_NAME, C_INTERNAL_ORDER From t_ps_category Where 1=1';
        $sql .= $other_clause;
        $sql .= ' Order By C_INTERNAL_ORDER';       
        return $this->db->getAll($sql);
    }
    //cache
}

?>

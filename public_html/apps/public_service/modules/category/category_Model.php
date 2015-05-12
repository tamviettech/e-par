<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class category_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function qry_all_category($other_clause = '')
    {
        $sql        = 'Select 
                        C.PK_CATEGORY, C.C_ORDER, C.C_INTERNAL_ORDER, C.C_SLUG
                        , C.C_NAME, C.C_STATUS, C.FK_PARENT';

        $sql .= ',(Select Count(*) From t_ps_category CC 
                Where CC.FK_PARENT =  C.PK_CATEGORY) as C_COUNT_CHILD_CAT';
        $sql .= ', (Select Count(*) From t_ps_category_article CACA
                Where CACA.FK_CATEGORY = C.PK_CATEGORY) as C_COUNT_CHILD_ART';

        $sql .= ' From t_ps_category C
            Where 1 = 1
                        ' . $other_clause . '
                        Order by C.C_INTERNAL_ORDER';
        return $this->db->getAll($sql);
    }

    public function swap_category_order($item1, $item2)
    {
        $item1        = replace_bad_char($item1);
        $item2        = replace_bad_char($item2);
      
        $this->swap_order('t_ps_category', 'PK_CATEGORY', 'C_ORDER', $item1, $item2);
        $this->build_internal_order(
                    't_ps_category', 'PK_CATEGORY', 'FK_PARENT'
                    , 'C_ORDER', 'C_INTERNAL_ORDER', 'NULL'
            );
        if ($this->db->ErrorNo() == 0)
            echo 'Thanh cong';
    }

    public function qry_single_category($id)
    {
        return $this->db->getRow(
                        'Select 
                            PK_CATEGORY
                            ,FK_PARENT                            
                            ,C_NAME
                            ,C_IS_VIDEO
                            ,C_ORDER
                            ,C_SLUG
                            ,C_INTERNAL_ORDER
                            ,C_STATUS
                        From t_ps_category
                        Where PK_CATEGORY = ?'
                        , array($id)
        );
    }

    public function update_category()
    {
        $data['id']         = intval(get_request_var('hdn_item_id', 0));
        $data['name']       = strval(get_request_var('txt_name', ''));
        $data['slug']       = auto_slug(strval(get_request_var('txt_slug', '')));
        $data['parent']     = intval(get_request_var('sel_category', '0'));
        $data['is_video']   = intval(get_request_var('chk_is_video',0));
        $data['status']     = isset($_POST['sel_status'])?1:0;
        $data['controller'] = strval(get_request_var('controller'));
        $data['order']      = intval(get_request_var('txt_order', 1));
        
        if ($data['slug'] == '')
        {
        	$data['slug']       = auto_slug($data['name']);
        }
        //validate
        if (empty($data['name']) OR empty($data['slug']))
        {
            $this->exec_fail($data['controller'], __('invalid request data'));
        }


        if ($data['parent'] > 0)
        {
            $count_cat = $this->db->getOne(
                    'Select Count(*) From t_ps_category Where PK_CATEGORY = ?'
                    , array($data['parent'])
            );
        }
        else
        {
            $data['parent'] = 0;
            $count_cat      = 1;
        }
        //exec
        //update
        if ($data['id'] > 0)
        {
            $sql = 'Update t_ps_category
            Set C_NAME = ?
                , C_SLUG = ?
                , FK_PARENT = ?
                , C_STATUS = ?
                , C_ORDER = ?
                , C_IS_VIDEO = ?
            Where PK_CATEGORY = ?';

            $param = array(
                $data['name'], $data['slug'], $data['parent']
                , $data['status'], $data['order'], $data['is_video'], $data['id']
            );

            $this->db->Execute($sql, $param);
        }
        //insert
        else
        {
            $sql = 'Insert Into t_ps_category(C_NAME
                                              , C_SLUG
                                              , FK_PARENT
                                              , C_STATUS
                                              , C_ORDER
                                              , C_IS_VIDEO)
                                              
                                    Values(   ?
                                            , ?
                                            , ?
                                            , ?
                                            , ?
                                            , ?
                                            )';

            $param = array(
                $data['name'], $data['slug'], $data['parent']
                , $data['status'], $data['order'], $data['is_video']
            );
            
            $this->db->Execute($sql, $param);
        }
        //exec done
        if ($this->db->ErrorNo() == 0)
        {
           
            $this->ReOrder('t_ps_category', 'PK_CATEGORY', 'C_ORDER', $data['id'], $data['order']);
            
           $this->build_internal_order(
                    't_ps_category', 'PK_CATEGORY', 'FK_PARENT'
                    , 'C_ORDER', 'C_INTERNAL_ORDER', 'NULL'
            );
            $this->exec_done($data['controller']);
        }
        else
        {
            $this->exec_fail($data['controller'], 'Cập nhật không thành công'. ':' . $this->db->ErrorMsg());
        }
    }

    public function delete_category()
    {
        $arr_item = isset($_POST['chk-item']) ? $_POST['chk-item'] : array();
        $controller = get_request_var('hdn_controller');
        if (empty($arr_item))
            $this->exec_done($controller);
        //validate
        $n          = count($arr_item);
        for ($i = 0; $i < $n; $i++)
        {
            //verify
            $arr_item[$i]  = intval($arr_item[$i]);
            $arr_child_cat = $this->qry_all_category(' And FK_PARENT = ' . $arr_item[$i]);
            if (count($arr_child_cat))
                $this->exec_fail($controller, __('invalid request data'));
        }
        $arr_item      = implode(', ', $arr_item);
        
        $sql = 'Delete From t_ps_category 
                          Where PK_CATEGORY In(' . $arr_item . ')';
        $this->db->Execute($sql);

        if ($this->db->ErrorNo() > 0)
            $this->exec_fail($controller, __('update fail'));
        else
            $this->exec_done($controller);
    }

    public function qry_all_featured()
    {
        
        $sql        = '
            Select C.PK_CATEGORY, C.C_NAME, C.C_STATUS, H.PK_HOMEPAGE_CATEGORY, H.C_ORDER
            From t_ps_category C
            Inner join t_ps_homepage_category H
            On H.FK_CATEGORY = C.PK_CATEGORY
            Order By H.C_ORDER';
        return $this->db->getAll($sql);
    }
    
    public function insert_featured_category()
    {
        $arr_category = isset($_POST['category']) ? $_POST['category'] : array();
        $n          = count($arr_category);

        if (!empty($arr_category))
        {
            for ($i = 0; $i < $n; $i++)
            {
                $arr_category[$i] = $arr_category[$i]['id'];
            }
            $arr_category     = replace_bad_char(implode(',', $arr_category));
            $sql              = "Insert Into t_ps_homepage_category (FK_CATEGORY)
                    Select  PK_CATEGORY
                    From t_ps_category C
                    Where C.PK_CATEGORY In($arr_category)
                    And C.PK_CATEGORY Not In (
                        Select FK_CATEGORY From t_ps_homepage_category 
                    )";
            $this->db->Execute($sql);
            $this->build_order('t_ps_homepage_category', 'PK_HOMEPAGE_CATEGORY', 'C_ORDER');
        }
    }

    public function delete_featured_category()
    {
        $arr_delete = isset($_POST['chk-item']) ? $_POST['chk-item'] : array();
        if (empty($arr_delete))
        {
            die('nothing added');
        }

        $arr_delete = replace_bad_char(implode(',', $arr_delete));
        $sql        = "Delete From t_ps_homepage_category
                       Where PK_HOMEPAGE_CATEGORY In($arr_delete)";
        $this->db->Execute($sql);
    }

    public function swap_featured_order()
    {
        $item1 = intval(get_request_var('item1', 0));
        $item2 = intval(get_request_var('item2', 0));

        $this->swap_order(
                't_ps_homepage_category', 'PK_HOMEPAGE_CATEGORY'
                , 'C_ORDER', $item1, $item2);
    }

    

    

}
?>
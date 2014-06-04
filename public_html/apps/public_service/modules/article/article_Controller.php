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

defined('DS') or die('no direct access');

class article_Controller extends Controller
{
    public function __construct()
    {
          //Kiem tra session
        @session::init();
        $login_name = session::get('login_name');
        if ($login_name == NULL)
        {
            session::destroy();
            header('location:' . SITE_ROOT . 'login.php');
            exit;
        }
        
        parent::__construct('public_service', 'article');
        $v_website_id = ((int)Session::get('session_website_id') > 0) ? (int) Session::get('session_website_id') : 0;
        define('OPT_TAGS', 'all_tags_of_website_' . $v_website_id );

//        if(DEBUG_MODE < 10) {$this->model->db->debug = false;}else{$this->model->db->debug = true;}
        
        
        //lay chuyen muc duoc phan quyen
        Session::set('granted_category', $this->model->qry_all_grant_category());
        $this->view->dsp_side_bar =true;
        $this->model->app_name = $this->app_name;
        $this->view->arr_count_article     = $this->model->gp_qry_count_article();
        $this->model->goback_url = $this->view->get_controller_url();
    }

    public function __destruct()
    {
        unset($_SESSION['granted_category']);
    }
   
    public function main()
    {
        $this->dsp_all_article();
    }

    public function dsp_all_article()
    {
         header('Content-type: text/html; charset=utf-8');
         //quyen 
         check_permission('XEM_DANH_SACH_TIN_BAI',$this->app_name) or $this->access_denied();
         
         $a_other_clause  = '';
         $c_other_clause  = '';
         $ca_other_clause = ''; 
         
        $granted_category = Session::get('granted_category');
        $granted_category = implode(',', $granted_category);

        $c_other_clause         = ' And PK_CATEGORY In(' . $granted_category . ')';
        $ca_other_clause        = ' And FK_CATEGORY In(' . $granted_category . ')';
        
        $data['arr_all_article'] = $this->model->qry_all_article(
                $a_other_clause
                , $c_other_clause
                , $ca_other_clause
        );

        $data['arr_all_user'] = $this->model->qry_all_user();

        //lay cat name cua searchbox
        $category_id           = intval(get_request_var('hdn_category'));
        $data['category_name'] = $this->model->qry_category_name($category_id);

        $data['count_article'] = $this->model->count_all_article();

        $this->view->layout_render('public_service/admin/dsp_layout_admin','dsp_all_article', $data);
    }

    public function dsp_single_article($id = 0)
    {
        $id                 = intval($id);
        $data['article_id'] = $id;
        $this->view->layout_render('public_service/admin/dsp_layout_admin','dsp_single_article', $data);
    }

    public function check_unique_title_service()
    {
        $title       = get_request_var('title');
        $id          = (int) get_request_var('id');
        $msg         = new stdClass();
        $msg->errors = '';
        if ($this->model->title_exists($id, $title) == true)
        {
            $msg->errors = __('this title already exists');
        }
        echo json_encode($msg);
    }

    public function dsp_general_info($id = 0)
    {
        $id = intval($id);
        //quyen
        if ($id)
        {
            if(!check_permission('SUA_TIN_BAI',$this->app_name) && !check_permission('THEM_MOI_TIN_BAI',$this->app_name))
            {
                $this->access_denied(); 
            }
        }
        $a_other_clause         = '';
        $c_other_clause         = '';
        $other_clause_by_status = '';
        
        $c_other_clause         = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';

        $data['arr_single_article'] = $this->model->qry_single_article($id, $a_other_clause, $c_other_clause);
        
        if (sizeof($data['arr_single_article']) <=0  && $id != 0)
        {
            die(__('this object is nolonger available!'));
        }

        $data['arr_all_category']     = $this->model->qry_all_category($c_other_clause);
        $data['arr_category_article'] = $this->model->qry_category_article($id);
        $data['arr_my_pen_name']      = $this->model->qry_my_pen_name();
        $data['arr_all_attachment']   = $this->model->qry_all_attachment($id);
        $this->view->render('dsp_general_info', $data);
    }

    public function update_general_info()
    {
        check_permission('SUA_TIN_BAI',$this->app_name) or $this->access_denied();
        $a_other_clause = '';
        $c_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        //$c_other_clause = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category',$this->app_name)) . ')';
        
        $v_website_id = Session::get('session_website_id');
        $v_website_id = ((int)($v_website_id) >0) ? $v_website_id : 0;
           
        $c_other_clause .= ' And FK_WEBSITE = ' . $v_website_id;

        $v_id               = intval(get_request_var('hdn_item_id', 0));
        $arr_single_article = $this->model->qry_single_article(
                intval(get_request_var('hdn_item_id', 0))
                , $a_other_clause
                , $c_other_clause
        );
        
        if (count($arr_single_article) == 0 && $v_id > 0)
        {
            echo json_encode(array(
                'msg'    => __('invalid request data'),
                'status' => 'error'
            ));
            return;
        }
        $return_msg = $this->model->update_general_info();
        echo json_encode($return_msg);
    }

    public function dsp_preview($id = 0)
    {
        check_permission('SUA_TIN_BAI',$this->app_name) or $this->access_denied();
        $a_other_clause         = '';
        $c_other_clause         = '';
        $c_other_clause .= ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';

        $v_website_id = Session::get('session_website_id');
        $v_website_id = ((int)($v_website_id) >0) ? $v_website_id : 0;
        
        $c_other_clause .= ' And FK_WEBSITE= ' . $v_website_id;
         
        $data['arr_single_article'] = $this->model->qry_single_article($id, $a_other_clause, $c_other_clause);
        if (empty($data['arr_single_article']))
        {
            die(__('this object is nolonger available!'));
        }
        $this->view->render('dsp_preview', $data);
    }

    public function dsp_edit_article($id = 0)
    {
        check_permission('SUA_TIN_BAI',$this->app_name) or $this->access_denied();

        $a_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        
        $v_website_id = Session::get('session_website_id');
        $v_website_id = ((int)($v_website_id) >0) ? $v_website_id : 0;
        $c_other_clause .= ' And FK_WEBSITE= ' . $v_website_id;
         

        $data['arr_single_article'] = $this->model->qry_single_article(intval($id), $a_other_clause, $c_other_clause);
        if (empty($data['arr_single_article']))
        {
            die(__('this object is nolonger available!'));
        }

        $data['arr_category_article'] = $this->model->qry_category_article($id);
        $data['arr_all_category']     = $this->model->qry_all_category($c_other_clause);
        $data['arr_sticky_category']  = $this->model->qry_sticky_category($id);

        $data['v_id'] = $id;
        $this->view->render('dsp_edit_article', $data);
    }

    public function update_edited_article($no_website = '0')
    {
        check_permission('SUA_TIN_BAI',$this->app_name) or $this->access_denied();
        $id             = intval(get_post_var('hdn_item_id', 0));
        $a_other_clause = '';
        $c_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        if($no_website == '0')
        {
            $v_website_id   =  Session::get('session_website_id');
            $v_website_id   = ((int)($v_website_id) >0) ? $v_website_id : 0;
           
            $c_other_clause .= " And FK_WEBSITE = $v_website_id " ;
            
        }
        else
        {
                $c_other_clause .= " And FK_WEBSITE = 0  " ;
        }
        

        $data["arr_single_article"] = $this->model->qry_single_article($id, $a_other_clause, $c_other_clause);
        if (empty($data['arr_single_article']))
        {
            die(json_encode(
                            array(
                                'msg'    => __('update fail'),
                                'status' => 'error',
                                'id'     => intval(get_post_var('hdn_item_id', 0))
                            )
                    )
            );
        }

        $a = $this->model->update_edited_article();
        echo json_encode($a);
    }

    public function dsp_all_version($id = 0)
    {
        
        check_permission('SUA_TIN_BAI',$this->app_name) or $this->access_denied();
         $v_website_id = Session::get('session_website_id');
         
         $v_website_id   = ((int)($v_website_id) >0) ? $v_website_id : 0;
         $c_other_clause = " And FK_WEBSITE= $v_website_id " ;

        $id = intval($id);

        $a_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause .= ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        



        $arr_single_article = $this->model->qry_single_article($id, $a_other_clause, $c_other_clause);
        if (!empty($arr_single_article))
        {
            
            $data['arr_all_version'] = array();
            $data['v_id'] = $id;
            
            $xml          = $arr_single_article['C_XML_VERSION'] ? $arr_single_article['C_XML_VERSION'] : '<root/>';
            $xml          = new SimpleXMLElement($xml);
            $xml          = $xml->xpath('//version');
            $i            = 0;
            
            foreach ($xml as $item)
            {
                $i++;
                $data['arr_all_version'][] = array(
                    'id'          => $i,
                    'date'        => strval($item->date),
                    'action'      => strval($item->action),
                    'status'      => strval($item->status),
                    'has_content' => strlen(strval($item->content)),
                    'user_name'   => $item->user_name != NULL ? $item->user_name : '?'
                );
            }

            $this->view->render('dsp_all_version', $data);
        }
        else
        {
            die(__('this object is nolonger available!'));
        }
    }

    public function dsp_single_version()
    {
        $article_id         = $data['article_id'] = intval(get_request_var('article'));
        $version_id         = $data['version_id'] = intval(get_request_var('version')) - 1;

        Session::check_permission('SUA_TIN_BAI') or $this->access_denied();

        $a_other_clause = '';
        $c_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $v_website_id = Session::get('session_website_id');
        $v_website_id   = ((int)($v_website_id) >0) ? $v_website_id : 0;
        $c_other_clause .= " And FK_WEBSITE= $v_website_id ";



        $arr_single_article = $this->model->qry_single_article($article_id, $a_other_clause, $c_other_clause);
        if (!empty($arr_single_article))
        {
            $xml = $arr_single_article['C_XML_VERSION'];
            $xml = new SimpleXMLElement($xml, LIBXML_NOCDATA);
            $xml = $xml->xpath("//version");

            if (empty($xml[$version_id]))
            {
                die(__('this object is nolonger available!'));
            }
            $xml                        = $xml[$version_id];
            $xml->summary               = $this->model->prepare_tinyMCE(html_entity_decode($xml->summary));
            $xml->content               = $this->model->prepare_tinyMCE(html_entity_decode($xml->content));
            $xml->title                 = $arr_single_article['C_TITLE'];
            $data['dom_single_version'] = $xml;
        }
        else
        {
            die(__('this object is nolonger available!'));
        }

        $this->view->render('dsp_single_version', $data);
    }

    public function restore_version()
    {
        $article_id = intval(get_post_var('article_id'));
        $version_id = intval(get_post_var('version_id'));
        
        
        check_permission('SUA_TIN_BAI',$this->app_name) or $this->access_denied();
        $a_other_clause = '';
        $c_other_clause = '';

        //neu la phong vien, bien tap vien chi hien category cua minh
        $c_other_clause = ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $v_website_id   = Session::get('session_website_id');
        $v_website_id   = ((int)($v_website_id) >0) ? $v_website_id : 0;
        
        $c_other_clause .= ' And FK_WEBSITE= ' . $v_website_id ;
        

        $arr_single_article = $this->model->qry_single_article($article_id, $a_other_clause, $c_other_clause);
        
        if (count($arr_single_article)>0)
        {
            $this->model->restore_version($article_id, $version_id);
        }
        else
        {
            die(__('invalid request data'));
        }
    }

    function dsp_all_article_svc()
    {
        check_permission('XEM_DANH_SACH_TIN_BAI',$this->app_name) or $this->access_denied();
        $v_website_id   = Session::get('session_website_id');
        $v_website_id   = ((int)($v_website_id) >0) ? (int)$v_website_id : 0;

        $v_website      = intval(get_request_var('sel_website', $v_website_id));
        $a_other_clause = ' And C_STATUS >= 0';
        
        if (intval(get_request_var('hdn_category')) == 0 && get_request_var('txt_title') == '')
        {
            $a_other_clause .= ' And 1=2';
        }
        $c_other_clause           = ' And FK_WEBSITE = ' . $v_website;
        
        $c_other_clause .= ' And PK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $ca_other_clause          = ' And FK_CATEGORY In(' . implode(',', Session::get('granted_category')) . ')';
        $data['v_website']        = 0;
        $data['arr_all_category'] = $this->model->qry_all_category($c_other_clause);
        $data['arr_all_article']  = $this->model->qry_all_article($a_other_clause, $c_other_clause, $ca_other_clause);
        
        $this->view->layout_render('public_service/admin/dsp_layout_admin_pop_win','dsp_all_article_svc', $data);
    }

    function delete_article()
    {
        $a_other_clause         = '';
        $c_other_clause         = '';
        $v_website_id   = Session::get('session_website_id');
        $v_website_id   = ((int)($v_website_id) >0) ? $v_website_id : 0;
        
        $c_other_clause .= " And FK_WEBSITE = $v_website_id";
        
        $granted_category       = implode(',', Session::get('granted_category'));

        $c_other_clause .= " And PK_CATEGORY In($granted_category)";

        $this->model->delete_article($a_other_clause, $c_other_clause);
    }


    public function fix_db_content()
    {
        $this->model->fix_db_content();
    }

    public function dsp_all_tags()
    {
        $data['arr_all_tags'] = unserialize($this->model->qry_all_tags());
        $this->view->layout_render('public_service/admin/dsp_layout_admin_pop_win','dsp_all_tags', $data);
    }
    
}

?>

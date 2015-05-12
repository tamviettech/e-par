<?php
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class dashboard_Controller extends Controller {

    function __construct() {
        parent::__construct('public_service','dashboard');
        //Kiem tra dang nhap
        $this->check_login();
        
        
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->dsp_side_bar = FALSE;
        
        $this->view->arr_count_article = $this->model->gp_qry_count_article();
    }
    
    function main() {
        $this->dsp_all_dashboard();
    }
    
    function dsp_all_dashboard()
    {
        $this->view->layout_render('public_service/admin/dsp_layout_admin','dsp_all_dashboard');
    }
    
    function do_change_session_menu_select($value)
    {
        $this->model->go_back_url = $this->view->get_controller_url('dashboard','admin');
        $this->model->do_change_session_menu_select($value);
    }
    
    public function do_change_session_website_id()
    {
        $v_website_id = get_request_var('website_id');
        $v_lang_id    = get_request_var('lang_id');
        $this->model->goback_url = $this->view->get_controller_url('dashboard','admin');
        $this->model->set_session($v_website_id,$v_lang_id);
    }
    
    public function dsp_change_password()
    {
        $this->view->render('dsp_change_password');
    }
    
    public function do_change_password()
    {
       $this->model->do_change_password();
    }
    
    //tao my sql
    public function create_mysql($table_name)
    {
        $this->model->create_mysql($table_name);
    }
    
    //chuyen doi du lieu mssql sang mysql
    public function mssql_to_mysql()
    {
//        $arr_table = array('T_CORES_APPLICATION','T_CORES_CALENDAR','T_CORES_GROUP','T_CORES_GROUP_FUNCTION','T_CORES_LIST','T_CORES_LISTTYPE',
//                            'T_CORES_OU','T_CORES_USER','T_CORES_USER_FUNCTION','T_CORES_USER_GROUP',
//                            'T_WEB_ADVERTISING','T_WEB_ADVERTISING_POSITION','T_WEB_ARTICLE','T_WEB_ARTICLE_ATTACHMENT','T_WEB_ARTICLE_COMMENT',
//                            'T_WEB_ARTICLE_RATING','T_WEB_BANNER',
//                            'T_WEB_BANNER_CATEGORY','T_WEB_CATEGORY','T_WEB_CATEGORY_ARTICLE','T_WEB_CQ','T_WEB_CQ_FIELD',
//                            'T_WEB_EVENT','T_WEB_EVENT_ARTICLE','T_WEB_GROUP_CATEGORY','T_WEB_HOMEPAGE_CATEGORY','T_WEB_MEDIA',
//                            'T_WEB_MENU','T_WEB_MENU_POSITION','T_WEB_OPTION','T_WEB_PHOTO_GALLERY','T_WEB_PHOTO_GALLERY_DETAIL','T_WEB_POLL',
//                            'T_WEB_POLL_DETAIL','T_WEB_SPOTLIGHT','T_WEB_SPOTLIGHT_POSITION','T_WEB_STATS_VISITORS','T_WEB_STICKY','T_WEB_SUBSCRIBER','T_WEB_USER_CATEGORY',
//                            'T_WEB_WEBLINK','T_WEB_WEBSITE','T_WEB_WEBSITE_THEME_WIDGET');
        
        $arr_table = array('T_CORES_APPLICATION','T_CORES_CALENDAR','T_CORES_GROUP','T_CORES_GROUP_FUNCTION','T_CORES_LIST','T_CORES_LISTTYPE',
                            'T_CORES_OU','T_CORES_USER','T_CORES_USER_FUNCTION','T_CORES_USER_GROUP',
                            );
//        $this->model->mssql_to_mysql($arr_table);
    }
    
    //update du lieu luu thua C_DEFAULT_CATEGORY and C_DEFAULT_WEBSITE
//    public function  update_extra_data()
//    {
//        $this->model->update_extra_data();
//    }
//    
    public function article_entity_endcode()
    {
        $this->model->article_entity_endcode();
    }
//    
//    public function abc()
//    {
//        $this->model->abc();
//    }
}
?>
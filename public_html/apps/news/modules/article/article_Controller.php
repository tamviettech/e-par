<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class article_Controller extends Controller {

    function __construct() {
        parent::__construct('news', 'article');
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->doc_direction = 'NEWS';

        //Kiem tra session
        session::init();
        //Kiem tra dang nhap
        session::check_login();
    }

    public function other($arg)
    {
        $this->dsp_single_article($arg);
    }

    public function main()
    {
        $this->dsp_all_article();
    }

    public function dsp_all_article()
    {
        $VIEW_DATA['txt_filter'] = isset($_POST['txt_filter']) ? $this->model->replace_bad_char($_POST['txt_filter']) : '';

        $VIEW_DATA['arr_all_article'] = $this->model->qry_all_article($VIEW_DATA['txt_filter'] );

        $this->view->render('dsp_all_article', $VIEW_DATA);
    }

    public function dsp_single_article($p_article_id)
    {
        $a = explode('-', $p_article_id);
        $v_article_id = $a[0];
        if (!( preg_match( '/^\d*$/', trim($v_article_id)) == 1 ))
        {
            $v_article_id = 0;
        }

        $arr_single_article = $this->model->qry_single_article($v_article_id);

        $v_is_edit = FALSE;
        $v_action = isset($_GET['action']) ? $this->model->replace_bad_char($_GET['action']) : '';

        if ( ($v_article_id == 0) && ($v_action=='edit') && $this->check_permission('THEM_MOI_TIN_BAI'))
        {
            $v_is_edit = TRUE;
        }

        if ( ($v_article_id > 0) && ($v_action=='edit'))
        {
            $v_create_by = $arr_single_article['C_CREATE_BY'];

            if ($v_create_by == session::get('login_name') OR $this->check_permission('SUA_TIN_BAI_DO_NGUOI_KHAC_DANG'))
            {
                $v_is_edit = TRUE;
            }
        }

        $VIEW_DATA['arr_single_article'] = $arr_single_article;
        if ($v_is_edit)
        {
            $this->view->render('dsp_single_article_edit', $VIEW_DATA);
        }
        else
        {
            $this->view->render('dsp_single_article', $VIEW_DATA);
        }
    }

    public function update_article()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->update_article();
    }

    public function delete_article()
    {

        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_article';
        $this->model->delete_article();
    }
}

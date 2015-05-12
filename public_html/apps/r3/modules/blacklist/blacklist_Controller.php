<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class blacklist_Controller extends Controller {
    function __construct()
    {
        parent::__construct('r3', 'blacklist');
        $this->view->template->show_left_side_bar = FALSE;
        
        //Kiem tra session
        session::init();
       //Kiem tra dang nhap
        session::check_login();
    }
    
    public function main()
    {
        $this->dsp_all_rule();
    }
    
    public function dsp_all_rule()
    {
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_option();
        $VIEW_DATA['arr_all_rule']          = $this->model->qry_all_rule();
        
        $this->view->render('dsp_all_rule', $VIEW_DATA);        
    }
    
    public function dsp_single_rule()
    {
        $v_rule_id = get_post_var('hdn_item_id',0);
        
        $VIEW_DATA['v_rule_id']   = $v_rule_id;
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_option();
        $VIEW_DATA['arr_single_rule']   = $this->model->qry_single_rule($v_rule_id);

        $this->view->render('dsp_single_rule', $VIEW_DATA);
    }
    
    public function update_rule()
    {
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_rule';
        $this->model->do_update_rule();
    }
    public function delete_rule()
    {
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_rule';
        $this->model->do_delete_rule();
    }
    
    public function dsp_plaintext_auto_lock_unlock()
    {
        $this->view->render('dsp_plaintext_auto_lock_unlock');
    }
    
    public function btn_update_plaintext_auto_lock_unlock()
    {
        
        $v_xml_file_path = get_post_var('hdn_xml_file_path', '',0);
        $v_xml_string         = get_post_var('txt_xml_string','<root/>',0);
        
        $ok = TRUE;
        $v_message = 'Cập nhật dữ liệu thất bại!. ';
        //Kiem tra xml welform
        $dom_flow = @simplexml_load_string($v_xml_string);
        if ($dom_flow != FALSE)
        {
            $ok = TRUE;
        }
        else
        {
            $ok = FALSE;
            $v_message .= 'XML không well-form';
        }
        
        //Ghi file
        if ($ok)
        {
            //chmod($v_xml_file_path, 777);
            $v_dir = dirname ($v_xml_file_path);
            if (!is_dir($v_dir))
            {
                @mkdir($v_dir);
            }
            $r = @file_put_contents($v_xml_file_path, $v_xml_string);
            if ($r === FALSE OR $r === 0)
            {
                $ok = FALSE;
                $v_message .= 'Không thể ghi được file dữ liệu!';
            }
        }
        
        if ($ok)
        {
            $this->model->popup_exec_done();
        }
        else
        {
            $this->model->popup_exec_fail($v_message);
        }
    }
}
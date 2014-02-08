<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class guidance_Controller extends Controller
{
    public  $get_controller_url;
    function __construct() 
    {
        parent:: __construct('guidance', 'guidance');
        $this->get_controller_url  = $this->view->get_controller_url();
        $this->view->template->get_controller_url = $this->view->get_controller_url();
    }

    public function main()
    {
        $VIEW_DATA['arr_all_guidance'] = $this->model->qry_all_linh_vuc();
        $this->view->render('dsp_all_guidance',$VIEW_DATA);
    }
    public function dsp_all_list_guidance($v_id = '')
    {
        $v_id       = intval(replace_bad_char($v_id));
        $v_keyword = isset($_REQUEST['keyword']) ? strval(replace_bad_char($_REQUEST['keyword'])) : '';
        if(!trim($v_keyword))
        {
            //neu khong co tu khoa tim kiem 
            $VIEW_DATA['arr_all_list_guidance'] = $this->model->qry_all_list_thu_tuc($v_id);
        }
        else 
        {
            // Ton tai tu khoa tim kiem
            $VIEW_DATA['arr_all_list_guidance'] = $this->model->qry_all_list_thu_tuc($v_id,$v_keyword);
            
        }
    
        $this->view->render('dsp_all_list_guidance',$VIEW_DATA);
    }
    public function dsp_single_guidance($v_id)
    {
        $v_id       = intval(replace_bad_char($v_id));
        $VIEW_DATA['arr_single_guidance'] = $this->model->qry_single_guidance($v_id);
        $this->view->render('dsp_single_guidance',$VIEW_DATA);
    } 
}
<?php

class poll_Controller extends Controller {

    function __construct() {
        parent::__construct('public_service','poll');
        
        $this->check_login();
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->arr_count_article = $this->model->gp_qry_count_article();
        $this->view->dsp_side_bar =true;
        $this->model->app_name = $this->app_name;
    }

    public function main() {
        $this->dsp_all_poll();
    }

    public function dsp_all_poll() 
    {
        $VIEW_DATA['arr_all_poll']      = $this->model->qry_all_poll();
        $this->view->layout_render('public_service/admin/dsp_layout_admin','dsp_all_poll',$VIEW_DATA);
    }
    public function dsp_single_poll($v_poll_id)
    {
        $arr_data = $this->model->qry_single_poll($v_poll_id);
        $VIEW_DATA['arr_single_poll']  = isset($arr_data['arr_single_poll'])?$arr_data['arr_single_poll']:array();
        $VIEW_DATA['arr_all_answer']   = isset($arr_data['arr_all_answer'])?$arr_data['arr_all_answer']:array();
        $this->view->layout_render('public_service/admin/dsp_layout_admin','dsp_single_poll',$VIEW_DATA);
    }
    public function swap_order()
    {
        $v_id       = get_post_var('hdn_item_id');
        $v_id_swap  = get_post_var('hdn_item_id_swap');
        $this->model->swap_order($v_id,$v_id_swap);
    }
    public function update_poll()
    {
        $this->model->update_poll();
    }
    public function delete_poll()
    {
        $this->model->delete_poll();
    }
}
?>


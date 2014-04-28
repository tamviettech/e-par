<?php

class mapping_Controller extends Controller
{
    /**
     *
     * @var \record_Model 
     */
    public $model;

    function __construct()
    {
        //Kiem tra session
        session::check_login();
        
        parent::__construct('r3', 'mapping');
        $this->view->template->show_left_side_bar = FALSE;
        $this->model->goback_url = $this->view->get_controller_url();
    }

    public function main()
    {
       $this->dsp_main();
    }
    
    public function dsp_main()
    {
        $VIEW_DATA['arr_all_mapping']     = $this->model->qry_all_mapping();
        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_record_type();
        $this->view->render('dsp_main',$VIEW_DATA);
    }  
    
    public function update_mapping()
    {
        
        $this->model->update_mapping();
    }
}
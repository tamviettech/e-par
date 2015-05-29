<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php
class main_Controller extends Controller
{

    /**
     *
     * @var \main_Model 
     */
    public $model;

    /**
     *
     * @var \view 
     */
    public $view;
    
    /**
     *
     * @var type \view
     */
    public $is_admin;
    function __construct()
    {
        //Kiem tra dang nhap
        session::check_login();
        
        parent::__construct('main', 'main');
        
        $this->is_admin = session::get('is_admin');
        
    }
    
    public function main()
    {         
        $VIEW_DATA['arr_all_application'] = $this->model->qry_all_permitted_application();
        if (count($VIEW_DATA['arr_all_application']) == 1 && $this->is_admin != 1)
        {
            ob_clean();
            header('Location: ' . SITE_ROOT . strtolower($VIEW_DATA['arr_all_application'][0]['C_CODE'] . '/' . $VIEW_DATA['arr_all_application'][0]['C_DEFAULT_MODULE']), true, ($permanent === true) ? 301 : 302);
        }
        $this->view->render('dsp_main',$VIEW_DATA);
    }
    
}

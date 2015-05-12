<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class doc_Controller extends Controller {

    private $_direction;
    private $_type;

    protected $_arr_doc_direction = array(
        'VBDEN'     => 'Văn bản đến',
        'VBDI'      => 'Văn bản đi',
        'VBNOIBO'   => 'Văn bản nội bộ',
        'VBTRACUU'  => 'Văn bản tra cứu',
    );

    function __construct() {
        parent::__construct('edoc', 'doc');
        $this->view->template->show_left_side_bar = TRUE;

        //Kiem tra session
        session::init();
        $login_name = session::get('login_name');
        if ($login_name == NULL)
        {
            session::destroy();
            header('location:' . SITE_ROOT . 'login.php');
            exit;
        }
    }

    function main(){
        $this->vbden();
    }

    public function get_direction()
    {
        return $this->_direction;
    }

    public function get_type()
    {
        return $this->_type;
    }

    private function _dsp_all_doc($direction, $type){
        $direction  = strtoupper($direction);
        $type       = strtoupper($type);

        $v_filter   = isset($_POST['txt_filter']) ? $_POST['txt_filter'] : '';

        $VIEW_DATA['direction']         = $direction;
        $VIEW_DATA['direction_text']    = $this->_arr_doc_direction[$direction];

        $VIEW_DATA['type']              = $type;
        $arr_all_doc_type_option        = $this->model->qry_all_doc_type_option($direction);
        $VIEW_DATA['type_text']         = $arr_all_doc_type_option[$type];

        $VIEW_DATA['arr_all_doc']       = $this->model->qry_all_doc($direction, $type, $v_filter);
        $VIEW_DATA['filter']            = $v_filter;

        $v_processing_filter            = isset($_POST['sel_processing']) ? $_POST['sel_processing'] : '-1';
        $VIEW_DATA['processing']        = $v_processing_filter;


        $this->view->render('dsp_all_doc', $VIEW_DATA);
    }

    private function _switch_method($direction, $type='')
    {
        $this->view->template->function_url = $this->view->get_controller_url() . $direction . '/';
        $arr_doc_type = $this->model->qry_all_doc_type_by_direction($direction);
        $this->view->template->arr_doc_type = $arr_doc_type;

        if (empty($type))
        {
            $t = array_keys($arr_doc_type);
            $type = $t[0];
        }
        $this->view->template->doc_direction = strtoupper($direction);
        $this->view->template->doc_type = $type;

        switch ($type)
        {
            case 'dsp_single_doc':
                $doc_id = isset($_REQUEST['hdn_item_id']) ? $_REQUEST['hdn_item_id'] : 0;
                $this->dsp_single_doc($doc_id);
                break;

            case 'update_doc':
                $this->update_doc();
                break;

            case 'delete_doc':
                $this->delete_doc();
                break;

            case 'dsp_add_doc_to_folder':
                $doc_id = isset($_REQUEST['hdn_item_id']) ? $_REQUEST['hdn_item_id'] : 0;
                $this->dsp_add_doc_to_folder($doc_id);
                break;

            case 'dsp_submit_doc':
                $doc_id = isset($_REQUEST['hdn_item_id']) ? $_REQUEST['hdn_item_id'] : 0;
                $this->dsp_submit_doc($doc_id);
                break;

            case 'dsp_allot_doc':
                $doc_id = isset($_REQUEST['hdn_item_id']) ? $_REQUEST['hdn_item_id'] : 0;
                $this->dsp_allot_doc($doc_id);
                break;

            case 'dsp_approve_doc':
                $doc_id = isset($_REQUEST['hdn_item_id']) ? $_REQUEST['hdn_item_id'] : 0;
                $this->dsp_approve_doc($doc_id);
                break;

            case 'dsp_exec_doc':
                $doc_id = isset($_REQUEST['hdn_item_id']) ? $_REQUEST['hdn_item_id'] : 0;
                $this->dsp_exec_doc($doc_id);
                break;

            case 'do_exec_doc':
                $doc_id = isset($_REQUEST['hdn_item_id']) ? $_REQUEST['hdn_item_id'] : 0;
                $this->model->do_exec_doc($doc_id);
                break;

            case 'delete_step_exec_doc':
                $this->model->delete_step_exec_doc();
                break;


            case 'dsp_sub_allot_doc':
                $doc_id = isset($_REQUEST['hdn_item_id']) ? $_REQUEST['hdn_item_id'] : 0;
                $this->dsp_sub_allot_doc($doc_id);
                break;

            case 'dsp_print_document_book_option':
                $this->dsp_print_document_book_option();
                break;

            case 'dsp_print_document_book':
                $this->dsp_print_document_book();
                break;

            case 'dsp_all_doc':
            default:
                $this->_dsp_all_doc($direction, $type);
                break;
        }
    }

    public function vbden($type=''){
        $this->_switch_method('vbden', $type);
    }

    public function vbdi($type=''){
        $this->_switch_method('vbdi', $type);
    }

    public function vbnoibo($type=''){
        $this->_switch_method('vbnoibo', $type);
    }

    public function vbtracuu($type='')
    {
        $v_filter               = isset($_POST['txt_filter']) ? $_POST['txt_filter'] : '';
        $v_processing_filter    = isset($_POST['sel_processing']) ? $_POST['sel_processing'] : '-1';

        $arr_filter['processing'] = $v_processing_filter;
        $arr_filter['filter']    = $v_filter;


        $arr_doc_type = $this->model->qry_all_doc_type_for_lookup();
        if (empty($type))
        {
            $t = array_keys($arr_doc_type);
            $type = $t[0];
        }

        $this->view->template->function_url = $this->view->get_controller_url() . 'vbtracuu/';
        $this->view->template->arr_doc_type = $arr_doc_type;
        $this->view->template->doc_direction = 'VBTRACUU';
        $this->view->template->doc_type = $type;

        $VIEW_DATA['direction'] = 'VBTRACUU';
        $VIEW_DATA['direction_text'] = $this->_arr_doc_direction[$VIEW_DATA['direction']];

        $VIEW_DATA['type'] = $type;
        $VIEW_DATA['type_text'] = $arr_doc_type[$type];

        $VIEW_DATA['filter']        = $v_filter;
        $VIEW_DATA['processing']    = $v_processing_filter;

        $VIEW_DATA['arr_all_lookup_doc'] = $this->model->qry_all_doc_for_lookup($type, $arr_filter);

        $this->view->render('dsp_lookup_doc', $VIEW_DATA);
    }

    public function dsp_all_doc(){
        $this->vbden();
    }

    public function dsp_single_doc($doc_id=0)
    {
        //$doc_id must be a integer
        if (!( preg_match( '/^\d*$/', trim($doc_id)) == 1 ))
        {
            $doc_id = 0;
        }

        $v_direction = isset($_REQUEST['direction']) ? $_REQUEST['direction'] : 'VBDEN';

        $VIEW_DATA['doc_id']                = $doc_id;
        $VIEW_DATA['arr_single_doc']        = $this->model->qry_single_doc($doc_id);
        $VIEW_DATA['arr_direction_text']    = $this->_arr_doc_direction;
        $VIEW_DATA['arr_type_option']       = $this->model->qry_all_doc_type_option($v_direction);

        //File dinh kem
        if ($doc_id > 0)
        {
            $arr_all_doc_file = $this->model->qry_all_doc_file($doc_id);
            $VIEW_DATA['arr_all_doc_file'] = $arr_all_doc_file;
        }
        $this->view->render('dsp_single_doc', $VIEW_DATA);
    }

    //Trinh lanh dao
    public function dsp_submit_doc($doc_id)
    {
        //$doc_id must be a integer
        if (!( preg_match( '/^\d*$/', trim($doc_id)) == 1 ))
        {
            $doc_id = 0;
        }

        $v_direction = isset($_REQUEST['direction']) ? $_REQUEST['direction'] : 'VBDEN';

        $VIEW_DATA['doc_id']                = $doc_id;
        //Danh sach CB co quyen phe duyet & phan cong thu ly (allot) van ban
        $VIEW_DATA['arr_all_allot_user']    = $this->model->qry_all_allot_user($v_direction);
        $VIEW_DATA['direction']             = $v_direction;

        $this->view->render('dsp_submit_doc', $VIEW_DATA);
    }

    //Dua VB vao HS Luu
    public function dsp_add_doc_to_folder($doc_id)
    {
        //$doc_id must be a integer
        if (!( preg_match( '/^\d*$/', trim($doc_id)) == 1 ))
        {
            $doc_id = 0;
        }

        $v_direction = isset($_REQUEST['direction']) ? $_REQUEST['direction'] : 'VBDEN';

        $VIEW_DATA['doc_id']                = $doc_id;
        //Danh sach HS chua dong
        $VIEW_DATA['arr_all_folder']        = $this->model->qry_all_folder(session::get('user_id'));
        $VIEW_DATA['direction']             = $v_direction;

        $this->view->render('dsp_add_doc_to_folder', $VIEW_DATA);
    }

    public function update_doc()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->update_doc();
    }

    public function delete_doc(){
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->delete_doc();
    }

    //
    public function do_submit_doc()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->do_submit_doc();
    }

    public function do_add_doc_to_folder()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->do_add_doc_to_folder();
    }

    public function dsp_allot_doc($doc_id)
    {
       //$doc_id must be a integer
        if (!( preg_match( '/^\d*$/', trim($doc_id)) == 1 ))
        {
            $doc_id = 0;
        }
        $VIEW_DATA['doc_id'] = $doc_id;

        //Danh sach phong ban
        $VIEW_DATA['arr_all_ou_option'] = $this->model->qry_all_ou_option();

        //Danh sach CB co nhiem vu phan phoi van ban cua phong
        $VIEW_DATA['arr_all_monitor_user'] = $this->model->qry_all_monitor_user();

        //Danh sach CB co quyen phe duyet & phan cong thu ly (allot) van ban
        $VIEW_DATA['arr_all_allot_user'] = $this->model->qry_all_allot_user();

        //Danh sach CB co thu ly van ban
        $VIEW_DATA['arr_all_exec_user'] = $this->model->qry_all_exec_user();

        $this->view->render('dsp_allot_doc', $VIEW_DATA);
    }

    public function do_allot_doc()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->do_allot_doc();
    }

    public function dsp_approve_doc($doc_id)
    {
        if (!( preg_match( '/^\d*$/', trim($doc_id)) == 1 ))
        {
            $doc_id = 0;
        }
        $VIEW_DATA['doc_id'] = $doc_id;


        $this->view->render('dsp_approve_doc', $VIEW_DATA);
    }



    public function do_approve_doc()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->do_approve_doc();
    }

    private function dsp_exec_doc($doc_id)
    {
        if (!( preg_match( '/^\d*$/', trim($doc_id)) == 1 ))
        {
            $doc_id = 0;
        }

        $VIEW_DATA['arr_single_doc']        = $this->model->qry_single_doc($doc_id);
        $VIEW_DATA['doc_id'] = $doc_id;


        $this->view->render('dsp_exec_doc', $VIEW_DATA);
    }
    public function do_exec_doc()
    {

    }

    private function dsp_sub_allot_doc($doc_id)
    {
        //Lay danh sach CB trong phong
        $VIEW_DATA['doc_id'] = $doc_id;
        $VIEW_DATA['arr_all_exec_user_in_ou'] = $this->model->qry_all_exec_user_in_ou();
        $this->view->render('dsp_sub_allot_doc', $VIEW_DATA);

    }

    public function do_sub_allot_doc()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->do_sub_allot_doc();
    }

    /*
    public function test1M()
    {
        $this->model->test1M();
    }
     * */

    public function get_next_doc_seq()
    {
        $v_direction = isset($_REQUEST['direction']) ? $_REQUEST['direction'] : 'VBDEN';
        $v_type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

        $v_current_no = $this->model->get_next_doc_seq($v_direction, $v_type);

        echo $v_current_no + 1;
    }

    public function dsp_print_document_book_option()
    {
        $this->view->render('dsp_print_document_book_option');
    }

    public function dsp_print_document_book()
    {

        $v_direction    = $this->model->replace_bad_char($_REQUEST['direction']);
        $v_type         = $this->model->replace_bad_char($_REQUEST['type']);
        $v_begin_date   = $this->model->replace_bad_char($_REQUEST['begin_date']);
        $v_end_date     = $this->model->replace_bad_char($_REQUEST['end_date']);

        $arr_all_doc_type_option        = $this->model->qry_all_doc_type_option($v_direction);

        $VIEW_DATA['direction']     = $v_direction;
        $VIEW_DATA['type']          = $v_type;
        $VIEW_DATA['begin_date']    = $v_begin_date;
        $VIEW_DATA['end_date']      = $v_end_date;
        $VIEW_DATA['arr_all_doc_type_option']      = $arr_all_doc_type_option;

        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
        $v_end_date   = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
        $VIEW_DATA['arr_all_doc_for_print'] = $this->model->qry_all_doc_for_print($v_direction, $v_type, $v_begin_date, $v_end_date);

        $this->view->render('dsp_print_document_book', $VIEW_DATA);
    }

    public function remove_doc_folder()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->remove_doc_folder();
    }

    public function dsp_all_doc_to_add()
    {
        $v_filter = isset($_POST['txt_filter']) ? $this->model->replace_bad_char($_POST['txt_filter']) : '';

        $VIEW_DATA['txt_filter'] = $v_filter;
        $VIEW_DATA['arr_all_doc_to_add']  = $this->model->qry_all_doc_to_add($v_filter);

        $this->view->render('dsp_all_doc_to_add', $VIEW_DATA);
    }
}

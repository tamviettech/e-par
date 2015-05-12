<?php

defined('DS') or die();

/**
 * @package chat
 * @author Duong Tuan Anh <goat91@gmail.com>
 */
class reportbook_Controller extends Controller
{
    /**
     *
     * @var \reportbook_Model 
     */
    public $model;

    /**
     *
     * @var \view 
     */
    public $view;

    function __construct()
    {
        parent::__construct('r3', 'reportbook');

        session::init();
        //Kiem tra dang nhap
        session::check_login();
        
        $is_admin       = Session::get('is_admin');
        $has_permission = check_permission('QUAN_TRI_SO_THEO_DOI_HO_SO', 'R3');
        ($is_admin OR $has_permission) OR die('Bạn không có quyền truy cập chức năng này');

        //tao array role tu r3_const
        $this->_arr_roles = json_decode(CONST_ALL_R3_ROLES,true);
        $this->view->template->show_left_side_bar = TRUE;
        $this->view->active_role = _CONST_BAO_CAO_ROLE;
        $this->view->template->active_role = _CONST_BAO_CAO_ROLE;
        $this->view->template->active_role = _CONST_BAO_CAO_ROLE;
        //tao controller url record cho menu
        
       
        //Danh muc bao cao
        $this->arr_all_report_type                 = $this->model->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_BAO_CAO, CONST_USE_ADODB_CACHE_FOR_REPORT);
        $this->view->template->arr_all_report_type = $this->arr_all_report_type;
        $this->view->template->current_report_type = '';
        //Mở rộng menu của report
        $this->view->template->controller_url      = $this->view->get_controller_url('record', 'r3');
        $this->view->template->reportbook_url      = $this->view->get_controller_url();
        
        
    }

    function main()
    {
        $data['arr_all_books'] = $this->model->qry_all_book();
        $this->view->render('dsp_main', $data);
    }

    private function _get_xml_template($book_code)
    {
        $book_code  = strtolower($book_code);
        $file_parts = array('apps', 'r3', 'xml-config', 'common');
        echo $file       = SERVER_ROOT . implode(DS, $file_parts) . DS . $book_code . '_book.xml';
        if (file_exists($file))
        {
            return file_get_contents($file);
        }
        return false;
    }

    function dsp_single_book($book_id =0)
    {
        if($book_id == 0)
        {
            $book_id =  get_request_var('sel_books',0);
        }
        $this->dsp_all_record_by_book($book_id);
    }

    function dsp_all_record_types($book_id)
    {
        $VIEW_DATA['arr_single_book'] = $this->model->qry_single_book($book_id);
    }

    /**
     * $_GET[begin_date]
     * $_GET[end_date]  
     * $_GET[file_type]  
     * $_GET[request_type] <ul><li>create</li><li>download</li></ul>
     * @param type $book_id
     */
    function export_book($book_id)
    {
        $v_begin_date = get_request_var('begin_date', date('1-1-Y'));
        $v_end_date   = get_request_var('end_date', date('31-12-Y'));
        $file_type    = get_request_var('file_type');
        $request_type = strtolower(get_request_var('request_type', 'create'));

        $data['arr_single_book'] = $this->model->qry_single_book($book_id);
        $data['arr_all_record']  = $this->model->qry_all_records($book_id, $v_begin_date, $v_end_date, false);
        $data['book_id']         = $book_id;
        $data['begin_date']      = $v_begin_date;
        $data['end_date']        = $v_end_date;
        $data['file_type']       = $file_type;

        $book_code                     = $data['arr_single_book']['c_code'];
        $filename                      = "{$book_code}_{$v_begin_date}_{$v_end_date}.{$file_type}";
        $data['filename']              = $filename;
        $temp_name                     = md5($filename . date('YmdH'));
        $temp_file                     = dirname(__FILE__) . DS . 'temp' . DS . $temp_name . '.tmp';
        $data['temp_name']             = $temp_name;
        $data['temp_file']             = $temp_file;
        $data['download_request_type'] = 'download';
        switch ($request_type)
        {
            case 'create':
                ignore_user_abort(1);
                // let's free the user, but continue running the
                // script in the background
                ob_end_clean();
                ob_start();
                //Kiểm tra nếu in PDF quá 2 tuần báo lỗi
                $date_inv = date_diff(date_create_from_format('d-m-Y', $v_begin_date), date_create_from_format('d-m-Y', $v_end_date))->format('%d');
                if($date_inv > 14 && $file_type == 'pdf'){
                    $data['error'] = 'Hiện tại chỉ hỗ trợ kết xuất dữ liệu trong vòng 2 tuần dưới dạng PDF!';
                }
                $this->view->render('export_book', $data);
                header('Connection: close');
                header('Content-length: ' . ob_get_length());
                ob_end_flush();
                flush();
                //create file if not exist
                if (!file_exists($temp_file) && !isset($data['error']))
                {
                    set_time_limit(0);
                    $func_name = "export_type_$file_type";
                    //avoid direct ouput
                    ob_start();
                    $this->$func_name($data);
                    //make sure directory exists
                    if (!is_dir(dirname($temp_file)))
                    {
                        mkdir(dirname($temp_file));
                    }
                    file_put_contents($temp_file, ob_get_clean());
                }
                break;
            case 'download':
                if (!DEBUG_MODE)
                {
                    // We'll be outputting
                    header("Content-type: application/$file_type");
                    // It will be called downloaded.pdf
                    header('Content-Disposition: inline; filename="' . $filename . '"');
                }
                //get file
                if (file_exists($temp_file))
                {
                    readfile($temp_file);
                    //unlink($temp_file);
                }
                break;
            default:
                echo 'invalid $_GET[request_type]';
        }
    }

    function svc_is_download_ready($temp_name)
    {
        $temp_file = dirname(__FILE__) . DS . 'temp' . DS . $temp_name . '.tmp';
        echo json_encode(file_exists($temp_file));
    }

    function dsp_all_record_type($book_id)
    {
        $VIEW_DATA['arr_single_book'] = $this->model->qry_single_book($book_id);
        if (!$VIEW_DATA['arr_single_book'])
        {
            die('Đối tượng không tồn tại');
        }
        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_record_type($VIEW_DATA['arr_single_book']['c_code']);
        $VIEW_DATA['book_id']             = $book_id;

        $this->view->render('dsp_all_record_type', $VIEW_DATA);
    }

    public function dsp_all_record_by_book($book_id)
    {
        $book_id = (int)$book_id;
        $v_begin_date = get_request_var('txt_begin_date', date('1-1-Y'));
        $v_end_date   = get_request_var('txt_end_date', date('31-12-Y'));
        
        $VIEW_DATA['arr_all_books']   = $this->model->qry_all_book();
        $VIEW_DATA['arr_all_record']  = $this->model->qry_all_records($book_id, $v_begin_date, $v_end_date);
        $VIEW_DATA['arr_single_book'] = $this->model->qry_single_book($book_id);
        $VIEW_DATA['arr_all_scope']   = $this->model->qry_all_scope();
        $VIEW_DATA['book_id']         = $book_id;
        $VIEW_DATA['begin_date']      = $v_begin_date;
        $VIEW_DATA['end_date']        = $v_end_date;
        
        $this->view->render('dsp_all_record_by_book', $VIEW_DATA);
    }

    function delete_record_type_from_book()
    {
        $this->model->delete_record_type_from_book();
    }

    function insert_record_type_to_book()
    {
        $this->model->insert_record_type_to_book();
    }

    function dsp_all_record_type_to_add($book_id)
    {
        $VIEW_DATA['book_id']             = $book_id;
        $VIEW_DATA['arr_single_book']     = $this->model->qry_single_book($book_id);
        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_record_type_to_add($VIEW_DATA['arr_single_book']['c_code']);
        $this->view->render('dsp_all_record_type_to_add', $VIEW_DATA);
    }

    private function export_type_xls($data)
    {
        $this->export_spreadsheet_abstract($data, 'xls');
    }

    private function export_type_cvs($data)
    {
        $this->export_spreadsheet_abstract($data, 'cvs');
    }

    private function export_type_pdf($data)
    {
        $this->view->render('export_pdf', $data);
    }

    private function export_spreadsheet_abstract($data, $type)
    {
        $class_names          = array(
            'xls' => 'PHPExcel_Writer_Excel5',
            'cvs' => 'PHPExcel_Writer_CSV'
        );
        $data['writer_class'] = $class_names[$type];
        $this->view->render('export_spread_sheet', $data);
    }

}
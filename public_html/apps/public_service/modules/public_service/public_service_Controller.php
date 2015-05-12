<?php

/**
 * Description of public_service_Controller
 *
 * @author Tam Viet
 */
class public_service_Controller extends Controller
{
    function __construct()
    {
        parent::__construct('public_service', 'public_service');       
        
        $this->view->DATETIME_NOW        = $this->model->get_datetime_now();
        $this->view->role_text           = json_decode(CONST_ALL_PUBLIC_SERVICE_ROLES,true);
        
    }   
    public function main()
    {
        $this->dsp_home_page();
    }
    /**
     * Tải file đính kèm của tin bài
     * @return type
     */
    public function attach_dowload()
    {
        $file_name  = isset($_REQUEST['file']) ? $_REQUEST['file'] : '';
        $dir_file = CONST_SERVER_UPLOADS_ROOT.$file_name;
        if(is_file($dir_file))
        {
            $file_basename  = basename($dir_file);
            $item           = explode('.', $file_basename);
            array_pop($item);
            $file_name      = isset($item[0]) ? trim($item[0]) : '';
            if($file_name == '')
            {
                echo 'Tập tin đã bị xóa hoặc xảy ra lỗi liên kết xin vui lòng kiểm tra lại hoặc liên lạc với nhà cung cấp dịch vụ';
                return FALSE;
            }
            ob_start();
            ob_end_flush();
            header('Content-Disposition:attachment;filename='.$file_basename);
            exit();
        }
        echo 'Tập tin đã bị xóa hoặc xảy ra lỗi liên kết xin vui lòng kiểm tra lại hoặc liên lạc với nhà cung cấp dịch vụ';
        return FALSE;

    }
    private function default_data()
    {
        $VIEW_DATA = array();
        $VIEW_DATA['arr_web_link'] = $this->model->qry_all_web_link();
        return $VIEW_DATA;
    }
    public function dsp_print_article()
    {
        $category_id                     = get_request_var('category_id', 0);
        $article_id                      = get_request_var('article_id', 0);
        $VIEW_DATA['arr_single_article'] = $this->model->qry_single_article($category_id, $article_id);

        $this->view->layout_render('public_service/panel/dsp_layout_pop_win','dsp_print_article', $VIEW_DATA);
    }
    
    
    
    public function dsp_home_page()
    {
        $resluts = $this->default_data();        
        $this->view->arr_web_link = $resluts['arr_web_link'];
        $this->view->menu_active = __FUNCTION__;
        
        //        ----
        $this->view->title = 'Trang chủ';
        $VIEW_DATA['arr_all_sticky']    = $this->model->gp_qry_all_sticky();
        
        //lay min max year
        $VIEW_DATA['arr_year']           = $this->model->get_year();       
        $VIEW_DATA['arr_single_poll']    = $this->model->qry_single_poll();
        $v_poll_id = isset($VIEW_DATA['arr_single_poll']['PK_POLL']) ? $VIEW_DATA['arr_single_poll']['PK_POLL'] : 0;
        $VIEW_DATA['arr_all_opt']        = $this->model->qry_all_poll_detail($v_poll_id);
        
        $this->view->layout_render('public_service/panel/dsp_layout', 'dsp_home_page',$VIEW_DATA);
    }
    /**
     * Chi tiet tin bai
     */
    public function dsp_single_article()
    {
        $resluts = $this->default_data();        
        $this->view->arr_web_link = $resluts['arr_web_link'];
        $this->view->menu_active = __FUNCTION__;
        #------------------
        
        $category_id                  = (int) get_request_var('category_id', 0);
        $article_id                   = (int) get_request_var('article_id', 0);

        $VIEW_DATA['arr_single_article'] = $this->model->qry_single_article($category_id, $article_id);
        $v_tags                          = get_array_value($VIEW_DATA['arr_single_article'], 'C_TAGS');
        
        //tin cung su kien
//        $VIEW_DATA['arr_all_category']   = $this->model->qry_all_category();
        
        //lay danh sach tin bai lien quan moi post
        $VIEW_DATA['arr_all_connection_article'] = $this->model->qry_all_connection_article($category_id,$article_id);
         if(sizeof($VIEW_DATA['arr_all_connection_article']) < COUNT_LIMIT_ARTICLE_CONNECT)
         {
             $VIEW_DATA['arr_all_article_khac'] = $this->model->get_article_khac($VIEW_DATA['arr_all_connection_article'],$article_id);
         }
        $VIEW_DATA['arr_single_poll']    = $this->model->qry_single_poll();
        $v_poll_id = isset($VIEW_DATA['arr_single_poll']['PK_POLL']) ? $VIEW_DATA['arr_single_poll']['PK_POLL'] : 0;
        $VIEW_DATA['arr_all_opt']        = $this->model->qry_all_poll_detail($v_poll_id);
        $this->view->layout_render('public_service/panel/dsp_layout', 'dsp_single_article',$VIEW_DATA);
    }


    public function handle_widget_poll($args = '')
    {
        $v_poll_id           = (int) get_request_var('pid');
        $v_choice            = (int) get_request_var('aid');
        if (empty($_POST) && !Cookie::get('WIDGET_POLL_' . $v_poll_id))
        {
            $this->view->render('handle_widget_poll');
        }
        else
        {
            //kiem tra da vote
            if (Cookie::get('WIDGET_POLL_' . $v_poll_id))
            {
                $this->dsp_poll_result($v_poll_id);
                return;
            }
            
            
            //include captcha
            $captcha_url = SERVER_ROOT.'apps/public_service/captcha/';
            require $captcha_url.'securimage.php';
            //kiem tra captcha
            $securimage = new Securimage();
            if ($securimage->check($_POST['txt_captcha_code']) == FALSE) 
            {
                $str = __('captcha not valid');
                $url = $this->view->get_controller_url() . 'handle_widget_poll'
                        . "&code=poll&pid=$v_poll_id&aid=$v_choice";
                $this->model->exec_fail($url, $str);
                exit;
            }

            $this->model->handle_widget_poll($v_poll_id, $v_choice);
            $this->dsp_poll_result($v_poll_id);
            return;
        }
    }

    
    
    public function dsp_poll_result($poll_id)
    {
        $poll_id                 = (int) $poll_id;
        $data['arr_single_poll'] = $this->model->qry_single_poll($poll_id);
        $data['arr_all_opt']     = $this->model->qry_all_poll_detail($poll_id);
        if (!$data['arr_single_poll'])
        {
            die(__('this object is nolong available'));
        }
        $this->view->layout_render('public_service/panel/dsp_layout_pop_win','dsp_poll_result', $data);   
    }

    /**
     * Lay du lieu hien thị bieu do pie
     * @param int $year
     */
    public function arp_get_record_process($year ='')
    {
         $year = replace_bad_char($year);
         if(DEBUG_MODE >10)
         {
             $this->model->db->debug =1;
         }
         $arr_record_progress        = $this->model->qry_record_progress($year);
         echo json_encode($arr_record_progress);
    }
    
    /**
     * Lay du lieu hien thi bieu do bar
     * @param int $year
     */
    public function arp_get_record_receive_respond($year ='')
    {
        $year = replace_bad_char($year);
        $arr_record_receive_respond = $this->model->qry_record_receive_respond($year);
        if(DEBUG_MODE >10)
        {
            $this->model->db->debug =1;
        }
        echo json_encode($arr_record_receive_respond);
    }
    
    public function dsp_search()
    {
        $resluts = $this->default_data();
            
        $this->view->arr_web_link = $resluts['arr_web_link'];
        $this->view->menu_active = __FUNCTION__;
        
        //----
        $this->view->title                     = 'Tra cứu thủ tục hành chính';
        $VIEW_DATA['arr_record_listtype']      = $this->model->qry_all_record_listtype();
        $v_record_list                         = get_post_var('sel_record_list',0);
        $VIEW_DATA['arr_all_record_type']      = $this->model->qry_all_record_type($v_record_list);
        
        $v_record_type_code                    = get_post_var('txt_record_type_code','');
        $VIEW_DATA['v_record_type_code']       = $v_record_type_code;
        
        $VIEW_DATA['arr_all_record']           = $this->model->qry_record_filter($v_record_type_code);
        
       
        $record_id = isset($VIEW_DATA['arr_all_record'][0]['PK_RECORD']) ? $VIEW_DATA['arr_all_record'][0]['PK_RECORD'] : 0;
        $VIEW_DATA['resluts_filter'] = $this->_record_statistics($record_id);
        
        $this->view->layout_render('public_service/panel/dsp_layout', 'dsp_search_page',$VIEW_DATA);
        
    }
    
    private function _record_statistics($record_id = 0)
    {
        is_id_number($record_id) OR $record_id = 0;
        if($record_id == 0)  return array();
        //Thông tin bản khai
        $VIEW_DATA['arr_single_record'] = $this->model->qry_single_record($record_id);

        $dom_processing = simplexml_load_string($VIEW_DATA['arr_single_record']['C_XML_PROCESSING']);

        //Thong tin tien do
        $v_record_type = $VIEW_DATA['arr_single_record']['C_RECORD_TYPE_CODE'];
        
        if ($VIEW_DATA['arr_single_record']['C_CLEAR_DATE'] == NULL)
        {
            $dom_step = simplexml_load_file($this->view->get_xml_config($v_record_type, 'workflow'));
        }
        else
        {
            $dom_step = simplexml_load_string($VIEW_DATA['arr_single_record']['C_XML_WORKFLOW']);
        }
        $dom_workflow   = $dom_step;
        $r              = xpath($dom_step, '//step/@time');
        $step_days_list = '';
        foreach ($r as $time)
        {
            $step_days_list .= ($step_days_list != '') ? ";$time" : $time;
        }
        $arr_step_formal_date              = $this->model->formal_record_step_days_to_date($record_id, $step_days_list);
        $VIEW_DATA['arr_step_formal_date'] = $arr_step_formal_date;

        //Lay danh sach step, ngay bat dau, ket thuc thuc te cua step, số ngày đã tiêu tốn thực tế để hoàn thành step
        $steps                            = xpath($dom_workflow, "//step[not(@no_chain = 'true')]");
        $index                            = -1;
        $arr_step_infact_formal_days_diff = array();
        foreach ($steps as $step)
        {
            $index++;
            $v_first_task_code = strval($step->task[0]->attributes()->code);
            $v_last_task_code  = strval($step->task[sizeof($step->task) - 1]->attributes()->code);

            //So ngay phai hoan thanh step theo quy dinh
            $v_formal_step_time = floatval(str_replace(',', '.', $step->attributes()->time));

            //=>Ngay bat dau thuc te cua step, la ngay thuc hien prev_task
            //$v_prev_task_code         = get_xml_value($dom_workflow, "//task[contains(@next,'$v_first_task_code')]/@code");
            //$v_step_begin_date_infact = get_xml_value($dom_processing, "//step[@code='$v_prev_task_code']/datetime");
            $v_step_begin_date_infact = '';
            foreach (xpath($dom_processing, "//step") as $item)
            {
                if (strval($item->attributes()->code) == $v_first_task_code)
                {
                    break;
                }
                else
                {
                    $v_step_begin_date_infact = (string) $item->datetime;
                }
            }

            //=>Ngay ket thuc thuc te cua step
            $v_step_end_date_infact = get_xml_value($dom_processing, "//step[@code='$v_last_task_code']/datetime");

            //=>Số ngày làm việc tiêu tốn thực tế để hoàn thành step
            //$v_step_spent_days_infact       = $this->model->days_between_two_date($v_step_begin_date_infact, $v_step_end_date_infact);
            $begin_count              = false;
            $begin_count_2            = true;
            $v_step_spent_days_infact = 0;
            $v_start                  = '';
            $v_end                    = '';
            $v_next_step              = isset($steps[$index + 1]) ? $steps[$index + 1] : null;
            $v_next_step_first_task   = '';
            if ($v_next_step)
            {
                $v_next_step_first_task = isset($v_next_step->task[0]) ? strval($v_next_step->task[0]->attributes()->code) : '';
            }
            foreach ($dom_processing->children() as $item)
            {
                if ($item->getName() == 'step')
                {
                    $v_end      = (string) $item->datetime;
                    $v_date_inv = $this->model->days_between_two_date($v_start, $v_end);
                    if ((string) $item->attributes()->code == $v_first_task_code)
                    {
                        $begin_count = true;
                    }
                    elseif ((string) $item->attributes()->code == $v_next_step_first_task)
                    {
                        break;
                    }
                    if (strpos((string) $item->attributes()->code, _CONST_THONG_BAO_BO_SUNG_ROLE) !== false)
                    {
                        $begin_count_2 = false;
                    }
                    if ($begin_count && $begin_count_2)
                    {
                        $v_step_spent_days_infact += $v_date_inv;
                    }
                    $v_start = (string) $item->datetime;
                }
                elseif ($item->getName() == 'action' && $item->attributes()->do == 'unpause')
                {
                    $begin_count_2 = true;
                }
            }
            //=> Step nhanh hay cham bao nhieu ngày?            
            $v_step_infact_formal_days_diff = $v_formal_step_time - $v_step_spent_days_infact;

            $arr_step_infact_formal_days_diff[md5($v_last_task_code)] = $v_step_infact_formal_days_diff;
        }
        $VIEW_DATA['arr_step_infact_formal_days_diff'] = $arr_step_infact_formal_days_diff;
        //Tai lieu mem
        $VIEW_DATA['arr_all_record_file'] = $this->model->qry_all_record_file($record_id);
        //Tai lieu
        $VIEW_DATA['arr_all_doc'] = $this->model->qry_all_record_doc($record_id);
       
        //----
        $this->view->title = 'Tra cứu thủ tục hành chính';
        return $VIEW_DATA;
    }
    public function dsp_guidance()
    {
        $resluts = $this->default_data();
        
        $this->view->arr_web_link = $resluts['arr_web_link'];
        $this->view->menu_active = __FUNCTION__;
        //----
        $this->view->title = 'Hướng dẫn thủ tục hành chính';

        //Lay danh sach linh vuc
        $VIEW_DATA['arr_all_list']          = $this->model->qry_all_record_listtype();
        //Lay danh sách thủ tục theo mã lĩnh vực. dùng cho filter
        $v_record_list                      = isset($_REQUEST['sel_record_list']) ? $_REQUEST['sel_record_list'] : 0; 
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type($v_record_list);
        
        //Lay danh sach tất cả các thu tục 
        $VIEW_DATA['arr_all_list_guidance'] = $this->model->qry_all_record_type_guidance();
      
        $this->view->layout_render('public_service/panel/dsp_layout', 'dsp_guidance',$VIEW_DATA);
    }
    //tải file về danh cho hướng dẫn thủ tục
    public function download()
   {
       $v_file_name          = get_request_var('file_name','',TRUE);
       $v_record_type_code   = get_request_var('record_code','',TRUE);
       $v_name               = get_request_var('name','',TRUE);
       if(trim($v_record_type_code) != '')
       {   
           $dir_path = CONST_TYPE_FILE_UPLOAD . 'template_files_types';
           if(!is_dir($dir_path))die('Đường dẫn không chính xác vui lòng kiểm tra lại! Hoặc liện hệ với nhà cung cấp để biết thêm chi tiết!');
           if(trim($v_file_name) != '')
           {
               //dowload tung file
                foreach (scandir($dir_path) as $item )
                {
                    if($v_file_name == md5($item) && $item != '.' && $item != '..')
                    {
                        $dir_path_file = CONST_TYPE_FILE_UPLOAD . 'template_files_types' . DS . $item;
                        if(is_file($dir_path_file))
                        {
                            if (file_exists($dir_path_file)) 
                            {
                                 header('Content-Description: File Transfer');
                                 header('Content-Type: application/octet-stream');
                                 header('Content-Disposition: attachment; filename='.basename($v_name));
                                 header('Expires: 0');
                                 header('Cache-Control: no-cache');
                                 header('Pragma: public');
                                 header('Content-Length: ' . filesize($dir_path_file));
                                 ob_clean();
                                 flush();
                                 readfile($dir_path_file);
                                 exit();
                             }
                       }
                       else
                       {
                           die('Tập tin bạn tải về không tồn tại hoặc đã bị xóa xin vui lòng liên hệ nhà cung cấp dịch vụ!');
                       }
                    }
                }
           }
        }
    }      
    
    
    //Lấy danh sách file hướng dẫn theo mã thủ tục 
    private function _get_all_file_guidance($record_type_id)
    {
        $record_type_id = replace_bad_char($record_type_id);
        $arr_all_file = array();
        $xml_data = $this->model->db->GetOne("select C_XML_DATA from t_r3_record_type where PK_RECORD_TYPE = $record_type_id");
        
        if(trim($xml_data) == '' OR $xml_data == NULL )
        {
            return;
        }
        
        $dom          = simplexml_load_string($xml_data);
        
        $v_xpath      = '//data/media/file/text()';
        $r            = $dom->xpath($v_xpath);
        $arr_all_file = array();
        foreach ($r as $item)
        {
            $item = (string)$item ;
            if(trim($item) != '' && $item != NULL)
            {   
                $v_path_file = CONST_TYPE_FILE_UPLOAD . 'template_files_types' .DS . $item;
                                
                if(is_file($v_path_file))
                {
                    $arr_string = explode('_', $item,2);
                    $key_file   = isset($arr_string[0]) ? $arr_string[0] : '';
                    $arr_all_file[$item]['name']      = isset($arr_string[1]) ? $arr_string[1] : '';
                    $arr_all_file[$item]['file_name'] =  $item;
                    $arr_all_file[$item]['path']      =  $v_path_file;
                    $arr_all_file[$item]['type']      = filetype($v_path_file);
                }
            }            
        }
        return $arr_all_file;
    }
    // cần xóa chưa xóa
    public function _get_file_type($file_name = '')
    {
        $file_name = trim($file_name);
        $arr_item   = explode('.',$file_name);
        return end($arr_item);
    }
    
    /**
     * Lấy chi tiết thông tin hướng dẫn của thủ tục
     * @param int $v_id ma thủ tục
     */
    public function dsp_single_guidance($v_id)
    {
        $resluts = $this->default_data();
        
        $this->view->arr_web_link = $resluts['arr_web_link'];
        $this->view->menu_active = __FUNCTION__;
        //----
    
        $this->view->title = 'Tiếp nhận hồ sơ';
        $v_id       = intval(replace_bad_char($v_id));
        $VIEW_DATA['arr_single_guidance'] = $this->model->qry_single_record_type($v_id);
        $this->view->layout_render('public_service/panel/dsp_layout', 'dsp_single_guidance',$VIEW_DATA);
    }
    
    /**
     * Lây tất cả danh sách thủ tục cấp độ >3(được đăng ký trực tuyến)
     */
    public function dsp_registration_list()
    {
        $resluts = $this->default_data();
        
        $this->view->arr_web_link = $resluts['arr_web_link'];
        $this->view->menu_active = __FUNCTION__;
        //----
   
        $this->view->title                  = 'Danh sách thủ tục đăng ký online';
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_record_type_send_internet(); 
        //Lấy danh sách file đính kèm ứng với từng thủ tục
        
        for($i = 0; $i < sizeof($VIEW_DATA['arr_all_record_type']); $i ++)
        {
            $v_XML_DATA     = $VIEW_DATA['arr_all_record_type'][$i]['C_XML_DATA'];
            
            if(sizeof($v_XML_DATA) <= 0)
            {
                continue;
            }
            
            $dom = simplexml_load_string($v_XML_DATA,'SimpleXMLElement',LIBXML_NOCDATA);
            $x_path = "//item";
            $arr_item = $dom->xpath($x_path);
            for($j =0;$j< sizeof($arr_item);$j ++)
            {
                $v_record_type_id     = (string) $arr_item[$j]->attributes()->PK_RECORD_TYPE;
                $VIEW_DATA['arr_all_record_type'][$i]['all_file'][$v_record_type_id] = $this->_get_all_file_guidance($v_record_type_id);
              
            }
        }
        $this->view->layout_render('public_service/panel/dsp_layout', 'dsp_registration_list',$VIEW_DATA);
    }
    
    /**
     * Thông kê
     */
    public function dsp_statistic()
    {
        $resluts = $this->default_data();
        
        $this->view->arr_web_link = $resluts['arr_web_link'];
        $this->view->menu_active = __FUNCTION__;
        
        //----
        $this->view->title = 'Bảng theo dõi thống kê';
        $VIEW_DATA['arr_year']   = $this->model->get_year();     
        $this->view->layout_render('public_service/panel/dsp_layout', 'dsp_statistic',$VIEW_DATA);
    }
   
    //Chỉnh sửa lại định dạng size của file .   cần xóa chưa xóa
    private function _get_file_size_unit($file_size) {
        switch (true) {
            case ($file_size / 1024 < 1) :
                return intval($file_size) . " Bytes";
                break;
            case ($file_size / 1024 >= 1 && $file_size / (1024 * 1024) < 1) :
                return intval($file_size / 1024) . " KB";
                break;
            default:
                return intval($file_size / (1024 * 1024)) . " MB";
        }
    }


    function vn_str_filter ($str){

       $unicode = array(

           'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',

           'd'=>'đ',

           'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',

           'i'=>'í|ì|ỉ|ĩ|ị',

           'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',

           'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',

           'y'=>'ý|ỳ|ỷ|ỹ|ỵ',

           'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',

           'D'=>'Đ',

           'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',

           'I'=>'Í|Ì|Ỉ|Ĩ|Ị',

           'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',

           'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',

           'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',

       );
      foreach($unicode as $nonUnicode=>$uni){
           $str = preg_replace("/($uni)/i", $nonUnicode, $str);
      }
       return $str;
   }
}
?>

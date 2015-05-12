<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); 
require SERVER_ROOT . 'libs/email_reader.php';

$dir_ws_model = SERVER_ROOT. 'apps' . DS . 'r3' . DS . 'modules' . DS . 'webservices' . DS . 'webservices_Model.php';
require $dir_ws_model;

$r3_view = SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'functions.php';
require $r3_view;

$r3_const = SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'r3_const.php';
require $r3_const;

require_once(SERVER_ROOT . "libs/nusoap/nusoap.php");

class webservices_Controller
{
    private $dir;
    private $ou_name;
    function __construct($app, $module) {
        $this->dir = SERVER_ROOT . DS . 'apps' . DS . $app . DS . 'modules' . DS . $module . DS;
        $this->ou_name = CONST_MY_OU_NAME;
    }
    public function main()
    {
        //đọc mail và lấy file đính kèm của đơn vị
        function read_mail($title_mail, $ou, $record_id,$type="EXCHANGE_RECORD")
        {
            $controller = factory_services::make_controller();
            $result = $controller->read_mail($title_mail, $ou, $record_id,$type);
            return $result;
        }
        
        //gửi hồ sơ liên thông
        function exchange_record($ou, $record_type, $record_id)
        {
            $controller = factory_services::make_controller();
            $result = $controller->exchange_record($ou, $record_type,$record_id);
            return $result;
        }
        //receive_internet_record
        function receive_internet_record($ou, $record_id)
        {
            $controller = factory_services::make_controller();
            $result = $controller->receive_internet_record($ou, $record_id);
            return $result;
        }
        
        //nhận kết quả liên thông
        function receive_exchange_result($result,$record_id,$v_record_type_from)
        {
            $controller = factory_services::make_controller();
            $result = $controller->receive_exchange_result($result,$record_id,$v_record_type_from);
            return $result;
        }
        
        //tiến độ xử lý của 1 hồ sơ
        function statistics($record_id_from, $index)
        {
            $controller = factory_services::make_controller();
            $result = $controller->statistics($record_id_from, $index);
            return $result;
        }
        
        //bao cao tien do xu ly ho so cua don vi
        function progress_report()
        {
            $controller = factory_services::make_controller();
            $result = $controller->progress_report();
            return $result;
        }
        
        function r3_staff()
        {
            $controller = factory_services::make_controller();
            $result = $controller->r3_staff();
            return $result;
        }
        
        $server = new soap_server();
        $server->soap_defencoding = 'UTF-8';
        $server->decode_utf8 = false;
        $server->encode_utf8 = true;
        $endpoint = FULL_SITE_ROOT . 'r3/webservices';
        
        $server->configureWSDL('Soap Services Mot cua dien tu','urn:http://tamviettech.vn',$endpoint);
        
        $_SERVER['PHP_SELF'] = $endpoint;
        
        $server->wsdl->addComplexType(
            'ProgressObj',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'district_info' => array('name'=>'district_info','type'=>'xsd:string'),
                'village_info' => array('name'=>'village_info','type'=>'xsd:string'),
                'internet_record_info' => array('name'=>'internet_record_info','type'=>'xsd:string')
            )
        );
        
        $server->register("read_mail", array("title_mail" => "xsd:string","ou" => "xsd:string","record_id" => "xsd:string","type" => "xsd:string")
                , array("return" => "xsd:boolean"), "urn://tyler/res", "urn://tyler/res#read_mail");
        $server->register("exchange_record", array("ou" => "xsd:string","record_type" => "xsd:string","record_id" => "xsd:string")
                , array("return" => "xsd:boolean"), "urn://tyler/res", "urn://tyler/res#exchange_record");
        $server->register("receive_exchange_result", array("result" => "xsd:string","record_id" => "xsd:string","record_type_from" => "xsd:string")
                , array("return" => "xsd:boolean"), "urn://tyler/res", "urn://tyler/res#receive_exchange_result");
        $server->register("statistics", array("record_id_from" => "xsd:string","index" => "xsd:string",), array("return" => "xsd:boolean"), "urn://tyler/res", "urn://tyler/res#statistics");
        $server->register("progress_report", array(), array("return" => "tns:ProgressObj"), "urn://tyler/res", "urn://tyler/res#progress_report");
        $server->register("receive_internet_record", array("ou" => "xsd:boolean","record_id" => "xsd:string"), array("return" => "xsd:string"), "urn://tyler/res", "urn://tyler/res#receive_internet_record");
        $server->register("r3_staff", array(), array("return" => "xsd:string"), "urn://tyler/res", "urn://tyler/res#r3_staff");
        
        $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA :  file_get_contents('php://input');
        $server->service($HTTP_RAW_POST_DATA);
//        $this->read_mail('Ho so dich vu cong cap do 3 20150112 03:54:52','DVC','15203','INTERNET_RECORD');
    }
    
    public function read_mail($title_mail, $ou, $record_id, $type="EXCHANGE_RECORD")
    {
        if($type == 'EXCHANGE_RECORD')
        {
            $dir = $this->dir . 'email_store'. DS .'LIEN_THONG_'.$ou.'_'.$record_id;
        }
        elseif($type == 'INTERNET_RECORD')
        {
            $dir = $this->dir . 'email_store'. DS .'INTERNET_RECORD_'. $ou . '_' . $record_id;
        }
        
        $mail = factory_services::make_email_reader();
        $criteria = 'UNSEEN SUBJECT "'.$title_mail.'"';
        $arr_mail_index = $mail->search_mail($criteria);

        if(!empty($arr_mail_index))
        {
            foreach($arr_mail_index as $index)
            {
                $mail->get_file_attach($index,$dir);
            }
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function exchange_record($ou, $record_type, $record_id)
    {
        $dir = $this->dir . 'email_store'. DS .'LIEN_THONG_'.$ou.'_'.$record_id . DS;
        $xml_data = file_get_contents($dir . 'xml_data.xml');
        $xml_exchange_data = file_get_contents($dir . 'xml_exchange.xml');
        $model = factory_services::make_model();
        $result = $model->do_insert_exchange_record($record_type,$record_id,$xml_data,$xml_exchange_data);
        return $result;
    }
    
    public function receive_exchange_result($result,$record_id,$v_record_type_from)
    {
        $model = factory_services::make_model();
//        $result = $model->receive_exchange_result($result,$record_id,$v_record_type_from);
        $model_result = $model->receive_exchange_result($result,$record_id,$v_record_type_from);
        return $model_result;
    }
    
    public function statistics($record_id_from, $index)
    {
        $model = factory_services::make_model();//tao instance model
        $view = factory_services::make_view();//tao instance view
        
        $record_id = $model->get_exchange_record($record_id_from, $index);
        is_numeric($record_id) OR $record_id = 0;
        if($record_id == 0)
        {
            $v_unit_full_name = $view->get_unit_info('full_name');
            return $v_unit_full_name. ' Chưa tiếp nhận hồ sơ';
        }
        //Thông tin bản khai
        $VIEW_DATA['arr_single_record'] = $model->qry_single_record($record_id);

        $dom_processing = simplexml_load_string($VIEW_DATA['arr_single_record']['C_XML_PROCESSING']);

        //Thong tin tien do
        $v_record_type = $VIEW_DATA['arr_single_record']['C_RECORD_TYPE_CODE'];
        if ($VIEW_DATA['arr_single_record']['C_CLEAR_DATE'] == NULL)
        {
            $workflow_file_dir = $view->get_xml_config($v_record_type, 'workflow');
            $dom_step = simplexml_load_file($workflow_file_dir);
        }
        else
        {
            $dom_step = simplexml_load_string($VIEW_DATA['arr_single_record']['C_XML_WORKFLOW']);
        }
        $dom_workflow = $dom_step;
        $r = xpath($dom_step, '//step/@time');
        $step_days_list = '';
        foreach ($r as $time)
        {
            $step_days_list .= ($step_days_list != '') ? ";$time" : $time;
        }
        $arr_step_formal_date = $model->formal_record_step_days_to_date($record_id, $step_days_list);
        $VIEW_DATA['arr_step_formal_date'] = $arr_step_formal_date;

        //Lay danh sach step, ngay bat dau, ket thuc thuc te cua step, số ngày đã tiêu tốn thực tế để hoàn thành step
        $steps = xpath($dom_workflow, "//step[not(@no_chain = 'true')]");
        $index = -1;
        $arr_step_infact_formal_days_diff = array();
        
        foreach ($steps as $step)
        {
            $index++;
            $v_first_task_code = strval($step->task[0]->attributes()->code);
            $v_last_task_code = strval($step->task[sizeof($step->task) - 1]->attributes()->code);

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
            $begin_count = false;
            $begin_count_2 = true;
            $v_step_spent_days_infact = 0;
            $v_start = '';
            $v_end = '';
            $v_next_step = isset($steps[$index + 1]) ? $steps[$index + 1] : null;
            $v_next_step_first_task = '';
            if ($v_next_step)
            {
                $v_next_step_first_task = isset($v_next_step->task[0]) ? strval($v_next_step->task[0]->attributes()->code) : '';
            }
            foreach ($dom_processing->children() as $item)
            {
                if ($item->getName() == 'step')
                {
                    $v_end = (string) $item->datetime;
                    $v_date_inv = $model->days_between_two_date($v_start, $v_end);
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
        
        //thong tin cua cac don vi lien thong
        $VIEW_DATA['arr_all_exchange_unit'] = $model->qry_exchange_unit();
        
        $VIEW_DATA['arr_single_record_fee'] = $model->qry_single_record_fee($record_id);
        ob_start();
        $view->render('dsp_statistic',$VIEW_DATA);
        $result = ob_get_clean();
        return $result;
    }
    /**
     * 
     * @return type
     */
    public function progress_report()
    {
        $unit_code = $this->ou_name;
        //bao cao cap huyen
        $model = factory_services::make_model();
        $model->call_control_save_history_stat();
        $arr_district = $model->qry_history_stat_of_district();
        $xml_district = '<?xml version="1.0"?><report>';
        if (count($arr_district) > 0)
        {
            $v_date = $arr_district[0]['C_HISTORY_DATE'];
        }
        else	
        {
            $v_date = date('Y-m-d');
        }
        $xml_district .= '<date>' . $v_date . '</date>';
        $xml_district .= '<form>2</form>';
        $xml_district .= '<unit_code>' . $unit_code . '</unit_code>';
        $xml_district .= '<data>';
        for ($i=0; $i < count($arr_district); $i++)
        {
            $xml_district .= '<row>';
            $xml_district .= '<spec_code>' . $arr_district[$i]['C_SPEC_CODE'] . '</spec_code>'; 
            $xml_district .= '<history_date>' . $arr_district[$i]['C_HISTORY_DATE'] . '</history_date>'; 
            $xml_district .= '<count_tong_tiep_nhan>' . $arr_district[$i]['C_COUNT_TONG_TIEP_NHAN'] . '</count_tong_tiep_nhan>'; 
            $xml_district .= '<count_tong_tiep_nhan_trong_thang>' . $arr_district[$i]['C_COUNT_TONG_TIEP_NHAN_TRONG_THANG'] . '</count_tong_tiep_nhan_trong_thang>'; 
            $xml_district .= '<count_dang_thu_ly>' . $arr_district[$i]['C_COUNT_DANG_THU_LY'] . '</count_dang_thu_ly>'; 
            $xml_district .= '<count_dang_cho_tra_ket_qua>' . $arr_district[$i]['C_COUNT_DANG_CHO_TRA_KET_QUA'] . '</count_dang_cho_tra_ket_qua>'; 
            $xml_district .= '<count_da_tra_ket_qua>' . $arr_district[$i]['C_COUNT_DA_TRA_KET_QUA'] . '</count_da_tra_ket_qua>'; 
            $xml_district .= '<count_dang_thu_ly_dung_tien_do>' . $arr_district[$i]['C_COUNT_DANG_THU_LY_DUNG_TIEN_DO'] . '</count_dang_thu_ly_dung_tien_do>'; 
            $xml_district .= '<count_dang_thu_ly_cham_tien_do>' . $arr_district[$i]['C_COUNT_DANG_THU_LY_CHAM_TIEN_DO'] . '</count_dang_thu_ly_cham_tien_do>'; 
            $xml_district .= '<count_da_tra_ket_qua_truoc_han>' . $arr_district[$i]['C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN'] . '</count_da_tra_ket_qua_truoc_han>'; 
            $xml_district .= '<count_da_tra_ket_qua_dung_han>' . $arr_district[$i]['C_COUNT_DA_TRA_KET_QUA_DUNG_HAN'] . '</count_da_tra_ket_qua_dung_han>'; 
            $xml_district .= '<count_da_tra_ket_qua_qua_han>' . $arr_district[$i]['C_COUNT_DA_TRA_KET_QUA_QUA_HAN'] . '</count_da_tra_ket_qua_qua_han>'; 
            $xml_district .= '<count_cong_dan_rut>' . $arr_district[$i]['C_COUNT_CONG_DAN_RUT'] . '</count_cong_dan_rut>'; 
            $xml_district .= '<count_tu_choi>' . $arr_district[$i]['C_COUNT_TU_CHOI'] . '</count_tu_choi>'; 
            $xml_district .= '<count_bo_sung>' . $arr_district[$i]['C_COUNT_BO_SUNG'] . '</count_bo_sung>'; 
            $xml_district .= '<count_thu_ly_qua_han>' . $arr_district[$i]['C_COUNT_THU_LY_QUA_HAN'] . '</count_thu_ly_qua_han>'; 
            $xml_district .= '<count_thue>' . $arr_district[$i]['C_COUNT_THUE'] . '</count_thue>'; 
            $xml_district .= '</row>';
        }
        $xml_district .= '</data>';
        //finish
        $xml_district .= '</report>';
        
        //bao cao xa
        $arr_village  = $model->qry_history_stat_of_village();
        $xml_village = '<?xml version="1.0"?><report>';
        if (count($arr_village) > 0)
        {
            $v_date = $arr_village[0]['C_HISTORY_DATE'];
        }
        else	
        {
            $v_date = date('Y-m-d');
        }
        $xml_village .= '<date>' . $v_date . '</date>';
        $xml_village .= '<form>3</form>';
        $xml_village .= '<unit_code>' . $unit_code . '</unit_code>';
        $xml_village .= '<data>';
        for ($i=0; $i < count($arr_village); $i++)
        {
            $xml_village .= '<row>';
            $xml_village .= '<village_name>' . $arr_village[$i]['C_NAME'] . '</village_name>';
            $xml_village .= '<village_id>' . $arr_village[$i]['FK_VILLAGE_ID'] . '</village_id>'; 
            $xml_village .= '<spec_code>' . $arr_village[$i]['C_SPEC_CODE'] . '</spec_code>'; 
            $xml_village .= '<history_date>' . $arr_village[$i]['C_HISTORY_DATE'] . '</history_date>'; 
            $xml_village .= '<count_tong_tiep_nhan>' . $arr_village[$i]['C_COUNT_TONG_TIEP_NHAN'] . '</count_tong_tiep_nhan>'; 
            $xml_village .= '<count_tong_tiep_nhan_trong_thang>' . $arr_village[$i]['C_COUNT_TONG_TIEP_NHAN_TRONG_THANG'] . '</count_tong_tiep_nhan_trong_thang>'; 
            $xml_village .= '<count_dang_thu_ly>' . $arr_village[$i]['C_COUNT_DANG_THU_LY'] . '</count_dang_thu_ly>'; 
            $xml_village .= '<count_dang_cho_tra_ket_qua>' . $arr_village[$i]['C_COUNT_DANG_CHO_TRA_KET_QUA'] . '</count_dang_cho_tra_ket_qua>'; 
            $xml_village .= '<count_da_tra_ket_qua>' . $arr_village[$i]['C_COUNT_DA_TRA_KET_QUA'] . '</count_da_tra_ket_qua>'; 
            $xml_village .= '<count_dang_thu_ly_dung_tien_do>' . $arr_village[$i]['C_COUNT_DANG_THU_LY_DUNG_TIEN_DO'] . '</count_dang_thu_ly_dung_tien_do>'; 
            $xml_village .= '<count_dang_thu_ly_cham_tien_do>' . $arr_village[$i]['C_COUNT_DANG_THU_LY_CHAM_TIEN_DO'] . '</count_dang_thu_ly_cham_tien_do>'; 
            $xml_village .= '<count_da_tra_ket_qua_truoc_han>' . $arr_village[$i]['C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN'] . '</count_da_tra_ket_qua_truoc_han>'; 
            $xml_village .= '<count_da_tra_ket_qua_dung_han>' . $arr_village[$i]['C_COUNT_DA_TRA_KET_QUA_DUNG_HAN'] . '</count_da_tra_ket_qua_dung_han>'; 
            $xml_village .= '<count_da_tra_ket_qua_qua_han>' . $arr_village[$i]['C_COUNT_DA_TRA_KET_QUA_QUA_HAN'] . '</count_da_tra_ket_qua_qua_han>'; 
            $xml_village .= '<count_cong_dan_rut>' . $arr_village[$i]['C_COUNT_CONG_DAN_RUT'] . '</count_cong_dan_rut>'; 
            $xml_village .= '<count_tu_choi>' . $arr_village[$i]['C_COUNT_TU_CHOI'] . '</count_tu_choi>'; 
            $xml_village .= '<count_bo_sung>' . $arr_village[$i]['C_COUNT_BO_SUNG'] . '</count_bo_sung>'; 
            $xml_village .= '<count_thu_ly_qua_han>' . $arr_village[$i]['C_COUNT_THU_LY_QUA_HAN'] . '</count_thu_ly_qua_han>'; 
            $xml_village .= '<count_thue>' . $arr_village[$i]['C_COUNT_THUE'] . '</count_thue>'; 
            $xml_village .= '</row>';
        }
        $xml_village .= '</data>';
        //finish
        $xml_village .= '</report>';
        
        
        //bao cao ho so internet
        $model->db->SetFetchMode(ADODB_FETCH_ASSOC);
        $sql_processing = "Select * From view_processing_record Where C_IS_INTERNET_RECORD = '1'";
        $arr_all_processing_internet_record = $model->db->getAll($sql_processing);

        $sql_today_return = "Select * From t_r3_record Where C_IS_INTERNET_RECORD = '1' And DateDiff(C_CLEAR_DATE, now())=0";
        $arr_today_return_internet_record = $model->db->getAll($sql_today_return);
        $xml_internet = '<?xml version="1.0"?><report>';
        $xml_internet .= '<date>' . date('Y-m-d') . '</date>';
        $xml_internet .= '<form>4</form>';
        $xml_internet .= '<unit_code>' . $unit_code . '</unit_code>';
        $xml_internet .= '<data>';

        $xml_internet .= '<processing><![CDATA[';
        $xml_internet .= htmlspecialchars(json_encode($arr_all_processing_internet_record));
        $xml_internet .= ']]></processing>';

        $xml_internet .= '<today_return><![CDATA[';
        $xml_internet .= htmlspecialchars(json_encode($arr_today_return_internet_record));
        $xml_internet .= ']]></today_return>';

        $xml_internet .= '</data>';
        $xml_internet .= '</report>';
        
        $model->db->SetFetchMode(ADODB_FETCH_BOTH);
//        ProgressArray {ID = 1, YourName = "Jon"};
        return array('district_info' => $xml_district,
                        'village_info' => $xml_village,
                        'internet_record_info' => $xml_internet);
    }
    public function receive_internet_record($ou, $record_id)
    {
        $dir = $this->dir . 'email_store' . DS .'INTERNET_RECORD'. '_' . $ou . '_' . $record_id;
        $model = factory_services::make_model();
        $result = $model->receive_internet_record($dir);
        return $result;
    }
    
    public function r3_staff()
    {
        $model = factory_services::make_model();
        $result = $model->r3_staff();
        return $result;
    }
}

class factory_services
{
    public static $email_reader;
    public static $controller;
    public static $model;
    public static $view;
    
    public static function make_email_reader()
    {
        if(empty(self::$email_reader))
        {
            $email_reader = new Email_reader();
            $email_reader->SetValueCnn(_CONST_IMAP_SERVER, _CONST_IMAP_USER, _CONST_IMAP_PASS, _CONST_IMAP_PORT);
            $email_reader->connect();
            self::$email_reader = $email_reader;
        }
        return self::$email_reader;
    }
    
    public static function make_controller()
    {
        if(empty(self::$controller))
        {
            self::$controller = new webservices_Controller('r3','webservices');
        }
        return self::$controller;
    }
    
    public static function make_model()
    {
        if(empty(self::$model))
        {
            self::$model = new webservices_Model();
        }
        return self::$model;
    }
    
    public static function make_view()
    {
        if(empty(self::$view))
        {
            self::$view = new r3_View('r3','webservices');
        }
        return self::$view;
    }
}



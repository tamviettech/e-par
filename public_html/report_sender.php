<?php
if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !=  '10.82.252.15')
{
	die('Access Denied!');
}

@date_default_timezone_set('Asia/Ho_Chi_Minh');

define('DS', DIRECTORY_SEPARATOR);
define('SERVER_ROOT', __DIR__ . DS);

require_once ('config.php');
ini_set('error_reporting',E_ALL);
ini_set('display_errors',1);

set_time_limit(0);

//library
require_once (SERVER_ROOT . 'libs/PEAR/PEAR.php');
require_once (SERVER_ROOT . 'libs/adodb5/adodb.inc.php');
require_once (SERVER_ROOT . 'libs/Swift/lib/swift_required.php');
require_once (SERVER_ROOT . 'libs/Log.class.php');

//mail config
$report_sender_cfg['unit_code'] = 'LANG_GIANG';
$report_sender_cfg['smtp_server'] = 'smtp.gmail.com';
$report_sender_cfg['smtp_port'] = '465';
$report_sender_cfg['smtp_ssl'] = TRUE;
$report_sender_cfg['smtp_account'] = 'motcuacaphuyen.bacgiang@gmail.com';
$report_sender_cfg['smtp_account_name'] = 'Một cửa Lạng Giang';
$report_sender_cfg['smtp_password'] = 'Muachimenbay^';
//$report_sender_cfg['to_address'] = 'thcb.bacgiang@gmail.com';
$report_sender_cfg['to_address'] = 'congnghetamviet@gmail.com';

/**********************************************************************************************************************/
//adodb
$adodb = ADONewConnection(CONST_MYSQL_DSN) or die('Cannot connect to MySQL Database Server!');

//Mail 
$ssl = $report_sender_cfg['smtp_ssl'] ? 'ssl' : NULL;
$transport   = Swift_SmtpTransport::newInstance($report_sender_cfg['smtp_server'], $report_sender_cfg['smtp_port'], $ssl);
$transport->setUsername($report_sender_cfg['smtp_account']);
$transport->setPassword($report_sender_cfg['smtp_password']);
// Mailer Object
$mailer = Swift_Mailer::newInstance($transport); 

//1. Mau report so 1: (form_1) Bao cao tinh trang hang ngay
$message = Swift_Message::newInstance('form_1-' . uniqid());

//On-the-Fly Attach
$arr_single_report_form_1 = $adodb->getRow("call sp_daily_stat_get_all('{$report_sender_cfg['unit_code']}')");

$xml_string = '<?xml version="1.0"?><report>';
$xml_string .= '<date>' . $arr_single_report_form_1['C_DATE'] . '</date>';
$xml_string .= '<form>1</form>';
$xml_string .= '<unit_code>' . $arr_single_report_form_1['C_UNIT_CODE'] . '</unit_code>';
$xml_string .= '<received>' . $arr_single_report_form_1['C_COUNT_TONG_TIEP_NHAN'] . '</received>';

//dang thu ly
$xml_string .= '<execing>';
$xml_string .= '<total>' . $arr_single_report_form_1['C_COUNT_TONG_DANG_THU_LY'] . '</total>';
$xml_string .= '<on_time>' . $arr_single_report_form_1['C_COUNT_DANG_THU_LY_DUNG_TIEN_DO'] . '</on_time>';
$xml_string .= '<over_time>' . $arr_single_report_form_1['C_COUNT_DANG_THU_LY_CHAM_TIEN_DO'] . '</over_time>';
$xml_string .= '<over_return>' . $arr_single_report_form_1['C_COUNT_DANG_THU_LY_QUA_HAN'] . '</over_return>';
$xml_string .= '</execing>';

//Cho tra
$xml_string .= '<waiting_return>' . $arr_single_report_form_1['C_COUNT_DANG_CHO_TRA_KET_QUA'] . '</waiting_return>';

//Da tra
$xml_string .= '<returned>';
$xml_string .= '<total>' . $arr_single_report_form_1['C_COUNT_DA_TRA_KET_QUA'] . '</total>';
$xml_string .= '<before>' . $arr_single_report_form_1['C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN'] . '</before>';
$xml_string .= '<on_time>' . $arr_single_report_form_1['C_COUNT_DA_TRA_KET_QUA_DUNG_HAN'] . '</on_time>';
$xml_string .= '<over_time>' . $arr_single_report_form_1['C_COUNT_DA_TRA_KET_QUA_QUA_HAN'] . '</over_time>';
$xml_string .= '</returned>';

//finish
$xml_string .= '</report>';

$form_1_attachment = Swift_Attachment::newInstance($xml_string, 'form_1.xml', 'text/xml');
// Attach it to the message
$message->attach($form_1_attachment);


$adodb->disconnect();
unset($adodb);
$adodb = ADONewConnection(CONST_MYSQL_DSN) or die('Cannot connect to MySQL Database Server!');
//2. Mau report so 2: (form_2) Bao cao tinh hinh trong thang
$sql = 'Select 	
            PK_HISTORY_STAT,
            C_SPEC_CODE,
            C_HISTORY_DATE,
            C_COUNT_TONG_TIEP_NHAN,
            C_COUNT_TONG_TIEP_NHAN_TRONG_THANG, 
            C_COUNT_DANG_THU_LY, 
            C_COUNT_DANG_CHO_TRA_KET_QUA, 
            C_COUNT_DA_TRA_KET_QUA, 
            C_COUNT_DANG_THU_LY_DUNG_TIEN_DO, 
            C_COUNT_DANG_THU_LY_CHAM_TIEN_DO, 
            C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN, 
            C_COUNT_DA_TRA_KET_QUA_DUNG_HAN, 
            C_COUNT_DA_TRA_KET_QUA_QUA_HAN, 
            C_COUNT_CONG_DAN_RUT, 
            C_COUNT_TU_CHOI, 
            C_COUNT_BO_SUNG, 
            C_COUNT_THU_LY_QUA_HAN, 
            C_COUNT_THUE
        From t_r3_record_history_stat 
        Where C_HISTORY_DATE=(SELECT MAX(C_HISTORY_DATE) From t_r3_record_history_stat)';
$arr_all_report_form_2 = $adodb->getAll($sql);

$xml_string = '<?xml version="1.0"?><report>';
if (count($arr_all_report_form_2) > 0)
{
    $v_date = $arr_all_report_form_2[0]['C_HISTORY_DATE'];
}
else	
{
    $v_date = date('Y-m-d');
}
$xml_string .= '<date>' . $v_date . '</date>';
$xml_string .= '<form>2</form>';
$xml_string .= '<unit_code>' . $report_sender_cfg['unit_code'] . '</unit_code>';
$xml_string .= '<data>';
for ($i=0; $i < count($arr_all_report_form_2); $i++)
{
    $xml_string .= '<row>';
    $xml_string .= '<spec_code>' . $arr_all_report_form_2[$i]['C_SPEC_CODE'] . '</spec_code>'; 
    $xml_string .= '<history_date>' . $arr_all_report_form_2[$i]['C_HISTORY_DATE'] . '</history_date>'; 
    $xml_string .= '<count_tong_tiep_nhan>' . $arr_all_report_form_2[$i]['C_COUNT_TONG_TIEP_NHAN'] . '</count_tong_tiep_nhan>'; 
    $xml_string .= '<count_tong_tiep_nhan_trong_thang>' . $arr_all_report_form_2[$i]['C_COUNT_TONG_TIEP_NHAN_TRONG_THANG'] . '</count_tong_tiep_nhan_trong_thang>'; 
    $xml_string .= '<count_dang_thu_ly>' . $arr_all_report_form_2[$i]['C_COUNT_DANG_THU_LY'] . '</count_dang_thu_ly>'; 
    $xml_string .= '<count_dang_cho_tra_ket_qua>' . $arr_all_report_form_2[$i]['C_COUNT_DANG_CHO_TRA_KET_QUA'] . '</count_dang_cho_tra_ket_qua>'; 
    $xml_string .= '<count_da_tra_ket_qua>' . $arr_all_report_form_2[$i]['C_COUNT_DA_TRA_KET_QUA'] . '</count_da_tra_ket_qua>'; 
    $xml_string .= '<count_dang_thu_ly_dung_tien_do>' . $arr_all_report_form_2[$i]['C_COUNT_DANG_THU_LY_DUNG_TIEN_DO'] . '</count_dang_thu_ly_dung_tien_do>'; 
    $xml_string .= '<count_dang_thu_ly_cham_tien_do>' . $arr_all_report_form_2[$i]['C_COUNT_DANG_THU_LY_CHAM_TIEN_DO'] . '</count_dang_thu_ly_cham_tien_do>'; 
    $xml_string .= '<count_da_tra_ket_qua_truoc_han>' . $arr_all_report_form_2[$i]['C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN'] . '</count_da_tra_ket_qua_truoc_han>'; 
    $xml_string .= '<count_da_tra_ket_qua_dung_han>' . $arr_all_report_form_2[$i]['C_COUNT_DA_TRA_KET_QUA_DUNG_HAN'] . '</count_da_tra_ket_qua_dung_han>'; 
    $xml_string .= '<count_da_tra_ket_qua_qua_han>' . $arr_all_report_form_2[$i]['C_COUNT_DA_TRA_KET_QUA_QUA_HAN'] . '</count_da_tra_ket_qua_qua_han>'; 
    $xml_string .= '<count_cong_dan_rut>' . $arr_all_report_form_2[$i]['C_COUNT_CONG_DAN_RUT'] . '</count_cong_dan_rut>'; 
    $xml_string .= '<count_tu_choi>' . $arr_all_report_form_2[$i]['C_COUNT_TU_CHOI'] . '</count_tu_choi>'; 
    $xml_string .= '<count_bo_sung>' . $arr_all_report_form_2[$i]['C_COUNT_BO_SUNG'] . '</count_bo_sung>'; 
    $xml_string .= '<count_thu_ly_qua_han>' . $arr_all_report_form_2[$i]['C_COUNT_THU_LY_QUA_HAN'] . '</count_thu_ly_qua_han>'; 
    $xml_string .= '<count_thue>' . $arr_all_report_form_2[$i]['C_COUNT_THUE'] . '</count_thue>'; 
    $xml_string .= '</row>';
}
$xml_string .= '</data>';

//finish
$xml_string .= '</report>';
$form_2_attachment = Swift_Attachment::newInstance($xml_string, 'form_2.xml', 'text/xml');
// Attach it to the message
$message->attach($form_2_attachment);

//Bao cao 3: tinh hinh cac xa
$sql_form_3 = 'Select 
            OU.C_NAME,
            HS.* 
          From
            t_r3_record_history_stat HS 
            Left Join t_cores_ou OU 
              On HS.FK_VILLAGE_ID = OU.PK_OU 
          Where datediff(C_HISTORY_DATE, now())=0
          order by HS.FK_VILLAGE_ID,
            HS.C_SPEC_CODE';
$arr_all_report_form_3 = $adodb->getAll($sql_form_3);
$xml_string = '<?xml version="1.0"?><report>';
if (count($arr_all_report_form_3) > 0)
{
    $v_date = $arr_all_report_form_3[0]['C_HISTORY_DATE'];
}
else	
{
    $v_date = date('Y-m-d');
}
$xml_string .= '<date>' . $v_date . '</date>';
$xml_string .= '<form>3</form>';
$xml_string .= '<unit_code>' . $report_sender_cfg['unit_code'] . '</unit_code>';
$xml_string .= '<data>';
for ($i=0; $i < count($arr_all_report_form_3); $i++)
{
    $xml_string .= '<row>';
    $xml_string .= '<village_name>' . $arr_all_report_form_3[$i]['C_NAME'] . '</village_name>';
    $xml_string .= '<village_id>' . $arr_all_report_form_3[$i]['FK_VILLAGE_ID'] . '</village_id>'; 
    $xml_string .= '<spec_code>' . $arr_all_report_form_3[$i]['C_SPEC_CODE'] . '</spec_code>'; 
    $xml_string .= '<history_date>' . $arr_all_report_form_3[$i]['C_HISTORY_DATE'] . '</history_date>'; 
    $xml_string .= '<count_tong_tiep_nhan>' . $arr_all_report_form_3[$i]['C_COUNT_TONG_TIEP_NHAN'] . '</count_tong_tiep_nhan>'; 
    $xml_string .= '<count_tong_tiep_nhan_trong_thang>' . $arr_all_report_form_3[$i]['C_COUNT_TONG_TIEP_NHAN_TRONG_THANG'] . '</count_tong_tiep_nhan_trong_thang>'; 
    $xml_string .= '<count_dang_thu_ly>' . $arr_all_report_form_3[$i]['C_COUNT_DANG_THU_LY'] . '</count_dang_thu_ly>'; 
    $xml_string .= '<count_dang_cho_tra_ket_qua>' . $arr_all_report_form_3[$i]['C_COUNT_DANG_CHO_TRA_KET_QUA'] . '</count_dang_cho_tra_ket_qua>'; 
    $xml_string .= '<count_da_tra_ket_qua>' . $arr_all_report_form_3[$i]['C_COUNT_DA_TRA_KET_QUA'] . '</count_da_tra_ket_qua>'; 
    $xml_string .= '<count_dang_thu_ly_dung_tien_do>' . $arr_all_report_form_3[$i]['C_COUNT_DANG_THU_LY_DUNG_TIEN_DO'] . '</count_dang_thu_ly_dung_tien_do>'; 
    $xml_string .= '<count_dang_thu_ly_cham_tien_do>' . $arr_all_report_form_3[$i]['C_COUNT_DANG_THU_LY_CHAM_TIEN_DO'] . '</count_dang_thu_ly_cham_tien_do>'; 
    $xml_string .= '<count_da_tra_ket_qua_truoc_han>' . $arr_all_report_form_3[$i]['C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN'] . '</count_da_tra_ket_qua_truoc_han>'; 
    $xml_string .= '<count_da_tra_ket_qua_dung_han>' . $arr_all_report_form_3[$i]['C_COUNT_DA_TRA_KET_QUA_DUNG_HAN'] . '</count_da_tra_ket_qua_dung_han>'; 
    $xml_string .= '<count_da_tra_ket_qua_qua_han>' . $arr_all_report_form_3[$i]['C_COUNT_DA_TRA_KET_QUA_QUA_HAN'] . '</count_da_tra_ket_qua_qua_han>'; 
    $xml_string .= '<count_cong_dan_rut>' . $arr_all_report_form_3[$i]['C_COUNT_CONG_DAN_RUT'] . '</count_cong_dan_rut>'; 
    $xml_string .= '<count_tu_choi>' . $arr_all_report_form_3[$i]['C_COUNT_TU_CHOI'] . '</count_tu_choi>'; 
    $xml_string .= '<count_bo_sung>' . $arr_all_report_form_3[$i]['C_COUNT_BO_SUNG'] . '</count_bo_sung>'; 
    $xml_string .= '<count_thu_ly_qua_han>' . $arr_all_report_form_3[$i]['C_COUNT_THU_LY_QUA_HAN'] . '</count_thu_ly_qua_han>'; 
    $xml_string .= '<count_thue>' . $arr_all_report_form_3[$i]['C_COUNT_THUE'] . '</count_thue>'; 
    $xml_string .= '</row>';
}
$xml_string .= '</data>';

//finish
$xml_string .= '</report>';
$form_3_attachment = Swift_Attachment::newInstance($xml_string, 'form_3.xml', 'text/xml');
// Attach it to the message
$message->attach($form_3_attachment);

//Bao cao 4: Tinh hinh hs internet
$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
$sql_processing = "Select * From view_processing_record Where C_IS_INTERNET_RECORD = '1'";
$arr_all_processing_internet_record = $adodb->getAll($sql_processing);

$sql_today_return = "Select * From t_r3_record Where C_IS_INTERNET_RECORD = '1' And DateDiff(C_CLEAR_DATE, now())=0";
$arr_today_return_internet_record = $adodb->getAll($sql_today_return);
$xml_string = '<?xml version="1.0"?><report>';
$xml_string .= '<date>' . date('Y-m-d') . '</date>';
$xml_string .= '<form>4</form>';
$xml_string .= '<unit_code>' . $report_sender_cfg['unit_code'] . '</unit_code>';
$xml_string .= '<data>';

$xml_string .= '<processing><![CDATA[';
$xml_string .= htmlspecialchars(json_encode($arr_all_processing_internet_record));
$xml_string .= ']]></processing>';

$xml_string .= '<today_return><![CDATA[';
$xml_string .= htmlspecialchars(json_encode($arr_all_processing_internet_record));
$xml_string .= ']]></today_return>';

$xml_string .= '</data>';
$xml_string .= '</report>';
$form_4_attachment = Swift_Attachment::newInstance($xml_string, 'form_4.xml', 'text/xml');
// Attach it to the message
$message->attach($form_4_attachment);
$adodb->SetFetchMode(ADODB_FETCH_BOTH);

//Send mail
$message->setBody('Bao cao tinh hinh giai quyet ho so tai don vi', 'text/plain');
$message->setFrom($report_sender_cfg['smtp_account']);
$message->addTo($report_sender_cfg['to_address']);

//Gui di
$result = $mailer->send($message);//TRUE - FALSE
$v_send_message = 'Ngay ' .  Date('d-m-Y') . ' gui bao cao bieu mau 01 THAT BAI';
if ($result){
    $v_send_message = 'Ngay ' .  Date('d-m-Y') . ' gui bao cao bieu mau 01 THANH CONG';
}

//log 	
$v_log_file_name = SERVER_ROOT . '/';
$log = new Log(SERVER_ROOT, 'report_sender', 'Nhat ky bao cao');
$log->logThis('f:nl'); //writes a new line, "\n"
$log->logThis($log->get_formatted_date() . ' ' . $v_send_message);

<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

define('_CONST_DEFAULT_ROWS_PER_PAGE', 10);
define('_CONST_LIST_DELIM', '<~>');
define('_CONST_DEFAULT_DW_OFF', '7,1'); //Ngay nghi cuoi tuan mac dinh: 7 => thu bay; 1 => Chu nhat
define('_CONST_DEFAULT_DATE_OFF', '1/1, 30/04, 01/05, 02/09'); //Ngay nghi le mac dinh: format: dd/mm

define('_CONST_STAFF_GROUP_CODE', 'CAN_BO');
define('_CONST_TEAM_LEADER_GROUP_CODE', 'LANH_DAO_PHONG');
define('_CONST_BOD_GROUP_CODE', 'LANH_DAO_DON_VI');


//Thu tu Mot-Cua
define('_CONST_XML_RTT_DELIM', '::');
define('_CONST_HTML_RTT_DELIM', '--');

define('_CONST_GET_NEW_RECORD_NOTICE_INTERVAL', 15000); //mili sec
//Role
define('_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE', 'XAC_NHAN_HO_SO_NOP_QUA_INTERNET');
define('_CONST_KIEM_TRA_TRUOC_HO_SO_ROLE', 'KIEM_TRA_TRUOC_HO_SO');
define('_CONST_TIEP_NHAN_ROLE', 'TIEP_NHAN');
define('_CONST_BAN_GIAO_ROLE', 'BAN_GIAO');
define('_CONST_RUT_ROLE', 'RUT_HO_SO');
define('_CONST_BO_SUNG_ROLE', 'BO_SUNG');
define('_CONST_TRA_KET_QUA_ROLE', 'TRA_KET_QUA');
define('_CONST_TRA_KET_QUA_LT_ROLE', 'TRA_KET_QUA_LT');
define('_CONST_IN_PHIEU_TIEP_NHAN_ROLE', 'IN_PHIEU_TIEP_NHAN');
define('_CONST_CHUYEN_LEN_HUYEN_ROLE', 'CHUYEN_LEN_HUYEN');
define('_CONST_TRA_HO_SO_VE_XA_ROLE', 'TRA_HO_SO_VE_XA');
define('_CONST_THONG_BAO_BO_SUNG_ROLE', 'THONG_BAO_BO_SUNG');
define('_CONST_TAI_CHINH_ROLE', 'TAI_CHINH');
define('_CONST_PHAN_CONG_ROLE', 'PHAN_CONG');
define('_CONST_PHAN_CONG_LAI_ROLE', 'PHAN_CONG_LAI');
define('_CONST_THU_LY_ROLE', 'THU_LY');
define('_CONST_THU_LY_CAP_XA_ROLE', 'THU_LY_CAP_XA');
define('_CONST_CHUYEN_YEU_CAU_XAC_NHAN_XUONG_XA_ROLE', 'CHUYEN_YEU_CAU_XAC_NHAN_XUONG_XA');
define('_CONST_THU_LY_HO_SO_LIEN_THONG_ROLE', 'THU_LY_HO_SO_LIEN_THONG');
define('_CONST_YEU_CAU_THU_LY_LAI_ROLE', 'YEU_CAU_THU_LY_LAI');
define('_CONST_XAC_NHAN_HO_SO_LIEN_THONG_ROLE', 'XAC_NHAN_LIEN_THONG');
define('_CONST_XET_DUYET_ROLE', 'XET_DUYET');
define('_CONST_XET_DUYET_BO_SUNG_ROLE', 'XET_DUYET_BO_SUNG');
define('_CONST_KY_ROLE', 'KY_DUYET');
define('_CONST_THU_PHI_ROLE', 'THU_PHI');
define('_CONST_Y_KIEN_LANH_DAO_ROLE', 'Y_KIEN_LANH_DAO');
define('_CONST_TRA_CUU_ROLE', 'TRA_CUU');
define('_CONST_TRA_CUU_LIEN_THONG_ROLE', 'TRA_CUU_HO_SO_LIEN_THONG');
define('_CONST_TRA_CUU_TAI_XA_ROLE', 'TRA_CUU_HO_SO_TAI_XA');
define('_CONST_BAO_CAO_ROLE', 'BAO_CAO');
define('_CONST_NOP_HO_SO_SANG_CHI_CUC_THUE_ROLE', 'NOP_HO_SO_SANG_CHI_CUC_THUE');
define('_CONST_NHAN_THONG_BAO_CUA_CHI_CUC_THUE_ROLE', 'NHAN_THONG_BAO_CUA_CHI_CUC_THUE');
define('_CONST_CHUYEN_THONG_BAO_THUE_VE_BP_MOT_CUA_ROLE', 'CHUYEN_THONG_BAO_THUE_VE_BP_MOT_CUA');
define('_CONST_TRA_THONG_BAO_NOP_THUE_ROLE', 'TRA_THONG_BAO_NOP_THUE');
define('_CONST_NHAN_BIEN_LAI_NOP_THUE_ROLE', 'NHAN_BIEN_LAI_NOP_THUE');
define('_CONST_CHUYEN_LAI_BUOC_TRUOC_ROLE', 'CHUYEN_LAI_BUOC_TRUOC');
define('_CONST_CHUYEN_HO_SO_LEN_SO_ROLE', 'CHUYEN_HO_SO_LEN_SO');
define('_CONST_NHAN_HO_SO_TU_SO_ROLE', 'NHAN_HO_SO_TU_SO');
define('_CONST_FINISH_NO_CHAIN_STEP_TASK', 'FINISH_NO_CHAIN_STEP');
define('_CONST_TIEP_NHAN_LIEN_THONG_ROLE', 'TIEP_NHAN_LIEN_THONG');

//Sau thue
define('_CONST_AFTER_TAX_SUFFIX', '_SAU_THUE');
define('_CONST_PHAN_CONG_SAU_THUE_ROLE', 'PHAN_CONG_SAU_THUE');
define('_CONST_THU_LY_SAU_THUE_ROLE', 'THU_LY_SAU_THUE');
define('_CONST_DUYET_SAU_THUE_ROLE', 'PHE_DUYET_VA_TRINH_KY_SAU_THUE');

//Ket qua thu ly ho so
define('_CONST_RECORD_APPROVAL_ACCEPT', 'ACCEPT');
define('_CONST_RECORD_APPROVAL_SUPPLEMENT', 'SUPPLEMENT');
define('_CONST_RECORD_APPROVAL_REEXEC', 'REEXEC');
define('_CONST_RECORD_APPROVAL_REJECT', 'REJECT');

//Gio lam viec hanh chinh
define('_CONST_MORNING_BEGIN_WORKING_TIME', '07:30');
define('_CONST_MORNING_END_WORKING_TIME', '11:30');
define('_CONST_AFTERNOON_BEGIN_WORKING_TIME', '13:30');
define('_CONST_AFTERNOON_END_WORKING_TIME', '16:00');

//Quan ly van ban
define('_CONST_EDOC_VBDEN', 'VBDEN');
define('_CONST_EDOC_VBDI', 'VBDI');
define('_CONST_EDOC_VBNOI_BO', 'VBNOI_BO');
define('_CONST_GET_NEW_DOC_NOTICE_INTERVAL', 3000);

//Role VBDEN
define('_CONST_VAO_SO_VAN_BAN_DEN_ROLE', 'VAO_SO_VAN_BAN_DEN');
define('_CONST_TRINH_VAN_BAN_DEN_ROLE', 'TRINH_VAN_BAN_DEN');
define('_CONST_DUYET_VAN_BAN_DEN_ROLE', 'DUYET_VAN_BAN_DEN');
define('_CONST_THU_LY_VAN_BAN_DEN_ROLE', 'THU_LY_VAN_BAN_DEN');
define('_CONST_PHOI_HOP_THU_LY_VAN_BAN_DEN_ROLE', 'PHOI_HOP_THU_LY_VAN_BAN_DEN');
define('_CONST_GIAM_SAT_THU_LY_VAN_BAN_DEN_ROLE', 'GIAM_SAT_THU_LY_VAN_BAN_DEN');
//Role VBDI
define('_CONST_SOAN_THAO_VAN_BAN_DI_ROLE', 'SOAN_THAO_VAN_BAN_DI');
define('_CONST_TRINH_DUYET_VAN_BAN_DI_ROLE', 'TRINH_DUYET_VAN_BAN_DI');
define('_CONST_DUYET_VAN_BAN_DI_ROLE', 'DUYET_VAN_BAN_DI');
define('_CONST_VAO_SO_VAN_BAN_DI_ROLE', 'VAO_SO_VAN_BAN_DI');
//Role VBNOIBO
define('_CONST_SOAN_THAO_VAN_BAN_NOI_BO_ROLE', 'SOAN_THAO_VAN_BAN_NOI_BO');
define('_CONST_TRINH_DUYET_VAN_BAN_NOI_BO_ROLE', 'TRINH_DUYET_VAN_BAN_NOI_BO');
define('_CONST_DUYET_VAN_BAN_NOI_BO_ROLE', 'DUYET_VAN_BAN_NOI_BO');
define('_CONST_VAO_SO_VAN_BAN_NOI_BO_ROLE', 'VAO_SO_VAN_BAN_NOI_BO');

define('_CONST_VAN_BAN_DUOC_CHIA_SE_ROLE', 'VAN_BAN_DUOC_CHIA_SE');

define('_CONTS_MAIL_LOG_DIR', SERVER_ROOT . DS . 'mail_log' . DS);
define('_CONST_SMTP_SERVER', 'smtp.gmail.com');
define('_CONST_SMTP_PORT', '465');
define('_CONST_SMTP_ACCOUNT', 'motcua.tamviettech@gmail.com');
define('_CONST_SMTP_ACCOUNT_NAME', 'Bộ phận một cửa');
define('_CONST_SMTP_PASSWORD', 'Muachimenbay^');
define('_CONST_SMTP_SSL', TRUE);
define('_CONST_INTERNET_RECORD_ACCEPT_EMAIL', "Kính gửi ông/bà: %s
        \n\nNgày %s, Bộ phận một cửa đã nhận được hồ sơ \"%s\" của ông bà nộp qua mạng Internet.
        \nBộ phận một cửa xin thông báo hồ sơ của ông/bà đã được chuyển tới bộ phận chuyên môn để giải quyết theo luật định.
        \nHồ sơ của ông/bà được cấp mã số là: %s. Để tra cứu, xin vui lòng truy cập vào địa chỉ: http://113.160.158.99:88/go-office/r3/mavach
        \nTheo quy định hiện hành, hồ sơ sẽ được giải quyết trong %s ngày làm việc, và trả kết quả vào ngày %s
        \nXin mời ông/bà đến ngày giờ trên mang hồ sơ tới đến bộ phận một cửa để đối chiếu và nhận kết quả.
        \nMọi thắc mắc xin liên hệ \"Bộ phận Một cửa\" - 0240.3638.505.
        \n\nTrân trọng.");

//ReCapcha
define('_CONST_RECAPCHA_PUBLIC_KEY', '6LdpjNoSAAAAAMvTFbLh2LPN4z32Dyb6YD2v8vUI');
define('_CONST_RECAPCHA_PRIVATE_KEY', '6LdpjNoSAAAAAB6kCDmrY8RmuysVHTWsr8qxSuQb');

//Danh muc
define('_CONST_DANH_MUC_LINH_VUC', 'DANH_MUC_LINH_VUC');
define('_CONST_DANH_MUC_BAO_CAO', 'DANH_MUC_BAO_CAO');
define('_CONST_DANH_MUC_TIEU_CHI_DANH_GIA', 'DM_TIEU_CHI_DANH_GIA');
//cho phép upload
define('EXT_DOCUMENT', 'doc,docx,odt,pdf,txt,rtf');
define('EXT_SPREADSHEET', 'xls,xlsx,ods,cvs');

/** File đính kèm hồ sơ */
define('_CONST_RECORD_FILE_ACCEPT', 'pdf,doc,docx,xls,xlsx,ppt,pptx');
//File dinh kem mau dang ky
define('_CONST_TYPE_FILE_ACCEPT', 'pdf,doc,docx,xls,xlsx,ppt,pptx');
//ext file media
define('_CONST_MEDIA_FILE_ACCEPT', 'pdf,jpg,png,doc,docx,xls,xlsx,ppt,pptx');


//dành cho media
$media_categories = array(
    'image'       => 'bmp, gif, jpg, png, psd, pspimage, thm, tif, yuv, swf',
    'text'        => 'doc, docx, log, msg, pages ,rtf, txt, wpd, wps',
    'spreadsheet' => 'xlr, xls, xlsx',
    'data'        => 'csv, dat, efx, gbr, key, pps, ppt, pptx, sdf, tax2010, vcf, xml',
    'audio'       => 'aif, iff, m3u, m4a, mid, mp3, mpa, ra, wav, wma',
    'video'       => '3g2, 3gp, asf, asx, avi, flv, mov, mp4, mpg, rm, swf, vob, wmv, mp3',
    'compressed'  => '7z, deb, gz, pkg, rar, rpm, sit, sitx, tar.gz, zip, zipx'
);

foreach ($media_categories as $categoryname => $categorydata)
{
    $categoryname = str_replace(' ', '', strtoupper($categoryname));
    if (!defined("EXT_{$categoryname}"))
    {
        define("EXT_$categoryname", $categorydata);
    }
}
define('EXT_ALL', implode(',', $media_categories));
define('_CONST_IMAGE_FILE_EXT', preg_replace('/\s+/', '', 'jpg, png, gif, bmp,swf'));
define('_CONST_WELLKNOWN_FILE_EXT', preg_replace('/\s+/', '', ' accdb,  avi, csv, doc, docx, mp3, mp4, mpeg, pdf, pps, rar, zip, swf, txt, flv'));
define('_CONST_UPLOAD_FILE_EXT', preg_replace('/\s+/', '', 'jpg, png, gif, bmp,swf, accdb,  avi, csv, doc, docx, mp3, mp4, mpeg, pdf, pps, rar, zip, swf, txt, xls'));

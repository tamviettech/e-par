<?php
$arr_all_r3_roles = array(
        //BP Mot-Cua
        _CONST_TIEP_NHAN_ROLE                         => 'Tiếp nhận'
        , _CONST_BAN_GIAO_ROLE                        => 'Bàn giao'
        , _CONST_RUT_ROLE                             => 'Rút hồ sơ'
        , _CONST_BO_SUNG_ROLE                         => 'Bổ sung'
        , _CONST_TRA_THONG_BAO_NOP_THUE_ROLE          => 'Trả TB thuế'
        , _CONST_NHAN_BIEN_LAI_NOP_THUE_ROLE          => 'Nhận BL thuế'
        , _CONST_THU_PHI_ROLE                         => 'Thu phí'
        , _CONST_TRA_KET_QUA_ROLE                     => 'Trả KQ'
        , _CONST_IN_PHIEU_TIEP_NHAN_ROLE              => 'In lại phiếu TN'
        , _CONST_CHUYEN_LEN_HUYEN_ROLE                => 'Chuyển HS lên Huyện'
        , _CONST_TRA_HO_SO_VE_XA_ROLE                 => 'Trả KQ về xã'
        , _CONST_XAC_NHAN_HO_SO_LIEN_THONG_ROLE       => 'Giải quyết/xác nhận HS liên thông'
        , _CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE => 'Hồ sơ Internet'
        , _CONST_KIEM_TRA_TRUOC_HO_SO_ROLE            => 'Kiểm tra trước hồ sơ'
        //BO SUNG
        , _CONST_THONG_BAO_BO_SUNG_ROLE               => 'Thông báo bổ sung'

        //Bo phan Thue
        , _CONST_NOP_HO_SO_SANG_CHI_CUC_THUE_ROLE         => 'Chuyển HS sang thuế'
        , _CONST_NHAN_THONG_BAO_CUA_CHI_CUC_THUE_ROLE     => 'Nhận TB của chi cục thuế'
        , _CONST_CHUYEN_THONG_BAO_THUE_VE_BP_MOT_CUA_ROLE => 'Chuyển TB thuế về "Môt-cửa"'

        //Phong chuyen mon
        , _CONST_PHAN_CONG_ROLE                        => 'Phân công thụ lý'
        , _CONST_PHAN_CONG_LAI_ROLE                    => 'Thay đổi thụ lý'
        , _CONST_THU_LY_ROLE                           => 'Thụ lý'
        , _CONST_THU_LY_CAP_XA_ROLE                    => 'Thụ lý cấp xã'
        , _CONST_CHUYEN_HO_SO_LEN_SO_ROLE              => 'Chuyển HS lên Sở'
        , _CONST_NHAN_HO_SO_TU_SO_ROLE                 => 'Nhận HS từ Sở'
        , _CONST_CHUYEN_YEU_CAU_XAC_NHAN_XUONG_XA_ROLE => 'Chuyển giải quyết/xác nhận xuống xã'
        , _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE          => 'Thụ lý HS liên thông'
        , _CONST_YEU_CAU_THU_LY_LAI_ROLE               => 'Yêu cầu thụ lý lại'
        , _CONST_XET_DUYET_ROLE                        => 'Xét duyệt HS'
        , _CONST_XET_DUYET_BO_SUNG_ROLE                => 'Xét duyệt HS bổ sung'
        , _CONST_CHUYEN_LAI_BUOC_TRUOC_ROLE            => 'Trả hồ sơ về bước trước'
        

        //Lanh dao don vi
        , _CONST_KY_ROLE              => 'Ký duyệt hồ sơ'
        , _CONST_Y_KIEN_LANH_DAO_ROLE => 'Ý kiến lãnh đạo'

        //Chung
        , _CONST_TRA_CUU_ROLE            => 'Tra cứu'
        , _CONST_TRA_CUU_LIEN_THONG_ROLE => 'Tra cứu hồ sơ liên thông'
        , _CONST_TRA_CUU_TAI_XA_ROLE     => 'Tra cứu hồ sơ tại xã'
        , 'REJECT'                       => 'Từ chối HS'
        , 'KHONG_NHAN_HO_SO'             => 'Không nhận hồ sơ'
    );

define('CONST_ALL_R3_ROLES', json_encode($arr_all_r3_roles));

//duong dan goc cua media
$file_upload_path = SERVER_ROOT . 'uploads'. DS . 'r3' . DS . 'media' . DS;
define('CONST_FILE_UPLOAD_PATH', $file_upload_path);

$file_upload_link = SITE_ROOT . 'uploads'. DS . 'r3' . DS . 'media' . DS;
define('CONST_FILE_UPLOAD_LINK', $file_upload_link);

$file_upload_template_file_type = SERVER_ROOT . 'uploads' . DS . 'r3' . DS;
define('CONST_TYPE_FILE_UPLOAD',$file_upload_template_file_type);

//phan quyen media
define('CONST_PERMIT_DELETE_MEDIA', 'DELETE');
define('CONST_PERMIT_UPLOAD_MEDIA', 'UPLOAD');

//Duog dan thu muc anh r3
define('CONST_DIRECT_R3_IMAGES',SERVER_ROOT.'uploads'.DS);

//path url image uploads logo user
define('CONST_URL_R3_IMAGES',SITE_ROOT.'uploads/');

//array ten don vi cần hiển thị cán bộ đánh giá
$arr_village_name = array(
                            'Văn phòng HĐND và UBND'
                        );
define('CONST_FILETER_CADRE_EVALUATON_VILLAGE',  json_encode($arr_village_name));

//CONST tên đơn vị đăng ký lên support (dùng cho support TamViet)
define('CONST_MY_OU_NAME','CAM_XUYEN');

//img upload
define('CONST_SITE_IMG_ROOT', FULL_SITE_ROOT . 'uploads/public_service/');
define('CONST_SERVER_IMG_ROOT', SERVER_ROOT . 'uploads/public_service/');

define('CONST_SITE_THEME_ROOT', FULL_SITE_ROOT . 'apps/public_service/');
define('CONST_SITE_SERVER_ROOT', SERVER_ROOT . 'apps\public_service'.DS);

/**
 * const su dung cho ma ho so (Ma ngan cua don vi)
 */
define('CONST_SHORT_CORD_OF_UNIT', 'CX');

#sms
define('CONST_TRIGGER_SEND_SMS', true);//bat hien thi chu nang send sms: open: true
// footer tin nhắn tin gửi công dân
define('CONST_HEAD_SEND_SMS','HS cua ong/ba: ');
define('CONST_FOOTER_SEND_SMS',' da co ket qua. Xin moi den nhan ket qua.');

#end sms

//const webservice
$ws_location = 'http://localhost/mot-cua-ha-tinh/index.php?url=r3/webservices';
define('CONST_WS_LOCATION',$ws_location);
define('CONST_WS_URI','urn://tyler/req');

#end sms

#mavach
// Thiet lap trang thai scroll auto cua module tra cuu ma vach
define('CONST_MAVACH_SCROLL',TRUE);
// Thoi gian delay chay
define('CONST_MAVACH_LIMIT_TIME_SCROLL',80);
#End ma vach>>>>>>> .r235

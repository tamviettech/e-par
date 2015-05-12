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
        , _CONST_BAO_CAO_ROLE            => 'Báo cáo'
        , 'REJECT'                       => 'Từ chối HS'
        , 'KHONG_NHAN_HO_SO'             => 'Không nhận hồ sơ'
    );

//fix width and height video trong chi tiet tin bai
define('CONST_VIDEO_WIDTH','100%');
define('CONST_VIDEO_HEIGHT','350px');

define('CONST_ALL_PUBLIC_SERVICE_ROLES', json_encode($arr_all_r3_roles));
define('CONTS_LIMIT_GUIDANCE_LIST',10);

// limit So tin bai noi bat(sticky) hien thi tren trang chu
define('CONST_LIMIT_STICKY_HOME_PAGE',50);
// Mac dinh so ban ghi hien thi tren mot trang
define('CONST_DEFAULT_ROWS_OTHER_NEWS',10);

//mang cau hinh thiet lap hinh anh icon cho cac file dowload guidance theo dinh dang doi file
$arr_icon_file_guidance = array(
                                    'doc'   => 'icon-doc.gif'
                                    ,'xlsx' => 'icon-xlsx.gif'
                                );
define('CONTS_ICON_FILE_GUIDANCE',  json_encode($arr_icon_file_guidance));

define('CONST_WEBLINK_GROUP','DM_NHOM_LIEN_KET_WEB');
//Tin tin bai mac dinh duoc hien thi
define('CONST_DEFAULT_ROWS_PER_PAGE', 10);

$file_upload_template_file_type = SERVER_ROOT . 'uploads' . DS . 'r3' . DS;
define('CONST_TYPE_FILE_UPLOAD',$file_upload_template_file_type);

//img upload
define('CONST_SITE_IMG_ROOT', FULL_SITE_ROOT . 'uploads/public_service/');
define('CONST_SERVER_IMG_ROOT', SERVER_ROOT . 'uploads/public_service/');
define('CONST_SERVER_UPLOADS_ROOT', SERVER_ROOT . 'uploads/public_service/');

// hien thi so tin bai lien quan
define('COUNT_LIMIT_ARTICLE_CONNECT',15);

define('CONST_SITE_THEME_ROOT', FULL_SITE_ROOT . 'apps/public_service/');
define('CONST_SITE_SERVER_ROOT', SERVER_ROOT . 'apps\public_service'.DS);
?>
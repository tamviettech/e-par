<?php
/**
Copyright (C) 2012 Tam Viet Tech.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class mavach_Controller extends Controller
{

    //Roles
    protected $_arr_roles = array(
        //BP Mot-Cua
        _CONST_TIEP_NHAN_ROLE                 => 'Tiếp nhận'
        , _CONST_BAN_GIAO_ROLE                  => 'Bàn giao'
        , _CONST_BO_SUNG_ROLE                   => 'Bổ sung'
        , _CONST_TRA_THONG_BAO_NOP_THUE_ROLE    => 'Trả TB thuế'
        , _CONST_NHAN_BIEN_LAI_NOP_THUE_ROLE    => 'Nhận BL thuế'
        , _CONST_THU_PHI_ROLE                   => 'Thu phí'
        , _CONST_TRA_KET_QUA_ROLE               => 'Trả KQ'
        , _CONST_IN_PHIEU_TIEP_NHAN_ROLE        => 'In lại phiếu TN'
        , _CONST_CHUYEN_LEN_HUYEN_ROLE          => 'Chuyển HS lên Huyện'
        , _CONST_TRA_HO_SO_VE_XA_ROLE           => 'Trả HS về xã'
        , _CONST_XAC_NHAN_HO_SO_LIEN_THONG_ROLE => 'Xác nhận hồ sơ liên thông'

        //BO SUNG
        , _CONST_THONG_BAO_BO_SUNG_ROLE => 'Thông báo bổ sung'

        //Bo phan Thue
        , _CONST_NOP_HO_SO_SANG_CHI_CUC_THUE_ROLE         => 'Chuyển hồ sơ sang chi cục thuế'
        , _CONST_NHAN_THONG_BAO_CUA_CHI_CUC_THUE_ROLE     => 'Nhận thông báo của chi cục thuế'
        , _CONST_CHUYEN_THONG_BAO_THUE_VE_BP_MOT_CUA_ROLE => 'Chuyển thông báo thuế về bộ phận "Môt-cửa"'

        //Phong chuyen mon
        , _CONST_PHAN_CONG_ROLE                        => 'Phân công thụ lý'
        , _CONST_PHAN_CONG_LAI_ROLE                    => 'Thay đổi thụ lý'
        , _CONST_THU_LY_ROLE                           => 'Thụ lý'
        , _CONST_CHUYEN_YEU_CAU_XAC_NHAN_XUONG_XA_ROLE => 'Chuyển yêu cầu xác nhận xuống xã'
        , _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE          => 'Thụ lý HS liên thông'
        , _CONST_YEU_CAU_THU_LY_LAI_ROLE               => 'Yêu cầu thụ lý lại'
        , _CONST_XET_DUYET_ROLE                        => 'Xét duyệt hồ sơ'
        , _CONST_XET_DUYET_BO_SUNG_ROLE                => 'Xét duyệt hồ sơ bổ sung'
        , _CONST_CHUYEN_LAI_BUOC_TRUOC_ROLE            => 'Yêu cầu chuyển hồ sơ'

        //Lanh dao don vi
        , _CONST_KY_ROLE              => 'Ký duyệt hồ sơ'
        , _CONST_Y_KIEN_LANH_DAO_ROLE => 'Ý kiến lãnh đạo'

        //Chung
        , _CONST_TRA_CUU_ROLE => 'Tra cứu'
        , _CONST_BAO_CAO_ROLE => 'Báo cáo'
        , 'REJECT'            => 'Từ chối HS'
    );
    protected $_active_role;
    protected $_record_type;
    protected $_activity_filter = array(
        0 => 'Tất cả hồ sơ'
        , 1 => 'Hồ sơ vừa tiếp nhận'
        , 2 => 'Hồ sơ chờ bổ sung'
        , 3 => 'Hồ sơ bị từ chối'
        , 4 => 'Hồ sơ đang giải quyết'
        , 5 => 'Hồ sơ đang trình ký'
        , 6 => 'Hồ sơ chờ trả kết quả'
        , 7 => 'Hồ sơ đã trả kết quả'
        , 8 => 'Hồ sơ đang chậm tiến độ'
        , 9 => 'Hồ sơ quá hạn trả kết quả'
    );

    function __construct()
    {
        parent::__construct('r3', 'mavach');
        $this->view->template->show_left_side_bar = 1;

        $this->view->template->controller_url  = $this->view->get_controller_url();
        $this->view->template->activity_filter = $this->_activity_filter;
        $this->view->role_text                 = $this->_arr_roles;

        #$this->model->db->debug=0;
    }

    function main()
    {
        $v_record_no              = get_request_var('txt_record_no', '');
        $VIEW_DATA                = NULL;
        $VIEW_DATA['v_record_no'] = $v_record_no;
        if ($v_record_no != '')
        {
            //Thông tin bản khai
            $VIEW_DATA['arr_single_record_statistic'] = $this->model->qry_single_record_statistics($v_record_no);
        }

        $this->view->render('tra_cuu_ma_vach', $VIEW_DATA);
    }

}
<?php
/**


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
defined('SERVER_ROOT') or die();
/**
 * @package logistic
 * @author Tam Viet <ltbinh@gmail.com>
 * @author Duong Tuan Anh <goat91@gmail.com>
 */
$arr_roles = array(
    _CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE     => 'Xác nhận hồ sơ nộp qua Internet',
    _CONST_TIEP_NHAN_ROLE                           => 'Tiếp nhận hồ sơ',
    _CONST_BAN_GIAO_ROLE                            => 'Bàn giao hồ sơ',
    _CONST_BO_SUNG_ROLE                             => 'Bổ sung hồ sơ',
    _CONST_TRA_KET_QUA_ROLE                         => 'Trả kết quả cho công dân',
    _CONST_IN_PHIEU_TIEP_NHAN_ROLE                  => 'In phiếu tiếp nhận',
    _CONST_CHUYEN_LEN_HUYEN_ROLE                    => 'Chuyển hồ sơ lên huyện',
    _CONST_TRA_HO_SO_VE_XA_ROLE                     => 'Trả hồ sơ về xã',
    _CONST_THONG_BAO_BO_SUNG_ROLE                   => 'Thông báo bổ sung hồ sơ',
    _CONST_TAI_CHINH_ROLE                           => 'Thực hiện nghĩa vũ tài chính',
    _CONST_PHAN_CONG_ROLE                           => 'Phân công thụ lý',
    _CONST_PHAN_CONG_LAI_ROLE                       => 'Phân công thụ lý lại',
    _CONST_THU_LY_ROLE                              => 'Thụ lý hồ sơ',
    _CONST_CHUYEN_YEU_CAU_XAC_NHAN_XUONG_XA_ROLE    => 'Chuyển yêu cầu xác nhận xuống xã',
    _CONST_THU_LY_HO_SO_LIEN_THONG_ROLE             => 'Thụ lý hồ sơ liên thông',
    _CONST_YEU_CAU_THU_LY_LAI_ROLE                  => 'Yêu cầu thụ lý lại',
    _CONST_XAC_NHAN_HO_SO_LIEN_THONG_ROLE           => 'Xác nhận hồ sơ liên thông',
    _CONST_XET_DUYET_ROLE                           => 'Xét duyệt',
    _CONST_XET_DUYET_BO_SUNG_ROLE                   => 'Xét duyệt bổ sung',
    _CONST_KY_ROLE                                  => 'Ký duyệt',
    _CONST_THU_PHI_ROLE                             => 'Thu phí',
    _CONST_Y_KIEN_LANH_DAO_ROLE                     => 'Ý kiến chỉ đạo',
    _CONST_TRA_CUU_ROLE                             => 'Tra cứu',
    _CONST_NOP_HO_SO_SANG_CHI_CUC_THUE_ROLE         => 'Nộp hồ sơ sang chi cục thuế',
    _CONST_NHAN_THONG_BAO_CUA_CHI_CUC_THUE_ROLE     => 'Nhận thông báo của chi cục thuế',
    _CONST_CHUYEN_THONG_BAO_THUE_VE_BP_MOT_CUA_ROLE => 'Chuyển thông báo của chi cục thuế về BP một cửa',
    _CONST_TRA_THONG_BAO_NOP_THUE_ROLE              => 'Trả thông báo nộp thuế',
    _CONST_CHUYEN_LAI_BUOC_TRUOC_ROLE               => 'Trả hồ sơ về bước trước',
    _CONST_CHUYEN_HO_SO_LEN_SO_ROLE                 => 'Chuyển hồ sơ lên sở',
    _CONST_NHAN_HO_SO_TU_SO_ROLE                    => 'Nhận hồ sơ từ sở',
    _CONST_PHAN_CONG_SAU_THUE_ROLE                  => 'Phân công Sau thuế',
    _CONST_THU_LY_SAU_THUE_ROLE                     => 'Thụ lý sau thuế',
    _CONST_DUYET_SAU_THUE_ROLE                      => 'Duyệt sau thuế',
    'TRA_HO_SO_VE_XA'                               => 'Trả hồ sơ về xã',
    'KHONG_NHAN_HO_SO'                              => 'Từ chối hồ sơ'
);
?>

<?php foreach ($arr_new_actions as $action): ?>
    <?php
    $v_date_time = date_create($action['C_DATE_TIME'])->format('d-m-Y H:i');
    $v_user_name = $action['C_USER_NAME'];
    $v_task_code = $action['C_TASK_CODE'];
    $v_role      = get_role_from_task_code($v_task_code);
    $text        = isset($arr_roles[$v_role]) ? $arr_roles[$v_role] : $v_role;
    ?>
    <span><b>[<?php echo $v_date_time ?>] <?php echo $v_user_name ?>:</b><?php echo $text ?></span><br/>
<?php endforeach; ?>

<?php

function get_role_from_task_code($task_code)
{
    $str = $task_code;
    $pos = strrpos($task_code, '::', -1);
    if ($pos !== false)
    {
        $str = substr($task_code, $pos + strlen('::'));
    }

    $pos = strpos($str, '[');
    if ($pos !== false)
    {
        $str = substr($str, 0, $pos);
    }
    return trim($str);
}
?>
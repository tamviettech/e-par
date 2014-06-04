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
<div class="navbar navbar-inverse top-nav">
    <div class="navbar-inner">
        <div class="container">
            <span class="home-link">
                <a href="<?php echo SITE_ROOT;?>" class="icon-home"></a>
            </span>
            
            <div class="nav-collapse">
                <ul class="nav">
                    <?php if (session::get('is_admin') > 0 OR check_permission('QUAN_TRI_BACKUP_RESTORE', 'CORES')): ?>
                        <li class="dropdown <?php echo ($this->active_menu == 'quan_tri_he_thong') ? ' active  ': '';?>">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;"><i class=" icon-cogs"></i>Quản trị hệ thống<b class="icon-angle-down"></b></a>
                            <div class="dropdown-menu">
                                <ul>
                                   <?php if (session::get('is_admin') > 0):; ?>
                                    <li>
                                        <a href="<?php echo SITE_ROOT . build_url('cores/xlist/');?>"><i class=" icon-file-alt"></i>Loại danh mục</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo SITE_ROOT . build_url('cores/xlist/dsp_all_list');?>"><i class=" icon-file-alt"></i>Đối tượng danh mục</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo SITE_ROOT . build_url('cores/user');?>"><i class=" icon-user"></i>Người sử dụng</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo SITE_ROOT . build_url('cores/calendar');?>"><i class=" icon-calendar"></i>Ngày làm việc/ngày nghỉ</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo SITE_ROOT . build_url('cores/application');?>"><i class=" icon-desktop"></i>Ứng dụng</a>
                                    </li>
                                    
                                    <?php endif;?>
                                    <!--Hiển thị khi có quyền backup-->
                                    <?php if(check_permission('QUAN_TRI_BACKUP_RESTORE', 'CORES')):; ?>
                                    <li>
                                        <a href="<?php echo SITE_ROOT . build_url('cores/backup_restore');?>"><i class=" icon-briefcase"></i>Sao lưu và khôi phục dữ liệu</a>
                                    </li>
                                    <?php endif; ?>
                                    <?php if (session::get('is_admin') > 0):; ?>
                                    <li>
                                        <a href="<?php echo SITE_ROOT . build_url('cores/system_config');?>"><i class=" icon-cogs"></i>Cấu hình tham số hệ thống</a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </li>
                    <?php endif;?>
                    <li class="dropdown <?php echo ($this->active_menu == 'quan_tri_ho_so') ? ' active  ': '';?>">
                        <?php if (check_permission('QUAN_TRI_DANH_MUC_LOAI_HO_SO', 'R3') OR  check_permission('QUAN_TRI_QUY_TRINH_XU_LY_HO_SO', 'R3') OR check_permission('QUAN_TRI_LUAT_CAN_LOC_HO_SO', 'R3') OR check_permission('THEO_DOI_NGUOI_DUNG', 'R3') ):?>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;"><i class=" icon-cogs"></i>Quản trị hồ sơ Một-Cửa<b class="icon-angle-down"></b></a>
                        <div class="dropdown-menu">
                            <ul>
                                <?php if (check_permission('QUAN_TRI_DANH_MUC_LOAI_HO_SO', 'R3')): ?>
                                    <li><a href="<?php echo SITE_ROOT . build_url('r3/record_type'); ?>"><i class=" icon-file-alt"></i>Quản trị danh mục loại hồ sơ</a></li>
                                <?php endif; ?>
                                <?php if (check_permission('QUAN_TRI_QUY_TRINH_XU_LY_HO_SO', 'R3')): ?>
                                    <li><a href="<?php echo SITE_ROOT . build_url('r3/workflow'); ?>"><i class=" icon-file-alt"></i>Quản trị quy trình xử lý hồ sơ</a></li>
                                <?php endif; ?>
                                <?php if (check_permission('QUAN_TRI_LUAT_CAN_LOC_HO_SO', 'R3')): ?>
                                    <li><a href="<?php echo SITE_ROOT . build_url('r3/blacklist'); ?>"><i class=" icon-file-alt"></i>Quản trị quy luật cản lọc hồ sơ</a></li>
                                <?php endif; ?>
                                <?php if (check_permission('THEO_DOI_NGUOI_DUNG', 'R3')): ?>
                                    <li><a href="<?php echo SITE_ROOT . build_url('r3/logistic'); ?>"><i class=" icon-file-alt"></i>Theo dõi hoạt động người dùng</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </li>
              </ul>
            </div>       
            
                   
            <div class="btn-toolbar pull-right notification-nav">
                <?php if (Session::get('login_name') !== NULL): ?>
                    <div class="btn-group">
                        <div class="dropdown">
                            <a data-toggle="dropdown" class="btn dropdown-toggle" href="javascript:void(0);">
                                <i class="icon-user"></i>
                                    <?php echo Session::get('user_name'); ?> 
                                <b class="icon-angle-down"></b>
                            </a>
                            <div class="dropdown-menu">
                                <ul>
                                <?php if (session::get('auth_by') != 'AD'): ?>
                                    <?php $v_change_password_url = SITE_ROOT . build_url('cores/user/dsp_change_password'); ?>
                                    <li>
                                        <a href="javascript:void(0)" onclick="showPopWin('<?php echo $v_change_password_url; ?>' , 500,400, null);">
                                            <i class="icon-lock"></i> Đổi mật khẩu
                                        </a>
                                    </li>
                                <?php endif; ?>
                                    <li><a href="<?php echo SITE_ROOT?>logout.php"><i class="icon-signout"></i> Đăng thoát</a></li>
                                    <li>
                                        <a href="<?php echo SITE_ROOT . build_url('r3/mapping');?>">
                                            <i class="icon-list-alt"></i> Bảng ánh xạ thủ tục
                                        </a>
                                    </li>
                                </ul>
                            </div>

                                <a class="btn btn-notification" href="<?php echo SITE_ROOT?>logout.php" title="Đăng thoát">
                                    <i class="icon-signout"></i>
                            </a>
                        </div>
				<?php endif;?>
                    </div>
        </div>
    </div>
</div>
</div>
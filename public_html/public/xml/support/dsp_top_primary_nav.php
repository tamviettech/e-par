<div class="navbar navbar-inverse top-nav">
    <div class="navbar-inner">
        <div class="container">
            <span class="home-link">
                <a href="<?php echo SITE_ROOT;?>" class="icon-home"></a>
            </span>
            
            <div class="nav-collapse">
                <ul class="nav">
                    <?php if (session::get('is_admin') > 0): ?>
                        <li class="dropdown <?php echo ($this->active_menu == 'quan_tri_he_thong') ? ' active  ': '';?>">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;"><i class=" icon-cogs"></i>Quản trị hệ thống<b class="icon-angle-down"></b></a>
                            <div class="dropdown-menu">
                                <ul>
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
                                    <li>
                                        <a href="<?php echo SITE_ROOT . build_url('cores/system_config');?>"><i class=" icon-cogs"></i>Cấu hình tham số hệ thống</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    <?php endif;?>
              </ul>
            </div>       
            
                   
            <div class="btn-toolbar pull-right notification-nav">
                <?php if (Session::get('login_name') !== NULL): ?>
                    <div class="btn-group">
						<div class="dropdown">
							<a data-toggle="dropdown" class="btn dropdown-toggle" href="javascript:void(0);">
							    <i class="icon-user"></i><?php echo Session::get('user_name'); ?> <b class="icon-angle-down"></b>
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
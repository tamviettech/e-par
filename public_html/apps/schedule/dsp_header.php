<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <link rel="shortcut icon" href="favicon.ico" />
        <title><?php echo session::get('user_name');?>::<?php echo $this->eprint($this->title); ?></title>
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/1008_24_1_1.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/winform.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/oss-style.css" type="text/css" media="screen" />

        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>
        <script type="text/javascript">
            var SITE_ROOT='<?php echo SITE_ROOT;?>';
            var _CONST_LIST_DELIM = '<?php echo _CONST_LIST_DELIM;?>';
        </script>
        <!--  Modal dialog -->
        <script src="<?php echo SITE_ROOT; ?>public/js/submodal.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/css/subModal.css" rel="stylesheet" type="text/css"/>

        <script src="<?php echo SITE_ROOT; ?>public/js/qm.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/css/qm.css" rel="stylesheet" type="text/css"/>

        <!-- Tooltip -->
        <script src="<?php echo SITE_ROOT; ?>public/js/overlib_mini.js" type="text/javascript"></script>

        <script src="<?php echo SITE_ROOT; ?>public/js/mylibs.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/DynamicFormHelper.js" type="text/javascript"></script>
		
		<!-- Upload -->
        <script src="<?php echo SITE_ROOT;?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT;?>public/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT;?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>
        
        <?php if (isset($this->local_js)):?>
            <script src="<?php echo $this->local_js;?>" type="text/javascript"></script>
        <?php endif;?>
    </head>
    <body>
        <DIV id=overDiv style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>
        <div class="container_24">
			<div class="grid_24" id="banner"></div>
            <div class="grid_24 top-nav-box" id="header">
                <div id="user_info">
                    <?php if (session::get('is_admin') > 0): ?>
                        <img src="<?php echo SITE_ROOT;?>public/images/config.png" />Quản trị hệ thống:
                        <a href="<?php echo SITE_ROOT;?>cores/xlist/">Loại danh mục</a>
                        | <a href="<?php echo SITE_ROOT;?>cores/xlist/dsp_all_list">Đối tượng danh mục</a>
                        | <a href="<?php echo SITE_ROOT;?>cores/user">Người sử dụng</a>
                        | <a href="<?php echo SITE_ROOT;?>cores/calendar">Ngày làm việc/ngày nghỉ</a>
                    <?php endif;?>
                </div>
                <div id="date"><?php echo jwDate::vn_day_of_week() . ', ' . date("d/m/Y");?>
                    <?php if (Session::get('login_name') !== NULL): ?>
                        <img src="<?php echo SITE_ROOT;?>public/images/users.png" />
                        <label><?php echo Session::get('user_name');?></label>
                        <?php $v_change_password_url = SITE_ROOT . 'cores/user/dsp_change_password';?>
                        <label>(<a href="javascript:void(0)" onclick="showPopWin('<?php echo $v_change_password_url;?>' , 500,400, null);">Đổi mật khẩu</a>)</label>
                        <label>(<a href="<?php echo SITE_ROOT;?>logout.php">Đăng thoát</a>)</label>
                    <?php endif; ?>
                </div>
			</div>
            <div class="grid_24 right">
                <div id="doc_direction_menu">
                    <?php if (!isset($this->doc_direction)){ $this->doc_direction = '';}?>
                    <ul>
                        <li><img src="<?php echo SITE_ROOT;?>/public/images/vbden.png" /><a href="<?php echo SITE_ROOT?>edoc/doc/vbden" <?php echo ($this->doc_direction == 'VBDEN') ? ' class="active"' : '';?>>Văn bản đến</a></li>
                        <li><img src="<?php echo SITE_ROOT;?>/public/images/vbdi.png" /><a href="<?php echo SITE_ROOT?>edoc/doc/vbdi" <?php echo ($this->doc_direction == 'VBDI') ? ' class="active"' : '';?>>Văn bản đi</a></li>
                        <li><img src="<?php echo SITE_ROOT;?>/public/images/vbnoibo.png" /><a href="<?php echo SITE_ROOT?>edoc/doc/vbnoibo" <?php echo ($this->doc_direction == 'VBNOIBO') ? ' class="active"' : '';?>>Văn bản nội bộ</a></li>
                        <li><img src="<?php echo SITE_ROOT;?>/public/images/tra_cuu.png" /><a href="<?php echo SITE_ROOT?>edoc/doc/vbtracuu" <?php echo ($this->doc_direction == 'VBTRACUU') ? ' class="active"' : '';?>>Tra cứu</a></li>
                        <li><img src="<?php echo SITE_ROOT;?>/public/images/folder-32x32.png" /><a href="<?php echo SITE_ROOT?>edoc/folder" <?php echo ($this->doc_direction == 'FOLDER') ? ' class="active"' : '';?>>Hồ sơ lưu</a></li>
                        <li><img src="<?php echo SITE_ROOT;?>/public/images/schedule-128.png" width="32px" height="32px"/><a href="<?php echo SITE_ROOT?>schedule/event" <?php echo ($this->doc_direction == 'EVENT') ? ' class="active"' : '';?>>Lịch công tác</a></li>
                        <li><img src="<?php echo SITE_ROOT;?>/public/images/news.png" /><a href="<?php echo SITE_ROOT?>news/article" <?php echo ($this->doc_direction == 'NEWS') ? ' class="active"' : '';?>>Bản tin</a></li>
                    </ul>
                </div>
			</div>
            <?php if ($this->show_left_side_bar): ?>
            <div class="clear">&nbsp;</div>
                <div class="container_24" id="wrapper">
                    <div class="grid_5">
                        <div class="edit-box" id="left_side_bar">
                            <?php if (isset($this->arr_doc_type)):?>
                            <ul class="doc-type-tree"><?php 
                       			
                                foreach ($this->arr_doc_type as $key => $val)
                                {
                                    $v_class_string = (strtolower($this->doc_type) == $key) ? ' class="active"' : '';
                                    echo '<li><a href="' . $this->function_url . $key . '"' . $v_class_string . '>' . $val . '</a></li>';
                                }
                                ?>
                            </ul>
                            <?php endif;?>
                        </div>
                    </div>
                    <div class="grid_19" id="content_right">
            <?php else: ?>
                    <div class="grid_24" id="content_right">
            <?php endif; ?>
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
        <link rel="stylesheet" href="<?php echo $this->stylesheet_url; ?>" type="text/css" media="screen" />
         <!-- easuui css-->
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>public/js/jquery/easyui/themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>public/js/jquery/easyui/themes/icon.css">
        <!--base-->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>public/js/jquery/easyui/jquery.easyui.min.js"></script>
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
        <script>
                    $(function(){
                    $(window).resize(function(){
                    $('#main-layout').layout('resize');
                    });
                    });
        </script>
        <div id="main-layout" class="easyui-layout" fit="true" style="height:600px;">
            <div data-options="region:'north',border:false" id="header" style="height: 123px;background-color: #E9F1FF">
                <div style="height: 70px" id="banner">
                    &nbsp;
                </div>
                <!--menu cores-->
                <div class="top-nav-box" id="header">
                    <div id="user_info">
                        <?php if (session::get('is_admin') > 0): ?>
                            <img src="<?php echo SITE_ROOT; ?>public/images/config.png" />Quản trị hệ thống:
                            <a href="<?php echo SITE_ROOT; ?>cores/xlist/">Loại danh mục</a>
                            | <a href="<?php echo SITE_ROOT; ?>cores/xlist/dsp_all_list">Đối tượng danh mục</a>
                            | <a href="<?php echo SITE_ROOT; ?>cores/user">Người sử dụng</a>
                            | <a href="<?php echo SITE_ROOT; ?>cores/calendar">Ngày làm việc/ngày nghỉ</a>
                        <?php endif; ?>
                    </div>
                    <div id="date"><?php echo jwDate::vn_day_of_week() . ', ' . date("d/m/Y"); ?>
                        <?php if (Session::get('login_name') !== NULL): ?>
                            <img src="<?php echo SITE_ROOT; ?>public/images/users.png" />
                            <label><?php echo Session::get('user_name'); ?></label>
                            <?php $v_change_password_url = SITE_ROOT . 'cores/user/dsp_change_password'; ?>
                            <label>(<a href="javascript:void(0)" onclick="showPopWin('<?php echo $v_change_password_url; ?>' , 500,400, null);">Đổi mật khẩu</a>)</label>
                            <label>(<a href="<?php echo SITE_ROOT; ?>logout.php">Đăng thoát</a>)</label>
                        <?php endif; ?>
                    </div>
                </div>
                <!--menu edoc-->
                <div class="right">
                    <div id="doc_direction_menu">
                        <?php
                        if (!isset($this->doc_direction)) {
                            $this->doc_direction = '';
                        }
                        $menu_images_root = SITE_ROOT . 'public/images/';
                        $main_menu        = array(
                            'VBDEN'    => array('img'   => 'vbden.png', 'label' => 'Văn bản đến', 'url'   => 'edoc/doc/vbden'),
                            'VBDI'     => array('img'   => 'vbdi.png', 'label' => 'Văn bản đi', 'url'   => 'edoc/doc/vbdi'),
                            'VBNOIBO'  => array('img'   => 'vbnoibo.png', 'label' => 'Văn bản nội bộ', 'url'   => 'edoc/doc/vbnoibo'),
                            'VBTRACUU' => array('img'   => 'tra_cuu.png', 'label' => 'Văn bản tra cứu', 'url'   => 'edoc/doc/vbtracuu'),
                            'FOLDER'   => array('img'   => 'folder-32x32.png', 'label' => 'Hồ sơ lưu', 'url'   => 'edoc/folder'),
                            'EVENT'    => array('img'   => 'schedule-128.png', 'label' => 'Lịch công tác', 'url'   => 'schedule/event'),
                            'NEWS'     => array('img'   => 'news.png', 'label' => 'Bản tin', 'url'   => 'news/article')
                        );
                        ?>
                        <ul>
                            <?php foreach($main_menu as $key => $details): ?>
                                <?php $active = $key == $this->doc_direction ? 'active' : '' ?>
                                <li class="<?php echo $active ?>">
                                    <a href="<?php echo SITE_ROOT . $details['url'] ?>">
                                        <img src="<?php echo $menu_images_root . $details['img'] ?>" width="16" height="16"/>
                                        <?php echo $details['label'] ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div><!-- /region:'north' #header -->
            <?php if ($this->show_left_side_bar): ?>
            <div region="west" style="width:200px;" >
                <div class="edit-box" id="left_side_bar">
                    <?php if (isset($this->arr_doc_type)): ?>
                        <ul class="doc-type-tree"><?php
                            foreach ($this->arr_doc_type as $key => $val) {
                                $v_class_string = (strtolower($this->doc_type) == $key) ? ' class="active"' : '';
                                echo '<li><a href="' . $this->function_url . $key . '"' . $v_class_string . '>' . $val . '</a></li>';
                            }
                            ?>
                        </ul>
                        <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <div region="center" class="div_main_content" title="" border="false" >   
            
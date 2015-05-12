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
<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <link rel="shortcut icon" href="favicon.ico" />
        <title><?php echo session::get('user_name');?>::<?php echo $this->eprint($this->title); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
<!--        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="screen" />
        <lin rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/1008_24_1_1.css" type="text/css" media="screen" />-->
        <!--<link rel="stylesheet" href="<?php echo $this->stylesheet_url;?>" type="text/css" media="screen" />-->
        
        
        
        <!--===============theme resource=================-->
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/bootstrap.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/bootstrap-responsive.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/font-awesome.css" type="text/css" media="screen" />
        <!--[if IE 7]>
            <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/public/themes/bootstrap/css/ie/ie7.css" />
        <![endif]-->
        <!--[if IE 8]>
            <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/public/themes/bootstrap/css/ie/ie8.css" />
        <![endif]-->
        <!--[if IE 9]>
            <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/public/themes/bootstrap/css/ie/ie9.css" />
        <![endif]-->
        <!--[if IE 7]>
            <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/public/themes/bootstrap/css/font-awesome-ie7.min.css" />
        <![endif]-->
        
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/styles.css" type="text/css" media="screen" />
        <link id='themes' href="#" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/dosis.css" type="text/css" media="screen" />
        
        <!-- fav and touch icons -->
        <link rel="shortcut icon" href="<?php echo SITE_ROOT; ?>favicon.ico" type="text/css" media="screen" />
        <link rel="apple-touch-icon-precomposed" size="144x144" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-144-precomposed.png" type="text/css" media="screen" />
        <link rel="apple-touch-icon-precomposed" size="114x114" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-114-precomposed.png" type="text/css" media="screen" />
        <link rel="apple-touch-icon-precomposed" size="72x72" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-72-precomposed.png" type="text/css" media="screen" />
        <link rel="apple-touch-icon-precomposed" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-57-precomposed.png" type="text/css" media="screen" />
        
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <!--===============javascript=================-->
<!--        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>-->
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/accordion.nav.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/custom.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/respond.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/ios-orientationchange-fix.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/bootbox.js" type="text/javascript"></script>
        <!--===============My resource=================-->
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>
        <!--  Right-click context menu -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.contextMenu.js" type="text/javascript"></script>
        <!-- Upload -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>
        <script type="text/javascript">
            var SITE_ROOT='<?php echo SITE_ROOT;?>';
            var _CONST_LIST_DELIM = '<?php echo _CONST_LIST_DELIM;?>';
            var QS = '<?php echo check_htacces_file() ? '?' : '&';?>';
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

        <?php if (isset($this->local_js)):?>
            <script src="<?php echo $this->local_js;?>" type="text/javascript"></script>
        <?php endif;?>
            
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/cores/style.css" type="text/css" media="screen" />   
    </head>
    <body>        
        <div class="layout">
            <DIV id=overDiv style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>
<!--            <div class="container_24" id="main">
			<div class="grid_24" id="banner">
			    <label id="unit_full_name"><?php echo get_xml_value(simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml'), '//full_name')?></label>
			</div>
            <div class="grid_24 top-nav-box" id="header">
                <div id="user_info">
                    <?php echo VIEW::nav_home();?>
                    <?php if (session::get('is_admin') > 0): ?>
                        <img src="<?php echo SITE_ROOT;?>public/images/config.png" />Quản trị hệ thống:
                        <a href="<?php echo SITE_ROOT . build_url('cores/xlist/');?>">Loại danh mục</a>
                        | <a href="<?php echo SITE_ROOT . build_url('cores/xlist/dsp_all_list');?>">Đối tượng danh mục</a>
                        | <a href="<?php echo SITE_ROOT . build_url('cores/user');?>">Người sử dụng</a>
                        | <a href="<?php echo SITE_ROOT . build_url('cores/calendar');?>">Ngày làm việc/ngày nghỉ</a>
                        | <a href="<?php echo SITE_ROOT . build_url('cores/application');?>">Ứng dụng</a>
                    <?php endif;?>
                </div>
                <div id="date"><?php echo jwDate::vn_day_of_week() . ', ' . date("d/m/Y");?>
                    <?php if (Session::get('login_name') !== NULL): ?>
                        <img src="<?php echo SITE_ROOT;?>public/images/users.png" />
                        <label><?php echo Session::get('user_name');?></label>
                        <?php $v_change_password_url = SITE_ROOT . build_url('cores/user/dsp_change_password');?>
                        <label>(<a href="javascript:void(0)" onclick="showPopWin('<?php echo $v_change_password_url;?>' , 500,400, null);">Đổi mật khẩu</a>)</label>
                        <label>(<a href="<?php echo SITE_ROOT;?>logout.php">Đăng thoát</a>)</label>
                    <?php endif; ?>
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
                    </div>-->

        <div style="width: 100%;">
            <div style="background: #da3610;width: 220px;float:left;height: 50px;">
                <center>
                    <img src="<?php echo SITE_ROOT; ?>public/logoQuocHuy.png" style="width: 45px;height: 45px;">
                </center>
            </div>
            <div class="main-wrapper" style="padding: 0;">
                <div class="navbar navbar-inverse top-nav">
                    <div class="navbar-inner">
                        <div class="container">
                            <span class="home-link">
                                <a href="<?php echo SITE_ROOT; ?>" class="icon-home"></a>
                            </span>
                            <div class="nav-collapse">
                                <ul class="nav">
                                    <li class="dropdown">
                                        <a class="dropdown-toggle" href="<?php echo SITE_ROOT . build_url('license/license_type/')?>">
                                            <i class="icon-cogs"></i>
                                            Quản lý loại hồ sơ
                                        </a>        
                                    </li>
                                    <li class="dropdown">
                                        <a  href="<?php echo SITE_ROOT . build_url('license/record/')?>">
                                            <i class="icon-cogs"></i>
                                            Quản lý hồ sơ
                                        </a>           
                                    </li>
                                    <li class="dropdown">
                                        <a  href="<?php echo SITE_ROOT . build_url('license/report/')?>">
                                            <i class="icon-cogs"></i>
                                            Báo cáo
                                        </a>           
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="btn-toolbar pull-right notification-nav">
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <a data-toggle="dropdown" class="btn dropdown-toggle" href="javascript:void(0);">
                                            <i class="icon-user"></i>
                                            Quản trị hệ thống 
                                            <b class="icon-angle-down"></b>
                                        </a>
                                        <div class="dropdown-menu">
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="showPopWin('<?php echo SITE_ROOT . '/cores/user/dsp_change_password'; ?>', 500,400, null);">
                                                        <i class="icon-lock"></i>
                                                        Đổi mật khẩu
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo SITE_ROOT . 'logout.php'?>">
                                                        <i class="icon-signout"></i>
                                                        Đăng thoát
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="showPopWin('<?php echo SITE_ROOT . '/cores/user/dsp_change_password'; ?>', 500,400, null);">
                                                        <i class="icon-list-alt"></i>
                                                         Bảng ánh xạ thủ tục                                        
                                                    </a>
                                                </li>
                                            </ul>
                                        </div> 
                                        <a class="btn btn-notification" href="<?php echo SITE_ROOT . 'logout.php'?>" title="Đăng thoát">
                                            <i class="icon-signout"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear">&nbsp;</div>
            
                        
            

        
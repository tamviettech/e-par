<?php if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
} ?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <link rel="shortcut icon" href="<?php echo SITE_ROOT; ?>favicon.ico" />
        <title>Bảng điều khiển trung tâm - Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- theme resource -->
        <link href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/font-awesome.css">
        <!--[if IE 7]>
            <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/font-awesome-ie7.min.css">
        <![endif]-->
        <link href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/styles.css" rel="stylesheet">
        <link id="themes" href="#" rel="stylesheet">
        <!--[if IE 7]>
            <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/ie/ie7.css" />
        <![endif]-->
        <!--[if IE 8]>
            <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/ie/ie8.css" />
        <![endif]-->
        <!--[if IE 9]>
            <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/ie/ie9.css" />
        <![endif]-->
        <link href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/dosis.css" rel="stylesheet" type="text/css">
        <!--fav and touch icons -->
        <link rel="shortcut icon" href="ico/favicon.ico">
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-57-precomposed.png">
        <link rel="stylesheet" href="<?php echo $this->stylesheet_url; ?>" type="text/css" media="screen" />
    </head>
    <body>
        <div class="layout">
            <DIV id=overDiv style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>
            <div style="width: 100%;">
                <div style="background: #da3610;width: 220px;float:left;height: 50px;">
                    <div style="float: left;padding-left: 10px;padding-top: 2px;">
                        <img src="<?php echo SITE_ROOT . 'public/logoQuocHuy.png' ?>" style="width: 45px;height: 45px;"/>
                    </div>
                    <center style="color: white;font-weight: bold;padding-top: 5px;font-size: 13px;font-family: Tohama">
                        <?php
                            $dom_unit      = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
                            if (Session::get('la_can_bo_cap_xa'))
                                $unit_fullname = Session::get('ou_name');
                            else
                                $unit_fullname = mb_strtoupper(xpath($dom_unit, '//full_name', XPATH_STRING), 'UTF-8');
                            echo $unit_fullname;
                        ?>
                    </center>
                </div>
                <div class="main-wrapper" style="padding: 0;">
                    <div class="navbar navbar-inverse top-nav">
                        <div class="navbar-inner">
                            <div class="container">
                                <div class="btn-toolbar pull-right notification-nav">
                                    <?php if (Session::get('login_name') !== NULL): ?>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <a data-toggle="dropdown" class="btn dropdown-toggle" href="javascript:void(0);">
                                                    <i class="icon-user"></i>
                                                        <?php echo Session::get('user_name'); ?> 
                                                    <i class="icon-th" style="font-size: 14px; margin-left: 5px;"></i>
                                                </a>
                                                <div class="dropdown-menu">
                                                    <ul>
                                                        <?php if (session::get('auth_by') != 'AD'): ?>
                                                            <?php $v_change_password_url = SITE_ROOT . build_url('cores/user/dsp_change_password'); ?>
                                                            <li>
                                                                <a href="javascript:void(0)" onclick="showPopWin('<?php echo $v_change_password_url; ?>', 500, 400, null);">
                                                                    <i class="icon-lock"></i> Đổi mật khẩu
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <li><a href="<?php echo SITE_ROOT ?>logout.php"><i class="icon-signout"></i> Đăng thoát</a></li>
                                                        <li>
                                                            <a href="<?php echo SITE_ROOT . build_url('r3/mapping'); ?>">
                                                                <i class="icon-list-alt"></i> Bảng ánh xạ thủ tục
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <a class="btn btn-notification" href="<?php echo SITE_ROOT ?>logout.php" title="Đăng thoát">
                                                    <i class="icon-signout"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>           




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
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Đăng nhập hệ thống</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- styles -->
        <link href="<?php echo SITE_ROOT ?>public/themes/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo SITE_ROOT ?>public/themes/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
        <link href="<?php echo SITE_ROOT ?>public/themes/bootstrap/css/font-awesome.css" rel="stylesheet" >
        <!--[if IE 7]>
                    <link rel="stylesheet" href="<?php echo SITE_ROOT ?>public/themes/bootstrap/css/font-awesome-ie7.min.css">
                <![endif]-->
        <link href="<?php echo SITE_ROOT ?>public/themes/bootstrap/css/styles.css" rel="stylesheet">
        <link id="themes" href="#" rel="stylesheet">

        <!--[if IE 7]>
                    <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT ?>public/themes/bootstrap/css/ie/ie7.css" />
                <![endif]-->
        <!--[if IE 8]>
                    <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT ?>public/themes/bootstrap/css/ie/ie8.css" />
                <![endif]-->
        <!--[if IE 9]>
                    <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT ?>public/themes/bootstrap/css/ie/ie9.css" />
                <![endif]-->
        <link href="<?php echo SITE_ROOT ?>public/themes/bootstrap/css/aristo-ui.css" rel="stylesheet">
        <link href="<?php echo SITE_ROOT ?>public/themes/bootstrap/css/elfinder.css" rel="stylesheet">
        <link href='<?php echo SITE_ROOT ?>public/themes/bootstrap/css/dosis.css' rel='stylesheet' type='text/css'>
        <!--fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo SITE_ROOT ?>public/themes/bootstrap/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo SITE_ROOT ?>public/themes/bootstrap/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo SITE_ROOT ?>public/themes/bootstrap/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="<?php echo SITE_ROOT ?>public/themes/bootstrap/ico/apple-touch-icon-57-precomposed.png">
        <!--============j avascript===========-->
        <script src="<?php echo SITE_ROOT ?>public/themes/bootstrap/js/jquery.js"></script>
        <script src="<?php echo SITE_ROOT ?>public/themes/bootstrap/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="<?php echo SITE_ROOT ?>public/themes/bootstrap/js/bootstrap.js"></script>
        
        
        <script type="text/javascript">
            function setFocus() {
                document.loginForm.txt_login_name.select();
                document.loginForm.txt_login_name.focus();
            }

            function btn_login_onclick() {
                var f = document.loginForm;
                if (f.txt_login_name.value == '') {
                    alert("Ban phai nhap [Ten dang nhap]!");
                    f.txt_login_name.focus();
                    return false;
                }
                if (document.loginForm.txt_password.value == '') {
                    alert("Ban phai nhap [Mat khau]!");
                    f.txt_password.focus();
                    return false;
                }
                f.submit();
            }

            function login(evt) {
                if (navigator.appName == "Netscape") {
                    theKey = evt.which
                }
                if (navigator.appName.indexOf("Microsoft") != -1) {
                    theKey = window.event.keyCode
                }
                if (theKey == 13) {
                    btn_login_onclick();
                }
            }
        </script>
        <style>
            .cchc 
            {
                background-color: #005393;
                color: #FFF;
            }
            .form-signin {
                border: 1px solid #005393 !important;
                border-top: #005393 4px solid !important;
            }
        </style>
    </head>
    <body>
        <?php
        //LienND update 2014-04-09: Luu thong tin dang nhap?
        $v_login_name = '';
        $v_remember_me_checked = '';
        if (Cookie::get('c_secure_login') != '')
        {
            $v_login_name = cookie_password_decode(Cookie::get('c_secure_login'));
            $v_remember_me_checked = ' checked';
        }
        $v_password = '';
        if (Cookie::get('c_secure_pass') != '')
        {
            $v_password = cookie_password_decode(Cookie::get('c_secure_pass'));
            $v_remember_me_checked = ' checked';
        }
        ?>
        <div class="layout">           
            <div class="container">
                <form action="<?php echo $this->get_controller_url(); ?>do_login/" method="post" name="loginForm" id="loginForm" class="form-signin">
                    <input type="hidden" id="hdn_back" name="hdn_back" value="<?php echo get_request_var('b','')?>">
                    <h3 class="form-signin-heading">Đăng nhập hệ thống</h3>
                    <div class="controls input-icon">
                        <i class=" icon-user-md"></i>
                        <input type="text" class="input-block-level" placeholder="Tên đăng nhập" name="txt_login_name" onkeypress="login(event);" autofocus="autofocus" value="<?php echo $v_login_name;?>">
                    </div>
                    <div class="controls input-icon">
                        <i class=" icon-key"></i>
                        <input type="password" class="input-block-level" placeholder="Mật khẩu" name="txt_password" onkeypress="login(event);" value="<?php echo $v_password;?>" autocomplete="off">
                    </div>
                    <label class="checkbox">
                        <input type="checkbox" value="1" name="chk_remember_me" <?php echo $v_remember_me_checked;?>>Lưu thông tin đăng nhập
                    </label>
                    <button class="btn btn-block cchc" type="button" onclick="btn_login_onclick()">Đăng nhập</button>
                    <h4>Quên mật khẩu ?</h4>
                    <p>
                        <a href="javascript:void(0)">Click vào đây</a> để yêu cầu cấp lại mật khẩu
                    </p>
                </form>
            </div>
        </div>
    </body>
</html>
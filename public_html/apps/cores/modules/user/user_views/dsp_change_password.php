<?php
/**
 * @copyright	Copyright (C) 2012 Tam Viet Tech. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Ngo Duc Lien <liennd@gmail.com>
 * @author		Luong Thanh Binh <ltbinh@gmail.com>
 */
?>
<?php if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
} ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <title>Đổi mật khẩu</title>
        <script language="javascript" type="text/javascript">

            function setFocus() {
                document.loginForm.txt_login_name.select();
                document.loginForm.txt_login_name.focus();
            }

            function btn_change_password_onclick() {
                if (document.loginForm.txt_current_password.value == '') {
                    alert("Ban phai nhap [Mat khau hien hanh]!");
                    document.loginForm.txt_current_password.focus();
                    return;
                }
                if (document.loginForm.txt_new_password.value == '') {
                    alert("Ban phai nhap [Mat khau moi]!");
                    document.loginForm.txt_new_password.focus();
                    return;
                }
                if (document.loginForm.txt_confirm_new_password.value == '') {
                    alert("Ban phai [Xac nhan mat khau moi]!");
                    document.loginForm.txt_confirm_new_password.focus();
                    return;
                }
                if (document.loginForm.txt_new_password.value != document.loginForm.txt_confirm_new_password.value) {
                    alert("Xac nhan mat khau khong dung!");
                    document.loginForm.txt_confirm_new_password.focus();
                    return;
                }

                document.forms[0].submit();
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
        <style type="text/css">
            body{color:#333;background-color:#FFF;font-size:11px;font-family:Arial, Helvetica, sans-serif;margin:0;padding:0;}form{margin:0;}.button{border:solid 1px #ccc;background:#E9ECEF;color:#666;font-weight:700;font-size:11px;padding:4px;}.login{margin-left:auto;margin-right:auto;margin-top:6em;border:1px solid #ccc;width:429px;background:#F1F3F5;padding:15px;}.login h1{background:url(../images/login_header.png) no-repeat;background-position:left top;color:#333;height:50px;text-align:left;font-size:1.5em;margin:0;padding:15px 4px 0 50px;}.login p{padding:0 1em;}.form-block{border:1px solid #ccc;background:#E9ECEF;padding:15px 10px 10px;}.login-form{text-align:left;float:right;width:60%;}.login-text{text-align:left;width:40%;float:left;}.inputlabel{font-weight:700;text-align:left;}.inputbox{width:150px;border:1px solid #ccc;margin:0 0 1em;}.clr{clear:both;}.ctr{text-align:center;}.version{font-size:.8em;}.message{margin-top:10px;width:400px;border:1px solid #B22222;background:#F1F3F5;color:#B22222;font-weight:700;font-size:13px;padding:7px;}
        </style>
    </head>
    <body>

        <div id="ctr" align="center">
            <div class="login">
                <div class="login-form">
                    <!--<img src="<?php echo SITE_ROOT ?>public/images/login.gif" alt="Login" />-->
                    <h4>Đổi mật khẩu</h4><?php echo Session::get('user_name'); ?>
                    <form action="<?php echo $this->get_controller_url(); ?>do_change_password/" method="post" name="loginForm" id="loginForm">
                        <input type="hidden" name="task" value="login"/>
                        <div class="form-block">
                            <div class="inputlabel">M&#7853;t kh&#7849;u hiện hành</div>
                            <div>
                                <input name="txt_current_password" type="password" class="inputbox"
                                       size="15" onkeypress="login(event);"
                                       />
                            </div>
                            <div class="inputlabel">Mật khẩu mới</div>
                            <div>
                                <input name="txt_new_password" type="password" class="inputbox"
                                       size="15" onkeypress="login(event);"
                                       />
                            </div>
                            <div class="inputlabel">Xác nhận mật khẩu mới</div>
                            <div>
                                <input name="txt_confirm_new_password" type="password" class="inputbox"
                                       size="15" onkeypress="login(event);"
                                       />
                            </div>
                            <div align="left">
                                <input type="button" name="btn_change_password" class="button"
                                       value="Đổi mật khẩu"
                                       onclick="btn_change_password_onclick();"
                                       />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="login-text">
                    <div class="ctr" style="vertical-align:middle"><img src="<?php echo SITE_ROOT ?>public/images/security.png" width="64" height="64" alt="Login" align="middle" /></div>
                </div>
                <div class="clr"></div>
            </div>
        </div>
        <div id="break"></div>
        <script language="javascript" type="text/javascript">
            document.loginForm.txt_current_password.focus();
        </script>
        <noscript>
        <h2 align="center">Th&#244;ng b&#225;o: Javascript &#273;ang b&#7883; c&#7845;m!</h2>
        </noscript>
    </body>
</html>
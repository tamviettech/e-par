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
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
//display header
$this->template->title = 'Đổi mật khẩu';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------
?>
<style type="text/css">
            body {
                margin: 0px;
                padding: 0px;
                color : #333;
                background-color : #FFF;
                font-size : 11px;
                font-family : Arial, Helvetica, sans-serif;
            }

            #wrapper {
                    border: 0px;
                    margin: 0px;
                    margin-left: auto;
                    margin-right: auto;
                    padding: 0px;
            }

            #header {
                    background-color: #FFF;
                    background-position: right top;
                    border-bottom: 4px solid #C64934;
                    background: url(../images/header_blue.jpg);
            }

            #mambo {
                    position: relative;
                    width: 100%;
                    /*background: url(../../templates/default/images/version.png) no-repeat;*/
                    background-position: bottom right;
                    margin: 0px;
                    padding: 0px;
            }

            #break {
                height: 50px;
            }

            form {
                margin: 0px;
            }



            .button {
                border : solid 1px #cccccc;
                background: #E9ECEF;
                color : #666666;
                font-weight : bold;
                font-size : 11px;
                padding: 4px;
            }

            .login {
                margin-left: auto;
                margin-right: auto;
                margin-top: 6em;
                padding: 15px;
                border: 1px solid #cccccc;
                width: 429px;
                background: #F1F3F5;
            }

            .login h1 {
                background: url(../images/login_header.png) no-repeat;
                background-position: left top;
                color: #333;
                margin: 0px;
                height: 50px;
                padding: 15px 4px 0 50px;
                text-align: left;
                font-size: 1.5em;
            }

            .login p {
                padding: 0 1em 0 1em;
                }

            .form-block {
                border: 1px solid #cccccc;
                background: #E9ECEF;
                padding-top: 15px;
                padding-left: 10px;
                padding-bottom: 10px;
                padding-right: 10px;
            }

            .login-form {
                text-align: left;
                float: right;
                width: 60%;
            }

            .login-text {
                text-align: left;
                width: 40%;
                float: left;
            }

            .inputlabel {
                font-weight: bold;
                text-align: left;
                }

            .inputbox {
                width: 150px;
                margin: 0 0 1em 0;
                border: 1px solid #cccccc;
                }

            .clr {
                clear:both;
                }

            .ctr {
                text-align: center;
            }

            .version {
                font-size: 0.8em;
            }

            .footer {

            }

            .message {
                margin-top: 10px;
                padding: 7px;
                width: 400px;
                border: 1px solid #B22222;
                background: #F1F3F5;
                color: #B22222;
                font-weight: bold;
                font-size: 13px;
            }
        </style>
<div id="ctr" align="center">
	<div class="login">
	<div class="login-form">
		<!--<img src="<?php echo SITE_ROOT?>public/images/login.gif" alt="Login" />-->
        <h4>Đổi mật khẩu</h4>
        <?php echo Session::get('user_name');?>
		<form action="<?php echo $this->get_controller_url();?>do_change_password/" method="post" name="loginForm" id="loginForm">
		<input type="hidden" name="task" value="login"/>
		<div class="form-block">
			<div class="inputlabel">M&#7853;t kh&#7849;u hiện hành</div>
			<div>
			     <input name="txt_current_password" type="password" class="inputbox"
                                    onblur="check_password_onblur(this)" 
                        size="15" onkeypress="login(event);"
                />
		     </div>
            <div class="inputlabel">Mật khẩu mới</div>
			<div>
			     <input name="txt_new_password" type="password" class="inputbox" 
                                    onblur="check_password_onblur(this)" 
                        size="15" onkeypress="login(event);"
                />
		     </div>
            <div class="inputlabel">Xác nhận mật khẩu mới</div>
			<div>
			     <input name="txt_confirm_new_password" type="password" class="inputbox"
                        size="15" onkeypress="login(event);"
                />
		     </div>
                <label id="pass_check" class="required"></label>
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
		<div class="ctr" style="vertical-align:middle"><img src="<?php echo SITE_ROOT?>public/images/security.png" width="64" height="64" alt="Login" align="middle" /></div>
	</div>
	<div class="clr"></div>
</div>
</div>
<script>
            document.loginForm.txt_current_password.focus();
            function setFocus() {
                document.loginForm.txt_login_name.select();
                document.loginForm.txt_login_name.focus();
            }
            function check_password_onblur(pass)
            {
                var no = $(pass).val().length;
                var str = '&nbsp;<?php echo __("check password");?>';
                if(no <6 && no >0)
                {
                    $('#pass_check').html('');
                    $('#pass_check').html(str);
                }
                else
                {
                    $('#pass_check').html('');
                }
            }
            function btn_change_password_onclick(){
                if (document.loginForm.txt_current_password.value == ''){
                    alert("Ban phai nhap [Mat khau hien hanh]!");
                    document.loginForm.txt_current_password.focus();
                    return;
                }
                if (document.loginForm.txt_new_password.value == ''){
                    alert("Ban phai nhap [Mat khau moi]!");
                    document.loginForm.txt_new_password.focus();
                    return;
                }
                if (document.loginForm.txt_confirm_new_password.value == ''){
                    alert("Ban phai [Xac nhan mat khau moi]!");
                    document.loginForm.txt_confirm_new_password.focus();
                    return;
                }
                if (document.loginForm.txt_new_password.value != document.loginForm.txt_confirm_new_password.value){
                    alert("Xac nhan mat khau khong dung!");
                    document.loginForm.txt_confirm_new_password.focus();
                    return;
                }
                if($('#pass_check').html().length != '')
                {
                    alert("Độ dài của mật khẩu nhỏ hơn 6!");
                    return;
                }
                $('#loginForm').submit();
            }

            function login(evt){
                if(navigator.appName=="Netscape"){theKey=evt.which}
                if(navigator.appName.indexOf("Microsoft")!=-1){theKey=window.event.keyCode}
                if(theKey==13){
                    btn_login_onclick();
                }
            }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');
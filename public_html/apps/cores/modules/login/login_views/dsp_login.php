<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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
        <title>Đăng nhập hệ thống</title>
        <script language="javascript" type="text/javascript">

            function setFocus() {
                document.loginForm.txt_login_name.select();
                document.loginForm.txt_login_name.focus();
            }

            function btn_login_onclick(){
                var f=document.loginForm;
                if (f.txt_login_name.value == ''){
                    alert("Ban phai nhap [Ten dang nhap]!");
                    f.txt_login_name.focus();
                    return false;
                }
                if (document.loginForm.txt_password.value == ''){
                    alert("Ban phai nhap [Mat khau]!");
                    f.txt_password.focus();
                    return false;
                }
                f.submit();
            }

            function login(evt){
                if(navigator.appName=="Netscape"){theKey=evt.which}
                if(navigator.appName.indexOf("Microsoft")!=-1){theKey=window.event.keyCode}
                if(theKey==13){
                    btn_login_onclick();
                }
            }
        </script>
        <style type="text/css">
            body{ margin:0px; padding:0px; color:#333; background-color:#FFF; font-size:11px; font-family:Arial,Helvetica,sans-serif} #break{ height:50px} form{ margin:0px} .button{ border:solid 1px #ccc; background:#E9ECEF; color:#666; font-weight:bold; font-size:11px; padding:4px} .login{ margin-left:auto; margin-right:auto; margin-top:6em; padding:15px; border:1px solid #ccc; width:429px; background:#F1F3F5} .form-block{ border:1px solid #ccc; background:#E9ECEF; padding-top:15px; padding-left:10px; padding-bottom:10px; padding-right:10px} .login-form{ text-align:left; float:right; width:60%} .login-text{ text-align:left; width:40%; float:left} .inputlabel{ font-weight:bold; text-align:left} .inputbox{ width:150px; margin:0 0 1em 0; border:1px solid #ccc} .clr{ clear:both} .ctr{ text-align:center}
        </style>
    </head>
<body>

<div id="ctr" align="center">
	<div class="login">
	<div class="login-form">
        <h4>Hệ thống phần mềm Một cửa-<?php echo get_xml_value(simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml'), '//full_name')?></h4>
		<form action="<?php echo $this->get_controller_url();?>do_login/" method="post" name="loginForm" id="loginForm">
		<div class="form-block">
			<div class="inputlabel">Tên đăng nhập</div>

			<div>
			     <input name="txt_login_name" type="text"
                        class="inputbox" size="15"
                        onchange="this.form.txt_password.focus();"
                        onkeypress="login(event);" autofocus="autofocus"
                        autocomplete="off"
                />
		     </div>
			<div class="inputlabel">Mật khẩu</div>
			<div>
			     <input name="txt_password" type="password" class="inputbox"
                        size="15" onkeypress="login(event);" value=""
                        autocomplete="off"
                />
		     </div>
		     <?php /*
			<!--<div class="inputlabel">AD Domain</div>
			<div>
			     <select name="sel_domain">
                     <option></option>
                     <option><?php echo AD_DOMAIN_NAME;?></option>
                 </select>
		     </div>-->*/?>
			 <br/>
			<div align="left">
			     <input type="button" name="btn_login" class="button"
                        value="Đăng nhập"
                        onclick="btn_login_onclick();"
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
<div id="break"></div>
<script>
    setFocus();
</script>
<noscript>
    <h2 align="center">Th&#244;ng b&#225;o: Javascript &#273;ang b&#7883; c&#7845;m!</h2>
</noscript>
</body>
</html>
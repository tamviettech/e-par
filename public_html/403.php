<?php
/**
Copyright (C) 2012 Tam Viet Tech.

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
<?php require_once('config.php');?>
<html>
    <head>
        <meta charset="utf-8">
        
        <style>
            .Error404Box 
            {
                margin: 60px auto;
                width: 490px;
            }
            .Error404BoxHead 
            {
                background: #D6E7FB;
                height: 57px;                
            }
            .Error404BoxContent 
            {
                border: 2px solid #E3E3E3;
                height: 130px;
                padding-left: 4px;
            }
            
            .Error404BoxHead h1 
            {
                background: url("<?php echo SITE_ROOT;?>public/images/icon-warning.png") no-repeat scroll 0 50% transparent;
                color: #A80000;
                font-size: 20px;
                font-weight: bold;
                height: 57px;
                line-height: 57px;
                margin-left: 20px;
                padding-left: 35px;
            }
        </style>
    </head>
    <body>
        <div class="clearfix">
            <div class="Error404Box">
                <div class="Error404BoxHead">
                    <h1>Tác vụ không thể thực hiện: truy cập không hợp lệ</h1>
                </div>
                <div class="Error404BoxContent">
                    <p>Xin mời bạn quay về <a href="<?php echo SITE_ROOT;?>">Trang chủ </a></p>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
http_response_code(403);
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
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>
<!DOCTYPE html>
<html lang="vi" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache" />
        <link rel="shortcut icon" href="favicon.ico" />
        <title>Nộp hồ sơ qua mạng internet</title>
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/1008_24_1_1.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo $this->template->stylesheet_url; ?>" type="text/css" media="screen" />
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo SITE_ROOT; ?>public/js/mylibs.js" type="text/javascript"></script>
        <!--<link href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/bootstrap.css" rel="stylesheet">-->
        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>
        <!-- Upload -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>

        <script src="<?php echo SITE_ROOT; ?>public/js/DynamicFormHelper.js" type="text/javascript"></script>

        <style type="text/css">
            .current{color: #333 !important}
            *{font-size: 13px;}
            ul,h1,h2,h3,h4,li,a
            {
                margin: 0;
                padding: 0;
                text-decoration: none
            }
            #date
            {
                background: #3498DB;height: 30px;color: white;line-height: 30px;padding-left: 10px;
            }
            #wrapper
            {
                overflow: hidden;
                border: solid 1px rgb(220, 215, 215);
                width: 1004px;
                padding-top: 10px;
                border-bottom: none;
            }
            #menuLeft
            {
                float: left;
                background: #D1D1D1;
                margin: 0px 0px 4px 4px;
            }
            #menuLeft li a
            {
                line-height: 18px;
                background: url(<?php echo SITE_ROOT?>apps/r3/images/dotG.gif) no-repeat 5px 7px;
                padding-left:15px;
                text-align:justify;
            }
            #menuLeft li
            {
                border-bottom: solid 1px #B9B3B3;; 
                padding-bottom: 5px;
                padding-top: 5px;
                padding-left: 2px;
            }
            #menuLeft li:last-child
            {
                border: none;
            }
            #menuLeft li:hover
            {
                background: #3498DB;
            }
            #menuLeft li:hover a
            {
                color: white;
                margin-bottom: 20px
            }
            #content_right
            {
                width: 790px;
            }
            a
            {color: black}
           
            #content_right
            {
                border-left: solid 2px rgb(243, 243, 243);
            }
           #content_right .panel_color
           {
                height: 25px;
                line-height: 25px;
                padding-left: 5px;
                font-weight: bold;
                background: rgba(52, 152, 219, 0.71);
                color: white;
           }
           #frmMain .no-border
           {
               margin-bottom: 10px;
           }
           #frmMain .no-border #txt_tu_khoa
           {
               display: inline-block;
                padding: 4px 10px;
                margin-bottom: 0;
                line-height: 15px;
                vertical-align: middle;
                color: black;
           }
           
           #frmMain .no-border .solid.search
           {
                display: inline-block;
                padding: 6px 10px;
                margin-bottom: 0;
                line-height: 15px;
                text-align: center;
                vertical-align: middle;
                cursor: pointer;
                border: 0px;
                color: #ffffff;
                background-color: #0049a3;
           }
          #content_right .adminlist
          {
              margin-top: 5px;
          }
          #content_right .adminlist
          {
            border: 1px solid #dddddd;
            border-collapse: separate;
            border-left: 0;
          }
          #content_right .adminlist thead
          {
              vertical-align: bottom;
          }
          #content_right .adminlist th
          {
              border-left: 1px solid #dddddd;
              font-weight: bold;
              line-height: 25px;
              padding-left: 10px
          }
          #content_right .adminlist td
          {
            padding: 8px;
            line-height: 20px;
            text-align: left;
            vertical-align: top;
            border: 1px solid #dddddd;
            border-bottom: none;
            border-right: none;
          }
          #content_right .adminlist td a:hover
          {
              color: red;
          }
          #content_right .adminlist tr.row1
          {
              background: #F7F6F6;
          }
          #footer
          {
              background: #333;
              color: white;
              margin-bottom: 20px;
              text-align: right;
              padding-right: 20px;
              height: 50px;
              line-height: 50px;
          }
        </style>
    </head>
    <body>
        <DIV id="overDiv" style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>
        <div class="container_24" id="main">
            <div class="grid_24" id="banner">
                <label id="unit_full_name"><?php echo get_xml_value(simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml'), '//full_name') ?></label>
            </div>
            <div class="grid_24 top-nav-box" id="header">
                <div id="date" class="navbar navbar-inverse top-nav" ><?php echo jwDate::vn_day_of_week() . ', ' . date("d/m/Y"); ?></div>
            </div>
            <div class="clear">&nbsp;</div>
            <div class="container_24" id="wrapper">
                <div class="grid_5">
                    <div class="edit-box" id="left_side_bar">
                        <div style="width: 96%; ">
                            <div class="menuLeft" id="menuLeft">
                                <ul class="menu">
                                    <li><a href="http://langgiang.gov.vn" target="_blank">Trang thông tin điện tử huyện Việt Yên</a></li>
                                    <li><a href="<?php echo SITE_ROOT; ?>mavach">Tra cứu hồ sơ </a></li>
                                        <li>
                                            <a class="current" href="<?php echo SITE_ROOT . 'nop_ho_so/danh_sach_thu_tuc' ?>">
                                                Nộp hồ sơ trực tuyến
                                            </a>
                                        </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid_19" id="content_right">
                    <div id="border-right"></div>
                    <?php echo $content ?>
                </div>
                <!-- #content_right-->
            </div>
            <!-- .container_24 #wrapper -->
            <div class="clear">&nbsp;</div>
            <div class="grid_24">
                <div id="footer">
                    R3 - Phần mềm hỗ trợ giải quyết thủ tục hành chính theo cơ chế một cửa </br>
                </div>
            </div>
            <div class="clear">&nbsp;</div>
        </div>
        <!-- class="container_24" #main -->
    </body>
</html>

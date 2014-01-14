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

        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>
        <!-- Upload -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>

        <script src="<?php echo SITE_ROOT; ?>public/js/DynamicFormHelper.js" type="text/javascript"></script>

        <style type="text/css">
            .current{color: #333 !important}
        </style>
    </head>
    <body>
        <DIV id="overDiv" style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>
        <div class="container_24" id="main">
            <div class="grid_24" id="banner">
                <label id="unit_full_name"><?php echo get_xml_value(simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml'), '//full_name') ?></label>
            </div>
            <div class="grid_24 top-nav-box" id="header">
                <div id="date"><?php echo jwDate::vn_day_of_week() . ', ' . date("d/m/Y"); ?></div>
            </div>
            <div class="clear">&nbsp;</div>
            <div class="container_24" id="wrapper">
                <div class="grid_5">
                    <div class="edit-box" id="left_side_bar">
                        <div style="width: 96%; padding: 4px;">
                            <div class="menuLeft" id="menuLeft">
                                <ul class="menu">
                                    <li><a href="http://langgiang.gov.vn" target="_blank">Trang thông tin điện tử huyện Lạng Giang</a></li>
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
                    <?php echo $content ?>
                </div>
                <!-- #content_right-->
            </div>
            <!-- .container_24 #wrapper -->
            <div class="clear">&nbsp;</div>
            <div class="grid_24">
                <div id="footer">
                    <hr />
                    R3 - Phần mềm hỗ trợ giải quyết thủ tục hành chính theo cơ chế một cửa </br>
                </div>
            </div>
            <div class="clear">&nbsp;</div>
        </div>
        <!-- class="container_24" #main -->
    </body>
</html>

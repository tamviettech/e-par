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
                </div><!-- #content_right-->
            <?php if ($this->show_left_side_bar): ?>
                </div> <!-- .container_24 #wrapper -->
            <?php endif; ?>
            <div class="clear">&nbsp;</div>
            <div class="grid_24">
                <div id="footer">
                    <hr>
                    <?php echo get_xml_value(simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml'), '//full_name')?>-Bộ phận tiếp nhận và trả hồ sơ <br/>
                    Thực hiện cải cách thủ tục hành chính theo cơ chế "một cửa"
                </div>
            </div>
            <div class="clear">&nbsp;</div>
        </div> <!-- class="container_24" #main -->
    </body>
</html>
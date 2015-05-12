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
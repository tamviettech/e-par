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
defined('SERVER_ROOT') or die('No direct script');

$v_active_tab = get_post_var('hdn_active_tab',0);
?>
<h2 class="module_title">
    Chuyên mục
</h2>
<div if="right_content">
    <div id="tabs">
        <ul>
            <?php if (check_permission('XEM_DANH_SACH_CHUYEN_MUC',$this->app_name)): ?>
                <li>
                    <a href="<?php echo $this->get_controller_url() . 'dsp_all_category/' ?>" >
                        Danh sách chuyên mục
                    </a>
                </li>
            <?php endif; ?>
            <?php if (check_permission('XEM_DANH_SACH_CHUYEN_MUC_NOI_BAT',$this->app_name)): ?>
<!--                <li>
                    <a href="<?php echo $this->get_controller_url() . 'dsp_featured_category/' ?>" >
                       Chuyên mục nổi bật
                    </a>
                </li>-->
            <?php endif; ?>
        </ul>
    </div>
</div>
<script>
    $(function(){
        $('#tabs').tabs(
                    { selected: <?php echo $v_active_tab; ?>}
                    ,{select: function(){
                                        $('#tabs .ui-tabs-panel').html('<center><img src="<?php echo SITE_ROOT ?>public/images/loading.gif"/></center>');
                                        }
                    }
                );
        $('#tabs .ui-tabs-panel').css('min-height', '368px');
    });
    function reload_current_tab()
    {
      
        var tabindex = $("#tabs").tabs('option', 'selected');
        $("#tabs").tabs('load',tabindex);
    }
    
</script>

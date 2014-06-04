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
defined('DS') or die('no direct access');
$controller            = $this->get_controller_url();
?>
<?php
echo $this->hidden('controller', $this->get_controller_url());
echo $this->hidden('hdn_delete_method', 'delete_sticky');
echo $this->hidden('hdn_insert_method', 'insert_sticky');
?>
<h2 class="module_title"><?php echo __('sticky') ?></h2>
<div id="tabs">
    <ul>
        <li>
            <a href="<?php echo $controller . 'dsp_all_sticky/1' ?>">
                <?php echo __('homepage sticky') ?>
            </a>
        </li>
<!--        <li>
            <a href="<?php echo $controller . 'dsp_all_sticky/0' ?>">
                <?php echo __('category sticky') ?>
            </a>
        </li>
        <li>
            <a href="<?php echo $controller . 'dsp_all_sticky/2' ?>">
                <?php echo __('breaking news') ?>
            </a>
        </li>-->
    </ul>
</div>


<script>
    
    $('#tabs').tabs({
        select: function(){
            $('#tabs .ui-tabs-panel').html('');
            $('#tabs .ui-tabs-panel').html('<center><img src="<?php echo SITE_ROOT ?>public/images/loading.gif"/></center>');
        }
    });
    $('#tabs .ui-tabs-panel').css('min-height', '368px');
    
    function load_current_tab()
    {
        var current_index = $("#tabs").tabs("option","selected");
        $("#tabs").tabs('load',current_index);
    }
</script>

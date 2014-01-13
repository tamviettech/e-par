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

<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = 'Cập nhật bước';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_step_id = get_request_var('step_id');
$dom = simplexml_load_string(session::get('v_current_xml_flow'));

$v_step_name = get_xml_value($dom, "//step[position()=$v_step_id]/@name");
$v_group     = get_xml_value($dom, "//step[position()=$v_step_id]/@group");
$v_time      = get_xml_value($dom, "//step[position()=$v_step_id]/@time");
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_update_step">
    <?php echo $this->hidden('hdn_step_id', $v_step_id);?>
    <table class="adminform">
        <tr>
            <td>a</td>
            <td>b</td>
        </tr>
    </table>
    <div class="Row">
        <div class="left-Col">
            <label for="Loại hồ sơ: ">Loại hồ sơ: </label>
        </div>
        <div class="right-Col">
            <?php echo get_request_var('record_type_code');?> - <?php echo get_request_var('record_type_name');?>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label>Tên bước:</label>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_name" id="txt_name" value="<?php echo $v_step_name;?>" style="width:80%"/>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label>Bộ phận thực hiện:</label>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_group" id="txt_group" value="<?php echo $v_group;?>" style="width:80%"/>
            <a href="javascript:void(0)" onclick="btn_select_group_onclick()"><img src="<?php echo SITE_ROOT?>public/images/user-group16.png" /></a>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label>Tổng số ngày thực hiện:</label>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_time" id="txt_time" value="<?php echo $v_time;?>" />
        </div>
    </div>

    <div class="clear">&nbsp;</div>
    <!-- Buttons -->
    <div class="button-area">
        <input type="button" name="btn_save_process_attributes" class="button save" value="Cập nhật" onclick="btn_save_process_attributes_onclick();" />
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>
    function btn_save_process_attributes_onclick()
    {
    	document.frmMain.submit();
    }
    function btn_select_group_onclick()
    {
        var v_url = '<?php echo SITE_ROOT;?>/cores/user/dsp_all_group_to_add/&pop_win=1';
        window.showPopWin(v_url, 400, 300, do_assign_group);
    }
    function do_assign_group(returnVal)
    {
        var obj_group = returnVal[0];
        $("#txt_group").val(obj_group.group_code);
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');
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
$this->template->title = 'Thông tin quy trình';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_update_process">
    <div class="Row">
        <div class="left-Col">
            <label for="Loại hồ sơ: ">Loại hồ sơ: </label>
        </div>
        <div class="right-Col">
            <?php echo $v_record_type_code;?> - <?php echo $v_record_type_name;?>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label>Tổng số ngày thực hiện:</label>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_total_time" id="txt_total_time" value="<?php echo $v_total_time;?>" />
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label>Phí, lệ phí:</label>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_fee" id="txt_total_time" value="<?php echo $v_fee;?>" /> (vnd)
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
</script>

<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');
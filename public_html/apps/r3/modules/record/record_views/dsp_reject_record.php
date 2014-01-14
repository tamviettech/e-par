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

<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

count($VIEW_DATA['arr_all_record']) > 0 OR DIE('ohhh');

$arr_all_record         = $VIEW_DATA['arr_all_record'];
$arr_single_task_info   = $VIEW_DATA['arr_single_task_info'];
$v_record_id_list       = $VIEW_DATA['record_id_list'];

$v_record_type_code = $arr_single_task_info['C_RECORD_TYPE_CODE'];
$v_record_type_name = $arr_single_task_info['C_RECORD_TYPE_NAME'];
$v_group_name       = $arr_single_task_info['C_GROUP_NAME'];

//display header
$this->template->title = 'Xét duyệt hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_promote = _CONST_RECORD_APPROVAL_REJECT;
$v_reason = '';

?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_reject_record">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');

    echo $this->hidden('pop_win', $v_pop_win);
    echo $this->hidden('hdn_item_id_list', $v_record_id_list);

    //Ma Loai HS
    echo $this->hidden('hdn_record_type_code', $v_record_type_code);

    //KQ thu ly
    echo $this->hidden('hdn_approval_value', $v_promote);
    ?>

    <div class="panel_color_form">Danh sách hồ từ chối</div>
    <div class="Row">
        <div class="left-Col">
            <label for="Loại hồ sơ: ">Loại hồ sơ: </label>
        </div>
        <div class="right-Col">
            <?php echo $v_record_type_code;?> - <?php echo $v_record_type_name;?>
        </div>
    </div>

    <!-- Record list -->
    <table cellpadding="4" cellspacing="0" width="100%" class="list">
        <tr>
            <th>STT</th>
            <th>Mã hồ sơ</th>
            <th>Người đăng ký</th>
            <th>Ngày nhận</th>
            <th>Hẹn trả công dân</th>
        </tr>
        <?php for ($i=0; $i<count($arr_all_record); $i++): ?>
            <tr>
                <td class="right"><?php echo ($i+1);?></td>
                <td><?php echo $arr_all_record[$i]['C_RECORD_NO'];?></td>
                <td><?php echo $arr_all_record[$i]['C_CITIZEN_NAME'];?></td>
                <td><?php echo jwDate::yyyymmdd_to_ddmmyyyy($arr_all_record[$i]['C_RECEIVE_DATE'], TRUE);?></td>
                <td><?php echo r3_View::return_date_by_text($arr_all_record[$i]['C_RETURN_DATE']);?></td>
            </tr>
        <?php endfor;?>
    </table>
    <!-- End: Record list -->
    <div class="panel_color_form">Lý do từ chối</div>
    <div id="divNote" class="Row">
        <div class="left-Col">
        </div>
        <div class="right-Col">
            <textarea style="width:100%;height:100px;" rows="3"
            	name="txt_reason" id="txt_reason" cols="20" maxlength="4000"
            	class="text ui-widget-content ui-corner-all"><?php echo $v_reason;?></textarea>
        </div>
    </div>


    <div class="clear">&nbsp;</div>
    <!-- Buttons -->
    <div class="button-area">
        <hr/>
        <input type="button" name="btn_do_approval" class="button save" value="Cập nhật" onclick="btn_do_approval_onclick();" />
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>

    function btn_do_approval_onclick()
    {
        var f = document.frmMain;
        var v_approval_value = $("#hdn_approval_value").val();
        var v_reason = trim($("#txt_reason").val());

        if (v_reason == '')
        {
            alert('Lý do không được bỏ trống!');
            f.txt_reason.focus();
            return false;
        }

        f.submit();
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');
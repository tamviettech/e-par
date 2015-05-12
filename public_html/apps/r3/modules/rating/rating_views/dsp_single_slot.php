<?php
defined('SERVER_ROOT') or die();

$this->template->title = 'Bàn tiếp nhận';
$this->template->display('dsp_header_pop_win.php');
$v_slot                = 0;
$v_user                = '';
if ($arr_single_slot)
{
    $v_slot = $arr_single_slot['C_SLOT'];
    $v_user = $arr_single_slot['C_LOGIN_NAME'];
}
?>
<br/>
<form method="post" id="frmMain" name="frmMain">
    <?php
    echo $this->hidden('hdn_item_id', $id);
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_update_method', 'update_slot');
    echo $this->hidden('XmlData', '');
    ?>
    <table class="no-border" style="width:100%">
        <colgroup>
            <col width="40%">
            <col width="60%">
        </colgroup>
        <tr>
            <td><label for="txt_slot"><b>Số bàn tiếp nhận <span class="required">(*)</span></b></label></td>
            <td>
                <input 
                    type="text" value="<?php echo $v_slot ?>" id="txt_slot" 
                    name="txt_slot" onkeypress="txt_slot_onkeypress(event)"
                    data-allownull="no" data-name="Bàn tiếp nhận"
                    data-validate="number"
                    />
            </td>
        </tr>
        <tr>
            <td><label for="sel_user"><b>Cán bộ tiếp nhận<span class="required">(*)</span></b></label></td>
            <td>
                <select id="sel_user" name="sel_user" data-allownull="no">
                    <option value="">Chọn cán bộ</option>
                    <?php foreach ($arr_users as $user): ?>
                        <?php $selected = $user['C_LOGIN_NAME'] == $v_user ? 'selected' : '' ?>
                        <option value="<?php echo $user['C_LOGIN_NAME'] ?>" <?php echo $selected ?>>
                            <?php echo $user['C_NAME'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <div class="button-area">
        <input 
            class="button save" type="button" 
            onclick="btn_update_onclick();" value="Cập nhật"
            />
        <input class="button close" type="button" onclick="window.parent.hidePopWin(false)" value="Quay lại"></input>
    </div>
</form>
<script>
                        function txt_slot_onkeypress(evt) {
                            var theEvent = evt || window.event;
                            var key = theEvent.keyCode || theEvent.which;
                            key = String.fromCharCode(key);
                            var regex = /[0-9]|\./;
                            if (!regex.test(key)) {
                                theEvent.returnValue = false;
                                if (theEvent.preventDefault)
                                    theEvent.preventDefault();
                            }
                        }
</script>
<?php
$this->template->display('dsp_footer_pop_win.php');
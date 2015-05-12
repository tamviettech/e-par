<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}
//display header
$this->template->title = 'Phê duyệt văn bản đi';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

//------------------------------------------------------------------------------
$v_doc_id                   = $VIEW_DATA['doc_id'];
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_approve_doc/"><?php
    echo $this->hidden('controller', $this->get_controller_url() . 'do_approve_doc/');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_doc');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_doc');
    echo $this->hidden('hdn_update_method', 'update_doc');
    echo $this->hidden('hdn_delete_method', 'delete_doc');

    echo $this->hidden('hdn_item_id', $v_doc_id);
    echo $this->hidden('pop_win', $v_pop_win);
    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Phê duyệt văn bản</h2>
    <!-- /Toolbar -->

    <div class="Row">
        <div class="left-Col"></div>
        <div class="right-Col">
            <input type="radio" name="rad_approve" value="1" id="rad_approve_1" checked="checked" onclick="document.getElementById('reject_reason').style.display='none'">
            <label for="rad_approve_1">Duyệt</label>

        </div>
    </div>
    <div class="Row">
        <div class="left-Col"></div>
        <div class="right-Col">
            <input type="radio" name="rad_approve" value="0" id="rad_approve_0" onclick="document.getElementById('reject_reason').style.display='block'">
            <label for="rad_approve_0">Không duyệt</label>
            <div id="reject_reason" style="display:none">
                <label>Lý do: </label><input type="text" name="txt_reject_reason" value="" size="90"/>
            </div>
        </div>
    </div>


    <!-- Button -->
    <div class="button-area">
        <a href="javascript:;" class="easyui-linkbutton" iconCls="icon-save" onclick="this.form.submit()">
            Xác nhận
        </a>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <a href="javascript:;" class="easyui-linkbutton" iconCls="icon-cancel" onclick="<?php echo $v_back_action;?>">
            <?php echo _LANG_CLOSE_WINDOW_BUTTON; ?>
        </a>
    </div>
</form>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');
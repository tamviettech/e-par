<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}
//display header
$this->template->title = 'Đưa văn bản vào hồ sơ lưu';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------
$v_doc_id               = $VIEW_DATA['doc_id'];
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_add_doc_to_folder"><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_doc');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_doc');
    echo $this->hidden('hdn_update_method', 'update_doc');
    echo $this->hidden('hdn_delete_method', 'delete_doc');

    echo $this->hidden('hdn_item_id', $v_doc_id);
    echo $this->hidden('pop_win', $v_pop_win);
    echo $this->hidden('hdn_allot_user_name', $v_pop_win);
    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Đưa văn bản vào hồ sơ lưu</h2>
    <!-- /Toolbar -->

     <div class="Row">
        <div class="left-Col">Chọn hồ sơ: <span class="required">(*)</span> </div>
        <div class="right-Col">
            <select name="sel_folder" id="sel_folder">
                <option value="-1">--Chọn từ danh sách--</option>
                <?php echo $this->generate_select_option($VIEW_DATA['arr_all_folder'], -1);?>
            </select>
        </div>
    </div>

    <!-- Button -->
    <div class="button-area">
        <input type="button" name="update" class="button save" value="Cập nhật" onclick="this.form.submit();"/>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo _LANG_CLOSE_WINDOW_BUTTON; ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');
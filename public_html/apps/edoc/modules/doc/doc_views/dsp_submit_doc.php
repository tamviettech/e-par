<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
//display header
$this->template->title = 'Trình văn bản lên lãnh đạo';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------
$v_doc_id  = $VIEW_DATA['doc_id'];
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url(); ?>do_submit_doc"><?php
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
    <h2 class="module_title">Trình văn bản lên lãnh đạo</h2>
    <!-- /Toolbar -->

    <div class="Row">
        <div class="left-Col">Trình lên cho: <span class="required">(*)</span> </div>
        <div class="right-Col">
            <select name="sel_allot_user" id="sel_allot_user" onchange="this.form.hdn_allot_user_name.value = this.options[this.selectedIndex].text">
                <option value="-1">--Chọn từ danh sách--</option>
                <?php echo $this->generate_select_option($VIEW_DATA['arr_all_allot_user'], -1); ?>
            </select>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Nội dung trình:</div>
        <div class="right-Col">
            <textarea rows="2" cols="90" name="txt_submit_message"></textarea>
        </div>
    </div>

    <!-- Button -->
    <div class="button-area">
        <a href="javscript:;" class="easyui-linkbutton" iconCls="icon-save" onclick="btn_submit_doc_onclick();">
            Trình lãnh đạo
        </a>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <a href="javscript:;" class="easyui-linkbutton" iconCls="icon-cancel" onclick="<?php echo $v_back_action; ?>">
            Quay lại
        </a>
    </div>
</form>
<script>
                function btn_submit_doc_onclick()
                {
                    var f = document.frmMain;

                    if (f.sel_allot_user.value == "-1")
                    {
                        alert('Bạn chưa chọn lãnh đạo!');
                        f.sel_allot_user.focus();
                        return;
                    }
                    f.submit();
                }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');
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
$v_doc_id                = $VIEW_DATA['doc_id'];
$arr_all_exec_user_in_ou = $VIEW_DATA['arr_all_exec_user_in_ou'];
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url(); ?>do_sub_allot_doc"><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_doc');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_doc');
    echo $this->hidden('hdn_update_method', 'update_doc');
    echo $this->hidden('hdn_delete_method', 'delete_doc');

    echo $this->hidden('hdn_item_id', $v_doc_id);
    echo $this->hidden('pop_win', $v_pop_win);

    echo $this->hidden('hdn_allot_by', 'c');
    echo $this->hidden('hdn_direct_exec_ou_id', '-1');
    echo $this->hidden('hdn_direct_exec_ou_name', '');
    echo $this->hidden('hdn_co_exec_ou_id_list', '');

    echo $this->hidden('hdn_direct_exec_user_id', '-1');
    echo $this->hidden('hdn_direct_exec_user_name', '');
    echo $this->hidden('hdn_co_exec_user_id_list', '');

    echo $this->hidden('hdn_monitor_user_id', '-1');
    echo $this->hidden('hdn_monitor_user_name', '');
    ?>
    <!-- Toolbar -->
    <h2 class="module_title">[<?php echo Session::get('ou_name'); ?>] Phân công thụ lý cho cán bộ trong phòng</h2>
    <!-- /Toolbar -->

    <div class="Row">
        <div class="left-Col">Cán bộ thụ lý chính <span class="required">(*)</span> </div>
        <div class="right-Col">
            <select name="sel_direct_exec_user" id="sel_direct_exec_user" onchange="sel_direct_exec_user_onchange(this)">
                <option value="-1">--Chọn cán bộ thụ lý chính--</option>
                <?php echo $this->generate_select_option($arr_all_exec_user_in_ou, -1); ?>
            </select>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Phối hợp</div>
        <div class="right-Col">
            <div id="div_co_exec_ou" style="height:160px; overflow: auto;border:1px solid;">
                <?php foreach ($arr_all_exec_user_in_ou as $code => $name) : ?>
                    <input type="checkbox" name="chk_co_exec_user" id="chk_co_exec_user<?php echo $code; ?>" value="<?php echo $code; ?>" />
                    <label for="chk_co_exec_user<?php echo $code; ?>"><?php echo $name; ?></label><br/>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Button -->
    <div class="button-area">
        <a href="javascript:;" class="easyui-linkbutton" iconCls="icon-save" onclick="btn_sub_allot_onlick();">
            Phân công
        </a>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <a href="javascript:;" class="easyui-linkbutton" iconCls="icon-cancel" onclick="<?php echo $v_back_action; ?>">
            <?php echo _LANG_CLOSE_WINDOW_BUTTON; ?>
        </a>
    </div>
</form>
<script>
                function sel_direct_exec_user_onchange(obj)
                {
                    var f = document.frmMain;
                    f.hdn_direct_exec_user_id.value = obj.options[obj.selectedIndex].value;
                    f.hdn_direct_exec_user_name.value = obj.options[obj.selectedIndex].text;
                }

                function btn_sub_allot_onlick()
                {
                    var f = document.frmMain;

                    //CB thu ly chinh
                    if (f.hdn_direct_exec_user_id.value == "-1")
                    {
                        alert('Phải xác định cán bộ thụ lý chính!');
                        f.sel_direct_exec_user.focus();
                        return false;
                    }

                    //Lay danh sach CB phoi hop thu ly
                    f.hdn_co_exec_user_id_list.value = get_all_checked_checkbox(f.chk_co_exec_user, ",");

                    f.submit();
                }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');
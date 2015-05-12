<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}
//display header
$this->template->title = 'Cập nhật tiến độ thụ lý văn bản';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

//------------------------------------------------------------------------------
$v_doc_id                   = $VIEW_DATA['doc_id'];
$arr_single_doc             = $VIEW_DATA['arr_single_doc'];

$v_xml_processing = $arr_single_doc['C_XML_PROCESSING'];

$v_form_action = $this->get_controller_url() . strtolower($_REQUEST['direction']) . '/do_exec_doc/'
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $v_form_action;?>"><?php
    echo $this->hidden('controller', $this->get_controller_url() . strtolower($_REQUEST['direction']) . '/') ;

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_doc');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_doc');
    echo $this->hidden('hdn_update_method', 'update_doc');
    echo $this->hidden('hdn_delete_method', 'delete_doc');

    echo $this->hidden('hdn_item_id', $v_doc_id);
    echo $this->hidden('pop_win', $v_pop_win);

    echo $this->hidden('direction', $_REQUEST['direction']);
    echo $this->hidden('type', $_REQUEST['type']);

    echo $this->hidden('hdn_deleted_step_id_list', '');

    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Cập nhật tiến độ thụ lý văn bản</h2>
    <!-- /Toolbar -->

    <table width="100%" class="adminlist" cellspacing="0" border="1">
        <col width="3%" /><col width="17%" /><col width="60%" /><col width="20%" />
        <tr>
            <th>#</th>
            <th>Ngày thực hiện</th>
            <th>Công việc</th>
            <th>Cán bộ thực hiện</th>
        </tr>
    </table>

    <!-- Cong viec da thuc hien -->
    <table width="100%" class="adminlist" cellspacing="0" border="1">
        <col width="3%" /><col width="17%" /><col width="60%" /><col width="20%" />
        <?php
        $dom_processing = simplexml_load_string($v_xml_processing);
        $steps = $dom_processing->xpath('//step');
        $row_index = 0;

        foreach ($steps as $step)
        {
            $v_row_class = 'row' . ($row_index % 2);

            $code = $step->attributes()->code;
            $seq = $step->attributes()->seq;
            $v_date = $step->datetime;
            $v_action_text = $step->action_text;

            $v_exec_user_name = '';
            $v_checkbox_disabled = ' disabled="disabled"';
            if ($code == 'init')
            {
                $v_exec_user_name = $step->init_user_name;
            }
            if ($code == 'submit')
            {
                $v_exec_user_name = $step->submit_user_name;
            }
            if ($code == 'allot')
            {
                $v_exec_user_name = $step->allot_user_name;
            }
            if ($code == 'sub_allot')
            {
                $v_exec_user_name = $step->allot_user_name;
            }
            if ($code == 'exec')
            {
                $v_exec_user_name = $step->exec_user_name;
                $v_checkbox_disabled = '';
            }
            if ($code == 'commit')
            {
                $v_exec_user_name = $step->commit_user_name;
                $v_checkbox_disabled = '';
            }

            $row_index++;
            ?>
            <tr class="<?php echo $v_row_class;?>">
                <td><input type="checkbox" name="chk_step_seq" value="<?php echo $seq;?>" <?php echo $v_checkbox_disabled;?> /></td>
                <td><?php echo jwDate::yyyymmdd_to_ddmmyyyy($v_date, 1);?></td>
                <td><?php echo $v_action_text;?></td>
                <td><?php echo $v_exec_user_name;?></td>
            </tr>
            <?php
        }
        echo $this->add_empty_rows($row_index + 1,_CONST_DEFAULT_ROWS_PER_PAGE,4);
        ?>
    </table>
    <br/>
    <div id="div_add_exec" style="display:none">
        <label>Nội dung công việc:</label>
        <input type="text" name="txt_exec" size="60"/>
        <input type="checkbox" name="chk_commit" value="1" id="chk_commit"/><label for="chk_commit">Hoàn thành công việc</label>
        <input type="button" class="button save" name="btn_do_exec" value="Cập nhật" onclick="btn_do_exec_onclick(this.form)"/>
        <input type="button" class="button cancel" name="btn_cancel_exec" value="Bỏ qua" onclick="btn_cancel_exec_onclick()"/>
    </div>

    <!-- Button -->
    <div class="button-area">
        <input type="button" name="btn_dsp_add_exec" class="button add" value="Thêm" onclick="btn_dsp_add_exec_onclick();" />
        <input type="button" name="delete_exec" class="button delete" value="Xoá bỏ" onclick="btn_delete_step_onclick()"/>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.document.frmMain.btn_filter.click();window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo _LANG_CLOSE_WINDOW_BUTTON;?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>
    function btn_do_exec_onclick(f)
    {
        if (f.txt_exec.value == "")
        {
            alert('Chưa nhập nội dung công việc!');
            f.txt_exec.focus();
            return false;
        }

        f.submit();
    }

    function btn_delete_step_onclick()
    {
        var f = document.frmMain;

        v_item_id_list = get_all_checked_checkbox(f.chk_step_seq,",");

        if (v_item_id_list == ""){
            alert('Chưa có đối tượng nào được chọn!');
            return;
        }
        if (confirm('Bạn chắc chắn xoá các đối tượng đã chọn?')){
            f.hdn_deleted_step_id_list.value =  v_item_id_list;
            m = $("#controller").val() + 'delete_step_exec_doc';
            $("#frmMain").attr("action", m);
            f.submit();
        }
    }

</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');
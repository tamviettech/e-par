<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}
//header
$this->template->title = $VIEW_DATA['direction_text'];
$this->template->display('dsp_header.php');
?>
<form name="frmMain" id="frmMain" action="#" method="POST">
    <?php
    $v_direction        = $VIEW_DATA['direction'];
    $v_direction_text   = $VIEW_DATA['direction_text'];
    $v_type             = $VIEW_DATA['type'];
    $v_type_text        = $VIEW_DATA['type_text'];
    $arr_all_doc        = $VIEW_DATA['arr_all_lookup_doc'];

    $v_filter            = $VIEW_DATA['filter'];
    $v_processing_filter =  $VIEW_DATA['processing'];

    echo $this->hidden('direction',$v_direction);
    echo $this->hidden('direction_text',$v_direction_text);
    echo $this->hidden('type',$v_type);
    echo $this->hidden('type_text',$v_type_text);

    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_doc');
    echo $this->hidden('hdn_dsp_all_method', strtolower($v_direction) . '/' . strtolower($v_type));
    echo $this->hidden('hdn_update_method','update_doc');
    echo $this->hidden('hdn_delete_method','delete_doc');
    ?>
    <!-- Toolbar -->
    <h2 class="module_title"><?php echo $v_direction_text;?> > <?php echo $v_type_text;?></h2>
    <!-- /Toolbar -->

    <!-- filter -->
    <div id="div_filter">
        <label>Trạng thái xử lý:</label>
        <select name="sel_processing">
            <option value="-1">-- Tất cả --</option>
            <option value="1" <?php echo ($v_processing_filter == 1) ? 'selected' : ''?>>Đang xử lý - Chưa đến hạn</option>
            <option value="2" <?php echo ($v_processing_filter == 2) ? 'selected' : ''?>>Đang xử lý - Đã quá hạn</option>
            <option value="3" <?php echo ($v_processing_filter == 3) ? 'selected' : ''?>>Đã hoàn thành - Đúng hạn</option>
            <option value="4" <?php echo ($v_processing_filter == 4) ? 'selected' : ''?>>Đã hoàn thành - Quá hạn</option>
        </select>

        <input type="text" name="txt_filter"
            value="<?php echo $v_filter;?>"
            class="inputbox" size="30" autofocus="autofocus"
            onkeypress="txt_filter_onkeypress(this.form.btn_filter,event);"
        />
        <a onclick="btn_filter_onclick();" class="easyui-linkbutton" iconCls="icon-search"><?php echo _LANG_FILTER_BUTTON;?></a>
    </div>

    <?php
    $xml_file = strtolower('xml_' . $v_type . '_vbtracuu_list.xml');
    if ($this->load_xml($xml_file))
    {
        echo $this->render_form_display_all_by_doc_type($arr_all_doc);
    }
    else
    {
        echo 'Chưa khai báo cấu hình hiển thị danh sách cho: ' . $v_direction_text . ' -> ' . $v_type_text;
        echo '<br>Cần khai báo tên theo tên file quy định: ' . $xml_file;
    }

    //Phan trang
    $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? $_POST['sel_rows_per_page'] : _CONST_DEFAULT_ROWS_PER_PAGE;
    if (isset($arr_all_doc[1]['TOTAL_RECORD'])){
        $v_page = isset($_POST['sel_goto_page']) ? $_POST['sel_goto_page'] : 1;
        $v_total_record = $arr_all_doc[1]['TOTAL_RECORD'];
    } else {
        $v_page = 1;
        $v_total_record = $v_rows_per_page;
    }
    echo $this->paging($v_page, $v_rows_per_page, $v_total_record);
    ?>
    <div class="button-area">
        <!-- Nothing here-->
	</div>
</form>
<script>
    function pop_up_add_new(){
        var url =  '<?php echo $this->get_controller_url() . strtolower($v_direction);?>/dsp_single_doc/&doc_id=0&hdn_item_id=0&pop_win=1&direction=<?php echo $v_direction;?>&type=<?php echo $v_type;?>';
        showPopWin(url ,920,550, null);
    }
</script>
<?php $this->template->display('dsp_footer.php');

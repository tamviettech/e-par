<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}
//header
$this->template->title = 'Hồ sơ văn bản';
$this->template->display('dsp_header.php');

$arr_all_folder = $VIEW_DATA['arr_all_folder'];
$v_filter = $VIEW_DATA['txt_filter'];
?>
<form name="frmMain" id="frmMain" action="#" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_folder');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_folder');
    echo $this->hidden('hdn_update_method','update_folder');
    echo $this->hidden('hdn_delete_method','delete_folder');
    ?>

    <!-- filter -->
    <div id="div_filter">
        <input type="text" name="txt_filter"
            value="<?php echo $v_filter;?>"
            class="inputbox" size="30" autofocus="autofocus"
            onkeypress="txt_filter_onkeypress(this.form.btn_filter,event);"
        />
        <a class="easyui-linkbutton" onclick="btn_filter_onclick();" iconCls="icon-search"><?php echo _LANG_FILTER_BUTTON;?></a>

    </div>
    <?php
    $xml_file = strtolower('xml_folder_list.xml');
   // Var_Dump($arr_all_folder);exit;
    if ($this->load_xml($xml_file))
    {
        echo $this->render_form_display_all($arr_all_folder);


    }
    else
    {
        echo 'Line: ' . __LINE__ . '<br>File: ' . __FILE__;
        var_dump::display($arr_all_folder);
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
    <?php if ($this->check_permission('MO_HO_SO_LUU')): ?>
        <div class="button-area">
            <a onclick="btn_addnew_onclick();" class="easyui-linkbutton" iconCls="icon-add"><?php echo _LANG_ADDNEW_BUTTON;?></a>
            <a onclick="btn_delete_onclick();" class="easyui-linkbutton" iconCls="icon-remove"><?php echo _LANG_DELETE_BUTTON;?></a>
        </div>
    <?php endif; ?>
</form>
<?php $this->template->display('dsp_footer.php');
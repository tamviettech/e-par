<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];

//header
$this->template->title = 'Xử lý hồ sơ';
$this->template->display('dsp_header.php');

?>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method','update_record');
    echo $this->hidden('hdn_delete_method','delete_record');


    ?>
    <?php echo $this->dsp_div_notice();?>
    <!-- filter -->
    <div id="div_filter">
        (1)&nbsp;<label>Mã loại hồ sơ</label>
        <input type="text" name="txt_record_type_code" id="txt_record_type_code"
               value="<?php echo $v_record_type_code; ?>"
               class="inputbox upper_text" size="10" maxlength="10"
               onkeypress="txt_record_type_code_onkeypress(event);"
               autofocus="autofocus"
               accesskey="1"
               />&nbsp;
        <select name="sel_record_type" id="sel_record_type" style="width:75%; color:#000000;"
                onchange="sel_record_type_onchange(this)">
            <option value="">-- Chọn loại hồ sơ --</option>
            <?php echo $this->generate_select_option($arr_all_record_type, $v_record_type_code); ?>
        </select>
        <input type="text" name="noname" style="visibility: hidden"/>
    </div>

    <div id="procedure">
        <?php
        if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list')))
        {
            echo $this->render_form_display_all_record($arr_all_record);
        }
        ?>
    </div>
	<div><?php echo $this->paging2($arr_all_record);?></div>
    <div class="button-area">
        <input type="button" name="addnew" class="button add" value="(2) <?php echo __('add new');?>" onclick="btn_addnew_onclick();" accesskey="2" />
        <input type="button" name="trash" class="button delete" value="<?php echo __('delete');?>" onclick="btn_delete_onclick();"/>
    </div>
</form>
<script>

    $(function() {
        <?php if (strtoupper($this->active_role) == _CONST_TIEP_NHAN_ROLE): ?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                //Print
                html = '<a href="javascript:void(0)" onclick="print_record_ho_for_citizen(\'' + v_item_id + '\')">'
                html += '<img src="' + SITE_ROOT + 'public/images/print_24x24.png" title="In phiếu biên nhận cho công dân" class="quick-action" /></a>';

                $(this).html(html);
            });

        <?php endif;?>
    });

    function print_record_ho_for_citizen(p_record_id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_print_ho_for_citizen/' + p_record_id;

        showPopWin(url, 1000, 600, null, true);
    }

    function txt_record_type_code_onkeypress(evt)
    {
        if (IE()){
            theKey=window.event.keyCode
        } else {
            theKey=evt.which;
        }

        if(theKey == 13){
            v_record_type_code = trim($("#txt_record_type_code").val()).toUpperCase();
            $("#sel_record_type").val(v_record_type_code);
            if ($("#sel_record_type").val() != '')
            {
                $("#frmMain").submit();
            }
            else
            {
                $("#procedure").html('');
            }
        }
        return false;
    }

    function sel_record_type_onchange(e)
    {
        e.form.txt_record_type_code.value = e.value;
        if (trim(e.value) != '')
        {
            e.form.submit();
        }
    }
</script>
<?php $this->template->display('dsp_footer.php');
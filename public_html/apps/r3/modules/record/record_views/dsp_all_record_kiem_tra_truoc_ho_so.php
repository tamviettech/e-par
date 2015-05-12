<?php
defined('SERVER_ROOT') or die();
$this->template->title = 'Kiểm tra trước hồ sơ';
$this->template->display('dsp_header.php');
$v_record_type_code    = $record_type_code;
?>

<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', '0');
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_internet_record');
    echo $this->hidden('hdn_dsp_all_method', 'ho_so/kiem_tra_truoc_ho_so');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');
    echo $this->hidden('hdn_return_method', 'return_internet_record');
    echo $this->hidden('hdn_goback', '');
    echo $this->hidden('record_type_code', $v_record_type_code);
    ?>
    <?php echo $this->dsp_div_notice($active_role_text); ?>

    <!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type); ?>
    <div class="clear"></div>
    <div id="solid-button" style="overflow: hidden;margin-bottom: 10px;">
        <button type="button" name="trash" class="btn" onclick="btn_return_onclick();">
            Trả kết quả kiểm tra
        </button>
        <br />
    </div>
    <div id="procedure">
        <?php
        if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list')))
        {
            echo $this->render_form_display_all($arr_all_record);
        }
        ?>
    </div>
    <div><?php echo $this->paging2($arr_all_record); ?></div>
</form>
<div class="clear"></div>
<script>
            window.btn_return_onclick = function() {
                var chk = $('[name=chk]:checked');
                if (!$(chk).length) {
                    alert('Bạn phải chọn ít nhất một hồ sơ');
                    return;
                }
                if (!confirm('Trả kết quả cho công dân (Chú ý: những hồ sơ nào chưa có lời nhắn sẽ không được trả)?')) {
                    return;
                }
                $('#hdn_item_id_list').val(chk[0].value);
                for (i = 1; i < $(chk).length; i++) {
                    $('#hdn_item_id_list').val($('#hdn_item_id_list').val() + ',' + chk[i].value);
                }
                $('#hdn_goback').val(window.location.href);
                $('#frmMain').attr('action', $('#controller').val() + $('#hdn_return_method').val()).submit();
            }
</script>
<?php
$this->template->display('dsp_footer.php');

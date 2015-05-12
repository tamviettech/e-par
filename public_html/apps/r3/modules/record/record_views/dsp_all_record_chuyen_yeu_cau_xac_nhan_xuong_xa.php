<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];
$MY_TASK                = $VIEW_DATA['MY_TASK'];

//header
$this->template->title = 'Chuyển yêu cầu xác nhận xuống xã';
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
    echo $this->hidden('hdn_handover_method','do_handover_record');

    echo $this->hidden('record_type_code', $v_record_type_code);
    echo $this->hidden('MY_TASK', $MY_TASK);

    ?>
    <?php echo $this->dsp_div_notice($VIEW_DATA['active_role_text'] );?>
    <!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type);?>
    <div id="solid-button">
        <button type="button"  class="btn solid transfer"
            name="btn_display_send_confirmation_request" onclick="btn_display_send_confirmation_request_onclick();" >
            Chuyển yêu cầu xác nhận xuống xã
        </button>
         
        <!--
        <button onclick="print_record_ho_for_bu();" type="button" name="addnew" class="btn solid print" ><i class="icon-print"></i>In giấy bàn giao</button>
        -->
    </div>
    <div class="clear" style="height: 10px">&nbsp;</div>
    <div id="procedure">
        <?php
        if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list')))
        {
            echo $this->render_form_display_all_record($arr_all_record, FALSE);
        }
        ?>
    </div>
	<div><?php echo $this->paging2($arr_all_record);?></div>
</form>
<script>

    $(function() {
    	//Quick action
        <?php if (strtoupper($this->active_role) == _CONST_CHUYEN_YEU_CAU_XAC_NHAN_XUONG_XA_ROLE): ?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                html = '';

                v_is_owner = $('.adminlist tr[data-item_id="' + v_item_id + '"]').attr('data-owner');
                if (v_is_owner == "1")
                {
                    html = '<a href="javascript:void(0)" onclick="btn_display_send_confirmation_request_onclick(\'' + v_item_id + '\');" class="quick_action" title="Chuyển yêu cầu xác nhận xuống xã" >';
                    html += '<i class="icon-mail-forward"></i></a>';
                }

                //Thong tin tien do
                html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\');" class="quick_action" title="Xem tiến độ">';
                html += '<i class="icon-bar-chart"></i></a>';

                $(this).html(html);
            });

        <?php endif;?>
    });

    function btn_display_send_confirmation_request_onclick(record_id_list)
    {
    	var f = document.frmMain;

    	//Danh sach ID Ho so da chon
    	if (typeof(record_id_list) == 'undefined')
    	{
    	    v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');
    	}
    	else
    	{
    		v_selected_record_id_list = record_id_list;
    	}

        if (v_selected_record_id_list != '')
        {
            var url = '<?php echo $this->get_controller_url();?>dsp_send_confirmation_request/' + v_selected_record_id_list
                + '/?record_type_code=' + $("#record_type_code").val()
                + '&pop_win=1';

            showPopWin(url, 900, 600, null, true);
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn!');
        }
    }

    function print_record_ho_for_bu()
    {
        var f = document.frmMain;

        //Danh sach ID Ho so da chon
        v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');

        if (v_selected_record_id_list != '')
        {
            var url = '<?php echo $this->get_controller_url();?>dsp_print_ho_for_bu/' + v_selected_record_id_list + '/?record_type_code=' + $("#record_type_code").val();

            showPopWin(url, 1000, 600, null, true);
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn!');
        }
    }
</script>
<?php $this->template->display('dsp_footer.php');
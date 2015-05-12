<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];

//header
$this->template->title = 'Trả kết quả hồ sơ liên thông';
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

    echo $this->hidden('hdn_role', _CONST_TRA_KET_QUA_ROLE);

    ?>
    <?php echo $this->dsp_div_notice($VIEW_DATA['active_role_text'] );?>
 	<!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type);?>

    <div id="solid-button">
        <!--button tra ket qua-->
        <button type="button" name="trash" class="btn" onclick="btn_return_onclick();" >
            Trả kết quả
        </button>    
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
    <?php echo $this->paging2($arr_all_record);?>
    <!--
    <div class="button-area">
        <input type="button" name="btn_return" class="button certificate" value="Trả kết quả" onclick="btn_return_onclick();"/>
    </div> -->

    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="return">
            <a href="#return">Trả kết quả</a>
        </li>
        <li class="statistics">
            <a href="#statistics">Xem tiến độ</a>
        </li>
    </ul>
</form>
<script>

    $(function() {
        //Show context on each row
        $(".adminlist tr[role='presentation']").contextMenu({
            menu: 'myMenu'
        }, function(action, el, pos) {
            v_record_id = $(el).attr('data-item_id');
            switch (action){
                case 'return':
                    btn_return_onclick(v_record_id);
                    break;

                case 'statistics':
                    dsp_single_record_statistics(v_record_id);
                    break;
            }
        });

        //Quick action
        <?php if (strtoupper($this->active_role) == _CONST_TRA_KET_QUA_LT_ROLE): ?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                html = '';

                //Inp hieu tu choi
               v_is_rejected = $(this).attr('data-reject');
               if (v_is_rejected == "1")
               {
            	   html += '<a href="javascript:void(0)" onclick="print_record_reject_for_citizen(\'' + v_item_id + '\')" class="quick_action" title="In phiếu từ chối">';
                   html += '<i class="icon-print"></i></a>';
               }

                //Tra ket qua
                html += '<a href="javascript:void(0)" onclick="btn_return_onclick(\'' + v_item_id + '\')" class="quick_action" title="Trả kết quả liên thông">';
                html += '<i class="icon-ok-sign"></i></a>';

                //Thong tin tien do
                html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\')" class="quick_action" title="Xem tiến độ">';
                html += '<i class="icon-ok-sign"></i></a>';

                $(this).html(html);
            });

        <?php endif;?>
    });

    function btn_return_onclick(record_id)
    {
        var f = document.frmMain;
        if (typeof(record_id) == 'undefined')
        {
            record_id = get_all_checked_checkbox(f.chk, ',');
        }

        if (record_id != '')
        {
            $("#hdn_item_id_list").val(record_id);
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn');
            return;
        }

        var url = '<?php echo $this->get_controller_url();?>dsp_exchange_record_result/' + record_id
                + '&pop_win=1'
                + '&record_type=' + $("#sel_record_type").val();

        showPopWin(url, 1000, 600, null, true);

    }

    function print_record_reject_for_citizen(p_record_id)
    {
    	var url = '<?php echo $this->get_controller_url();?>dsp_print_record_reject_for_citizen/' + p_record_id; 
        url += QS + 'pop_win=1';
        showPopWin(url, 800, 450, null, true);
    }
</script>
<?php $this->template->display('dsp_footer.php');
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];

//header
$this->template->title = 'Chuyển hồ sơ lên Sở';
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
    echo $this->hidden('hdn_do_action_method','do_go_forward_record');

    echo $this->hidden('record_type_code', $v_record_type_code);

    ?>
    <?php echo $this->dsp_div_notice($VIEW_DATA['active_role_text'] );?>

    <!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type);?>

    <div id="solid-button">
        <button type="button" name="trash" class="btn btn-primary" onclick="do_send_forward_onclick();" accesskey="2">
            <i class="icon-exchange"></i>
            Chuyển hồ sơ lên sở
        </button>
        <div class="clear" style="height: 10px;"></div>
    </div>
    <div class="clear"></div>
    <div id="procedure">
        <?php
        if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list')))
        {
            echo $this->render_form_display_all_record($arr_all_record, FALSE);
        }
        ?>
    </div>
    <div><?php echo $this->paging2($arr_all_record);?></div>
    <div class="button-area">
        <button type="button" name="trash" class="btn btn-primary" onclick="do_send_forward_onclick();" accesskey="2">
            <i class="icon-exchange"></i>
            Chuyển hồ sơ lên sở
        </button>
    </div>

    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="do_action">
            <a href="#do_action">Chuyển hồ sơ lên sở</a>
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
                case 'do_action':
                    do_send_forward_onclick(v_record_id);
                    break;

                case 'statistics':
                    dsp_single_record_statistics(v_record_id);
                    break;
            }
        });
        //Quick action
        <?php if (strtoupper($this->active_role) == _CONST_CHUYEN_HO_SO_LEN_SO_ROLE): ?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                html = '';

                //Hoan thanh thu ly
                //Thong tin tien do
                v_is_owner = $('.adminlist tr[data-item_id="' + v_item_id + '"]').attr('data-owner');
                if (v_is_owner == "1")
                {
                    html += '<a href="javascript:void(0)" onclick="do_send_forward_onclick(\'' + v_item_id + '\')" class="quick_action" title="Chuyển hồ sơ lên sở">';
                    html += '<i class="icon-mail-forward"></i></a>';
                }
                
                //Thong tin tien do
                html += '&nbsp;<a href="javascript:void(0)" onclick="dsp_record_statistics(\'' + v_item_id + '\')" class="quick_action" title="Xem tiến độ">';
                html += '<i class="icon-bar-chart"></i></a>';

                $(this).html(html);
            });
        <?php endif;?>
    });

    function do_send_forward_onclick(record_id)
    {
        var f = document.frmMain;

        //Danh sach ID Ho so da chon
        if (typeof(record_id) == 'undefined')
        {
            //Lay danh sach HS da chon
            v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');
        }
        else
        {
            v_selected_record_id_list = record_id;
        }

        $("#hdn_item_id_list").val(v_selected_record_id_list);

        if ( $("#hdn_item_id_list").val() == '')
        {
            alert('Chưa có hồ sơ nào được chọn!');
            return;
        }

        m = $("#controller").val() + $("#hdn_do_action_method").val();
        $("#frmMain").attr("action", m);

        f.submit();

    }


</script>
<?php $this->template->display('dsp_footer.php');
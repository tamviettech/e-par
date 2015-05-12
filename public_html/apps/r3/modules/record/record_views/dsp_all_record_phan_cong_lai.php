<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];

//header
$this->template->title = 'Phân công thụ lý hồ sơ';
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

    echo $this->hidden('record_type_code', $v_record_type_code);
    ?>
    <?php echo $this->dsp_div_notice($VIEW_DATA['active_role_text'] );?>
    <!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type);?>

    <div class="clear"></div>
    <div id="procedure">
        <?php if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list'))): ?>
            <?php echo $this->render_form_display_all_record($arr_all_record, FALSE); ?>
        <?php endif; ?>
    </div>
    <?php echo $this->paging2($arr_all_record);?>

    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="allot">
            <a href="#allot">Thay đổi Phân công thụ lý</a>
        </li>
        <li class="reject">
            <a href="#reject">Từ chối phê duyệt hồ sơ</a>
        </li>
        <li class="statistics">
            <a href="#statistics">Xem tiến độ</a>
        </li>
        <li class="paste">
            <a href="#paste">In phiếu yêu cầu bổ sung</a>
        </li>
        <li class="delete">
            <a href="#delete">Yêu cầu chuyển hồ sơ</a>
        </li>
    </ul>
</form>
<script>
    $(document).ready( function() {
        //Show context on each row
        $(".adminlist tr[role='presentation']").contextMenu({
            menu: 'myMenu'
        }, function(action, el, pos) {
            v_record_id = $(el).attr('data-item_id');
            switch (action){
                case 'allot':
                    dsp_reallot_single_record(v_record_id);
                    break;

                case 'reject':
                    //dsp_record_statistics(v_record_id);
                    void(0);
                    break;

                case 'statistics':
                    dsp_record_statistics(v_record_id);
                    break;
            }
        });

        //Quick action
        <?php if (strtoupper($this->active_role) == _CONST_PHAN_CONG_LAI_ROLE): ?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                html = '';

                //Phan cong thu ly lai
                html = '<a href="javascript:void(0)" onclick="dsp_reallot_single_record(\'' + v_item_id + '\')" class="quick_action" title="Thay đổi Phân công thụ lý" >';
                html += '<i class="icon-cog"></i></a>';

                //Thong tin tien do
                html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\')" class="quick_action" title="Xem tiến độ">';
                html += '<i class="icon-bar-chart"></i></a>';

                $(this).html(html);
            });

        <?php endif;?>
    });

    function allot_pop_win(url)
    {
        showPopWin(url, 1000, 600, null, true);
    }

    function dsp_reallot_single_record(record_id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_reallot/' + record_id
            + '/?record_type_code=' + $("#record_type_code").val()
            + '&pop_win=1';
        allot_pop_win(url);
    }
</script>
<?php $this->template->display('dsp_footer.php');
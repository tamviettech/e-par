<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];

//header
$this->template->title = 'Hồ sơ phải bổ sung, chưa thông báo cho công dân';
$this->template->display('dsp_header_pop_win.php');

?>
<div id="overDiv" style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></div>
<form name="frmMain" id="frmMain" action="" method="POST" style="background-color: white;">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method','update_record');
    echo $this->hidden('hdn_delete_method','delete_record');
    echo $this->hidden('hdn_handover_method','do_handover_record');
    echo $this->hidden('hdn_announce_method','do_announce_record');

    echo $this->hidden('record_type_code', $v_record_type_code);

    echo $this->hidden('hdn_supplement_status', 0);

    ?>
     <div class="clear" style="height: 10px">&nbsp;</div>
    <!--<div class="page-title">Hồ sơ phải bổ sung, chưa thông báo cho công dân</div>-->
    
    <?php echo $this->dsp_div_notice();?>
    <!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type);?>
    <!-- 
    <div id="solid-button">
        <input type="button" class="solid transfer" value="Thông báo cho Công dân"
               onclick="btn_announce_onclick();" />
    </div>
     -->

     <div class="clear"></div>
    
    <div id="procedure">
        <?php
        if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list')))
        {
            echo $this->render_form_display_all_record($arr_all_record, 1);
        }
        ?>
    </div>
    <div><?php echo $this->paging2($arr_all_record);?></div>
    <!--
    <div class="button-area">
        <input type="button" name="addnew" class="button transfer" value="Thông báo cho Công dân" onclick="btn_announce_onclick();"/>
    </div> -->
    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="announce">
            <a href="#announce">Thông báo cho công dân</a>
        </li>
        <li class="print">
            <a href="#print">In phiếu thông báo</a>
        </li>
        <li class="statistics">
            <a href="#statistics">Xem tiến độ</a>
        </li>
    </ul>
</form>
<script>
    $(document).ready(function() {
    	//Khong thongbao thoi han
    	$('.days-remain').html('');
    });

    get_supplement_notice();
    setInterval(get_supplement_notice, <?php echo _CONST_GET_NEW_RECORD_NOTICE_INTERVAL;?>);
    
    //Overwrite row_onclick
    function row_onclick(v_record_id)
    {
    	dsp_print_announce_for_citizen_onclick(v_record_id);
    }

    $(document).ready( function() {
        //Show context on each row
        $(".adminlist tr[role='presentation']").contextMenu({
            menu: 'myMenu'
        }, function(action, el, pos) {
            v_record_id = $(el).attr('data-item_id');
            switch (action){
                case 'announce':
                    btn_announce_onclick(v_record_id);
                    break;

                case 'print':
                	dsp_single_record_statistics(v_record_id);
                    break;

                case 'statistics':
                    dsp_single_record_statistics(v_record_id);
                    break;
            }
        });

        //Quick action
        $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
            v_item_id = $(this).attr('data-item_id');

            html = '';

            //In phieu thong bao
            html += '<a href="javascript:void(0)" onclick="dsp_print_announce_for_citizen_onclick(\'' + v_item_id + '\')" class="quick_action" title="In phiếu thông báo">';
            html += '<i class="icon-print"></i></a>';

           //Thong bao
            html += '<a href="javascript:void(0)" onclick="btn_announce_onclick(\'' + v_item_id + '\')" class="quick_action" title="Thông báo cho công dân">';
            html += '<i class="icon-comment"></i></a>';

            //Thong tin tien do
            html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\')" class="quick_action" title="Xem tiến độ">';
            html += '<i class="icon-bar-chart"></i></a>';

            $(this).html(html);
        });

    });

    function btn_announce_onclick(record_id)
    {
        var f=document.frmMain;
        if (typeof(record_id) == 'undefined')
        {
            record_id = get_all_checked_checkbox(f.chk, ',');
        }

        if (record_id == '')
        {
            alert('Chưa có hồ sơ nào được chọn!');
            return false;
        }

        if (confirm('Bạn chắc chắn trả thông báo yêu cầu bổ sung hồ sơ cho công dân?'))
        {
            $("#hdn_item_id_list").val(record_id);
            var m = $("#controller").val() + 'do_announce_record';
            $("#frmMain").attr('action', m);
            f.submit();
        }
    }

    function dsp_print_announce_for_citizen_onclick(record_id)
    {
    	var v_url = '<?php echo $this->get_controller_url();?>dsp_print_announce_for_citizen/'  + record_id + '/'; 
    	v_url += QS + 'record_id=1' + record_id;
    	v_url += '&pop_win=1';
    	showPopWin(v_url, 700, 450, null, true);
    }
    
    
</script>
<?php $this->template->display('dsp_footer_pop_win.php');
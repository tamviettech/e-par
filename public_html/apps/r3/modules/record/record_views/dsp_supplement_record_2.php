<?php
/**


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];

//header
$this->template->title = 'Hồ sơ phải bổ sung, đã nhận bổ sung';
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
    echo $this->hidden('hdn_handover_method','do_handover_supplement_record');
    echo $this->hidden('hdn_announce_method','do_receive_supplement_record');

    echo $this->hidden('record_type_code', $v_record_type_code);

    echo $this->hidden('hdn_supplement_status', 2);

    ?>
     <div class="clear" style="height: 10px">&nbsp;</div>
    <!--<div class="page-title">Hồ sơ phải bổ sung, đã nhận giấy tờ bổ sung</div>-->
    <?php echo $this->dsp_div_notice();?>
    <!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type);?>
    <div id="solid-button">
        <!--button ban giao-->
        <button type="button" name="trash" class="btn btn-primary" onclick="btn_handover_onclick();" >
            <i class="icon-exchange"></i>
            Bàn giao bổ sung
        </button>            
        <!--button in-->
        <button type="button" name="trash" class="btn btn-info" onclick="print_record_ho_for_bu();">
            <i class="icon-print"></i>
            In biên bàn bàn giao bổ sung
        </button>
    </div>
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
        <input type="button" name="addnew" class="button transfer" value="Bàn giao bổ sung" onclick="btn_handover_onclick();"/>
        <input type="button" name="btn_print" class="button print" value="In biên bản bàn giao hồ sơ bổ sung cho phòng chuyên môn" onclick="btn_handover_onclick();"/>
    </div> -->
    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="handover">
            <a href="#handover">Bàn giao cho phòng chuyên môn</a>
        </li>
        <li class="print">
            <a href="#print">In biên bản bàn giao hồ sơ bổ sung cho phòng chuyên môn</a>
        </li>
        <li class="statistics">
            <a href="#statistics">Xem tiến độ</a>
        </li>
    </ul>
</form>
<script>
    get_supplement_notice();
    setInterval(get_supplement_notice, <?php echo _CONST_GET_NEW_RECORD_NOTICE_INTERVAL;?>);

    //Overwrite row_onclick -> In bien nhan hồ sơ bo sung
    function row_onclick(v_record_id)
    {
    	print_record_ho_for_citizen(v_record_id);
    }
    
    $(document).ready( function() {

    	//Khong thongbao thoi han
    	$('.days-remain').html('');

    	
        //Show context on each row
        $(".adminlist tr[role='presentation']").contextMenu({
            menu: 'myMenu'
        }, function(action, el, pos) {
            v_record_id = $(el).attr('data-item_id');
            switch (action){
                case 'handover':
                    btn_handover_onclick(v_record_id);
                    break;

                case 'print':
                    Alert('In bien bản bàn giao hồ sơ bổ sung!');
                    break;

                case 'statistics':
                    dsp_single_record_statistics(v_record_id);
                    break;
            }
        });

        //Quick action
        $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
            v_item_id =   $(this).attr('data-item_id');

            html = '';

            //In phieu biên nhận hồ sơ bổ sung cho cong dan
            html += '<a href="javascript:void(0)" onclick="print_record_ho_for_citizen(\'' + v_item_id + '\')" class="quick_action" >';
            html += '<img src="' + SITE_ROOT + 'public/images/print_24x24.png" title="In phiếu biên nhận hồ sơ bổ sung" /></a>';

            /*
            //Ban giao
            html = '<a href="javascript:void(0)" onclick="btn_handover_onclick(\'' + v_item_id + '\')" class="quick_action" >';
            html += '<img src="' + SITE_ROOT + 'public/images/announce_16x16.png" title="Bàn giao cho phòng chuyên môn" /></a>';
            */

            /*
            //In phieu biên nhận hồ sơ bổ sung
            html += '<a href="javascript:void(0)" onclick="dsp_print_ho_supplement_record(\'' + v_item_id + '\')" class="quick_action" >';
            html += '<img src="' + SITE_ROOT + 'public/images/print_24x24.png" title="In biên bản bàn giao hồ sơ bổ sung cho phòng chuyên môn" /></a>';
            */

            //Thong tin tien do
            html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\')" class="quick_action" >';
            html += '<img src="' + SITE_ROOT + 'public/images/statistics-16x16.png" title="Xem tiến độ" /></a>';

            $(this).html(html);
        });
    });

    function btn_handover_onclick(record_id)
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

        if (confirm('Bạn chắc chắn bàn giao bổ sung các hồ sơ đã chọn?'))
        {
            $("#hdn_item_id_list").val(record_id);
            var m = $("#controller").val() + 'do_handover_supplement_record';
            $("#frmMain").attr('action', m);
            f.submit();
        }
    }

    function print_record_ho_for_citizen(p_record_id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_print_supplement_ho_for_citizen/' + p_record_id;

        showPopWin(url, 700, 450, null, true);
    }
    
    function print_record_ho_for_bu()
    {
        var f = document.frmMain;

        //Danh sach ID Ho so da chon
        v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');

        if (v_selected_record_id_list != '')
        {
            var url = '<?php echo $this->get_controller_url();?>dsp_print_supplement_ho_for_bu/' + v_selected_record_id_list + '/';
            url += QS + 'record_id_list=' + v_selected_record_id_list;
            url += '&record_type_code=' + $("#record_type_code").val();
            url += '&a=' + encodeURI($("#sel_record_type>option:selected").text());
            url += '&type=' + $("#hdn_handover_type").val();

            showPopWin(url, 700, 450, null, true);
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn!');
        }
    }

</script>
<?php $this->template->display('dsp_footer_pop_win.php');
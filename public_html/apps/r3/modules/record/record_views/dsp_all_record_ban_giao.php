<?php 
/**
// File name   : 
// Version     : 1.0.0.1
// Begin       : 2012-12-01
// Last Update : 2010-12-25
// Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
//Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

// E-PAR is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// E-PAR is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// See LICENSE.TXT file for more information.
*/

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');


//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];
$MY_TASK                = $VIEW_DATA['MY_TASK'];

$v_handover_type = 1; //Tu MC -> Chuyen mon
if (count($arr_all_record) >0)
{
    //Xac dinh xem ai ban giao? MOTCUA -> PHONG-CHUYEN-MON hay nguoc lai
    $dom_test = simplexml_load_string($arr_all_record[0]['C_XML_PROCESSING']);
    $r = $dom_test->xpath("//step[contains(@code,'" . _CONST_XML_RTT_DELIM . _CONST_KY_ROLE ."')]");
    if (sizeof($r)>0)
    {
        $v_handover_type = 2;
    }
}

//header
$this->template->title = 'Bàn giao hồ sơ';
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
    echo $this->hidden('hdn_handover_type',$v_handover_type);

    echo $this->hidden('record_type_code', $v_record_type_code);
    echo $this->hidden('MY_TASK', $MY_TASK);

    ?>
    <?php echo $this->dsp_div_notice($VIEW_DATA['active_role_text'] );?>
    <!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type);?>
    <div id="solid-button">
        <input type="button" class="solid transfer" value="Bàn giao"
               onclick="btn_handover_onclick();" />
        <input type="button" name="addnew" class="solid print" value="In giấy bàn giao"
               onclick="print_record_ho_for_bu();" />
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
    <!--
    <div class="button-area">
        <input type="button" name="addnew" class="button transfer" value="Bàn giao" onclick="btn_handover_onclick();"/>
        <input type="button" name="addnew" class="button print" value="In giấy bàn giao" onclick="print_record_ho_for_bu();"/>
    </div> -->
</form>
<script>

    $(function() {
    	$('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
            v_item_id =   $(this).attr('data-item_id');

            html = '';

            //Thong tin tien do
            html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\');" class="quick_action" >';
            html += '<img src="' + SITE_ROOT + 'public/images/statistics-16x16.png" title="Xem tiến độ" /></a>';

            $(this).html(html);
        });
    });

    function btn_handover_onclick()
    {
        var f = document.frmMain;

        //Lay danh sach HS da chon
        $("#hdn_item_id_list").val(get_all_checked_checkbox(f.chk, ','));

        if ( $("#hdn_item_id_list").val() == '')
        {
            alert('Chưa có hồ sơ nào được chọn!');
            return;
        }

        m = $("#controller").val() + $("#hdn_handover_method").val();
        $("#frmMain").attr("action", m);
        v_confirm_message = ($("#hdn_handover_type").val() == '1') ? 'Bạn chắc chắn bàn giao số hồ sơ đã chọn cho phòng chuyên môn' : 'Bạn chắc chắn bàn giao số hồ sơ đã chọn về bộ phận Một-Cửa?';
        if (confirm(v_confirm_message))
        {
            f.submit();
        }
    }

    function print_record_ho_for_bu()
    {
        var f = document.frmMain;

        //Danh sach ID Ho so da chon
        v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');

        if (v_selected_record_id_list != '')
        {

            var url = '<?php echo $this->get_controller_url();?>dsp_print_ho_for_bu/' + v_selected_record_id_list + '/';
            url += '&record_id_list=' + v_selected_record_id_list;
            url += '&record_type_code=' + $("#record_type_code").val();
            url += '&record_type_name=' + encodeURI($("#sel_record_type>option:selected").text());
            url += '&type=' + $("#hdn_handover_type").val();

            showPopWin(url, 1000, 600, null, true);
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn!');
        }
    }
</script>
<?php $this->template->display('dsp_footer.php');
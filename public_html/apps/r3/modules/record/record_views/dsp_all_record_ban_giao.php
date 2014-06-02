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
        <!--button ban giao-->
        <button type="button" name="trash" class="btn btn-primary" onclick="btn_handover_onclick();" accesskey="9">
            <i class="icon-exchange"></i>
            Bàn giao
        </button>
        
        <!--button in-->
        <button type="button" name="trash" class="btn btn-info" onclick="print_record_ho_for_bu();">
            <i class="icon-print"></i>
            In giấy bàn giao
        </button>
        <!--button in-->
        <button type="button" name="trash" class="btn btn-info" onclick="print_record_ho_for_bu(1);">
            <i class="icon-print"></i>
            In và bàn giao
        </button>
        <!--button in theo don vi tiep nhan (tat ca cac loai ho so don vi tiep nhan)-->
        <button type="button" name="trash" class="btn btn-info" onclick="select_bu_to_print();">
            <i class="icon-print"></i>
            In giấy bàn giao theo đơn vị tiếp nhận
        </button>
    </div>
    <div class="clear" style="height: 10px;"></div>
    
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
    var ret_ho_count = 0;
    $(function() {
    	//Pham vi thu tuc?
        v_cope = $("#sel_record_type>option:selected").attr("data-scope");
                    
    	$('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
            v_item_id =   $(this).attr('data-item_id');

            html = '';

            //Thong tin tien do
            html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\');" class="quick_action" >';
            html += '<img src="' + SITE_ROOT + 'public/images/statistics-16x16.png" title="Xem tiến độ" /></a>';

            <?php if (!Session::get('la_can_bo_cap_xa')):?>
                //Tra lai ho so ve xa
                if (v_cope == '1')
                {
                	 html += '<a href="javascript:void(0)" onclick="btn_stop_cross_over_record_onclick(\'' + v_item_id + '\');" class="quick_action" >';
                     html += '<img src="' + SITE_ROOT + 'public/images/stop-16x16.png" title="Không nhận" /></a>';
                }
            <?php endif;?>

            $(this).html(html);
        });
    });

    
    function btn_stop_cross_over_record_onclick(v_record_id)
    {
    	var url = '<?php echo $this->get_controller_url();?>dsp_stop_cross_over_record/' + v_record_id + '/';
        url += QS + 'record_id_list=' + v_record_id;
        url += '&record_type_code=' + $("#record_type_code").val();
        url += '&record_type_name=' + encodeURI($("#sel_record_type>option:selected").text());
        url += '&type=' + $("#hdn_handover_type").val();
        url += '&pop_win=1';

        showPopWin(url, 500, 250, null, true);
    }

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
        else
        {
          ret_ho_count = 0;
        }
    }
    //in ho so theo 1 loai thu tuc
    function print_record_ho_for_bu(ho)
    {
        var f = document.frmMain;
        if(typeof ho == 'undefined')
        {
            ho = 0;
        }
        
        //Danh sach ID Ho so da chon
        var v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');

        if (v_selected_record_id_list != '')
        {

            var url = '<?php echo $this->get_controller_url();?>dsp_print_ho_for_bu/' + v_selected_record_id_list + '/';
            url += QS + 'record_id_list=' + v_selected_record_id_list;
            url += '&record_type_code=' + $("#record_type_code").val();
            url += '&record_type_name=' + encodeURI($("#sel_record_type>option:selected").text());
            url += '&type=' + $("#hdn_handover_type").val();
            url += '&ho=' + ho;
            showPopWin(url, 1000, 600, returnPrint, true);
           
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn!');
        }
    }
    function returnPrint(retVal)
    {
        var ho = retVal.ho;
        if(parseInt(ho) == 1 && ret_ho_count == 0)
        {
            btn_handover_onclick();
            ret_ho_count++;
        }
    }
    //in ho so theo nhieu loai thu tuc
    function select_bu_to_print()
    {
        url =  url = '<?php echo $this->get_controller_url();?>dsp_select_bu_to_print/';
        
        showPopWin(url, 1000, 600, null, true);
    }
</script>
<?php $this->template->display('dsp_footer.php');
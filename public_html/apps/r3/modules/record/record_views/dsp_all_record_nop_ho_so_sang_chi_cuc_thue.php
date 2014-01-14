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

<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

//View data
$arr_all_record_type = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code  = $VIEW_DATA['record_type_code'];
$arr_all_record      = $VIEW_DATA['arr_all_record'];

//header
$this->template->title = 'Nộp hồ sơ sang chi cục thuế';
$this->template->display('dsp_header.php');
?>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', '0');
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');
    echo $this->hidden('hdn_send_to_tax_method', 'do_send_to_tax');

    echo $this->hidden('record_type_code', $v_record_type_code);
    ?>
    <?php echo $this->dsp_div_notice($VIEW_DATA['active_role_text']); ?>

    <!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type); ?>

    <div id="solid-button">
        <input type="button" class="solid transfer" value="Chuyển"
               onclick="btn_send_to_tax_onclick();" />
        <input type="button" class="solid print" value="In Giấy bàn giao"
               onclick="print_record_ho_for_tax();" />
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
    <div><?php echo $this->paging2($arr_all_record); ?></div>
    <div class="button-area">
        <input type="button" name="btn_send_to_tax" class="button transfer" value="Chuyển" onclick="btn_send_to_tax_onclick();"/>
        <input type="button" name="btn_print" class="button print" value="In Giấy bàn giao" onclick="print_record_ho_for_tax();"/>
    </div>

    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="transfer">
            <a href="#send_to_tax">Chuyển hồ sơ sang chi cục thuế</a>
        </li>
        <li class="print">
            <a href="#print_ho">In giấy bàn giao</a>
        </li>
        <li class="print">
            <a href="#print_announce">In Giấy hẹn nhận thông báo thuế</a>
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
                           switch (action) {
                               case 'send_to_tax':
                                   btn_send_to_tax_onclick(v_record_id);
                                   break;
                               case 'print_ho':
                                   print_record_ho_for_tax(v_record_id);
                                   break;
                               case 'print_announce':
                                   print_announce_tax(v_record_id);
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

                           //Thong tin tien do
                           html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\');" class="quick_action" >';
                           html += '<img src="' + SITE_ROOT + 'public/images/statistics-16x16.png" title="Xem tiến độ" /></a>';

                           html += '<a href="javascript:void(0)" onclick="btn_send_to_tax_onclick(\'' + v_item_id + '\');" class="quick_action" >';
                           html += '<img src="' + SITE_ROOT + 'apps/r3/images/btn_transfer_16x16.png" title="Chuyển hồ sơ sang chi cục thuế" /></a>';
                           
                           html += '<a href="javascript:void(0)" onclick="print_record_ho_for_tax(\'' + v_item_id + '\');" class="quick_action" >';
                           html += '<img src="' + SITE_ROOT + 'apps/r3/images/print_16x16.png" title="In Giấy bàn giao" /></a>';
                           
                           html += '<a href="javascript:void(0)" onclick="print_announce_tax(\'' + v_item_id + '\');" class="quick_action" >';
                           html += '<img src="' + SITE_ROOT + 'apps/r3/images/print_16x16.png" title="In Giấy hẹn nhận thông báo nộp thuế" /></a>';
                           $(this).html(html);
                       });

                   });

                   function btn_send_to_tax_onclick(record_id)
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

                       if ($("#hdn_item_id_list").val() == '')
                       {
                           alert('Chưa có hồ sơ nào được chọn!');
                           return;
                       }

                       m = $("#controller").val() + $("#hdn_send_to_tax_method").val();
                       $("#frmMain").attr("action", m);

                       f.submit();
                   }

                   function print_record_ho_for_tax(record_id)
                   {
                       var f = document.frmMain;
                       if (typeof record_id != 'undefined')
                           v_selected_record_id_list = record_id;
                       else
                           v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');

                       if (v_selected_record_id_list != '')
                       {
                           var url = '<?php echo $this->get_controller_url(); ?>dsp_print_ho_for_tax/' + v_selected_record_id_list + '/';
                           url += QS + 'record_id_list=' + v_selected_record_id_list;
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

                   function print_announce_tax(v_selected_record_id_list) {
                       if (!v_selected_record_id_list)
                       {
                           alert('Chưa có hồ sơ nào được chọn!');
                           return;
                       }
                       url = '<?php echo $this->get_controller_url(); ?>dsp_print_announce_tax/' + v_selected_record_id_list + '/';
                       window.showPopWin(url, 800, 600, function(data) {

                       });
                   }
</script>
<?php
$this->template->display('dsp_footer.php');
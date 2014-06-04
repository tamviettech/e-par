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
$this->template->title = 'Ký duyệt hồ sơ';
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
    echo $this->hidden('hdn_handover_method', 'do_handover_record');

    echo $this->hidden('record_type_code', $v_record_type_code);

    echo $this->hidden('hdn_role', _CONST_KY_ROLE);
    ?>
    <?php echo $this->dsp_div_notice($VIEW_DATA['active_role_text']); ?>
    <!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type); ?>
    
    <!--btn rut ho so-->
    <div id="solid-button">
        <button type="button" name="trash" class="btn btn-danger" onclick="btn_reject_onclick();">
            <i class="icon-ban-circle"></i>
            Từ chối hồ sơ
        </button>
    </div>
    <div class="clear" style="height: 10px;">&nbsp;</div>

    <div id="procedure">
        <?php
        if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list')))
        {
            echo $this->render_form_display_all_record($arr_all_record, FALSE);
        }
        ?>
    </div>
    <div><?php echo $this->paging2($arr_all_record); ?></div>

    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="print">
            <a href="#print">In đơn xin rút hồ sơ cho công dân</a>
        </li>
        <li class="delete">
            <a href="#delete">Rút hồ sơ</a>
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
                        case 'print':
                            btn_print_onclick(v_record_id);
                            break;

                        case 'delete':
                            btn_delete_onclick(v_record_id);
                            break;
                    }
                });

                //Quick action

                $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                    v_item_id = $(this).attr('data-item_id');

                    html = '';

                    html += '<a href="javascript:void(0)" onclick="btn_print_onclick(\'' + v_item_id + '\')" class="quick_action" >';
                    html += '<img src="' + SITE_ROOT + 'apps/r3/images/print_16x16.png" title="In Đơn xin rút hồ sơ" /></a>';

                    //Thong tin tien do
                    html += '<a href="javascript:void(0)" onclick="btn_reject_onclick(\'' + v_item_id + '\')" class="quick_action" >';
                    html += '<img src="' + SITE_ROOT + 'apps/r3/images/btn_delete.png" title="Rút hồ sơ"/></a>';
                    $(this).html(html);
                });

            });

            function btn_print_onclick(record_id)
            {
                var f = document.frmMain;

                //Danh sach ID Ho so da chon
                if (typeof(record_id) == 'undefined')
                {
                    v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');
                }
                else
                {
                    v_selected_record_id_list = record_id;
                }

                if (v_selected_record_id_list !== '')
                {
                    var url = '<?php echo $this->get_controller_url(); ?>print_cancel_request/' + v_selected_record_id_list
                            + '/?record_type_code=' + $("#record_type_code").val()
                            + '&pop_win=1';

                    showPopWin(url, 1000, 600, null, true);
                }
                else
                {
                    alert('Chưa có hồ sơ nào được chọn!');
                }

            }
    /**
     * Comment fc hiển thị dialog reject
     */
    function btn_reject_onclick(record_id)
    {
         var f = document.frmMain;
        if(typeof(record_id) == 'undefined' || record_id.trim().length == 0)
        {
            //Danh sach ID Ho so da chon
            var v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');
        }
        else
        {
            var v_selected_record_id_list = record_id;
        }
    	$('#hdn_item_id_list').val(v_selected_record_id_list);
            if (v_selected_record_id_list != '')
            {
                var url = $('#controller').val() +'dsp_reject_drawn_record/' + v_selected_record_id_list
                    + '/?record_type_code=' + $("#record_type_code").val()
                    + '&pop_win=1';

                   showPopWin(url, 800, 500, null, true);
            }
            else
            {
                alert('Chưa có hồ sơ nào được chọn!');
            }
    }

</script>
<?php
$this->template->display('dsp_footer.php');
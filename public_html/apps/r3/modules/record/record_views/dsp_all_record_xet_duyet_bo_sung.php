<?php 
/**
 * @copyright	Copyright (C) 2012 Tam Viet Tech. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Ngo Duc Lien <liennd@gmail.com>
 * @author		Luong Thanh Binh <ltbinh@gmail.com>
 */

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];

//header
$this->template->title = 'Phê duyệt và trình ký';
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
    <div id="solid-button">
         <!--button xet duyet-->
            <button type="button" name="trash" class="btn" onclick="btn_dsp_approval_onclick();">
                <i class="icon-ok-sign"></i>
                Xét duyệt
            </button>
         <!--button in-->
        <button type="button" name="trash" class="btn" onclick="print_record_ho_for_bu_onclick();">
            <i class="icon-print"></i>
            In giấy bàn giao
        </button>
               <!-- 
        <input type="button" class="solid print" value="In danh sách ký duyệt"
               onclick="print_record_ho_for_bu();" /> -->
    </div>
    <div class="clear">&nbsp;</div>

    <div id="procedure">
        <?php if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list'))): ?>
            <?php echo $this->render_form_display_all_record($arr_all_record, FALSE); ?>
        <?php endif; ?>
    </div>
    <?php echo $this->paging2($arr_all_record);?>

    <!-- Buttons
    <div class="button-area">
        <input type="button" name="btn_allot" class="button print" value="In danh sách ký duyệt" onclick="alert('In phieu ban giao');" />
        <input type="button" name="btn_dsp_allot" class="button allot" value="Phê duyệt và trình lãnh đạo   " onclick="btn_dsp_approval_onclick();" />
    </div>-->

    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="approval">
            <a href="#approval">Xét duyệt</a>
        </li>
        <li class="statistics">
            <a href="#statistics">Xem tiến độ</a>
        </li>
        <li class="print">
            <a href="#paste">In phiếu yêu cầu bổ sung</a>
        </li>
        <li class="print">
            <a href="#delete">In phiếu trình lãnh đạo</a>
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
                case 'approval':
                    btn_dsp_approval_onclick(v_record_id);
                    break;

                case 'statistics':
                    dsp_single_record_statistics(v_record_id);
                    break;
            }
        });

        //Quick action
        <?php if (strtoupper($this->active_role) == _CONST_XET_DUYET_BO_SUNG_ROLE): ?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                html = '';

                //Phe duyet
                html += '<a href="javascript:void(0)" onclick="btn_dsp_approval_onclick(\'' + v_item_id + '\')" class="quick_action" title="Phê duyệt và trình lãnh đạo">';
                html += '<i class="icon-ok-sign"></i></a>';

                //Thong tin tien do
                html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\')" class="quick_action" title="Xem tiến độ">';
                html += '<i class="icon-bar-chart"></i></a>';

                $(this).html(html);
            });

        <?php endif;?>
    });

    function btn_dsp_approval_onclick(record_id)
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

        if (v_selected_record_id_list != '')
        {
            var url = '<?php echo $this->get_controller_url();?>dsp_approval_supplement/' + v_selected_record_id_list
                + '/?record_type_code=' + $("#record_type_code").val()
                + '&pop_win=1';

            showPopWin(url, 1000, 600, null, true);
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn!');
        }
    }

    function btn_dsp_print_supplement_request_onclick(record_id)
    {
        alert(1);
    }

    function print_record_ho_for_bu_onclick(record_id)
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

        if (v_selected_record_id_list != '')
        {
            var url = '<?php echo $this->get_controller_url();?>dsp_print_ho_between_2_bu/' + v_selected_record_id_list
                + '/?record_type_code=' + $("#record_type_code").val()
                + '&pop_win=1';
                + '&type=1';

            showPopWin(url, 1000, 600, null, true);
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn!');
        }
    }
</script>
<?php $this->template->display('dsp_footer.php');
<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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
    echo $this->hidden('MY_TASK', $MY_TASK);

    ?>
    <?php echo $this->dsp_div_notice($VIEW_DATA['active_role_text'] );?>
    <!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type);?>

    <div id="solid-button">
        <input type="button" class="solid allot" value="Phân công thụ lý"
               onclick="btn_dsp_allot_onclick();" />
         
        <?php if (isset($this->arr_roles['XET_DUYET'])): ?>
            <input type="button" name="addnew" class="solid reject" value="Từ chối phê duyệt hồ sơ" onclick="btn_reject_onclick();" />
            <input type="button" name="btn_supplement_request" class="solid supplement" value="Yêu cầu bổ sung hồ sơ" onclick="btn_supplement_request_onclick();" />
        <?php endif;?>
        
        <input type="button" name="addnew" class="solid rollback" value="Không nhận, do chưa bàn giao đủ giấy tờ"
               onclick="btn_rollback_onclick();" />
    </div>
    <div class="clear"></div>

    <div id="procedure">
        <?php if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list'))): ?>
            <?php echo $this->render_form_display_all_record($arr_all_record, FALSE); ?>
        <?php endif; ?>
    </div>
    <?php echo $this->paging2($arr_all_record);?>

    <!-- Buttons -->
    <!-- 
    <div class="button-area">
        <input type="button" name="btn_dsp_allot" class="button allot" value="Phân công thụ lý" onclick="btn_dsp_allot_onclick();" />
        <?php if (isset($this->arr_roles['XET_DUYET'])): ?>
            <input type="button" name="btn_allot" class="button reject" value="Từ chối phê duyệt hồ sơ" onclick="btn_reject_onclick();" />
        <?php endif;?>
        <input type="button" name="btn_rollback" class="button exchange" value="Không nhận, do chưa bàn giao đủ giấy tờ" onclick="btn_rollback_onclick();" />
    </div>
     -->

    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="allot">
            <a href="#allot">Phân công thụ lý</a>
        </li>
        <li class="reject">
            <a href="#reject">Từ chối phê duyệt hồ sơ</a>
        </li>
        <li class="statistics">
            <a href="#statistics">Xem tiến độ</a>
        </li>
        <li class="print">
            <a href="#print">In phiếu yêu cầu bổ sung</a>
        </li>
        <li class="rollback">
            <a href="#rollback">Yêu cầu chuyển hồ sơ</a>
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
                    dsp_allot_single_record(v_record_id);
                    break;

                case 'reject':
                    dsp_record_statistics(v_record_id);
                    break;

                case 'statistics':
                    dsp_record_statistics(v_record_id);
                    break;

                case 'rollback':
                    btn_rollback_onclick(v_record_id);
                    break;

            }
        });

        //Quick action
        <?php if (strtoupper($this->active_role) == _CONST_PHAN_CONG_ROLE): ?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                html = '';

                //Phan cong thu ly
                html = '<a href="javascript:void(0)" onclick="dsp_allot_single_record(\'' + v_item_id + '\');" class="quick_action" >';
                html += '<img src="' + SITE_ROOT + 'public/images/allot-16x16.png" title="Phân công thụ lý" /></a>';

                //Thong tin tien do
                html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\');" class="quick_action" >';
                html += '<img src="' + SITE_ROOT + 'public/images/statistics-16x16.png" title="Xem tiến độ" /></a>';

                $(this).html(html);
            });

        <?php endif;?>
    });

    function allot_pop_win(url)
    {
        showPopWin(url, 1000, 600, null, true);
    }

    function dsp_allot_single_record(record_id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_allot/' + record_id
            + '/?record_type_code=' + $("#record_type_code").val()
            + '&pop_win=1';
        allot_pop_win(url);
    }

    function btn_dsp_allot_onclick()
    {
        var f = document.frmMain;

        //Danh sach ID Ho so da chon
        v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');

        if (v_selected_record_id_list != '')
        {
            var url = '<?php echo $this->get_controller_url();?>dsp_allot/' + v_selected_record_id_list
                + '/?record_type_code=' + $("#record_type_code").val()
                + '&pop_win=1';

            allot_pop_win(url);
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn!');
        }
    }

    function btn_reject_onclick()
    {
    	var f = document.frmMain;

        //Danh sach ID Ho so da chon
        v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');

        if (v_selected_record_id_list != '')
        {
            var url = '<?php echo $this->get_controller_url();?>dsp_reject/' + v_selected_record_id_list
                + '/?record_type_code=' + $("#record_type_code").val()
                + '&pop_win=1';

            allot_pop_win(url);
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn!');
        }
    }

    function btn_rollback_onclick(record_id_list)
    {
        var f = document.frmMain;

        if (typeof(record_id_list) == 'undefined')
        {
            record_id_list = get_all_checked_checkbox(f.chk, ',');
        }

        if (record_id_list != '')
        {
            var url = '<?php echo $this->get_controller_url();?>dsp_rollback/' + record_id_list
                + '/?record_type_code=' + $("#record_type_code").val()
                + '&pop_win=1'
                + '&role=<?php echo _CONST_PHAN_CONG_ROLE;?>';

            allot_pop_win(url);
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn!');
        }
    }

    function btn_supplement_request_onclick()
    {
    	var f = document.frmMain;

        //Danh sach ID Ho so da chon
        v_selected_record_id_list = get_all_checked_checkbox(f.chk, ',');

        if (v_selected_record_id_list != '')
        {
        	var url = '<?php echo $this->get_controller_url();?>dsp_supplement_request_record/' + v_selected_record_id_list
            + '/?record_type_code=' + $("#record_type_code").val()
            + '&pop_win=1';

        	allot_pop_win(url);
        }
        else
        {
            alert('Chưa có hồ sơ nào được chọn!');
        }
    }

</script>
<?php $this->template->display('dsp_footer.php');
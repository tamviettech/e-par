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

//header
$this->template->title = 'Xác nhận hồ sơ liên thông';
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

    echo $this->hidden('hdn_role',$this->active_role);

    echo $this->hidden('record_type_code', $v_record_type_code);
    ?>
    <?php echo $this->dsp_div_notice($VIEW_DATA['active_role_text'] );?>
    <!-- filter -->
    <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type);?>

    <!--
    <div id="solid-button">
        <input type="button" class="solid commit" value="Xác nhận"
               onclick="btn_dsp_exec_onclick();" />
    </div> -->
    <div class="clear"></div>

    <div id="procedure">
        <?php if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list'))): ?>
            <?php echo $this->render_form_display_all_record($arr_all_record, FALSE); ?>
        <?php endif; ?>
    </div>
	<div><?php echo $this->paging2($arr_all_record);?></div>
</form>
<script>
    $(document).ready( function() {
        //Quick action
        <?php if (strtoupper($this->active_role) == _CONST_XAC_NHAN_HO_SO_LIEN_THONG_ROLE): ?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                html = '';

                //Hoan thanh thu ly
                //Thong tin tien do
                v_is_owner = $('.adminlist tr[data-item_id="' + v_item_id + '"]').attr('data-owner');
                if (v_is_owner == "1")
                {
                    html += '<a href="javascript:void(0)" onclick="dsp_send_confirmation_response(\'' + v_item_id + '\')" class="quick_action" >';
                    html += '<img src="' + SITE_ROOT + 'public/images/btn_commit.png" title="Hoàn thành thụ lý" /></a>';
                }
                //Thong tin tien do
                html += '&nbsp;<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\')" class="quick_action" >';
                html += '<img src="' + SITE_ROOT + 'public/images/statistics-16x16.png" title="Xem tiến độ" /></a>';

                $(this).html(html);
            });

        <?php endif;?>
    });


    function dsp_send_confirmation_response(record_id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_send_confirmation_response/' + record_id
            + '/?record_type_code=' + $("#record_type_code").val()
            + '&pop_win=1&hdn_item_id=' + record_id;
        showPopWin(url, 900, 600, null, true);
    }

</script>
<?php $this->template->display('dsp_footer.php');
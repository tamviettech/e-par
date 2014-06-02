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
$this->template->title = 'Ý kiến lãnh đạo';
$this->template->display('dsp_header.php');


$v_record_no_filter = isset($_POST['txt_record_no_filter']) ? replace_bad_char($_POST['txt_record_no_filter']) : '';
$v_citizen_name_filter = isset($_POST['txt_citizen_name_filter']) ? replace_bad_char($_POST['txt_citizen_name_filter']) : '';


?>
<form name="frmMain" id="frmMain"
	action="" method="POST">
	<?php
	echo $this->hidden('controller',$this->get_controller_url());
	echo $this->hidden('hdn_item_id','0');
	echo $this->hidden('hdn_item_id_list','');

	echo $this->hidden('hdn_dsp_single_method','dsp_single_record');
	echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
	echo $this->hidden('hdn_update_method','update_record');
	echo $this->hidden('hdn_delete_method','delete_record');

	echo $this->hidden('hdn_role', _CONST_BAN_GIAO_ROLE);
	//echo $this->hidden('MY_TASK', $MY_TASK);
	?>
	<?php //echo $this->dsp_div_notice($VIEW_DATA['active_role_text'] );?>
	<!-- filter -->
	<div id="div_filter">
		<table style="width: 100%" class="none-border-table">
			<tr>
				<td width="15%"><label>Mã loại hồ sơ (Alt+1)</label>
				</td>
				<td>
                    <input type="text" name="txt_record_type_code"
                            id="txt_record_type_code"
                            value="<?php echo $v_record_type_code; ?>"
                            class="inputbox upper_text" size="10" maxlength="10"
                            onkeypress="txt_record_type_code_onkeypress(event);"
                            autofocus="autofocus" accesskey="1" 
                    />&nbsp;

                                <select
					name="sel_record_type" id="sel_record_type"
					style="width: 75%; color: #000000;"
					onchange="sel_record_type_onchange(this)">
						<option value="">-- Chọn loại hồ sơ --</option>
                                                <?php foreach($arr_all_record_type as $v): ?>
                                                    <?php $selected = $v_record_type_code == $v['C_CODE'] ? 'selected' : '' ?>
                                                    <option value="<?php echo $v['C_CODE'] ?>" <?php echo $selected ?>>
                                                        <?php echo $v['C_NAME'] ?>
                                                    </option>
                                                <?php endforeach; ?>
				</select>
				</td>
			</tr>
			<tr>
			    <td><label>Mã hồ sơ:</label></td>
			    <td>
			        <input type="text" name="txt_record_no_filter" style="width: 50%" value="<?php echo $v_record_no_filter;?>" />
			    </td>
			</tr>
			<tr>
			    <td>
			        <label>Người đăng ký:</label>
			    </td>
			    <td>
			        <input  type="text" name="txt_citizen_name_filter" style="width: 50%" value="<?php echo $v_citizen_name_filter;?>" />
			        <input type="button" name="btn_search" value="Lọc" class="btn solid search" onclick="this.form.submit()" />
			    </td>
			</tr>
		</table>
	</div>


	<div class="clear">&nbsp;</div>

	<div id="procedure">
		<?php
		if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'lookup')))
		{
		    echo $this->render_form_display_all_record($arr_all_record,0);
        }?>
	</div>
	<div><?php echo $this->paging2($arr_all_record);?></div>
	<!-- Context menu -->
	<ul id="myMenu" class="contextMenu">
		<li class="statistics_comment">
            <a href="#statistics_comment">Ý kiến chỉ đạo</a>
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
            switch (action){
                case 'statistics':
                	dsp_single_record_statistics(v_record_id);
                    break;

                case 'statistics_comment':
                	dsp_single_record_statistics(v_record_id, 'comment');
                    break;
            }
        });

        $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
            v_item_id =   $(this).attr('data-item_id');

            html = '';

            //Print
            html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\', \'comment\')" class="quick_action">';
            html += '<img src="' + SITE_ROOT + 'public/images/icon-32-comment-add.png" title="Ý kiến chỉ đạo" /></a>';

            //Thong tin tien do
            html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\')" class="quick_action">';
            html += '<img src="' + SITE_ROOT + 'public/images/statistics-16x16.png" title="Xem tiến độ" /></a>';

            $(this).html(html);
        });

    });

    function print_record_ho_for_citizen(p_record_id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_print_ho_for_citizen/' + p_record_id;

        showPopWin(url, 1000, 600, null, true);
    }
</script>
<?php $this->template->display('dsp_footer.php');
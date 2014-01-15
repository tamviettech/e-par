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

//display header
$this->template->title = 'Cập nhật biểu mẫu';
$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_record_type_code = get_request_var('sel_record_type');
$v_record_type_name = get_request_var('record_type_name');

$arr_all_form_file = Array();
array_push($arr_all_form_file, $this->get_xml_config($v_record_type_code, 'form_struct'));
array_push($arr_all_form_file, $this->get_xml_config($v_record_type_code, 'workflow'));
array_push($arr_all_form_file, $this->get_xml_config($v_record_type_code, 'list'));
array_push($arr_all_form_file, $this->get_xml_config($v_record_type_code, 'lookup'));
array_push($arr_all_form_file, $this->get_xml_config($v_record_type_code, 'auto_lock_unlock'));
array_push($arr_all_form_file, $this->get_xml_config($v_record_type_code, 'result'));
array_push($arr_all_form_file, $this->get_xsl_ho_teplate($v_record_type_code));
array_push($arr_all_form_file, SERVER_ROOT . 'public' . DS . 'xml' . DS .'xml_phuong_xa.xml');
array_push($arr_all_form_file, SERVER_ROOT . 'public' . DS . 'xml' . DS .'xml_unit_info.xml');

$v_selected_file_path = get_post_var('sel_form','', FALSE);
$v_selected_file_content = '';
if (file_exists($v_selected_file_path))
{
    $v_selected_file_content = file_get_contents($v_selected_file_path);
}
?>
<form name="frmMain" method="post" id="frmMain" action="" >
    <?php echo $this->hidden('hdn_record_type_code',$v_record_type_code);?>
    <?php echo $this->hidden('hdn_selected_file_path',$v_selected_file_path);?>
    <?php echo $this->hidden('hdn_record_type_name',$v_record_type_name);?>
    <h4>Cập nhật biểu mẫu: <?php echo $v_record_type_code;?> - <?php echo $v_record_type_name;?></h4>
    <b>Chọn file: </b>
    <select name="sel_form" onchange="sel_form_onchange(this)">
        <option value="">-- Chọn biểu mẫu--</option>
        <?php foreach ($arr_all_form_file as $v_path): ?>
            <option value="<?php echo $v_path;?>" <?php echo ($v_selected_file_path == $v_path) ? ' selected' : '';?>><?php echo pathinfo($v_path, PATHINFO_BASENAME  );?></option>
        <?php endforeach; ?>
    </select>
    <?php 
    if (isset($_GET['ok']))
    {
        if ($_GET['ok'] == 0)
        {
            echo '<span style="color:#CE4B27">Cập nhật file ' . $v_selected_file_path . ' thất bại! </span>';
        }
        else
        {
            echo '<span style="color:#468847">Cập nhật file ' . $v_selected_file_path . ' thành công! </span>';
        }
    }?>
    <textarea name="txt_file_content" style="width: 900px; margin: 2px 0px; height: 400px;font-family: Consolas, Monaco, monospace;font-size: 12.5px;background: #F9F9F9;"><?php echo $v_selected_file_content;?></textarea>
    <!-- Button -->
	<div class="button-area">
		<input type="button" name="update" class="button save" 	value="<?php echo __('update'); ?> (Alt+2)"	onclick="btn_update_all_form_onclick();" accesskey="2" />
		<?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
	</div>
</form>
<script>
    function btn_update_all_form_onclick()
    {
        $("#frmMain").attr('action', "<?php echo $this->get_controller_url();?>update_all_form");
        $("#frmMain").submit();
    }
    function sel_form_onchange(sel)
    {
        if (sel.value != '')
        {
            sel.form.hdn_selected_file_path.value = sel.value;
            sel.form.submit();
        }
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');
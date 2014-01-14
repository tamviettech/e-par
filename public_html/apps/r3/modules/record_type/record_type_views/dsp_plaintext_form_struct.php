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
$this->template->title = 'Cập nhật cấu trúc mẫu đơn';
$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_record_type_code = get_request_var('sel_record_type');
$xml_file_path = $this->get_xml_config($v_record_type_code, 'form_struct');

if (is_file($xml_file_path))
{
    $v_xml_string = file_get_contents($xml_file_path);
}
else
{
    $v_xml_string = '';
    $xml_file_path = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . $v_record_type_code . DS . $v_record_type_code . '_' . 'form_struct' . '.xml';
}
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>/update_plaintext_form_struct" >
    <?php echo $this->hidden('hdn_record_type_code',$v_record_type_code);?>
    <?php echo $this->hidden('hdn_xml_file_path',$xml_file_path);?>
    <textarea name="txt_xml_string" style="width: 954px; margin: 2px 0px; height: 466px;"><?php echo $v_xml_string;?></textarea>
    <!-- Button -->
	<div class="button-area">
		<input type="button" name="update" class="button save" 	value="<?php echo __('update'); ?> (Alt+2)"	onclick="btn_update_plaintext_formstruct_onclick();" accesskey="2" />
		<?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
	</div>
</form>
<script>
    function btn_update_plaintext_formstruct_onclick()
    {
        $("#frmMain").submit();
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');
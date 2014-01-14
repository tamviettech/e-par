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
$this->template->title = 'Kết quả hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
?>
<form name="frmMain" id="frmMain" action="<?php echo $this->get_controller_url();?>do_return_record" method="POST">
	<?php
	echo $this->hidden('controller',$this->get_controller_url());
	echo $this->hidden('hdn_item_id',$v_record_id);
	echo $this->hidden('hdn_item_id_list',$v_record_id);
	echo $this->hidden('sel_record_type',$record_type);
	echo $this->hidden('hdn_update_method','do_return_record');
	echo $this->hidden('pop_win','1');

    echo $this->hidden('XmlData','');
    ?>
    <div id="record_result">
        <?php echo $this->transform($this->get_xml_config(NULL, 'result')); ?>
	</div>
    <!-- Button -->
	<div class="button-area">
		<input type="button" name="update" class="button save" value="<?php echo __('update'); ?> (Alt+2)" onclick="btn_update_onclick();" accesskey="2" />
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
	</div>
</form>
<script>
    $(document).ready(function() {
        <?php 
        $dom_workflow = simplexml_load_file($this->get_xml_config($record_type, 'workflow'));
        $dom_workflow_record_result = $dom_workflow->xpath('//results');
        $arr_workflow_record_result = $dom_workflow_record_result[0]->result;
        	
        if ($arr_workflow_record_result != NULL)
        {
            foreach ($arr_workflow_record_result as $v_result_id)
            {
                $v_result_id      = strval($v_result_id);
                echo "$('#$v_result_id').attr('checked','checked');\n";
            }//end foreach $arr_workflow_record_result
        }
        ?>
    });
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');